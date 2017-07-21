<?php
namespace Home\Controller;
use Think\Model;
use Think\Page;

class RMAController extends AuthController{

    //客诉处理
    public function index(){

        $model = new Model();

        # 客诉表实例
        $customer = M('Oacustomercomplaint');

        # 筛选基本条件：主表和从表关联
        $where = 'a.id=b.main_assoc AND b.step_assoc=c.id ';

        # 检测是否勾选与我相关
        if( !empty(I('get.withme')) ){
            $where .= 'AND b.operation_person LIKE "%'.session('user')['id'].'%" ';  //与我相关，查询所有自己参与的客诉
        }

        # 指定步骤
        if( !empty(I('get.step')) ){
            if( I('get.step') != 'close' ){  //如果提交的进度值为数字类型则表明筛选该步骤id的数据，我、如果不是则为已关闭的类型
                //$where = 'a.id=b.main_assoc ';
                $where .= 'AND a.now_step="'.I('get.step').'" AND a.rma_state="N" ';    //查询所有等于该步骤的客诉
            }else{
                $where .= 'AND a.rma_state="Y" ';    //查询所有已关闭的客诉
            }
        }

        # 时间区
        if( !empty(I('get.start_date')) && !empty(I('get.end_date')) ){
            $where .= 'AND a.cc_time>="'.I('get.start_date').'" AND a.cc_time<="'.I('get.end_date').'" ';
        }elseif( !empty(I('get.start_date')) && empty(I('get.end_date')) ){
            $where .= 'AND a.cc_time>="'.I('get.start_date').'" ';
        }elseif( empty(I('get.start_date')) && !empty(I('get.end_date')) ){
            $where .= 'AND a.cc_time<="'.I('get.end_date').'" ';
        }

        # 单号
        if( !empty(I('get.order')) ){
            $where .= 'AND a.sale_order='.I('get.order').' ';
        }

        # 销售：为兼容旧版客诉，获取人员采取下拉式选择非手动填写保证数据正确性
        if( !empty(I('get.salesperson')) ){
            $where .= 'AND a.salesperson="'.I('get.salesperson').'" ';
        }

        # 客户
        if( !empty(I('get.customer')) ){
            $where .= 'AND a.customer="'.I('get.customer').'" ';
        }

        # 设备厂商
        if( !empty(I('get.vendor')) ){
            $where .= 'AND a.vendor="'.I('get.vendor').'" ';
        }

        # 搜索
        if( !empty(I('get.search')) ){
            $where = 'a.id=b.main_assoc AND b.step_assoc=c.id ';
            $where .= 'AND CONCAT(a.salesperson,a.customer,a.sale_order,a.vendor,a.model,a.error_message) LIKE "%'.I('get.search').'%"';
        }

        # 只查询新版本客诉
        $where .= 'AND a.version="new" AND a.show=1';   //show字段 作用于该客诉是否显示

        # 用户表实例
        $user = M('User');

        # 检测登录用户部门
        /*if($levelReport['department']==4){
            if($levelReport['level']<=4){
                $reportList = $user->field('id,account,report')->where('report='.session('user')['id'])->select();
                if($reportList){
                    //递归查询下属数据
                    //print_r($reportList);
                    $this->subordinates($reportList);
                    $condition['salesperson'] = array('in',$this->in);
                }else{
                    $condition['salesperson'] = session('user')['account'];
                    //goto emptyData;
                }
            }else{
                goto step;
            }
        }*/


        $count = $customer->table('atop_oacustomercomplaint a,atop_oacustomeroperation b,atop_oacustomerstep c')->where($where)->group('a.id')->select();
        $count = count($count);

        # 分页
        $page = new Page($count,C('LIMIT_SIZE'));
        $page->setConfig('prev','<span aria-hidden="true">上一页</span>');
        $page->setConfig('next','<span aria-hidden="true">下一页</span>');
        $page->setConfig('first','<span aria-hidden="true">首页</span>');
        $page->setConfig('last','<span aria-hidden="true">尾页</span>');

        # 是否注入分页信息
        if(C('PAGE_STATUS_INFO')){
            $page->setConfig ( 'theme', '<li><a href="javascript:void(0);">当前%NOW_PAGE%/%TOTAL_PAGE%</a></li>  %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%' );
        }

        # 查询数据
        $result = $customer->table('atop_oacustomercomplaint a,atop_oacustomeroperation b,atop_oacustomerstep c')
                            ->field('a.id,a.cc_time,a.salesperson,a.customer,a.sale_order,a.pn,a.vendor,a.model,a.error_message,a.reason,a.comments,a.status,a.uid,a.rma_state,a.version,b.main_assoc,b.step_assoc,c.id step_id,c.step_name')
                            ->where($where)->order('a.cc_time DESC,a.id DESC')->group('a.id')->limit($page->firstRow.','.$page->listRows)->select();

        # 步骤表实例
        $step = M('Oacustomerstep');
        # 获取到步骤不等于1（下单）的数据
        $step_data = $step->where('id > 1')->select();
        $this->assign('stepData',$step_data);

        foreach($result as $key=>&$value){

            //如果用户为空则采用默认头像
            $face = $user->table('__USER__ u,__OACUSTOMERCOMPLAINT__ o')->field('u.nickname,u.face')->where('u.account=o.salesperson AND o.id='.$value['id'])->select()[0];

            if(!empty($face['face'])){
                $value['face'] = $face['face'];
            }else{
                $value['face'] = '/Public/home/img/face/default_face.png';
            }

            if(!empty($face['face'])){
                $value['nickname'] = $face['nickname'];
            }

            //过滤html标签
            $value['error_message'] = str_replace('<br />','',$value['error_message']);
            $value['reason'] = str_replace('<br />','',$value['reason']);
            $value['now_step'] = M()->field('b.id,b.step_name')->table('atop_oacustomercomplaint a,atop_oacustomerstep b')->where('a.now_step=b.id AND a.id='.$value['id'])->select()[0];    //将最新步骤信息注入模板

        }

        # 获取销售部门人员信息
        $sales = $model->table(C('DB_PREFIX').'user')->field('account,nickname')->where('department=4')->order('id ASC')->select();
        $brands = $model->table(C('DB_PREFIX').'vendor_brand')->order('brand ASC')->select();

        $filter_data['sales'] = $sales;
        $filter_data['brands'] = $brands;

        $this->assign('filter',$filter_data);

        $pageShow = $page->show();
        $this->assign('pageShow',$pageShow);

        //print_r($result);
        $this->assign('customer',$result);

        //当数据为空显示图片
        $this->assign('empty','<div class="permission-denied">
					<div class="permission-denied-icon"><i class="icon-exclamation-sign"></i></div>
					<p class="permission-denied-text-zh">没有数据</p>
					<p class="permission-denied-text-en">No data</p>
				</div>');
        $this->display();

    }

    //获取所有下属资料
    public function subordinates($reportList){
        $user = M('User');
        if( is_array($reportList) && !empty($reportList) ){
            foreach($reportList as $key=>&$value){
                $this->str .= $value['account'].',';
                $result = $user->field('id,account,report')->where('report='.$value['id'])->select();
                $this->subordinates($user,$result);
            }
        }else{
            $this->str = mb_substr($this->str,0,-1);
            $in = explode(',',$this->str);
            //去除掉数组中重复的元素
            $this->in = array_unique($in);
        }
    }

    //删除附件
    public function removeFile(){
        if( IS_POST ){
            if( I('post.filepath') ){
                $dir_name = dirname( dirname( I('post.filepath') ) );
                # 检查文件是否存在，如果存在则删除
                if( file_exists( I('post.filepath') ) ){
                    if( unlink( I('post.filepath') ) ){
                        rm_empty_dir($dir_name);    //删除空目录
                        $this->ajaxReturn( ['flag'=>1,'msg'=>'删除成功'] );
                    }else{
                        $this->ajaxReturn( ['flag'=>0,'msg'=>'删除失败'] );
                    }
                }
            }
        }
    }
    
    //新增客诉
    public function add(){
        if(IS_POST){

            $post = I('post.');
            $pattern = array(
                '/ /',//半角下空格
                '/　/',//全角下空格
                '/\r\n/',//window 下换行符
                '/\n/',//Linux && Unix 下换行符
            );
            $replace = array("&nbsp;","&nbsp;","<br />","<br />");
            $data['cc_time'] = I('post.cc_time');
            $data['salesperson'] = I('post.salesperson');
            $data['customer'] = I('post.customer');
            $data['sale_order'] = I('post.sale_order');
            $data['pn'] = I('post.pn');
            $data['vendor'] = I('post.vendor');
            $data['model'] = I('post.model');
            $data['error_message'] = preg_replace($pattern, $replace,I('post.error_message'));
            $data['reason'] = preg_replace($pattern, $replace,I('post.reason'));
            $data['comments'] = preg_replace($pattern, $replace,I('post.comments'));
            $data['status'] = 1;
            $data['uid'] = session('user')['id'];
            $data['version'] = 'new';
            $data['now_step'] = 2;  //当数据新增成功，步骤为2

            # 开启事务支持(当添加新客诉时在log表中默认添加一条新数据并记录当前操作者及指定FAE工程师)
            // $complaint = M('Oacustomercomplaint');

            $model = new Model();

            $model->startTrans();   //开启事务

            $complaint_addID = $model->table('atop_oacustomercomplaint')->add($data);

            //将当前操作人姓名添加至邮件推送内容
            $post['nickname'] = session('user')['nickname'];

            # 推送邮件到用户指定FAE工程师
            $fae_user = I('post.operation_person'); //获取到指定fae工程师的id
            $fae_user_info = M('User')->field('email,nickname')->find($fae_user);
            $post['fae_nickname'] = $fae_user_info['nickname'];

            # 记录日志
            $log['cc_id'] = $complaint_addID;
            $log['log_date'] = I('post.cc_time');
            $log['log_content'] = '['.session('user')['nickname'].'] 录入了新客诉。';
            $log['attachment'] = I('post.attachment','',false);
            $log['recorder'] = 'OASystem';
            $log['timestamp'] = date('Y-m-d H:i:s');
            $log['uid'] = session('user')['id'];
            $log['version'] = 'new';
            $complaintlog = M('Oacustomercomplaintlog');
            $complaintlog_addID = $model->table('atop_oacustomercomplaintlog')->add($log);


            # 记录步骤操作人
            // $oacustomeroperation_model = M('Oacustomeroperation');
            $operation['main_assoc'] = $complaint_addID;
            $operation['step_assoc'] = 1;
            $operation['operation_person'] = session('user')['id'];
            $operation_id_1 = $model->table('atop_oacustomeroperation')->add($operation);  // 记录步骤1

            $operation['step_assoc'] = 2;
            $operation['operation_person'] = $fae_user;
            $operation_id_2 = $model->table('atop_oacustomeroperation')->add($operation);  // 记录步骤2


            # 当每一步数据记录成功后返回结果，若其中一步错误则全部回滚
            if( $complaint_addID && $complaintlog_addID && $operation_id_1 && $operation_id_2 ){
                $model->commit();

                # 给FAE工程师推送邮件
                $this->pushEmail('ADD_CUSTOMER',$fae_user_info['email'],$post,$complaint_addID);

                $this->ajaxReturn( ['flag'=>1,'msg'=>'数据添加成功','id'=>$complaint_addID,'logid'=>$complaintlog_addID] );
            }else{
                $model->rollback();
                $this->ajaxReturn( ['flag'=>0,'msg'=>'数据添加失败'] );
            }
        }else{
            //将用户列表注入模板，（FAE工程师选择）
            $user = M('User');

            //如果不是销售部门人员访问该页面则提示错误
            if( session('user')['department'] != 4 ){
                $this->error('您没有权限访问该页面！');
            }

            $vendor_brand_model = M('VendorBrand');
            $vendor_brand_data = $vendor_brand_model->order('brand ASC')->select();
            $this->assign('vendorBrand',$vendor_brand_data);
            $userlist = $user->field('id,nickname')->where('position=12')->select();
            $this->assign('productFilter',$this->getProductData()); //注入产品筛选数据
            $this->assign('userlist',$userlist);
            $this->display();
        }
    }

    //插入附件
    public function insertAttachment(){
        if( IS_POST ){
            if( trim(I('post.logid')) != '' ){
                $RMA_LOG_MODEL = M('Oacustomercomplaintlog');
                $save_id = $RMA_LOG_MODEL->save( ['id'=>I('post.logid'),'attachment'=>I('post.attachments','',false)] );
                if( $save_id ){
                    $this->ajaxReturn( ['flag'=>1,'msg'=>'添加成功'] );
                }else{
                    $this->ajaxReturn( ['flag'=>0,'msg'=>'添加失败'] );
                }
            }
        }
    }
    
    //客诉详情
    public function details(){
        if(!I('get.id')) {
            $this->error('参数错误');
        };
        $user = M('User');

        //获取客诉处理记录并注入模板
        $complaint = M('Oacustomercomplaint');
        $complaintlog = M('Oacustomercomplaintlog');

        //获取当前id指定的客诉
        $resultData = $complaint->find(I('get.id'));
        //print_r($resultData);

        //如果当前访问客诉为老版客诉则直接跳转到老版客诉页面
        if( $resultData['version'] != 'new' ){
            $this->redirect(__ROOT__.'/Customer/details/id/'.I('get.id'));
            exit;
        }

        if( $resultData['rma_state'] == 'Y' && $resultData['assoc_close_reason'] != '' ) {   //如果客诉已关闭并且关闭原因不为空则将关闭原因注入模板
            $this->assign('RMACloseReason',M('Oacustomerclosereason')->find( $resultData['assoc_close_reason'] ) );
        }

        foreach($resultData as $key=>&$value){
            if($key=='error_message'){
                $value = htmlspecialchars_decode($value);
            }
            if($key=='reason'){
                $value['reason'] = htmlspecialchars_decode($value);
            }
            if($key=='salesperson'){
                $condition['account'] = $value;
                $nickname = $user->field('nickname')->where($condition)->select();
                //print_r($nickname);
                if($nickname){
                    $resultData['actual_name'] = $nickname[0]['nickname'];
                    $resultData['act_name'] = $nickname[0]['nickname'];
                }else{
                    $resultData['actual_name'] = $value;
                    $resultData['act_name'] = $value;
                }
            }
        }


        # 获取步骤数据
        $oacustomerstep_model = M('Oacustomerstep');
        $oacustomeroperation_model = M('Oacustomeroperation');

        $oacustomerstep_model_result = $oacustomerstep_model->field('a.id,a.step_name,a.fallback,a.transfer')->table('atop_oacustomerstep a,atop_oacustomeroperation b')->where('b.main_assoc='.I('get.id').' AND step_assoc=a.id')->group('b.step_assoc')->order('b.step_assoc ASC')->select();

        //print_r($resultData);

        $max_step = $oacustomerstep_model->max('id');   //获取到最大步骤
        $this->assign('maxStep',$max_step);

        //$oacustomeroperation_model_result = M()->field('a.now_step,b.operation_person')->table('atop_oacustomercomplaint a,atop_oacustomeroperation b')->where('a.now_step=b.step_assoc AND a.id='.I('get.id'))->order('b.id DESC')->limit(1)->select();
        /*$oacustomeroperation_model_result = $oacustomeroperation_model->where('main_assoc='.I('get.id'))->order('step_assoc DESC')->limit(1)->select();*/

        # 获取到当前正在进行的步骤
        $now_step = $resultData['now_step'];

        //$this->assign();

        $now_step_person_data = $oacustomeroperation_model->where( ['main_assoc'=>I('get.id'),'step_assoc'=>$now_step] )->select()[0];

        $now_operation_person = $now_step_person_data['operation_person'];

        //print_r($now_operation_person);

        $resultData['now_step_info'] = $oacustomerstep_model->find($now_step);

        if( $now_step < $max_step ){
            if( $now_step == 4 ){   //如果步骤进行到RMA分析报告（QA），则将产生两个可选步骤
                $resultData['next_step'] = $oacustomerstep_model->where('id>4')->select();
            }else{
                $resultData['next_step'] = $oacustomerstep_model->find($now_step+1);
            }
        }

        # 将关闭原因注入模板，但只有用户选择关闭客诉的时候才显示
        $close_reason = M('Oacustomerclosereason')->select();
        $this->assign('closeReason',$close_reason);

        $resultData['operation_person'] = $now_operation_person;

        $resultData['operation_person_name'] = $user->field('nickname')->find($now_operation_person)['nickname'];

        //print_r($resultData);

        # 如果当前步骤在2、4、5则将下一步的推送人注入模板
        if( $resultData['now_step'] == 2 || $resultData['now_step'] == 4 || $resultData['now_step'] == 5 ){
            if( $resultData['now_step'] == 2 ){
                $push_person_data = M()->field('b.id,b.nickname')->table(C('DB_PREFIX').'oacustomeroperation a,'.C('DB_PREFIX').'user b')->where( 'a.main_assoc='.I('get.id').' AND a.step_assoc=1 AND a.operation_person=b.id AND b.state=1' )->select()[0];
                $this->assign('pushPerson',$push_person_data['nickname']);
            }elseif( $resultData['now_step'] == 4 ){
                $productrelationships_model = M('Productrelationships');
                $oacustomercomplaint_model = M('Oacustomercomplaint');
                $oacustomercomplaint_result = $oacustomercomplaint_model->field('pn')->find( I('get.id') );  //获取到该客诉产品列表

                if( $oacustomercomplaint_result ) {
                    if (strpos($oacustomercomplaint_result['pn'], ',') !== false) {   //检测产品集中是否包含,号，如果包含则说明该客诉存在多个产品
                        $pns = explode(',', $oacustomercomplaint_result['pn']);
                        foreach ($pns as $key => &$value) {
                            $value = trim($value);  //去掉前后空格
                        }
                        $_map['pn'] = ['in', $pns]; //根据pn号查询数据
                        $productrelationships_result = $productrelationships_model->field('manager')->where($_map)->select();  //获取pn对应的产品经理
                        $operation_person_ids = [];
                        foreach ($productrelationships_result as $key => &$value) {   //拼装产品经理id
                            $operation_person_ids[] = $value['manager'];
                        }
                        $operation_person_ids = array_unique($operation_person_ids);  //未避免出现重复的产品经理，当id一致时保留唯一
                        $push_person_data = M('User')->field('nickname')->where( ['id'=>['in',$operation_person_ids]] )->select();
                        $person_str = '';
                        foreach( $push_person_data as $key=>&$value ){
                            $person_str .= $value['nickname'].'/';
                        }
                        $person_str = substr($person_str,0,-1);
                        $this->assign('pushPersonX',$person_str);
                    } else {
                        $push_person_data = M()->field('nickname')->table(C('DB_PREFIX').'productrelationships a,'.C('DB_PREFIX').'user b')->where( 'a.pn="'.trim($oacustomercomplaint_result['pn']).'" AND a.manager=b.id AND b.state=1 ' )->select()[0];
                        $this->assign('pushPersonX',$push_person_data['nickname']);
                    }
                }
                $push_person_data_2 = M()->field('b.id,b.nickname')->table(C('DB_PREFIX').'oacustomeroperation a,'.C('DB_PREFIX').'user b')->where( 'a.main_assoc='.I('get.id').' AND a.step_assoc=2 AND a.operation_person=b.id AND b.state=1' )->select()[0];
                $this->assign('pushPerson',$push_person_data_2['nickname']);
            }elseif( $resultData['now_step'] == 5 ){
                $push_person_data = M()->field('b.id,b.nickname')->table(C('DB_PREFIX').'oacustomeroperation a,'.C('DB_PREFIX').'user b')->where( 'a.main_assoc='.I('get.id').' AND a.step_assoc=2 AND a.operation_person=b.id AND b.state=1' )->select()[0];
                $this->assign('pushPerson',$push_person_data['nickname']);
            }
        }

        # 如果当前步骤为销售确认是否转RMA则将QA部门(品质部)人员列表注入模板
        if( $resultData['now_step'] == 3 || $resultData['now_step'] == 4 ){
            $QA_list = $user->where( 'department=3 AND id<>'.session('user')['id'].' AND state=1' )->select(); //获取到同部门且不包含自己的人员列表
            $this->assign('QAlist',$QA_list);
        }elseif( $resultData['now_step'] == 5 || $resultData['now_step'] == 6 || $resultData['now_step'] == 2 ){


            # 获取上一个步骤id
            $step_info_data = M('Oacustomercomplaintlog')->field('step')->where( 'cc_id='.I('get.id').' AND recorder<>"OASystem" AND step<'.$now_step)->order('id DESC')->limit(1)->select();

            $prev_step_info = $oacustomeroperation_model->where( ['main_assoc'=>$resultData['id'],'step_assoc'=>$step_info_data[0]['step']] )->select();
            //print_r($step_info_data);

            //echo $oacustomeroperation_model->getLastSql();

            //print_r($prev_step_info);

            //如果当前步骤在可以回退则获取回退步骤列表
            $oacustomeroperation_result = $prev_step_info[0];  //获取到上一个步骤节点

            if( strpos($oacustomeroperation_result['operation_person'],',') ){  //如果操作人id包含逗号说明有多个操作人
                $in['id'] = ['in',$oacustomeroperation_result['operation_person']];
                $step_person_list = $user->field('nickname')->where( $in )->select(); //查询在id集中的人员数据并获取姓名
                $names = '';
                foreach($step_person_list as $key=>$value){
                    $names .= $value['nickname'].'/';   //为方便模板展示，将id集中的人员拼装为字符串再输出到模板
                }
                $step_info['nickname'] = substr($names,0,-1);
            }else{
                $step_info['nickname'] = M('user')->field('nickname')->find( $oacustomeroperation_result['operation_person'] )['nickname']; //获取上一个步骤人的姓名
            }
            $tmp_step_data = $oacustomerstep_model->field('id,step_name')->find( $step_info_data[0]['step'] );

            $step_info['step_name'] = $tmp_step_data['step_name'];

            $step_info['step_id'] = $tmp_step_data['id'];


            //print_r($step_info);
            $this->assign('fallback',$step_info);
            if( $resultData['now_step'] == 6 || $resultData['now_step'] == 2 ){
                $FAE_list = M('User')->where( 'position=12 AND id<>'.session('user')['id'].' AND state=1' )->select(); //获取到同职位且不包含自己的人员列表
                $this->assign('FAElist',$FAE_list);
            }
        }

        //print_r($resultData);

        foreach( $oacustomerstep_model_result as $keyy=>&$valuee ){
            /*if( $resultData['version'] == 'new' ){
                if( $valuee['id'] != $now_step ){
                    $valuee['class'] = 'timeline-hide';
                }
            }*/
            $valuee['log'] = $complaintlog->where( ['step'=>$valuee['id'],'cc_id'=>I('get.id')] )->order('log_date,id ASC')->select();
            foreach($valuee['log'] as $key=>&$value){
                //为保留原始数据attachment列存入数据为文件名和json数据，为保证数据不冲突判断列数据是否为json格式来区分新版还是老版
                //判断附件是否为空并且是否为json格式，如果为json格式则用新版读取方式否则用老版数据
                if(!empty($value['attachment']) && $value['attachment']!='NULL'){
                    $json = json_decode($value['attachment'], true);
                    if(!is_null($json)){
                        if( count($json) == count($json,1) ){   //一维数组
                            $value['attachment'] = $json;
                            //遍历附件列表检查附件后缀
                            foreach($value['attachment'] as $ke=>&$val){
                                //获取文件扩展名
                                $old_filetype = getExtension($val);
                                //如果数据为image则为图像显示，否则一律为文件处理(下载)
                                if( $old_filetype=='jpeg' || $old_filetype=='jpg' || $old_filetype=='gif' || $old_filetype=='png' || $old_filetype=='bmp' ){
                                    $val = array('filename'=>getBasename($val),'filetype'=>'image','filepath'=>$val);
                                }else{
                                    $val = array('filename'=>getBasename($val),'filetype'=>'other','filepath'=>$val);
                                }
                            }
                        }else{  //二维数组
                            $value['attachment'] = $json;
                            foreach($value['attachment'] as $ke=>&$val){
                                //$mimetype = mime_content_type($val['SavePath']);    //获取文件类型
                                $old_filetype = getExtension($val['SavePath']);
                                //获取文件扩展名
                                if( $old_filetype=='jpeg' || $old_filetype=='jpg' || $old_filetype=='gif' || $old_filetype=='png' || $old_filetype=='bmp' ){
                                    $val = array('filename'=>$val['SourceName'],'filetype'=>'image','filepath'=>$val['SavePath']);
                                }else{
                                    $val = array('filename'=>$val['SourceName'],'filetype'=>'other','filepath'=>$val['SavePath']);
                                }
                            }
                        }
                    }else{
                        $oldFilePath = '/Uploads/OldCustomerComplaintAttachment/'.$value['cc_id'].'/';
                        $value['attachment'] = explode(',',$value['attachment']);
                        foreach($value['attachment'] as $ke=>&$val){
                            if($val==''){
                                unset($value['attachment'][$ke]);
                            }
                            //获取文件扩展名
                            $old_filetype = getExtension($val);
                            //如果数据为image则为图像显示，否则一律为文件处理(下载)
                            if( $old_filetype=='jpeg' || $old_filetype=='jpg' || $old_filetype=='gif' || $old_filetype=='png' || $old_filetype=='bmp' ){
                                $val = array('filename'=>getBasename($val),'filetype'=>'image','filepath'=>$oldFilePath.$val);
                            }else{
                                $val = array('filename'=>getBasename($val),'filetype'=>'other','filepath'=>$oldFilePath.$val);
                            }
                        }
                    }
                }
                //判断picture列是否为空
                if(!empty($value['picture']) && $value['picture']!='NULL'){
                    $old_FilePath = '/Uploads/OldCustomerComplaintAttachment/'.$value['cc_id'].'/';
                    $value['picture'] = explode(',',$value['picture']);
                    foreach($value['picture'] as $ke=>&$val){
                        if($val==''){
                            unset($value['picture'][$ke]);
                        }
                        $val = array('filepath' => $old_FilePath . $val, 'filename' => $val);
                    }
                }
                //如果该用户头像文件不存在则采用默认头像
                if( $value['recorder'] != 'OASystem' ){
                    if( $value['uid'] != 0 ){
                        $face = $user->field('nickname,face')->find($value['uid']);
                        if($face){
                            $value['face'] = $face['face'];
                            $value['nickname'] = $face['nickname'];
                        }else{
                            $value['face'] = '/Public/home/img/face/default_face.png';
                            $value['nickname'] = 'Unknow';
                        }
                    }else{
                        $face = $user->field('nickname,face')->where('account="'.$value['recorder'].'"')->limit(1)->select()[0];
                        if($face){
                            $value['face'] = $face['face'];
                            $value['nickname'] = $face['nickname'];
                        }else{
                            $value['face'] = '/Public/home/img/face/default_face.png';
                            $value['nickname'] = $value['recorder'];
                        }
                    }
                }else{
                    $value['face'] = '/Public/home/img/OA_ststem_face.png';
                }
                //将数据进行反编译，实现br标签换行
                $value['log_content']= htmlspecialchars_decode($value['log_content']);
            }
        }
        //print_r($oacustomerstep_model_result);
        //print_r($this->getDepartmentsAndUsers());
        $this->assign('ccList',$this->getDepartmentsAndUsers());

        $QA_list = M('User')->where(['department'=>'3'])->select();  //获取QA（品质部）人员
        $this->assign('QAlist',$QA_list);
        $this->assign('details',$resultData);
        $this->assign('nodeData',$oacustomerstep_model_result);
        //print_r($resultData);

        $this->display();
    }

    /**
     * 客诉满意度汇总
     */
    public function satisfaction(){
        $model = new Model();
        $count = $model->table(C('DB_PREFIX').'rma_satisfaction')->count();
        //数据分页
        $page = new Page($count, C('LIMIT_SIZE'));
        $page->setConfig('prev','<span aria-hidden="true">上一页</span>');
        $page->setConfig('next','<span aria-hidden="true">下一页</span>');
        $page->setConfig('first','<span aria-hidden="true">首页</span>');
        $page->setConfig('last','<span aria-hidden="true">尾页</span>');
        if( C('PAGE_STATUS_INFO') ){
            $page->setConfig ( 'theme', '<li><a href="javascript:void(0);">当前%NOW_PAGE%/%TOTAL_PAGE%</a></li>  %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%' );
        }
        $satisfactions = $model->table(C('DB_PREFIX').'rma_satisfaction')->limit($page->firstRow.','.$page->listRows)->order('id DESC')->select();
        foreach( $satisfactions as $key=>&$value ){
            if( !empty($value['attachment']) ) $value['attachment'] = json_decode($value['attachment'], true);
        }
        $issues = $this->getIssues();
        $this->assign('issues', $issues);
        $pageShow = $page->show();
        if(I('get.p')){
            $this->assign('pagenumber',I('get.p')-1);
        }
        $this->assign('limitsize', C('LIMIT_SIZE'));
        $this->assign('pageShow',$pageShow);
        $this->assign('satisfactions', $satisfactions);
        $this->display();
    }

    /**
     * 添加送样客诉
     */
    public function addSatisfaction(){
        if( !IS_POST ){
            $issues = $this->getIssues();
            $this->assign('issues', $issues);
            $this->display();
        }else{
            $model = new Model();
            $postData = I('post.', '', false);
            $postData['create_time'] = time();
            $postData['create_user'] = session('user')['id'];
            $id = $model->table(C('DB_PREFIX').'rma_satisfaction')->add($postData);
            if( $id ){
                $this->ajaxReturn( ['flag'=>$id, 'msg'=>'添加成功'] );
            }else{
                $this->ajaxReturn( ['flag'=>0, 'msg'=>'添加失败'] );
            }
        }
    }

    /**
     * 保存送样客诉编辑
     */
    public function saveEditData(){
        if( IS_POST ){
            $postData = I('post.', '', false);
            $postData['update_time'] = time();
            $model = new Model();
            $flag = $model->table(C('DB_PREFIX').'rma_satisfaction')->save($postData);
            if( $flag !== false ){
                $this->ajaxReturn( ['flag'=>1, 'msg'=>'保存成功'] );
            }else{
                $this->ajaxReturn( ['flag'=>0, 'msg'=>'保存失败'] );
            }
        }
    }

    /**
     * 删除指定送样客诉
     */
    public function deleteSatisfaction(){
        if( IS_POST && I('post.id') && is_numeric(I('post.id')) ){
            $model = new Model();
            $result = $model->table(C('DB_PREFIX').'rma_satisfaction')->find(I('post.id', '', false));
            if( !empty($result['attachment']) ){    //如果当前送样客诉包含附件则将附件源文件一同删除
                $files = json_decode($result['attachment'], true);
                foreach( $files as $key=>&$value ){
                    //兼容文件存在中文字符导致的不能识别问题，将UTF-8编码转换为GB2312
                    $filePath = iconv('UTF-8', 'GB2312', substr($value['path'],1));
                    //将相对路径改为绝对路径保证文件的正确检测
                    if( file_exists( getcwd().$filePath ) ) unlink( getcwd().$filePath );
                }
            }
            $id = $model->table(C('DB_PREFIX').'rma_satisfaction')->delete(I('post.id', '', false));
            if( $id ){
                $this->ajaxReturn( ['flag'=>1, 'msg'=>'删除成功'] );
            }else{
                $this->ajaxReturn( ['flag'=>0, 'msg'=>'删除失败'] );
            }
        }
    }

    /**
     * 送样客诉附件上传
     */
    public function SF_Upload(){
        if( I('post.SUB_NAME', '', false) && is_numeric(I('post.SUB_NAME')) ){
            $result = upload( '/RMA/SatisfactionDegree/', I('post.SUB_NAME', '', false) );
            $this->ajaxReturn( $result );
        }
    }

    /**
     * 添加问题点
     */
    public function addIssue(){
        if( IS_POST ){
            $model = new Model();
            $postData = I('post.', '', false);
            $id = $model->table(C('DB_PREFIX').'rma_issue')->add($postData);
            if( $id ){
                $this->ajaxReturn( ['flag'=>$id, 'msg'=>'添加成功'] );
            }else{
                $this->ajaxReturn( ['flag'=>0, 'msg'=>'添加失败'] );
            }
        }
    }

    /**
     * 获取所有问题点
     * @param string $id
     * @return mixed
     */
    private function getIssues($id = ''){
        $model = new Model();
        if( $id ){
            $result = $model->table(C('DB_PREFIX').'rma_issue')->find($id);
            if( $result ){
                return $result;
            }
        }else{
            $result = $model->table(C('DB_PREFIX').'rma_issue')->select();
            if( $result ){
                return $result;
            }
        }
    }

    /**
     * 客诉流程页
     */
    public function flowchart(){
        $this->display();
    }

    /**
     * 获取部门和人员
     * @return mixed
     */
    private function getDepartmentsAndUsers(){
        $result['departments'] = M('Department')->select();
        $result['users'] = M('User')->where('id<>1 AND state=1')->select();
        return $result;
    }

    /**
     * 文件上传
     * 1. js端指定上传路径
     * 2. js端指定子目录生成名称
     */
    public function upload(){
        $subName = I('post.SUB_NAME');
        // 如果需要按id为子目录则必须填写第二个参数，否则直接保存在当前目录
        if( $subName != '' ){
            $result = upload( '/RMA/', $subName );
            $this->ajaxReturn( $result );
        }
    }
    
    //文件上传(客诉处理/客诉详情/添加客诉)
    public function uploadFile(){
        $info = FileUpload('/CustomerComplaintAttachment/',true,true,'RMA_');
        if( is_array( $info ) && !empty( $info ) ) {
            $oldname = $info['Filedata']['name'];
            $newname = $info['Filedata']['savename'];
            $filePath = './Uploads'.$info['Filedata']['savepath'].$info['Filedata']['savename'];
            $arr['flag'] = 1;
            $arr['OldFileName'] = $oldname;
            $arr['NewFileName'] = $newname;
            $arr['FileSavePath'] = $filePath;
            $this->ajaxReturn($arr);
            exit;
        }else{
            $arr['flag'] = 0;
            $arr['msg'] = $info;
            $this->ajaxReturn($arr);
        }
    }


    /**
     * 获取指定步骤前左右的参与人员数据
     * @param $customer_id  客诉id号
     * @return array
     */
    private function GetInvolvedIn($customer_id){
        $oacustomeroperation_model = M('Oacustomeroperation');
        $now_step = $oacustomeroperation_model->where('main_assoc='.$customer_id)->max('step_assoc'); //获取当前步骤
        $involed = $oacustomeroperation_model->field('operation_person')->where('main_assoc='.$customer_id.' AND step_assoc < '.$now_step)->select();  //获取到参与当前客诉的有关人员
        $persons = [];  //存放参与人员id的数组
        foreach($involed as $key=>$value){
            if( strpos($value['operation_person'],',') ){   //如果参与人员id中包含逗号，则说明存在多个人员
                $tmp_arr = explode(',',$value['operation_person']);
                foreach($tmp_arr as $k=>$v){
                    array_push($persons,$v);
                }
            }else{
                array_push($persons,$value['operation_person']);
            }
        }
        $persons = array_unique($persons);  //为防止出现重复参与人保证唯一
        $user_model = M('User');
        $map['id'] = ['in',$persons];
        $users_result = $user_model->field('email')->where( $map )->select();   //获取到参与该客诉人员的Email
        $emails = [];
        foreach($users_result as $key=>$value){  //将得到的结果集转为一维数组
            array_push($emails,$value['email']);
        }
        return $emails;
    }

    //新增RMA处理日志
    public function addRMA_OperationLog(){
        if( IS_POST ){

            $post = I('post.','',false);

            # 记录录入信息
            $logData['cc_id'] = $post['cc_id'];
            $logData['log_date'] = date('Y-m-d');
            $logData['log_content'] = replaceEnterWithBr(strip_tags($post['log_content']));
            $logData['attachment'] = $post['attachments'];
            $logData['recorder'] = $post['recorder'];
            $logData['timestamp'] = date('Y-m-d H:i:s');
            $logData['uid'] = session('user')['id'];
            $logData['step'] = $post['step'];
            $logData['version'] = $post['version'];

            //print_r($post);
            if( $post['operation_type'] == 'X' ){ //如果操作类型为0则表示该操作为添加日志，如果不为0则表示推送到该步骤

                $oacustomercomplaintlog_model = M('Oacustomercomplaintlog');

                $add_id = $oacustomercomplaintlog_model->add($logData);

                if( $add_id ){

                    # 如果是添加日志，则获取到当前步骤前的所有步骤处理人员，分别推送邮件
                    $emails = $this->GetInvolvedIn($post['cc_id']);

                    array_push($emails,session('user')['email']);   //将当前用户邮箱添加到邮件推送列表

                    # 检查用户是否开启抄送功能，如果开启则将被选中的抄送人添加至发送列表
                    if( isset($post['cc_on']) && $post['cc_on'] == 'Y' ){
                        $this->pushEmail('ADD_LOG',$emails,$logData,$post['cc_id'],$post['cc_email_list']);
                    }else{
                        $this->pushEmail('ADD_LOG',$emails,$logData,$post['cc_id']);
                    }

                    $this->ajaxReturn( ['flag'=>1,'msg'=>'添加成功','id'=>$post['cc_id'],'logid'=>$add_id] );
                }else{
                    $this->ajaxReturn( ['flag'=>0,'msg'=>'添加失败'] );
                }
            }elseif( $post['operation_type'] == 'N' ){  //如果操作类型为N则表示将该客诉关闭并且不能继续往下推送

                $oacustomercomplaintlog_model = M('Oacustomercomplaintlog');

                $oacustomercomplaint_model = M('Oacustomercomplaint');

                # 开启事务
                M()->startTrans();

                $add_id_1 = $oacustomercomplaintlog_model->add($logData);
                //$step_result = M('Oacustomerstep')->find($post['step_name']);

                $logDataBak = $logData;

                # 记录操作日志
                $logData['log_content'] = '['.session('user')['nickname'].'] 关闭客诉。';
                $logData['recorder'] = 'OASystem';
                $logData['attachment'] = '';
                $add_id_2 = $oacustomercomplaintlog_model->add($logData);

                $saveData['id'] = $post['cc_id'];
                $saveData['rma_state'] = 'Y';   //Y表示当前客诉关闭
                $saveData['assoc_close_reason'] = $post['assoc_close_reason'];   //记录关闭原因
                $save_id = $oacustomercomplaint_model->save($saveData);

                if( $add_id_1 && $add_id_2 && $save_id ){
                    M()->commit();

                    $close_reason_text = M('Oacustomerclosereason')->find($post['assoc_close_reason']);
                    $logDataBak['close_reason_text'] = $close_reason_text['close_reason'];

                    $emails = $this->GetInvolvedIn($post['cc_id']);

                    # 检查用户是否开启抄送功能，如果开启则将被选中的抄送人添加至发送列表
                    if( isset($post['cc_on']) && $post['cc_on'] == 'Y' ) {
                        $this->pushEmail('CUSTOMER_CLOSE', $emails, $logDataBak, $post['cc_id'],$post['cc_email_list']);
                    }else{
                        $this->pushEmail('CUSTOMER_CLOSE', $emails, $logDataBak, $post['cc_id']);
                    }

                    $this->ajaxReturn( ['flag'=>1,'msg'=>'添加成功','id'=>$post['cc_id'],'logid'=>$add_id_1] );
                }else{
                    M()->rollback();
                    $this->ajaxReturn( ['flag'=>0,'msg'=>'添加失败'] );
                }
            }elseif( $post['operation_type'] == 'Z' ){   //如果操作类型为transfer，则说明用户将该步骤操作人更换为其他人选

                # 开启事务
                $model = new Model();
                $model->startTrans();

                $add_id_1 = $model->table(C('DB_PREFIX').'oacustomercomplaintlog')->add($logData);

                # 获取转交人的数据
                $QA_person_data = M('User')->field('id,nickname,email')->find( $post['operation_person'] );
                $save_id = $model->table(C('DB_PREFIX').'oacustomeroperation')->where( ['main_assoc'=>$post['cc_id'],'step_assoc'=>$post['step']] )->save( ['operation_person'=>$post['operation_person']] );

                $logDataBak = $logData;
                $logDataBak['QA_person_nickname'] = $QA_person_data['nickname'];

                # 记录操作日志
                $logData['log_content'] = '['.session('user')['nickname'].'] 将操作人转交给 ['.$QA_person_data['nickname'].']。';
                $logData['recorder'] = 'OASystem';
                $logData['attachment'] = '';
                $add_id_2 = $model->table(C('DB_PREFIX').'oacustomercomplaintlog')->add($logData);

                if( $add_id_1 && $add_id_2 && $save_id ){
                    $model->commit();

                    $emails = $this->GetInvolvedIn($post['cc_id']);

                    # 检查用户是否开启抄送功能，如果开启则将被选中的抄送人添加至发送列表
                    # 如果已经存在抄送人则将已存在的和用户选择的合并
                    if( isset($post['cc_on']) && $post['cc_on'] == 'Y' ) {
                        $this->pushEmail('TRANSFER', $QA_person_data['email'], $logDataBak, $post['cc_id'], array_merge( $emails, $post['cc_email_list'] ) );
                    }else{
                        $this->pushEmail('TRANSFER', $QA_person_data['email'], $logDataBak, $post['cc_id'], $emails);
                    }

                    $this->ajaxReturn( ['flag'=>1,'msg'=>'添加成功','id'=>$post['cc_id'],'logid'=>$add_id_1] );
                }else{
                    $model->rollback();
                    $this->ajaxReturn( ['flag'=>0,'msg'=>'添加失败'] );
                }

            }elseif( $post['operation_type'] == 'H' ){  //如果操作类型为H，则说明用户需要将步骤回退到指定的步骤

                # 开启事务
                $model = new Model();

                $model->startTrans();

                $add_id_1 = $model->table('atop_oacustomercomplaintlog')->add($logData);

                $step_result = M()->field('b.id,b.step_name')
                                  ->table(C('DB_PREFIX').'oacustomeroperation a,'.C('DB_PREFIX').'oacustomerstep b')
                                  ->where( 'a.main_assoc='.$post['cc_id'].' AND a.step_assoc<'.$post['step'].' AND a.step_assoc=b.id' )
                                  ->group('b.id')
                                  ->order('a.step_assoc DESC')
                                  ->limit(1)
                                  ->select()[0];

                $logDataBak = $logData;

                # 记录操作日志
                $logData['log_content'] = '['.session('user')['nickname'].'] 将步骤回退到：Step'.$step_result['id'].' - '.$step_result['step_name'].'。';
                $logData['recorder'] = 'OASystem';
                $logData['attachment'] = '';
                $add_id_2 = $model->table('atop_oacustomercomplaintlog')->add($logData);

                # 修改主表当前步骤
                $save_id = $model->table('atop_oacustomercomplaint')->save( ['id'=>$post['cc_id'],'now_step'=>$step_result['id']] );

                if( $add_id_1 && $add_id_2 && $save_id !== false ){

                    $model->commit();

                    $emails = $this->GetInvolvedIn($post['cc_id']);
                    $logDataBak['step_id'] = $step_result['id'];
                    $logDataBak['step_name'] = $step_result['step_name'];

                    array_push($emails,session('user')['email']);

                    # 检查用户是否开启抄送功能，如果开启则将被选中的抄送人添加至发送列表
                    if( isset($post['cc_on']) && $post['cc_on'] == 'Y' ) {
                        $this->pushEmail('FALLBACK', $emails, $logDataBak, $post['cc_id'],$post['cc_email_list']);
                    }else{
                        $this->pushEmail('FALLBACK', $emails, $logDataBak, $post['cc_id']);
                    }

                    $this->ajaxReturn( ['flag'=>1,'msg'=>'添加成功','id'=>$post['cc_id'],'logid'=>$add_id_1] );
                }else{
                    $model->rollback();
                    $this->ajaxReturn( ['flag'=>0,'msg'=>'添加失败'] );
                }

            }else{

                $oacustomercomplaint_model = M('Oacustomercomplaint');
                $oacustomeroperaion_model = M('Oacustomeroperation');

                # 开启事务
                $model = new Model();

                $add_id_1 = $model->table('atop_oacustomercomplaintlog')->add($logData);

                $logDataBak = $logData; //防止系统日志冲突

                $logDataBak['push_step'] = $post['operation_type']; //将用户选择的推送步骤添加到邮件数据

                if( $post['step'] == 2 ){
                    $operation_person_result = $oacustomeroperaion_model->field('operation_person')->where( ['main_assoc'=>$post['cc_id'],'step_assoc'=>1] )->select();
                    $operation_person_id = $operation_person_result[0]['operation_person'];     //获取到销售id作为下一步推送人
                }elseif( $post['step'] == 3 ){
                    $operation_person_id = $post['operation_person'];     //获取到QA作为下一步推送人
                }elseif( $post['operation_type'] == 5 ){  //如果用户将步骤推送到协助处理RMA，则获取该客诉产品的产品经理

                    $productrelationships_model = M('Productrelationships');
                    $oacustomercomplaint_result = $oacustomercomplaint_model->field('pn')->find( $post['cc_id'] );  //获取到该客诉产品列表
                    if( $oacustomercomplaint_result ){
                        if( strpos($oacustomercomplaint_result['pn'],',') !== false ){   //检测产品集中是否包含,号，如果包含则说明该客诉存在多个产品
                            $pns = explode(',',$oacustomercomplaint_result['pn']);
                            foreach($pns as $key=>&$value){
                                $value = trim($value);  //去掉前后空格
                            }
                            $condition['pn'] = ['in',$pns]; //根据pn号查询数据
                            $productrelationships_result = $productrelationships_model->field('manager')->where($condition)->select();  //获取pn对应的产品经理
                            $operation_person_ids = [];
                            foreach( $productrelationships_result as $key=>&$value ){   //拼装产品经理id
                                $operation_person_ids[] = $value['manager'];
                            }
                            $operation_person_ids = array_unique($operation_person_ids);  //未避免出现重复的产品经理，当id一致时保留唯一
                            $operation_person_ids = implode(',',$operation_person_ids); //如果存在多个产品经理则用逗号隔开拼接成字符串
                            $operation_person_id = $operation_person_ids;
                        }else{
                            $productrelationships_result = $productrelationships_model->field('manager')->where( ['pn'=>trim($oacustomercomplaint_result['pn'])] )->select();
                            $operation_person_id = $productrelationships_result[0]['manager'];
                        }
                    }
                }elseif( $post['operation_type'] == 6 ){
                    $operation_person_result = $oacustomeroperaion_model->field('operation_person')->where( ['main_assoc'=>$post['cc_id'],'step_assoc'=>2] )->select();
                    $operation_person_id = $operation_person_result[0]['operation_person'];     //获取到FAE工程师id作为下一步推送人
                }

                $step_result = M('Oacustomerstep')->find($post['operation_type']);  //获取到下一个步骤

                # 记录操作日志
                $logData['log_content'] = '['.session('user')['nickname'].'] 将步骤推送到：Step'.$step_result['id'].' - '.$step_result['step_name'].'。';
                $logData['recorder'] = 'OASystem';
                $logData['attachment'] = '';
                $add_id_2 = $model->table('atop_oacustomercomplaintlog')->add($logData);

                # 修改主表当前步骤
                $save_id = $model->table('atop_oacustomercomplaint')->save( ['id'=>$post['cc_id'],'now_step'=>$step_result['id']] );

                $operationData['main_assoc'] = $post['cc_id'];
                $operationData['step_assoc'] = $post['operation_type'];
                $operationData['operation_person'] = $operation_person_id;

                # 为保证操作表步骤唯一，每当推送时始终检查数据库是否存在该客诉的步骤节点信息，如果存在为修改，不存在则新增
                $unique_operation = $model->table('atop_oacustomeroperation')->where( ['main_assoc'=>$post['cc_id'],'step_assoc'=>$post['operation_type']] )->select();

                if( empty($unique_operation) ){
                    $add_id_3 = $model->table('atop_oacustomeroperation')->add($operationData);
                }else{
                    $add_id_3 = $model->table('atop_oacustomeroperation')->where( ['main_assoc'=>$post['cc_id'],'step_assoc'=>$post['operation_type']] )->save( ['operation_person'=>$operation_person_id] );
                }

                if( $add_id_1 && $add_id_2 && $add_id_3 !== false && $save_id !== false ){
                    $model->commit();

                    # 如果是添加日志，则获取到当前步骤前的所有步骤处理人员，分别推送邮件
                    $userInfo = $userEmail = $this->GetPushPerson($operation_person_id);   //获取收件人信息
                    $emails = $this->GetInvolvedIn($post['cc_id']);     //获取抄送人
                    $logDataBak['dear'] = $userInfo['nickname'];

                    # 检查用户是否开启抄送功能，如果开启则将被选中的抄送人添加至发送列表
                    # 如果已经存在抄送人则将已存在的和用户选择的合并
                    if( isset($post['cc_on']) && $post['cc_on'] == 'Y' ) {
                        $this->pushEmail('PUSH_STEP', $userInfo['email'], $logDataBak, $post['cc_id'], array_merge( $emails, $post['cc_email_list'] ) );
                    }else{
                        $this->pushEmail('PUSH_STEP', $userInfo['email'], $logDataBak, $post['cc_id'], $emails);
                    }

                    $this->ajaxReturn( ['flag'=>1,'msg'=>'添加成功','id'=>$post['cc_id'],'logid'=>$add_id_1] );
                }else{

                    $model->rollback();

                    $this->ajaxReturn( ['flag'=>0,'msg'=>'添加失败'] );

                }

            }

        }

    }


    /**
     * 获取指定推送人的邮箱(可能包含多个)
     * @param $userid 用户id
     * @return array
     */
    private function GetPushPerson($userid){
        $user_model = M('User');
        $map['id'] = ['in',$userid];
        $user_result = $user_model->field('email,nickname')->where($map)->select();
        $email = [];
        $nickname = '';
        foreach( $user_result as $key=>$value ){
            array_push($email,$value['email']);
            $nickname .= $value['nickname'].'&';
        }
        $userInfo['email'] = $email;
        $userInfo['nickname'] = substr($nickname,0,-1);
        return $userInfo;
    }

    # 检测收件人信息
    private function checkEmailOnly( $emails ){
        $call = '';
        if( is_array( $emails ) ){
            $emails_num = count( $emails );
            if( $emails_num <= 1 ){
                $users = M('User')->field('nickname')->where('email="'.$emails_num[0].'"')->select();
                if( count( $users ) == 1 ){
                    $call = $users[0]['nickname'];
                }else{
                    foreach( $users as $key=>&$value ){
                        $call .= $value['nickname'].'&';
                    }
                    $call = substr($call,0,-1);
                }
            }else{
                $call = 'All';
            }
        }else{
            $users = M('User')->field('nickname')->where('email="'.$emails.'"')->select();
            if( count( $users ) == 1 ){
                $call = $users[0]['nickname'];
            }else{
                foreach( $users as $key=>&$value ){
                    $call .= $value['nickname'].'&';
                }
                $call = substr($call,0,-1);
            }
        }
        return $call;
    }


    //推送邮件
    private function pushEmail( $type,$emails,$data,$id='',$cc=[] ){
        $http_host = $_SERVER['HTTP_HOST'];
        $user_nickname = session('user')['nickname'];
        extract($data);
        $customer_result = M('Oacustomercomplaint')->field('pn,vendor,model,error_message')->find($id); //获取客诉基本信息
        extract($customer_result);
        if( $type == 'IS_RMA' ){    // 如果是IS_RMA则为通知销售是否转RMA
            $subject = '客诉转RMA';
            $body = <<<HTML
<p>是否转RMA</p>
<p>点击链接查看详情：<a target="_blank" href="http://$http_host/RMA/details/id/$id">http://$http_host/RMA/details/id/$id</a></p>
HTML;
        }elseif( $type == 'ADD_CUSTOMER' ){
            $subject = '[ '.$nickname.' ] 的客户有一条新客诉，请关注！';
            $body = <<<HTML
<style>
p {
    line-height: 25px;
}
</style>
<p>Dear $fae_nickname,</p>
<p>[ $nickname ] 的客户有一条新客诉，请关注！</p>
<p><b>产品型号：</b>$pn</p>
<p><b>设备厂商：</b>$vendor</p>
<p><b>设备型号：</b>$model</p>
<p><b>错误现象：</b>$error_message</p>
<p><b>详情请点击链接：</b><a href="http://$http_host/RMA/details/id/$id">http://$http_host/RMA/details/id/$id</a></p>
HTML;

        }elseif( $type == 'ADD_LOG' ){  //添加日志推送邮件
            $call = $this->checkEmailOnly( $emails );
            $subject = '客诉日志更新 [ ID:'.$id.' ]';
            $body = <<<HTML
<style>
p {
    line-height: 25px;
}
</style>
<p>Dear $call,</p>
<p>[$user_nickname] 更新了客诉日志，内容如下：</p>
<p>$log_content</p><br>
<p><b>客诉基本信息：</b></p>
<p>产品型号：$pn</p>
<p>设备厂商：$vendor</p>
<p>设备型号：$model</p>
<p>错误现象：$error_message</p><br>
<p>详情请点击链接：<a target="_blank" href="http://$http_host/RMA/details/id/$id">http://$http_host/RMA/details/id/$id</a></p>
HTML;
        }elseif( $type == 'PUSH_STEP' ){
            $step_result = M('Oacustomerstep')->find($push_step);  //获取到下一个步骤数据
            $step_name = $step_result['step_name'];
            $step_id = $step_result['id'];
            $subject = '客诉步骤更新 [ ID:'.$id.' ]';
            $body = <<<HTML
<style>
p {
    line-height: 25px;
}
</style>
<p>Dear $dear,</p>
<p>[$user_nickname] 将客诉步骤推送到：Step$step_id - $step_name</p>
<p>备注：$log_content</p><br>
<p><b>客诉基本信息：</b></p>
<p>产品型号：$pn</p>
<p>设备厂商：$vendor</p>
<p>设备型号：$model</p>
<p>错误现象：$error_message</p><br>
<p>详情请点击链接：<a target="_blank" href="http://$http_host/RMA/details/id/$id">http://$http_host/RMA/details/id/$id</a></p>
HTML;
        }elseif( $type == 'CUSTOMER_CLOSE' ){   //如果是关闭客诉
            $call = $this->checkEmailOnly( $emails );
            $subject = '客诉关闭 [ ID:'.$id.' ]';
            $body = <<<HTML
<style>
p {
    line-height: 25px;
}
</style>
<p>Dear $call,</p>
<p>[$user_nickname] 将客诉关闭</p>
<p>关闭原因：$close_reason_text</p>
<p>备注：$log_content</p><br>
<p><b>客诉基本信息：</b></p>
<p>产品型号：$pn</p>
<p>设备厂商：$vendor</p>
<p>设备型号：$model</p>
<p>错误现象：$error_message</p><br>
<p>详情请点击链接：<a target="_blank" href="http://$http_host/RMA/details/id/$id">http://$http_host/RMA/details/id/$id</a></p>
HTML;
        }elseif( $type == 'TRANSFER' ){
            $call = $this->checkEmailOnly( $emails );
            $step_result = M('Oacustomerstep')->field('id,step_name')->find($step);
            $step_name = $step_result['step_name'];
            $step_id = $step_result['id'];
            $subject = '客诉处理人变更 [ ID:'.$id.' ]';
            $body = <<<HTML
<style>
p {
    line-height: 25px;
}
</style>
<p>Dear $call,</p>
<p>[$user_nickname] 将客诉处理人转交给 [$QA_person_nickname]，当前步骤：Step$step_id - $step_name</p>
<p>备注：$log_content</p><br>
<p><b>客诉基本信息：</b></p>
<p>产品型号：$pn</p>
<p>设备厂商：$vendor</p>
<p>设备型号：$model</p>
<p>错误现象：$error_message</p><br>
<p>详情请点击链接：<a target="_blank" href="http://$http_host/RMA/details/id/$id">http://$http_host/RMA/details/id/$id</a></p>
HTML;
        }elseif( $type == 'FALLBACK' ){
            $call = $this->checkEmailOnly( $emails );
            $subject = '客诉步骤回退 [ ID:'.$id.' ]';
            $body = <<<HTML
<style>
p {
    line-height: 25px;
}
</style>
<p>Dear $call,</p>
<p>[$user_nickname] 将步骤回退到：Step$step_id - $step_name</p>
<p>备注：$log_content</p><br>
<p><b>客诉基本信息：</b></p>
<p>产品型号：$pn</p>
<p>设备厂商：$vendor</p>
<p>设备型号：$model</p>
<p>错误现象：$error_message</p><br>
<p>详情请点击链接：<a target="_blank" href="http://$http_host/RMA/details/id/$id">http://$http_host/RMA/details/id/$id</a></p>
HTML;
        }
        if( empty($cc) ){
            $result = send_Email($emails,'',$subject,$body);
            if( $result != 1 ){ //如果邮件发送失败则返回错误信息
                $this->ajaxReturn( ['falg'=>0,'msg'=>$result] );
            }
        }else{
            $result = send_Email($emails,'',$subject,$body,$cc);  //$cc
            if( $result != 1 ){ //如果邮件发送失败则返回错误信息
                $this->ajaxReturn( ['falg'=>0,'msg'=>$result] );
            }
        }
    }
    
    //添加处理记录
    public function addComplaintLog(){
        if(!IS_AJAX) return false;
        $complaintLog = M('Oacustomercomplaintlog');
        $data_status_doing['cc_id'] = I('post.cc_id');
        $data_status_doing['log_date'] = I('post.log_date');
        $data_status_doing['log_content'] = replaceEnterWithBr('['.I('post.recorder').']'.I('log_content'));
        $data_status_doing['attachment'] = I('post.attachment','',false);
        $data_status_doing['recorder'] = session('user')['account'];
        $data_status_doing['timestamp'] = date('Y-m-d H:i:s',time());
        $data_status_doing['uid'] = session('user')['id'];
        $ID = $complaintLog->add($data_status_doing);
        if(I('post.status')==-1){
            //如果状态为-1则添加log日志，并将客诉状态更改为无法处理


            # 当状态被改为无法处理时，将消息推送到销售，由销售决定是否转为RMA...
            # write code ...
            $complaint = M('Oacustomercomplaint');
            $complaintResult = $complaint->find( I('post.cc_id') );   //获取销售账号
            $userModel = M('User');
            $salepersonEmail = $userModel->field('email')->where('account="'.$complaintResult['salesperson'].'"')->limit(1)->select()[0];    //获取到销售邮箱

            # 为保证销售有独立的处理操作，将sale_audit值改为1(默认0)作为唯一销售审核标志
            $saveData['id'] =  I('post.cc_id');
            $saveData['sale_audit'] = 1;
            $impace_line = $complaint->save($saveData);
            if( $impace_line ){ // 如果更改成功则给销售推送邮件告知销售该客诉是否需要转为RMA
                $this->pushEmail('IS_RMA','vinty_email@163.com',$complaintResult); //将消息推送给销售，让销售决定是否转RMA
            }


            $data_status_error['cc_id'] = I('post.cc_id');
            $data_status_error['log_date'] = I('post.log_date');
            $data_status_error['log_content'] = '['.I('post.recorder').']'.'状态改为[无法处理]。';
            //$data_status_error['attachment'] = I('post.attachment','',false);
            $data_status_error['recorder'] = session('user')['account'];
            $data_status_error['timestamp'] = date('Y-m-d H:i:s',time());
            $data_status_error['uid'] = session('user')['id'];
            $addID_error = $complaintLog->add($data_status_error);
            $complaint = M('Oacustomercomplaint');
            $map['id'] = I('post.cc_id');
            $map['status'] = -1;
            $saveID_error = $complaint->save($map);
        }elseif(I('post.status')==0){
            //如果状态为1则添加log日志，并将客诉状态更改为已经处理
            $data_status_success['cc_id'] = I('post.cc_id');
            $data_status_success['log_date'] = I('post.log_date');
            $data_status_success['log_content'] = '['.I('post.recorder').']'.'状态改为[已处理]。';
            //$data_status_success['attachment'] = I('post.attachment','',false);
            $data_status_success['recorder'] = session('user')['account'];
            $data_status_success['timestamp'] = date('Y-m-d H:i:s',time());
            $data_status_success['uid'] = session('user')['id'];
            $addID_success = $complaintLog->add($data_status_success);
            $complaint = M('Oacustomercomplaint');
            $map['id'] = I('post.cc_id');
            $map['status'] = 0;
            $saveID_success = $complaint->save($map);
        }
        if($ID){
            $this->ajaxReturn(array('flag'=>1,'msg'=>'记录添加成功'));
        }else{
            $this->ajaxReturn(array('flag'=>0,'msg'=>'记录添加失败'));
        }
    }
    
    //删除图像
    public function unlinkPic(){
        if(!IS_AJAX) return false;
        $source = iconv('UTF-8','GB2312',I('post.source'));
        unlink($source);
        if(!file_exists($source)){
            $this->ajaxReturn('true');exit;
        }else{
            $this->ajaxReturn('false');exit;
        }
    }
    
    //下载文件
    public function downloadFile(){
        //检测文件路径是否包含中文，如果存在中文则转换编码
        if(preg_match("/[\x7f-\xff]/", '.'.I('post.filepath'))){
            $filePath = iconv('UTF-8','GB2312', '.'.I('post.filepath'));
        }else{
            $filePath = '.'.I('post.filepath');
        }
        $fileName = getBasename(I('post.filepath'));
        //检测文件名是否包含中文，如果包含中文则转换编码
        if (preg_match("/[\x7f-\xff]/", $fileName)) {
            $fileName = urlencode($fileName);
        }
        //实例化thinkphp下载类
        ob_end_clean();
        $http = new \Org\Net\Http;
        $http->download($filePath,$fileName);
    }

    //统计
    public function chart(){

        # 获取到当前月份
        /*$now_month = (int)date('m');

        $model = new Model();

        $StackingArea = [];

        for( $i = 1; $i <= $now_month; $i++ ){

            # 准备指定月份时间
            $time_str = '2017-0'.$i.'-01';

            # 获取月份第一天
            $BeginDate = date( 'Y-m-01', strtotime( $time_str ) );

            # 获取月份最后一天
            $EndDate = date( 'Y-m-d', strtotime("$BeginDate +1 month -1 day") );

            $StackingArea[] = $model->table( C('DB_PREFIX').'oacustomercomplaint' )->where( 'UNIX_TIMESTAMP(cc_time) > '.strtotime( $BeginDate ).' AND UNIX_TIMESTAMP(cc_time) < '.strtotime( $EndDate ) )->order('cc_time ASC')->select();

        }

        $StackingAreaMonth = [];

        foreach( $StackingArea as $key=>&$value ){
            array_push( $StackingAreaMonth, ($key+1).'月' );
        }*/

        //echo json_encode($StackingAreaMonth);

        //print_r($StackingArea);

        //$timestamp = strtotime('2017-01-01 00:00:00');

        //echo date('Y-m-d H:i:s',$timestamp);

        //$this->assign('StackingArea',$StackingArea);

        $this->display();
    }
    
}