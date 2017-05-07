<?php 
namespace Home\Controller;
use Think\Model;
use Think\Page;

class SampleController extends AuthController {

    # 初始化样品管理首页
    public function index(){

        $person = M('Sample');

        # 是否有查询
        if(I('get.search')){
            $count = $person->where('order_num LIKE "%'.I('get.search').'%" OR create_person_name LIKE "%'.I('get.search').'%"')->count();
            $this->assign( 'search' , I('get.search') );
        }else{
            $count = $person->count();
        }

        # 数据分页
        $page = new Page($count,15);
        $page->setConfig('prev','<span aria-hidden="true">上一页</span>');
        $page->setConfig('next','<span aria-hidden="true">下一页</span>');
        $page->setConfig('first','<span aria-hidden="true">首页</span>');
        $page->setConfig('last','<span aria-hidden="true">尾页</span>');

        if(C('PAGE_STATUS_INFO')){
            $page->setConfig ( 'theme', '<li><a href="javascript:void(0);">当前%NOW_PAGE%/%TOTAL_PAGE%</a></li>  %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%' );
        }

        # 根据条件筛选数据
        if(I('get.search')){
            $sampleResult = $person->where('order_num LIKE "%'.I('get.search').'%" OR create_person_name LIKE "%'.I('get.search').'%"')->order('id DESC')->limit($page->firstRow.','.$page->listRows)->select();
        }else{
            $sampleResult = $person->order('id DESC')->limit($page->firstRow.','.$page->listRows)->select();
        }

        $pageShow = $page->show();

        $sample_detail = M('Sample_detail');

        $user = M('user');

        foreach ($sampleResult as $key=>&$value) {

            # 获取销售头像
            $value['face'] = $user->field('face')->find( $value['create_person_id'] )['face'];

            $num = 0;

            # 与订单详情表对接
            $value['child'] = $sample_detail->field('a.id,a.pn,a.detail_assoc,a.product_id,a.count,a.brand,a.model,a.note,a.requirements_date,a.expect_date,a.actual_date,a.manager,a.state,a.now_step,b.type')
                                            ->table(C('DB_PREFIX').'sample_detail a,'.C('DB_PREFIX').'productrelationships b')
                                            ->where('a.detail_assoc="' . $value['id'] . '" AND a.product_id=b.id')
                                            ->select();

            foreach( $value['child'] as $k=>&$v ){

                # 如果产品步骤大于6则说明该单已经完成
                if( $v['now_step'] > 6 ) $num++;

            }

            # 如果该单的产品数量和产品完成数量一致则说明该单完成
            if( count( $value['child'] ) == $num ){
                $value['state'] = 'success';
            }else{
                $value['state'] = 'processing';
            }

        }

        $this->assign('page',$pageShow);
        $this->assign('sampleResult',$sampleResult);
        $this->display();
    }

    # 初始化添加页面及添加入库
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
                        $value['note'] = replaceEnterWithBr($value['note']);    #  保留复制换行符
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
                            $tmp_op_data[1]['op_time'] = '';    # 当用户操作成功后重写此字段

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

            # 检测访问该页面的用户是否所属销售部门
            if( session('user')['department'] != 4 ){
                $this->error('你没有权限访问该页面');
            }

            $this->assign('productFilter', $this->getProductData());
            $this->display();
        }

    }


    # 上传附件
    public function upload(){

        $subName = I('post.SUB_NAME');

        #  如果需要按id为子目录则必须填写第二个参数，否则直接保存在当前目录
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

    # 写入测试报告数据
    public function insertTestReport(){
        if( IS_POST ){
            if( trim(I('post.SUB_NAME')) != '' ){
                $SAMPLE_MODEL = M('Sample');
                $save_id = $SAMPLE_MODEL->save( ['id'=>I('post.SUB_NAME'),'test_report'=>I('post.attachments','',false)] );
                if( $save_id ){
                    $this->ajaxReturn( ['flag'=>1,'msg'=>'添加成功'] );
                }else{
                    $this->ajaxReturn( ['flag'=>0,'msg'=>'添加失败'] );
                }
            }
        }
    }


    # 样品单总览
    public function overview(){

        if( I('get.id') ){

            $model = new Model();

            # 获取订单产品基本数据
            $child_data = $model->field('a.id,a.pn,a.count,a.detail_assoc,a.customer,a.brand,a.model,a.note,a.requirements_date,a.expect_date,a.actual_date,a.now_step,a.state,b.type,b.pn,c.nickname,d.create_time,d.create_person_name,d.order_num,d.order_charge,e.name step_name')
                                ->table(C('DB_PREFIX') . 'sample_detail a,' . C('DB_PREFIX') . 'productrelationships b,' . C('DB_PREFIX') . 'user c,' . C('DB_PREFIX') . 'sample d,'.C('DB_PREFIX').'sample_step e')
                                ->where('a.detail_assoc=' . I('get.id') . ' AND a.product_id=b.id AND b.manager=c.id AND a.detail_assoc=d.id AND a.now_step=e.id')
                                ->select();

            $step_data = $model->table(C('DB_PREFIX').'sample_step')->select();

            $this->assign('stepData', $step_data);

            $sample_log_model = M('SampleLog');
            $sample_operating_model = M('SampleOperating');

            foreach( $child_data as $key=>&$value ){

                $value['max_step'] = $step_data[count($step_data)-1]['id'];

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

            $this->assign('orderData',$order_data);

            $this->assign('progress', $child_data);

            $this->display();
        }

    }

    # 样品汇总
    public function summary(){

        $model = new Model();

        $summary = $model->table(C('DB_PREFIX').'sample')->select();

        foreach( $summary as $key=>&$value ){

            # 获取订单产品基本数据
            $value['detail'] = $model->field('a.id,a.pn,a.count,a.detail_assoc,a.customer,a.brand,a.model,a.note,a.requirements_date,a.expect_date,a.actual_date,a.now_step,a.state,b.type,b.pn,c.nickname,d.create_time,d.create_person_name,d.order_num,d.order_charge,e.name step_name')
                ->table(C('DB_PREFIX') . 'sample_detail a,' . C('DB_PREFIX') . 'productrelationships b,' . C('DB_PREFIX') . 'user c,' . C('DB_PREFIX') . 'sample d,'.C('DB_PREFIX').'sample_step e')
                ->where('a.detail_assoc=' . $value['id'] . ' AND a.product_id=b.id AND b.manager=c.id AND a.detail_assoc=d.id AND a.now_step=e.id')
                ->select();

        }

        //print_r($summary);

        $sample_log_model = M('SampleLog');
        $sample_operating_model = M('SampleOperating');

        /*foreach( $summary as $key=>&$value ){

            # 步骤表数据
            $value['operating'] = $sample_operating_model->field('a.operator,a.op_time,a.asc_detail,b.id,b.name,b.transfer,b.rollback,b.termination,c.nickname')
                ->table(C('DB_PREFIX').'sample_operating a,'.C('DB_PREFIX').'sample_step b,'.C('DB_PREFIX').'user c')
                ->where( 'a.asc_detail='.$value['id'].' AND a.asc_step=b.id AND a.operator=c.id' )
                ->select();

        }*/

        $this->assign('summary',$summary);
        $this->display();

    }

    # 订单详情页
    public function detail(){

        if( I('get.id') ){

            # 获取产品基本信息
            $detailResult = M()->table(C('DB_PREFIX').'sample a,'.C('DB_PREFIX').'sample_detail b,'.C('DB_PREFIX').'user c,'.C('DB_PREFIX').'productrelationships d,'.C('DB_PREFIX').'sample_step e')
                               ->field('a.id sample_id,a.attachment,a.test_report,a.create_person_id sales_id,a.create_person_name sales_name,b.id,a.order_num,b.detail_assoc,b.count,b.customer,b.brand,b.model,b.note,b.requirements_date,b.expect_date,b.actual_date,b.manager,b.now_step,b.state,b.logistics,b.waybill,c.nickname,d.type,d.pn,e.name,e.transfer,e.rollback,e.termination,e.extension')
                               ->where('a.id=b.detail_assoc AND b.manager=c.id AND b.product_id=d.id AND b.id='.I('get.id').' AND b.now_step=e.id')
                               ->select();

            # 如果物流数据不为空则将物流数据注入模板
            if( !empty($detailResult[0]['logistics']) && !empty($detailResult[0]['waybill']) ){
                $logistics = M('Logistics')->find( $detailResult[0]['logistics'] );
                $detailResult[0]['logistics_data'] = $logistics;
            }

            # 注入步骤表数据
            $step_model = M('SampleStep');
            $stepResult = $step_model->select();
            $this->assign('step',$stepResult);

            $detailResult[0]['max_step'] = $stepResult[count($stepResult)-1]['id'];

            # 将下一步数据添加至数据集
            $detailResult[0]['next_step'] = $step_model->find( ($detailResult[0]['now_step']+1) );
            $detailResult[0]['prev_step'] = $step_model->find( ($detailResult[0]['now_step']-1) );

            # 步骤表数据
            $detailResult[0]['operating'] = M('SampleOperating')->field('a.operator,a.op_time,a.asc_detail,b.id,b.name,b.transfer,b.rollback,b.termination,c.nickname')
                                                                ->table(C('DB_PREFIX').'sample_operating a,'.C('DB_PREFIX').'sample_step b,'.C('DB_PREFIX').'user c')
                                                                ->where( 'a.asc_detail='.I('get.id').' AND a.asc_step=b.id AND a.operator=c.id' )
                                                                ->select();

            $sample_log_model = M('SampleLog');

            $log_data = M()->field('a.context,a.asc_detail,a.log_time,b.id step_id,b.name,c.nickname,c.face')
                ->table(C('DB_PREFIX').'sample_log a,'.C('DB_PREFIX').'sample_step b,'.C('DB_PREFIX').'user c')
                ->where('a.log_step=b.id AND a.person=c.id AND a.asc_detail='.I('get.id'))
                ->order('a.log_time DESC')
                ->select();

            $this->assign('newlog',$log_data);

            # 获取日志数据
            foreach( $detailResult[0]['operating'] as $key=>&$value ){
                $value['log'] = $sample_log_model->where( ['asc_detail'=>$value['asc_detail'],'log_step'=>$value['id']] )->order('log_time ASC')->select();
            }

            # 检查附件和测试报告是否为空，如果不为空则将附件转换为数组
            if( !empty($detailResult[0]['attachment']) ){
                $detailResult[0]['attachment'] = json_decode($detailResult[0]['attachment'], true);
            }
            if( !empty($detailResult[0]['test_report']) ){
                $detailResult[0]['test_report'] = json_decode($detailResult[0]['test_report'], true);
            }

            # 根据当前步骤获取下一个步骤的处理人
            switch( $detailResult[0]['now_step'] ){
                case 2:
                    $persons = M('user')->where( ['department'=>5] )->select();
                    $this->assign('persons',$persons);
                    break;
                case 3:
                    $persons = M('user')->where( ['position'=>6] )->select();
                    $this->assign('persons',$persons);

                    if( $detailResult[0]['transfer'] == 'Y' ){
                        # 获取转交人数据
                        $transfer = M('User')->where('department='.session('user')['department'].' AND id<>'.session('user')['id'])->select();
                        $this->assign('transfer',$transfer);
                    }

                    break;
                case 4:
                    $persons = M('user')->where( ['position'=>11] )->select();
                    $this->assign('persons',$persons);

                    if( $detailResult[0]['rollback'] == 'Y' ) {
                        # 获取回退处理人数据
                        $rollback = M()->field('b.id,b.nickname')->table(C('DB_PREFIX').'sample_operating a,'.C('DB_PREFIX').'user b')->where('a.asc_detail='.I('get.id').' AND a.asc_step='.$detailResult[0]['prev_step']['id'].' AND a.operator=b.id')->select();
                        $this->assign('rollback', $rollback);
                    }

                    if( $detailResult[0]['transfer'] == 'Y' ) {
                        # 获取转交人数据
                        $transfer = M('User')->where('position=' . session('user')['position'] . ' AND id<>' . session('user')['id'])->select();
                        $this->assign('transfer', $transfer);
                    }

                    break;
                case 5:
                    $persons = M('user')->where( ['position'=>6] )->select();
                    $this->assign('persons',$persons);

                    if( $detailResult[0]['rollback'] == 'Y' ) {
                        # 获取回退处理人数据
                        $rollback = M()->field('b.id,b.nickname')->table(C('DB_PREFIX').'sample_operating a,'.C('DB_PREFIX').'user b')->where('a.asc_detail='.I('get.id').' AND a.asc_step='.$detailResult[0]['prev_step']['id'].' AND a.operator=b.id')->select();
                        $this->assign('rollback', $rollback);
                    }

                    if( $detailResult[0]['transfer'] == 'Y' ) {
                        # 获取转交人数据
                        $transfer = M('User')->where('position=' . session('user')['position'] . ' AND id<>' . session('user')['id'])->select();
                        $this->assign('transfer', $transfer);
                    }

                    break;
                case 6:
                    $persons = M('user')->where( 'id='.$detailResult[0]['sales_id'] )->select();
                    $this->assign('persons',$persons);

                    # 注入物流公司数据
                    $logistics = M('Logistics')->select();

                    $this->assign('logistics',$logistics);

                    if( $detailResult[0]['rollback'] == 'Y' ) {
                        # 获取回退处理人数据
                        $rollback = M()->field('b.id,b.nickname')->table(C('DB_PREFIX').'sample_operating a,'.C('DB_PREFIX').'user b')->where('a.asc_detail='.I('get.id').' AND a.asc_step='.$detailResult[0]['prev_step']['id'].' AND a.operator=b.id')->select();
                        $this->assign('rollback', $rollback);
                    }

                    break;
            }

            $this->assign('detailResult',$detailResult[0]);
            $this->display();
        }

    }

    # 添加记录日志
    public function addSampleLog(){

        if(IS_POST){

            $post = I('post.');

            $model = new Model();

            # 获取推送人员的邮件(不包含自己)
            $push_person_info = M()->field('b.nickname,b.email')
                                   ->table(C('DB_PREFIX').'sample_operating a,'.C('DB_PREFIX').'user b')
                                   ->where('a.operator=b.id AND a.asc_detail='.$post['asc_detail'].' AND a.operator<>'.session('user')['id'])
                                   ->select();

            # 拼装推送人员邮箱
            foreach( $push_person_info as $key=>&$value ){
                $emails[] = $value['email'];
            }

            # 获取产品基本信息
            $orderData = M()->field('a.id sample_id,a.order_num,a.create_person_name,b.id detail_id,b.pn,b.count,b.customer,b.brand,b.model,b.note,b.requirements_date,b.now_step,c.id step_id,c.name step_name')
                            ->table(C('DB_PREFIX').'sample a,'.C('DB_PREFIX').'sample_detail b,'.C('DB_PREFIX').'sample_step c')
                            ->where( 'a.id=b.detail_assoc AND b.id='.$post['asc_detail'].' AND b.now_step=c.id' )
                            ->select();

            # 检查是否存在预计交期
            if( isset($post['expect_date']) && !empty($post['expect_date']) ){
                $save_result = $model->table(C('DB_PREFIX').'sample_detail')->save( ['id'=>$post['asc_detail'],'expect_date'=>$post['expect_date']] );
                if( $save_result === false ){
                    $this->ajaxReturn( ['flag'=>0,'msg'=>'错误'] );
                }
            }


            $logData['asc_detail'] = $post['asc_detail'];
            $logData['context'] = strip_tags($post['context']);
            $logData['log_time'] = time();
            $logData['person'] = session('user')['id'];
            $logData['log_step'] = $orderData[0]['now_step'];

            # 将留言信息添加到邮件发送正文
            $orderData[0]['context'] = strip_tags($post['context']);

            # 判断用户选择的操作类型
            switch( $post['type'] ){

                case 'log':     # 添加日志

                    $model->startTrans();   # 开启事物

                    # 记录日志
                    $add_log_id = $model->table(C('DB_PREFIX').'sample_log')->add( $logData );

                    if( $add_log_id ){

                        $model->commit();

                        $this->pushEmail('LOG', $emails, $orderData[0]);

                        $this->ajaxReturn( ['flag'=>1,'msg'=>'添加成功'] );

                    }else{
                        $model->rollback();

                        $this->ajaxReturn( ['flag'=>0,'msg'=>'添加失败'] );
                    }

                    break;

                case 'push':    # 推送

                    $now_step_id = $orderData[0]['now_step'];

                    $step_rel = M('SampleStep')->where( ['id'=>['in',[$now_step_id,$now_step_id+1]]] )->order('id ASC')->select();

                    # 拼装当前步骤信息和下一步步骤信息
                    $now_stepStr ='Step'.$step_rel[0]['id'].'-'.$step_rel[0]['name'];
                    $next_stepStr ='Step'.$step_rel[1]['id'].'-'.$step_rel[1]['name'];

                    # 将步骤信息传入邮件内容
                    $orderData[0]['now_step_str'] = $now_stepStr;
                    $orderData[0]['next_step_str'] = $next_stepStr;

                    # 推送下一步的数据
                    $next_step_data['operator'] = $post['operator'];
                    $next_step_data['asc_detail'] = $post['asc_detail'];
                    $next_step_data['asc_step'] = $now_step_id+1;

                    $model = new Model();

                    # 开启事务
                    $model->startTrans();

                    # 检测是否存在物流数据
                    if( isset($post['logistics']) && isset($post['waybill']) ){
                        $logistics_save = $model->table(C('DB_PREFIX').'sample_detail')->save( ['id'=>$post['asc_detail'],'logistics'=>$post['logistics'],'waybill'=>$post['waybill']] );
                    }

                    # 检查当前推送时是否已经是发货步骤，如果是则写入最终交期
                    if( $now_step_id == 6 ){
                        $actual_date_save = $model->table(C('DB_PREFIX').'sample_detail')->save( ['id'=>$post['asc_detail'],'actual_date'=>date('Y-m-d')] );
                    }

                    # 插入下一步操作人及步骤信息
                    $add_push_id = $model->table(C('DB_PREFIX').'sample_operating')->add($next_step_data);
                    
                    # 记录推送时产生的日志
                    $add_push_log_id = $model->table(C('DB_PREFIX').'sample_log')->add($logData);

                    # 完成当前步骤的时间
                    $op_time_save = $model->table(C('DB_PREFIX').'sample_operating')->where('asc_step ='.$now_step_id.' AND asc_detail ='.$post['asc_detail'])->save( ['op_time'=>time()] );

                    # 修改主表步骤
                    $save_detail_row = $model->table(C('DB_PREFIX').'sample_detail')->save( ['id'=>$post['asc_detail'],'now_step'=>$now_step_id+1] );

                    if( isset($logistics_save) ){

                        if( $op_time_save &&  $add_push_log_id && $add_push_id && $save_detail_row && $logistics_save ){

                            $model->commit();

                            $this->pushEmail('PUSH', $emails, $orderData[0]);

                            $this->ajaxReturn( ['flag'=>1,'msg'=>'添加成功'] );


                        }else{
                            $model->rollback();

                            $this->ajaxReturn( ['flag'=>0,'msg'=>'添加失败'] );
                        }

                    }elseif( isset($actual_date_save) ){

                        if( $op_time_save &&  $add_push_log_id && $add_push_id && $save_detail_row && $actual_date_save ){

                            $model->commit();

                            $this->pushEmail('PUSH', $emails, $orderData[0]);

                            $this->ajaxReturn( ['flag'=>1,'msg'=>'添加成功'] );


                        }else{
                            $model->rollback();

                            $this->ajaxReturn( ['flag'=>0,'msg'=>'添加失败'] );
                        }

                    }else{

                        if( $op_time_save &&  $add_push_log_id && $add_push_id && $save_detail_row){

                            $model->commit();

                            $this->pushEmail('PUSH', $emails, $orderData[0]);

                            $this->ajaxReturn( ['flag'=>1,'msg'=>'添加成功'] );


                        }else{
                            $model->rollback();

                            $this->ajaxReturn( ['flag'=>0,'msg'=>'添加失败'] );
                        }

                    }

                    break;

                case 'termination':     # 终止

                    # 修改产品状态
                    $state_save_result = $model->table(C('DB_PREFIX').'sample_detail')->save( ['id'=>$post['asc_detail'],'state'=>'Y'] );

                    # 记录终止时产生的日志
                    $add_termination_log_id = $model->table(C('DB_PREFIX').'sample_log')->add($logData);

                    if( $state_save_result && $add_termination_log_id ){

                        $model->commit();

                        $this->pushEmail('TERMINATION', $emails, $orderData[0]);

                        $this->ajaxReturn( ['flag'=>1,'msg'=>'添加成功'] );

                    }else{
                        $model->rollback();

                        $this->ajaxReturn( ['flag'=>0,'msg'=>'添加失败'] );
                    }

                    break;

                case 'transfer':    # 转交

                    # 记录转交时产生的日志
                    $add_transfer_log_id = $model->table(C('DB_PREFIX').'sample_log')->add($logData);

                    $transfer_save_result = $model->table(C('DB_PREFIX').'sample_operating')->where( ['asc_detail'=>$post['asc_detail'],'asc_step'=>$orderData[0]['now_step']] )->save( ['operator'=>$post['transfer']] );

                    $recipient = M('User')->field('id,nickname,email')->find( $post['transfer'] );

                    # 将收件人名称注入邮件正文
                    $orderData[0]['recipient_name'] = $recipient['nickname'];

                    if( $transfer_save_result && $add_transfer_log_id ){

                        $model->commit();

                        $this->pushEmail('TRANSFER', $recipient['email'], $orderData[0], $emails);

                        $this->ajaxReturn( ['flag'=>1,'msg'=>'添加成功'] );

                    }else{
                        $model->rollback();

                        $this->ajaxReturn( ['flag'=>0,'msg'=>'添加失败'] );
                    }

                    break;

                case 'rollback':    # 回退

                    # 产品表当前步骤-1
                    $detail_now_step_save = $model->table(C('DB_PREFIX').'sample_detail')->save( ['id'=>$post['asc_detail'],'now_step'=>($orderData[0]['now_step']-1)] );

                    # 删除操作表当前步骤记录
                    $operating_now_step_del = $model->table(C('DB_PREFIX').'sample_operating')->where('asc_detail='.$post['asc_detail'].' AND asc_step='.$orderData[0]['now_step'])->delete();

                    # 清空上一步骤操作时间
                    $emp_prev_step_op_time = $model->table(C('DB_PREFIX').'sample_operating')->where('asc_detail='.$post['asc_detail'].' AND asc_step='.($orderData[0]['now_step']-1))->save(['op_time'=>'']);

                    # 记录回退时产生的日志
                    $add_rollback_log_id = $model->table(C('DB_PREFIX').'sample_log')->add($logData);

                    # 获取上一个步骤操作人的数据
                    $recipient = M('User')->field('id,nickname,email')->find( $post['rollback'] );

                    # 获取上一个步骤的名称
                    $prev_step_data = M('SampleStep')->field('id,name')->find( ($orderData[0]['now_step']-1) );

                    # 将上一步骤数据注入邮件正文
                    $orderData[0]['prev_step_id'] = $prev_step_data['id'];
                    $orderData[0]['prev_step_name'] = $prev_step_data['name'];

                    # 将收件人名称注入邮件正文
                    $orderData[0]['recipient_name'] = $recipient['nickname'];

                    if( $detail_now_step_save && $operating_now_step_del && $emp_prev_step_op_time && $add_rollback_log_id ){

                        $model->commit();

                        $this->pushEmail('ROLLBACK', $recipient['email'], $orderData[0], $emails);

                        $this->ajaxReturn( ['flag'=>1,'msg'=>'添加成功'] );

                    }else{
                        $model->rollback();

                        $this->ajaxReturn( ['flag'=>0,'msg'=>'添加失败'] );
                    }

                    break;

            }

        }

    }

    # 邮件推送
    public function pushEmail( $type, $address, $data, $cc='' ){

        $http_host = $_SERVER['HTTP_HOST'];

        extract($data);

        # 如果是单人则显示收件人姓名否则群发显示All
        if( isset($recipient_name) ){
            $call = $recipient_name;
        }else{
            $call = 'All';
        }

        $order_basic = <<<BASIC
<p>内容：$context</p><br>
<p><b>订单基本信息</b></p>
<p>销售人员：$create_person_name</p>
<p>产品型号：$pn</p>
<p>模块数量：$count</p>
<p>客户名称：$customer</p>
<p>设备品牌：$brand</p>
<p>设备型号：$model</p>
<p>备注：$note</p>
<p>要求交期：$requirements_date</p><br>
<p>详情请点击链接：<a href="http:# $http_host/Sample/detail/id/$detail_id" target="_blank">http:# $http_host/Sample/detail/id/$detail_id</a></p>
BASIC;


        switch( $type ){
            case 'LOG':
                $subject = '样品订单 '.$order_num.' 日志更新';
                $body = <<<HTML
<style>
.step {
    padding: 2px 5px;
    -webkit-border-radius: 2px;
    -moz-border-radius: 2px;
    border-radius: 2px;
    border: solid 1px #000;
}
</style>
<p>Dear $call,</p>
<p>样品订单 <b>$order_num</b> 日志更新</p>
<p>当前步骤：<span class="step">Step$step_id-$step_name</span></p>
HTML;

                break;
            case 'PUSH':

                $user = session('user')['nickname'];

                $subject = '样品订单 '.$order_num.' 步骤更新';
                $body = <<<HTML
<style>
.step {
    padding: 2px 5px;
    -webkit-border-radius: 2px;
    -moz-border-radius: 2px;
    border-radius: 2px;
    border: solid 1px #000;
}
</style>
<p>Dear $call,</p>
<p>样品订单 <b>$order_num</b> 步骤更新  </p>
<p>由 [$user] 将 <span class="step">$now_step_str</span> 推送到 <span class="step">$next_step_str</span></p>
HTML;

                break;
            case 'TERMINATION':

                $user = session('user')['nickname'];

                $subject = '样品订单 '.$order_num.' 已终止';
                $body = <<<HTML
<p>Dear $call,</p>
<p>样品订单 <b>$order_num</b> 被 [$user] 终止</p>
<p>当前步骤：Step$step_id-$step_name</p>
HTML;

                break;

            case 'TRANSFER':

                $user = session('user')['nickname'];

                $subject = '样品订单 '.$order_num.' 处理人转交';
                $body = <<<HTML
<p>Dear $call,</p>
<p>样品订单 <b>$order_num</b> 步骤更新  </p>
<p>[$user] 将处理人转交给 [$call]</p>
<p>当前步骤：Step$step_id-$step_name</p>
HTML;

                break;
            case 'ROLLBACK':

                $user = session('user')['nickname'];

                $subject = '样品订单 '.$order_num.' 步骤回退';
                $body = <<<HTML
<style>
.step {
    padding: 2px 5px;
    -webkit-border-radius: 2px;
    -moz-border-radius: 2px;
    border-radius: 2px;
    border: solid 1px #000;
}
</style>
<p>Dear $call,</p>
<p>样品订单 <b>$order_num</b> 步骤回退  </p>
<p>[$user] 将步骤回退到 <span class="step">Step$prev_step_id-$prev_step_name</span></p>
HTML;

                break;

        }

        # 检查邮件发送结果
        if( $cc == '' ){
            $result = send_Email( 'vinty_email@163.com', '', $subject, $body.$order_basic );
            if( $result != 1 ){
                $this->ajaxReturn( ['flag'=>0,'msg'=>'邮件发送失败'] );
            }
        }else{
            $result = send_Email( 'vinty_email@163.com', '', $subject, $body.$order_basic, '2737583968@qq.com' );   # $cc
            if( $result != 1 ){
                $this->ajaxReturn( ['flag'=>0,'msg'=>'邮件发送失败'] );
            }
        }

    }





}