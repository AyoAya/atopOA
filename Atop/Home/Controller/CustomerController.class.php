<?php
namespace Home\Controller;

use Think\Model;
use Think\Page;
class CustomerController extends AuthController{
    private $str = '';
    private $in = array();
    private $filter = array();
    //客诉处理
    public function index(){
        $customer = M('Oacustomercomplaint');
        /*//已经处理的条数
        $customerTotal['y'] = $customer->where('status=0')->count();
        //无法处理的条数
        $customerTotal['n'] = $customer->where('status=-1')->count();
        //处理中的条数
        $customerTotal['d'] = $customer->where('status=1')->count();
        $this->assign('customerTotal',$customerTotal);

        $this->assign('customerAllTotal',$customer->count());*/

        if( I('get.status')!='' && I('get.status')!=2 ){
            $condition['status'] = I('get.status');
        }else{
            $condition['status'] = array('in','-1,0,1');
        }
        //时间段区间查询
        if( !empty(I('get.startdate')) && !empty(I('get.enddate')) ){
            $condition['cc_time'] = array(array('elt',I('get.enddate')),array('egt',I('get.startdate')),'AND');
        }
        if( !empty(I('get.order')) ){
            $condition['sale_order'] = I('get.order');
        }
        if( !empty(I('get.person')) ){
            $condition['salesperson'] = I('get.person');
        }
        if( !empty(I('get.customer')) ){
            $condition['customer'] = I('get.customer');
        }
        if( !empty(I('get.vendor')) ){
            $condition['vendor'] = I('get.vendor');
        }
        if( !empty(I('get.searchtext')) ){
            $condition['customer|pn|vendor|error_message'] = array('like','%'.I('get.searchtext').'%');
        }
        if( !empty(I('get.salesperson')) ){
            $condition['salesperson'] = I('get.salesperson');
        }
        $condition['version'] = 'old';
        $user = M('User');
        $levelReport = $user->field('level,report,department')->find(session('user')['id']);
        $str = '';
        if($levelReport['department']==4){
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
        }
        //die;
        step:
        $count = $customer->where($condition)->count();
        //echo $customer->getLastSql();
        //分页
        $page = new Page($count,C('LIMIT_SIZE'));
        $page->setConfig('prev','<span aria-hidden="true">上一页</span>');
        $page->setConfig('next','<span aria-hidden="true">下一页</span>');
        $page->setConfig('first','<span aria-hidden="true">首页</span>');
        $page->setConfig('last','<span aria-hidden="true">尾页</span>');
        if(C('PAGE_STATUS_INFO')){
            $page->setConfig ( 'theme', '<li><a href="javascript:void(0);">当前%NOW_PAGE%/%TOTAL_PAGE%</a></li>  %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%' );
        }
        $result = $customer->where($condition)->order('cc_time DESC')->limit($page->firstRow.','.$page->listRows)->select();

        foreach($condition as $key=>&$value){
            if($key='cc_time'){
                foreach($value as $ke=>&$val){
                    if(is_array($val)){
                        if($val[0]=='egt'){
                            $condition['startdate'] = $val[1];
                        }else{
                            $condition['enddate'] = $val[1];
                        }
                    }
                }
            }
        }
        //将搜索文本注入模板
        if(!empty(I('get.searchtext'))){
            $condition['searchtext'] = I('get.searchtext');
        }
        //整合标题，为考虑页面布局，文本宽度大于一定条件后将隐藏部分并添加省略号
        $user = M('User');
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
            //如果文本长度大于100，则显示省略号
            /*if(mb_strwidth($title,'utf8') > 90){
                $value['title'] = mb_strimwidth($title, 0, 90, '...', 'utf8');
            }else{
                $value['title'] = $title;
            }*/
            //过滤html标签
            $value['error_message'] = str_replace('<br />','',$value['error_message']);
            $value['reason'] = str_replace('<br />','',$value['reason']);
        }
        $this->assign('filter',$condition);
        $pageShow = $page->show();
        $this->assign('pageShow',$pageShow);
        emptyData:
        $this->assign('customer',$result);
        //当数据为空显示图片
        $this->assign('empty','<div class="empty-box"><span class="empty-pic"></span></div>');
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
    
    //新增客诉
    public function addCustomer(){
        if(IS_POST){

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
            $complaint = M('Oacustomercomplaint');
            $complaint_addID = $complaint->add($data);

            //如果存在多个产品则分别给每个产品经理推送邮件,如果存在相同的产品经理取唯一
            $managers = I('post.managers');
            if(strpos($managers,',')){
                $manager = array_unique(explode(',',$managers));
            }else{
                $manager = I('post.managers');
            }
            //如果存在多个产品经理则分别获取对应邮箱
            if(is_array($manager)){
                $emailArr = array();
                foreach($manager as $key=>$value){
                    $managerData = M('User')->field('nickname,email')->find($value);
                    $address = $managerData['email'];
                    $nickname = $managerData['nickname'];
                    $subject = I('post.salesperson_name').'的客户有一条新客诉，请关注！';
                    $body = '<p>Dear '.$nickname.',</p><br><p>'.I('post.salesperson_name').'的客户有一条新客诉，请关注！</p><br><p>产品型号：'.I('post.pn').'</p><p>设备厂商：'.I('post.vendor').'</p><p>设备型号：'.I('post.model').'</p><p>错误现象：'.$data['error_message'].'</p><br>详情请点击链接：<a href="http://'.$_SERVER['HTTP_HOST'].'/customerDetails/'.$complaint_addID.'" target="_blank">http://'.$_SERVER['HTTP_HOST'].'/customerDetails/'.$complaint_addID.'</a>';
                    send_Email($address,$nickname,$subject,$body);
                }
            }else{
                $managerData = M('User')->field('nickname,email')->find($manager);
                $address = $managerData['email'];
                $nickname = $managerData['nickname'];
                $subject = I('post.salesperson_name').'的客户有一条新客诉，请关注！';
                $body = '<p>Dear '.$nickname.',</p><br><p>'.I('post.salesperson_name').'的客户有一条新客诉，请关注！</p><br><p>产品型号：'.I('post.pn').'</p><p>设备厂商：'.I('post.vendor').'</p><p>设备型号：'.I('post.model').'</p><p>错误现象：'.$data['error_message'].'</p><br>详情请点击链接：<a href="http://'.$_SERVER['HTTP_HOST'].'/customerDetails/'.$complaint_addID.'" target="_blank">http://'.$_SERVER['HTTP_HOST'].'/customerDetails/'.$complaint_addID.'</a>';
                send_Email($address,$nickname,$subject,$body);
            }

            $log['cc_id'] = $complaint_addID;
            $log['log_date'] = I('post.cc_time');
            $log['log_content'] = '新客诉。';
            $log['recorder'] = session('user')['account'];
            $log['timestamp'] = date('Y-m-d H:i:s');
            $log['uid'] = session('user')['id'];
            $complaintlog = M('Oacustomercomplaintlog');
            $complaintlog_addID = $complaintlog->add($log);
            //开启事务支持(当添加新客诉时在log表中默认添加一条新数据，记录当前操作者)
            $complaint->startTrans();
            if($complaint_addID && $complaintlog_addID){
                $complaint->commit();
                $this->ajaxReturn(array('flag'=>1,'msg'=>'数据添加成功','id'=>$complaint_addID));
            }else{
                $complaint->rollback();
                $this->ajaxReturn(array('flag'=>0,'msg'=>'数据添加失败'));
            }
        }else{
            //将用户列表注入模板，（销售人员选择）
            $user = M('User');
            $userlist = $user->field('id,account,nickname')->where('department=4')->select();
            $Productrelationships = M('Productrelationships');
            $filterArr = array();
            $filterArr['filterType'] = $Productrelationships->distinct(true)->field('type')->order('type')->select();
            $filterArr['filterWavelength'] = $Productrelationships->distinct(true)->field('wavelength')->order('wavelength')->select();
            $filterArr['filterConnector'] = $Productrelationships->distinct(true)->field('connector')->select();
            $filterArr['filterCasetemp'] = $Productrelationships->distinct(true)->field('casetemp')->select();
            $filterArr['filterReach'] = $Productrelationships->distinct(true)->field('reach')->select();
            $filterData = $Productrelationships->field('p.pn,p.id pid,u.nickname,p.wavelength,p.reach,p.connector,p.casetemp,p.type,u.id uid,p.manager')->table('__USER__ u,__PRODUCTRELATIONSHIPS__ p')->where('p.manager=u.id')->select();
            $result = $user->select();
            $this->assign('filter',$filterArr);
            $this->assign('filterdata',$filterData);
            $this->assign('userlist',$userlist);
            $this->display();
        }
    }
    
    //客诉详情
    public function details(){
        if(!I('get.id')) return false;
        $user = M('User');
        //获取客诉处理记录并注入模板
        $complaint = M('Oacustomercomplaint');
        $complaintlog = M('Oacustomercomplaintlog');
        //获取当前id指定的客诉
        $resultData = $complaint->find(I('get.id'));
        //print_r($resultData);

        $productModel = $resultData['vendor'];

        //print_r($productModel);

        $vendor_brand = M('VendorBrand');
        $is_exists = $vendor_brand->where( ['brand'=>$productModel] )->select();

        if( empty($is_exists) ){   //查看该客诉的设备品牌是否存在于表中，如果存在则不用选择，不存在则注入模板
            $this->assign('vendorBrand',$vendor_brand->order('brand ASC')->select());
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
                if($nickname){
                    $resultData['nickname'] = $nickname[0]['nickname'];
                }else{
                    $resultData['nickname'] = $value;
                }
            }
        }
        $map['cc_id'] = $resultData['id'];
        //获取指定id的客诉处理记录
        $resultData['log'] = $complaintlog->where($map)->order('log_date DESC,id DESC')->select();
        foreach($resultData['log'] as $key=>&$value){
            //为保留原始数据attachment列存入数据为文件名和json数据，为保证数据不冲突判断列数据是否为json格式来区分新版还是老版
            //判断附件是否为空并且是否为json格式，如果为json格式则用新版读取方式否则用老版数据
            if(!empty($value['attachment']) && $value['attachment']!='NULL'){
                $json = json_decode($value['attachment'],true);
                if(!is_null($json)){
                    # 针对不同的附件存入方式进行不同的操作
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
                        /*if(file_exists(checkZH_CN($oldFilePath.$val))){
                            //获取文件扩展名
                            $old_filetype = getExtension($val);
                            //如果数据为image则为图像显示，否则一律为文件处理(下载)
                            if($old_filetype=='jpeg' || $old_filetype=='jpg' || $old_filetype=='gif' || $old_filetype=='png' || $old_filetype=='bmp'){
                                $val = array('filename'=>getBasename($val),'filetype'=>'image','filepath'=>$oldFilePath.$val);
                            }else{
                                $val = array('filename'=>getBasename($val),'filetype'=>'other','filepath'=>$oldFilePath.$val);
                            }
                        }else{
                            $val = '';
                        }*/
                        //获取文件扩展名
                        $old_filetype = getExtension($val);
                        //如果数据为image则为图像显示，否则一律为文件处理(下载)
                        if($old_filetype=='jpeg' || $old_filetype=='jpg' || $old_filetype=='gif' || $old_filetype=='png' || $old_filetype=='bmp'){
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
                    /*if(file_exists(checkZH_CN($old_FilePath.$val))){
                        $val = array('filepath' => $old_FilePath . $val, 'filename' => $val);
                    }else{
                        $val = '';
                    }*/
                    $val = array('filepath' => $old_FilePath . $val, 'filename' => $val);
                }
            }
            //如果该用户头像文件不存在则采用默认头像
            $face = $user->field('face')->find($value['uid']);
            if($face){
                $value['face'] = $face['face'];
            }else{
                $value['face'] = '/Public/home/img/face/default_face.png';
            }
            //将数据进行反编译，实现br标签换行
            $value['log_content']= htmlspecialchars_decode($value['log_content']);
        }
        # 如果当前登录的用户是该条客诉的销售，那么将FAE工程师列表注入模板
        if( session('user')['account'] == $resultData['salesperson'] ){
            $FAE_person_list = $user->field('id,nickname')->where( ['position'=>12,'state'=>1] )->select(); //筛选所有职位为FAE工程师并状态为正常的人员集
            $this->assign('FAE_person_list',$FAE_person_list);
        }
        $this->assign('details',$resultData);
        $sessionArr['session_name'] = session_name();
        $sessionArr['session_id'] = session_id();
        $this->assign('sessionArr',$sessionArr);
        //print_r($resultData);
        $this->display();
    }

    # 转为新版客诉
    public function changeNewVersion(){
        if( IS_POST ){

            $post = I('post.');

            $oacustomercomplaint_model = M('Oacustomercomplaint');
            //$oacustomercomplaintlog_model = M('Oacustomercomplaintlog');
            //$oacustomeroperation_model = M('Oacustomeroperation');

            $customer_basic_info = $oacustomercomplaint_model->field('pn,vendor,model eq_model,error_message')->find( $post['main_assoc'] );

            extract($customer_basic_info);

            $model = new Model();   //准备事务开启模型
            $model->startTrans();   //开启事务

            # 转为新版客诉1：将主表上的version字段值改为new
            if( isset($post['vendor']) ){   //如果vendor存在则说明须重写该字段
                $customercomplaint_save_id = $model->table(C('DB_PREFIX').'oacustomercomplaint')->save( ['id'=>$post['main_assoc'],'version'=>'new','vendor'=>$post['vendor'],'now_step'=>2] );
            }else{
                $customercomplaint_save_id = $model->table(C('DB_PREFIX').'oacustomercomplaint')->save( ['id'=>$post['main_assoc'],'version'=>'new','now_step'=>2] );
            }

            # 转为新版客诉2：将从表上的version字段值改为new并且内容等于新客诉的改为步骤1，不为新客诉的改为2
            $customercomplaintlog_save_id_version = $model->table(C('DB_PREFIX').'oacustomercomplaintlog')->where( 'cc_id='.$post['main_assoc'] )->save( ['version'=>'new'] );
            $customercomplaintlog_save_id_step = $model->table(C('DB_PREFIX').'oacustomercomplaintlog')->where( 'cc_id='.$post['main_assoc'].' AND log_content<>"新客诉。"' )->save( ['step'=>2] );

            # 当由旧版客诉转为新版客诉是默认插入一条数据
            $log_data['cc_id'] = $post['main_assoc'];
            $log_data['log_date'] = date('Y-m-d');
            $log_data['log_content'] = '['.session('user')['nickname'].'] 将该客诉由旧版转入。';
            $log_data['recorder'] = 'OASystem';
            $log_data['timestamp'] = date('Y-m-d H:i:s');
            $log_data['uid'] = session('user')['id'];
            $log_data['step'] = 2;
            $log_data['version'] = 'new';
            $customercomplaintlog_add_id = $model->table(C('DB_PREFIX').'oacustomercomplaintlog')->add($log_data);

            # 转为新版客诉3：将步骤信息记录到操作表
                # 1) 记录销售步骤操作人
            $operation_data['main_assoc'] = $post['main_assoc'];
            $operation_data['step_assoc'] = 1;
            $operation_data['operation_person'] = session('user')['id'];
            $operation_add_id_sale = $model->table(C('DB_PREFIX').'oacustomeroperation')->add($operation_data);
                # 1) 记录FAE步骤操作人
            $operation_data['step_assoc'] = 2;
            $operation_data['operation_person'] = $post['operation_person'];
            $operation_add_id_fae = $model->table(C('DB_PREFIX').'oacustomeroperation')->add($operation_data);

            # 当每个环节都成功之后再返回最终结果
            if( $customercomplaint_save_id !== false && $customercomplaintlog_save_id_version !== false && $customercomplaintlog_save_id_step !== false && $customercomplaintlog_add_id && $operation_add_id_sale && $operation_add_id_fae ){

                $model->commit();

                # 当由旧版客诉转为新版客诉时，给fae工程师推送邮件
                $id = $post['main_assoc'];
                $user_nickname = session('user')['nickname'];
                $user_data = M('User')->field('nickname dear,email')->find( $post['operation_person'] );
                extract($user_data);
                $http_host = $_SERVER['HTTP_HOST'];
                $step_data = M('Oacustomerstep')->field('id step_id,step_name')->find(2);
                extract($step_data);
                $subject = '客诉步骤更新 [ ID:'.$id.' ]';
                $body = <<<HTML
<style>
p {
    line-height: 25px;
}
</style>
<p>Dear $dear,</p>
<p>[$user_nickname] 将客诉步骤推送到：Step$step_id - $step_name</p><br>
<p><b>客诉基本信息：</b></p>
<p>产品型号：$pn</p>
<p>设备厂商：$vendor</p>
<p>设备型号：$eq_model</p>
<p>错误现象：$error_message</p><br>
<p style="color: #d9534f;;">* 该客诉由旧版客诉转入</p>
<p>详情请点击链接：<a target="_blank" href="http://$http_host/RMA/details/id/$id">http://$http_host/RMA/details/id/$id</a></p>
HTML;
                $result = send_Email($email,'',$subject,$body);  //$email
                if( $result != 1 ){ //如果邮件发送失败则返回错误信息
                    $this->ajaxReturn( ['falg'=>0,'msg'=>$result] );exit;
                }
                $this->ajaxReturn( ['flag'=>1,'msg'=>'操作成功','id'=>$post['main_assoc']] );
            }else{
                $model->rollback();
                $this->ajaxReturn( ['flag'=>0,'msg'=>'操作失败'] );
            }
        }
    }
    
    //文件上传(客诉处理/客诉详情/添加客诉)
    public function uploadFile(){
        //$sessionid = I('post.PHPSESSID');
        /*if($sessionid){
            session_id(I('post.PHPSESSID'));
        }else{
            exit;
        }*/
        $info = FileUpload('/CustomerComplaintAttachment/');
        if(!$info) return false;
        $oldname = $info['Filedata']['name'];
        $newname = $info['Filedata']['savename'];
        $filePath = './Uploads'.$info['Filedata']['savepath'].$info['Filedata']['savename'];
        $arr['flag'] = 1;
        $arr['OldFileName'] = $oldname;
        $arr['NewFileName'] = $newname;
        $arr['FileSavePath'] = $filePath;
        $this->ajaxReturn($arr);
        exit;
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
        $status = array();
        $customer = M('Oacustomercomplaint');
        $status['y'] = $customer->where('status=0')->count();
        $status['n'] = $customer->where('status=-1')->count();
        $status['d'] = $customer->where('status=1')->count();
        $this->assign('status',$status);
        $this->display();
    }
    
}