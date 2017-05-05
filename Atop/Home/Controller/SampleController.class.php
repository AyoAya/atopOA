<?php 
namespace Home\Controller;
use Think\Model;
use Think\Page;

class SampleController extends AuthController {

    //初始化样品管理首页
    public function index(){
        $person = M('Sample');
        //是否有查询
        if(I('get.search')){
            $count = $person->where('order_num LIKE "%'.I('get.search').'%" OR create_person_name LIKE "%'.I('get.search').'%"')->count();
            $this->assign( 'search' , I('get.search') );
            //print_r($count);
        }else{
            $count = $person->count();
        }
        //数据分页
        $page = new Page($count,15);
        $page->setConfig('prev','<span aria-hidden="true">上一页</span>');
        $page->setConfig('next','<span aria-hidden="true">下一页</span>');
        $page->setConfig('first','<span aria-hidden="true">首页</span>');
        $page->setConfig('last','<span aria-hidden="true">尾页</span>');
        if(C('PAGE_STATUS_INFO')){
            $page->setConfig ( 'theme', '<li><a href="javascript:void(0);">当前%NOW_PAGE%/%TOTAL_PAGE%</a></li>  %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%' );
        }
        //是否需要显示查询结果
        if(I('get.search')){
            $sampleResult = $person->where('order_num LIKE "%'.I('get.search').'%" OR create_person_name LIKE "%'.I('get.search').'%"')->order('id DESC')->limit($page->firstRow.','.$page->listRows)->select();
        }else{
            $sampleResult = $person->order('id DESC')->limit($page->firstRow.','.$page->listRows)->select();
        }
        $pageShow = $page->show();
        $sample_detail = M('Sample_detail');
        $user = M('user');
        foreach ($sampleResult as $key=>&$value) {
            //与订单详情表对接
            $value['child'] = $sample_detail
                                ->field('a.id,a.pn,a.detail_assoc,a.product_id,a.count,a.brand,a.model,a.note,a.requirements_date,a.expect_date,a.actual_date,a.manager,a.detail_state,a.now_step,b.type')
                                ->table(C('DB_PREFIX').'sample_detail a,'.C('DB_PREFIX').'productrelationships b')
                                ->where('a.detail_assoc="' . $value['id'] . '" AND a.product_id=b.id')
                                ->select();
            //订单状态颜色
            //print_r($value['order_state'].'----');
            if ($value['order_state'] == 'N') {
                $value['color'] = '#428bca';
            } elseif ($value['order_state'] == 'Y') {
                $value['color'] = '#5cb85c';
            } else {
                $value['color'] = '#d9534f';
            }
            //统计子数据条数
            foreach ($value['child'] as $name => $id) {
                $value['total'] = $sample_detail->where('detail_assoc="' . $value['id'] . '"')->count();
            }
            //与user表对接
            $value['u'] = $user->where('id="' . $value['create_person_id'] . '"')->select();
            foreach ($value['u'] as $name=>$id) {
            }
        }
        $this->assign('personList',$sampleResult);
        $this->assign('page',$pageShow);
        $this->assign('sampleResult',$sampleResult);
        //print_r($sampleResult);
        $this->display();
    }

    //初始化添加页面及添加入库
    public function add(){

        if(IS_POST) {
            if (I('post.order_num')) {

                $sample = M('sample');
                $sample_data['order_num'] = I('post.order_num');
                $sample_data['order_charge'] = I('post.order_charge');
                $sample_data['create_time'] = time();
                $sample_data['create_person_id'] = session('user')['id'];
                $sample_data['create_person_name'] = session('user')['nickname'];
                $sample_data['order_state'] = 1;

                # 订单号是否重复
                $rel = $sample->where('order_num="'.I('post.order_num').'"')->select();

                if(!empty($rel)){
                    $this->ajaxReturn(['flag'=>0,'msg'=>'订单已存在！']);
                }else{
                    $sample_model = new Model();
                    $sample_model->startTrans();
                    $sample_id = $sample_model->table(C('DB_PREFIX').'sample')->add($sample_data);
                    $product_data = I('post.sample_details','',false);
                    foreach ($product_data as $key=>&$value){

                        # 将步骤和主表id注入产品详情表
                        $value['detail_assoc'] = $sample_id;
                        $value['now_step'] = 2;
                        $value['note'] = replaceEnterWithBr($value['note']);    // 保留复制换行符
                        $tmp_detail_id = $sample_model->table(C('DB_PREFIX').'sample_detail')->add($value);

                        if( $tmp_detail_id ){

                            # 步骤一数据
                            $tmp_op_data[0]['asc_step'] = 1;
                            $tmp_op_data[0]['asc_detail'] = $tmp_detail_id;
                            $tmp_op_data[0]['operator'] = session('user')['id'];
                            $tmp_op_data[0]['op_time'] = time();
                            # 步骤二数据
                            $tmp_op_data[1]['asc_step'] = 2;
                            $tmp_op_data[1]['asc_detail'] = $tmp_detail_id;
                            $tmp_op_data[1]['operator'] = $value['manager'];
                            $tmp_op_data[1]['op_time'] = '';    //当用户操作成功后重写此字段

                            # 写入步骤数据
                            $tmp_op_id = $sample_model->table(C('DB_PREFIX').'sample_operating')->addAll($tmp_op_data);

                            if( !$tmp_op_id ){
                                $sample_model->rollback();
                                $this->ajaxReturn(['flag'=>0,'msg'=>'添加订单失败！']);
                                exit;
                            }
                        }else{
                            $sample_model->rollback();
                            $this->ajaxReturn(['flag'=>0,'msg'=>'添加订单失败！']);
                            exit;
                        }
                    }

                    $sample_model->commit();
                    $this->ajaxReturn(['flag'=>1,'msg'=>'添加订单成功！','id'=>$sample_id]);
                }
            }
        } else {
            $this->assign('productFilter', $this->getProductData());
            $this->display();
        }

    }


    # 上传附件
    public function upload(){
        /*$info = FileUpload('/SampleAttachment/',1,0,'sample_attachment_');
        if( is_array($info) ){  //检测文件上传是否成功，如果返回的结果为数组表示成功，否则为失败
            $return_data['flag'] = 1;
            $return_data['source'] = $info['Attachment']['name'];
            $return_data['newname'] = $info['Attachment']['savename'];
            $return_data['size'] = $info['Attachment']['size'];
            $return_data['ext'] = $info['Attachment']['ext'];
            $return_data['savepath'] = '/Uploads'.$info['Attachment']['savepath'].$info['Attachment']['savename'];
            $this->ajaxReturn($return_data);
        }else{
            $return_data['flag'] = 0;
            $return_data['msg'] = $info;
            $this->ajaxReturn($return_data);
            exit;
        }*/

        $subName = I('post.SUB_NAME');
        // 如果需要按id为子目录则必须填写第二个参数，否则直接保存在当前目录
        if( $subName != '' ){
            $result = upload( I('post.DIR'), $subName );
            $this->ajaxReturn( $result );
        }

    }

    # 写入附件数据
    public function insertAttachment(){
        if( IS_POST ){
            if( trim(I('post.SUB_NAME')) != '' ){
                $SAMPLE_MODEL = M('Sample');
                $save_id = $SAMPLE_MODEL->save( ['id'=>I('post.SUB_NAME'),'attachment'=>I('post.attachments','',false)] );
                if( $save_id ){
                    $this->ajaxReturn( ['flag'=>1,'msg'=>'添加成功'] );
                }else{
                    $this->ajaxReturn( ['flag'=>0,'msg'=>'添加失败'] );
                }
            }
        }
    }


    //样品单总览
    public function overview(){
        if( I('get.id') ){

            $model = new Model();

            # 获取订单产品基本数据
            $child_data = $model->field('a.id,a.pn,a.count,a.detail_assoc,a.customer,a.brand,a.model,a.note,a.requirements_date,a.expect_date,a.actual_date,a.now_step,b.type,b.pn,c.nickname,d.create_time,d.create_person_name,d.order_num,d.order_charge,e.name step_name')
                                ->table(C('DB_PREFIX') . 'sample_detail a,' . C('DB_PREFIX') . 'productrelationships b,' . C('DB_PREFIX') . 'user c,' . C('DB_PREFIX') . 'sample d,'.C('DB_PREFIX').'sample_step e')
                                ->where('a.detail_assoc=' . I('get.id') . ' AND a.product_id=b.id AND b.manager=c.id AND a.detail_assoc=d.id AND a.now_step=e.id')
                                ->select();

            $step_data = $model->table(C('DB_PREFIX').'sample_step')->select();

            $this->assign('stepData', $step_data);

            $sample_log_model = M('SampleLog');
            $sample_operating_model = M('SampleOperating');

            foreach( $child_data as $key=>&$value ){

                # 步骤表数据
                $value['operating'] = $sample_operating_model->field('a.operator,a.op_time,a.asc_detail,b.id,b.name,b.transfer,b.rollback,b.termination,c.nickname')
                                                             ->table(C('DB_PREFIX').'sample_operating a,'.C('DB_PREFIX').'sample_step b,'.C('DB_PREFIX').'user c')
                                                             ->where( 'a.asc_detail='.$value['id'].' AND a.asc_step=b.id AND a.operator=c.id' )
                                                             ->select();

                # 获取当前步骤产生的日志
                foreach( $value['operating'] as $k=>&$v ){
                    $v['log'] = $sample_log_model->where( ['asc_detail'=>$v['asc_detail'],'log_step'=>$v['id']] )->order('log_time ASC')->select();
                }

            }

            # 获取订单基本信息及附件/测试报告
            $order_data = $model->table(C('DB_PREFIX').'sample')->find(I('get.id'));

            $order_data['attachment'] = json_decode($order_data['attachment'], true);

            //print_r($child_data);

            $this->assign('orderData',$order_data);

            $this->assign('progress', $child_data);

            # print_r($child_data);

            $this->display();
        }
    }



    //订单详情页
    public function detail(){
        if( I('get.id') ){

            //print_r( $_SERVER );

            $detailResult = M()->table(C('DB_PREFIX').'sample a,'.C('DB_PREFIX').'sample_detail b,'.C('DB_PREFIX').'user c,'.C('DB_PREFIX').'productrelationships d,'.C('DB_PREFIX').'sample_step e')
                               ->field('a.id sample_id,b.id,a.order_num,b.detail_assoc,b.count,b.customer,b.brand,b.model,b.note,b.requirements_date,b.expect_date,b.actual_date,b.manager,b.now_step,c.nickname,d.type,d.pn,e.name')
                               ->where('a.id=b.detail_assoc AND b.manager=c.id AND b.product_id=d.id AND b.id='.I('get.id').' AND b.now_step=e.id')
                               ->select();

            $stepResult = M('SampleStep')->select();

            $this->assign('step',$stepResult);

            # 步骤表数据
            $detailResult[0]['operating'] = M('SampleOperating')->field('a.operator,a.op_time,a.asc_detail,b.id,b.name,b.transfer,b.rollback,b.termination,c.nickname')
                                                                ->table(C('DB_PREFIX').'sample_operating a,'.C('DB_PREFIX').'sample_step b,'.C('DB_PREFIX').'user c')
                                                                ->where( 'a.asc_detail='.I('get.id').' AND a.asc_step=b.id AND a.operator=c.id' )
                                                                ->select();


            $sample_log_model = M('SampleLog');

            //print_r($detailResult[0]['operating']);

            foreach( $detailResult[0]['operating'] as $key=>&$value ){
                $value['log'] = $sample_log_model->where( ['asc_detail'=>$value['asc_detail'],'log_step'=>$value['id']] )->order('log_time ASC')->select();
            }

            //print_r($detailResult[0]);

            # 获取订单基本信息及附件/测试报告
            $order_data = M('Sample')->find( $detailResult[0]['sample_id'] );

            $order_data['attachment'] = json_decode($order_data['attachment'], true);

            //print_r($child_data);

            $this->assign('orderData',$order_data);

            # 获取最新日志
            $new_log = M()->field('a.context,a.log_time,b.nickname,b.face,b.account,c.name')
                          ->table(C('DB_PREFIX').'sample_log a,'.C('DB_PREFIX').'user b,'.C('DB_PREFIX').'sample_step c')
                          ->where( 'a.person=b.id AND a.asc_detail='.I('get.id').' AND a.log_step=c.id' )
                          ->order('log_time DESC')
                          ->select();

            $this->assign('newlog',$new_log);

            //print_r( $new_log );


            # 将当前步骤信息注入模板
            $now_step_info = $detailResult[0]['operating'][count($detailResult[0]['operating'])-1];

            $this->assign('nowStep',$now_step_info);

            switch( $now_step_info['id'] ){
                case 2:
                    $persons = M('user')->where( ['department'=>5] )->select();
                    $this->assign('persons',$persons);
                    break;
                case 3:
                    $persons = M('user')->where( ['position'=>6] )->select();
                    $this->assign('persons',$persons);
                    break;
                case 4:
                    $persons = M('user')->where( ['position'=>11] )->select();
                    $this->assign('persons',$persons);
                    break;
                case 5:
                    $persons = M('user')->where( ['position'=>6] )->select();
                    $this->assign('persons',$persons);
                    break;
            }

            //print_r($detailResult[0]);

            $step = M('SampleStep');
            $stepResult = $step->select();
            $this->assign('stepResult',$stepResult);
            //$this->assign('attachmentResult',$attachmentResult);
            $this->assign('detailResult',$detailResult[0]);
            $this->display();
        }

    }


    public function addSampleLog(){
        if(IS_POST){

            $post = I('post.');

            # 获取当前步骤
            $pushOper = M('SampleDetail')->find($post['asc_detail']);

            # 获取推送人员的邮件
            $push_person_info = M()->field('b.nickname,b.email')
                ->table(C('DB_PREFIX').'sample_operating a,'.C('DB_PREFIX').'user b')
                ->where('a.operator=b.id AND asc_detail='.$post['asc_detail'].' AND a.operator<>'.session('user')['id'])
                ->select();

            # 获取产品基本信息
            $orderData = M()->field('a.order_num,a.create_person_name,b.id detail_id,b.pn,b.count,b.customer,b.brand,b.model,b.note,b.requirements_date')
                ->table(C('DB_PREFIX').'sample a,'.C('DB_PREFIX').'sample_detail b')
                ->where( 'a.id=b.detail_assoc AND b.id='.$post['asc_detail'] )
                ->select();

            # 拼装推送人员邮箱
            foreach( $push_person_info as $key=>&$value ){
                $emails[] = $value['email'];
            }

            # 判断用户选择的操作类型
            switch( $post['type'] ){
                case 'log':
                    $logData['asc_detail'] = $post['asc_detail'];
                    $logData['context'] = strip_tags($post['context']);
                    $logData['log_time'] = time();
                    $logData['person'] = session('user')['id'];
                    $logData['log_step'] = $pushOper['now_step'];

                    $model = new Model();

                    $model->startTrans();   //开启事物

                    $add_log_id = $model->table(C('DB_PREFIX').'sample_log')->add( $logData );

                    $orderData[0]['context'] = strip_tags($post['context']);

                    if( $add_log_id ){

                        $model->commit();

                        $this->pushEmail('LOG', $emails, $orderData[0]);

                        $this->ajaxReturn( ['flag'=>1,'msg'=>'添加成功'] );

                    }else{
                        $model->rollback();

                        $this->ajaxReturn( ['flag'=>0,'msg'=>'添加失败'] );
                    }

                    break;

                # 推送操作
                case 'push':

                    $pushOperRel = $pushOper['now_step'];

                    $step_rel = M('SampleStep')->where( ['id'=>['in',[$pushOperRel,$pushOperRel+1]]] )->order('id asc')->select();

                    $now_stepStr ='Step'.$step_rel[0]['id'].'-'.$step_rel[0]['name'];
                    $next_stepStr ='Step'.$step_rel[1]['id'].'-'.$step_rel[1]['name'];

                    $orderData[0]['now_step'] = $now_stepStr;
                    $orderData[0]['next_step'] = $next_stepStr;

                    # 推送下一步的数据
                    $pushOperating['operator'] = $post['operator'];
                    $pushOperating['asc_detail'] = $post['asc_detail'];
                    $pushOperating['asc_step'] = $pushOperRel+1;


                    //print_r($pushOperating);


                    # 推送时产生的日志
                    $pushData['context'] = strip_tags($post['context']);
                    $pushData['log_time'] = time();
                    $pushData['person'] = session('user')['id'];
                    $pushData['asc_detail'] = $post['asc_detail'];
                    $pushData['log_step'] = $pushOper['now_step'];


                    $model = new Model();

                    $model->startTrans();   //开启事物

                    $add_push_id = $model->table(C('DB_PREFIX').'sample_operating')->add($pushOperating);
                    $add_push_log_id = $model->table(C('DB_PREFIX').'sample_log')->add($pushData);

                    # 完成当前步骤的时间
                    $savar_now_row = M('SampleOperating')->where('asc_step ='.$pushOperRel.' AND asc_detail ='.$post['asc_detail'])->save(['op_time'=>time()]);

                    # 修改主表步骤
                    $save_detail_row = M('SampleDetail')->save(['id'=>$post['asc_detail'],'now_step'=>$pushOperRel+1]);

                    if( $savar_now_row &&  $add_push_log_id && $add_push_id && $save_detail_row){

                        $model->commit();

                        $this->pushEmail('PUSH', $emails, $orderData[0]);

                        $this->ajaxReturn( ['flag'=>1,'msg'=>'添加成功'] );


                    }else{
                        $model->rollback();

                        $this->ajaxReturn( ['flag'=>0,'msg'=>'添加失败'] );
                    }


                    break;
                case 'close':

                    break;
                default:

            }

        }
    }

    # 邮件推送
    public function pushEmail( $type, $address, $data ){

        $http_host = $_SERVER['HTTP_HOST'];

        extract($data);

        switch( $type ){
            case 'LOG':
                $subject = '样品订单 '.$order_num.' 日志更新';
                $body = <<<HTML
<p>Dear All,</p>
<p>样品订单 <b>$order_num</b> 日志更新</p>
<p>更新内容：$context</p><br>
<p><b>订单基本信息</b></p>
<p>销售人员：$create_person_name</p>
<p>产品型号：$pn</p>
<p>模块数量：$count</p>
<p>客户名称：$customer</p>
<p>设备品牌：$brand</p>
<p>设备型号：$model</p>
<p>备注：$note</p>
<p>要求交期：$requirements_date</p><br>
<p>详情请点击链接：<a href="http://$http_host/Sample/detail/id/$detail_id" target="_blank">http://$http_host/Sample/detail/id/$detail_id</a></p>
HTML;

                break;
            case 'PUSH':

                $user = session('user')['nickname'];

                $subject = '样品订单 '.$order_num.' 步骤更新';
                $body = <<<HTML
<p>Dear All,</p>
<p>样品订单 <b>$order_num</b> 步骤更新  </p>
<p>由 [$user] 将 $now_step 推送到 $next_step</p>
<p>更新内容：$context</p><br>
<p><b>订单基本信息</b></p>
<p>销售人员：$create_person_name</p>
<p>产品型号：$pn</p>
<p>模块数量：$count</p>
<p>客户名称：$customer</p>
<p>设备品牌：$brand</p>
<p>设备型号：$model</p>
<p>备注：$note</p>
<p>要求交期：$requirements_date</p><br>
<p>详情请点击链接：<a href="http://$http_host/Sample/detail/id/$detail_id" target="_blank">http://$http_host/Sample/detail/id/$detail_id</a></p>
HTML;

                break;
            case 'CLOSE':

                break;
            default:

        }

        $result = send_Email( 'm18581898939@163.com', '', $subject, $body );
        if( $result != 1 ){
            $this->ajaxReturn( ['flag'=>0,'msg'=>'邮件发送失败'] );
            exit;
        }

    }





}