<?php
/**
 * Created by PhpStorm.
 * User: Fulwin
 * Date: 2017/1/10
 * Time: 11:02
 */
namespace Home\Controller;
use Think\Model;

class ApprovalController extends AuthController  {
    private $director = array();

    //初始化页面
    public function index(){

        $model = new model();

        # 所有数据
        $tmpArr = $model->table(C('DB_PREFIX').'approval')->select();

        foreach ($tmpArr as $key=>&$value){
            $value['step'] = $model->table(C('DB_PREFIX').'approval_step')->where('a_id ='.$value['id'])->select();
        }

        //print_r($tmpArr);


        # 默认加载的集数据
        $tmpSeData = $model->table(C('DB_PREFIX').'approval')->field('name aName,id')->order('id ASC')->limit(1)->select();
        foreach ($tmpSeData as $key=>&$value){
            $value['step'] = $model->table(C('DB_PREFIX').'approval_step')->where('a_id ='.$value['id'])->select();
            foreach ($value['step'] as $k=>&$v){
                if($v['type'] == 'department'){
                    $v['person'] = $model->table(C('DB_PREFIX').'user a,'.C('DB_PREFIX').'department b')
                                         ->where('b.id ='.$v['position'].' AND a.department = b.id AND a.state = 1')
                                         ->field('a.nickname,a.email,a.id')
                                         ->select();
                }else{
                    $v['person'] = $model->table(C('DB_PREFIX').'user a,'.C('DB_PREFIX').'position b')
                        ->where('b.id ='.$v['position'].' AND a.position = b.id AND a.state = 1')
                        ->field('a.nickname,a.email,a.id')
                        ->select();
                }
            }
        }

        if(IS_POST){
            print_r(I('post.'));
        }

        //print_r($tmpArr);
        $this->assign('approvalData',$tmpArr);
        $this->assign('tmpSeData',$tmpSeData);
        $this->display();
    }

    # select 首页监听数据
    public function category(){

        $model = new model();

        $tmpSe = I('post.id');
        # 监听选择集数据
        if(IS_POST){
            $rel = $model->table(C('DB_PREFIX').'approval')->field('name aName,id')->where('id ='.$tmpSe)->select();
            foreach ($rel as $key=>&$value){
                $value['step'] = $model->table(C('DB_PREFIX').'approval_step')->where('a_id ='.$value['id'])->select();
            }

        }

        $tmpRel = json_encode($rel);
        $this->ajaxReturn($tmpRel);

    }

    # 第一步开始操作人
    public function person(){

        $model = new model();
        $tmpSe = I('post.id');

        # 选择后的部门或岗位人员
        $tmpPerson = $model->table(C('DB_PREFIX').'approval a,'.C('DB_PREFIX').'approval_step b')->where('a.id = b.a_id AND a.id='.$tmpSe)->order('b.id ASC')->limit(1)->select();

        if($tmpPerson[0]['type'] == 'department') {
            $person = $model->table(C('DB_PREFIX') . 'user a,' . C('DB_PREFIX') . 'department b')->where(' a.department= b.id AND b.id='.$tmpPerson[0]['position'].' AND a.state = 1')->field('a.nickname,a.id,a.email')->select();
        }else{
            $person = $model->table(C('DB_PREFIX') . 'user a,' . C('DB_PREFIX') . 'position b')->where(' a.position= b.id AND b.id='.$tmpPerson[0]['position'].' AND a.state = 1')->field('a.nickname,a.id,a.email')->select();
        }

        $this->ajaxReturn($person);
    }

    # select add监听数据
    public function aprType(){

        $model = new model();

        $type = I('post.type');

        if(IS_POST){
            if($type === '部门'){

                $position = $model->table(C('DB_PREFIX').'department')->field('id,name')->select();

            }else{

                $position = $model->table(C('DB_PREFIX').'position')->field('id,name')->select();

            }

            $this->ajaxReturn($position);
        }

    }

    //添加集
    public function add(){

        if (IS_POST){

            $postBasic = I('post.basic');
            $postStep = I('post.step');

            $model = new model();
            $model->startTrans();

            # 集表数据
            $approval['name'] = $postBasic['set_name'];
            $approval['create_person'] = session('user')['id'];
            $approval['archive_dir'] = $postBasic['archive_dir'];

            $approval_id = $model->table(C('DB_PREFIX').'approval')->add($approval);

            if( $approval_id ){
                # 收集步骤表数据
                foreach ($postStep as $key=>&$value){
                    $tmpStep['name'] = $value['step_name'];
                    $tmpStep['step'] = $key+1;
                    $tmpStep['type'] = $value['type'];
                    $tmpStep['transfer'] = $value['transfer'];
                    $tmpStep['refuse'] = $value['refuse'];
                    $tmpStep['a_id'] = $approval_id;

                    if(!empty($value['department'])){
                        $tmpStep['position'] =$value['department'];
                    }else{
                        $tmpStep['position'] =$value['position'];
                    }

                    $a_step[] = array_merge($tmpStep);
                }

                $step_id = $model->table(C('DB_PREFIX').'approval_step')->addAll($a_step);

                if($step_id){
                    $model->commit();
                    $this->ajaxReturn( ['flag'=>1,'msg'=>'添加成功!'] );

                }else{
                    $model->rollback();
                    $this->ajaxReturn( ['flag'=>0,'msg'=>'添加失败!'] );
                }
            }
        }


        $this->display();
    }

    public function detail(){
        $this->display();
    }

    //将审批类型转换为中文
    private function conversionZH_CN( &$arr ){
        #print_r($arr);
        if( is_array($arr) && !empty($arr) ){
            foreach($arr as $key=>&$value){
                #echo $value['type'];
                switch($value['type']){
                    case 'expense':
                        $value['type_name'] = '报销';
                        $value['background'] = '#428bca';
                        $value['icon'] = 'icon-credit-card';
                        break;
                    default:
                        $value['type_name'] = 'UNKNOW';
                        $value['background'] = '#fff';
                        $value['icon'] = 'icon-bookmark-empty';
                }
            }
            return $arr;
        }
    }

    //添加状态样式
    private function addStateStyle( $arr ){
        if( is_array($arr) && !empty($arr) ){
            foreach($arr as $key=>&$value){
                if( $value['state']=='' ){
                    $value['icon_class'] = 'icon-spinner icon-spin';
                    $value['flag'] = 'flag-wait';
                }elseif( $value['state']=='YYY' ){
                    $value['icon_class'] = 'icon-ok-sign';
                    $value['flag'] = 'flag-active';
                }else{
                    if( strpos($value['state'],'N') === false ){
                        $value['icon_class'] = 'icon-spinner icon-spin';
                        $value['flag'] = 'flag-wait';
                    }else{
                        $value['icon_class'] = 'icon-remove-sign';
                        $value['flag'] = 'flag-error';
                    }
                }
            }
            return $arr;
        }
    }

    //ajax分页(我发起的)
    public function initiate_page(){
        if( !IS_POST || !I('post.pagenumber') || empty(I('post.pagenumber')) ) return;
        $ApprovalExpense = M('ApprovalExpense');
        $total = $ApprovalExpense->where('expense_id='.session('user')['id'])->count();
        $page = ceil($total / C('LIMIT_SIZE'));
        $ApprovalExpenseExtend = M('ApprovalExpenseExtend');
        $ApprovalCenter = M('ApprovalCenter');
        //获取当前登录用发起的所有审批
        $centerdata = $ApprovalCenter->where('initiate='.session('user')['id'])->select();
        //根据审批的类型连接相应的表
        foreach($centerdata as $key=>&$value){
            switch($value['type']){
                case 'expense':
                    $result = M()->field('a.id,a.create_time,a.expense_name,a.dda_state,a.dda_name,a.fda_state,a.fda_name,a.vpa_state,a.vpa_name,a.status,a.total_money,a.attachment,b.type,c.face')
                        ->table('atop_approval_expense a,atop_approval_center b,atop_user c')
                        ->where('b.initiate='.session('user')['id'].' AND b.assoc=a.id AND b.initiate=c.id  AND a.state = 1')
                        ->order('a.create_time DESC')
                        ->limit((I('post.pagenumber')-1)*C('LIMIT_SIZE'),C('LIMIT_SIZE'))
                        ->select();
                    break;
                case 'leave':

                    break;
            }
        }
        $result['page'] = $page;
        //更具审批类型不同获取对应子表数据
        foreach($result as $key=>&$value){
            switch($value['type']){
                case 'expense':
                    //$value['child'] = $ApprovalExpenseExtend->where('belong='.$value['id'])->select();
                    //$value['attachment'] = explode(',',$value['attachment']);
                    if( $value['dda_state']==1 && $value['fda_state']==1 && $value['vpa_state']==1 ){
                        $value['icon_class'] = 'icon-ok-sign';
                        $value['step_state'] = '审批已完成';
                        if($value['status']==1){
                            $value['label_text'] = '通过';
                            $value['label_class'] = 'label label-success';
                        }elseif($value['status']==2){
                            $value['label_text'] = '拒绝';
                            $value['label_class'] = 'label label-danger';
                        }
                    }else{
                        if( $value['dda_state']==0 && $value['fda_state']==0 && $value['vpa_state']==0 ){
                            $value['icon_class'] = 'icon-spinner icon-spin';
                            $value['step_state'] = '等待'.$value['dda_name'].'审批中';
                            $value['label_text'] = '';
                            $value['label_class'] = '';
                        }elseif( $value['dda_state']==2 && $value['fda_state']==0 && $value['vpa_state']==0 ){
                            $value['icon_class'] = 'icon-remove-sign';
                            $value['step_state'] = $value['dda_name'].'审批未通过';
                            $value['label_text'] = '拒绝';
                            $value['label_class'] = 'label label-danger';
                        }elseif( $value['dda_state']==1 && $value['fda_state']==0 && $value['vpa_state']==0 ){
                            $value['icon_class'] = 'icon-spinner icon-spin';
                            $value['step_state'] = '等待'.$value['fda_name'].'审批中';
                            $value['label_text'] = '';
                            $value['label_class'] = '';
                        }elseif( $value['dda_state']==1 && $value['fda_state']==2 && $value['vpa_state']==0 ){
                            $value['icon_class'] = 'icon-remove-sign';
                            $value['step_state'] = $value['fda_name'].'审批未通过';
                            $value['label_text'] = '拒绝';
                            $value['label_class'] = 'label label-danger';
                        }elseif( $value['dda_state']==1 && $value['fda_state']==1 && $value['vpa_state']==0 ){
                            $value['icon_class'] = 'icon-spinner icon-spin';
                            $value['step_state'] = '等待'.$value['vpa_name'].'审批中';
                            $value['label_text'] = '';
                            $value['label_class'] = '';
                        }elseif( $value['dda_state']==1 && $value['fda_state']==1 && $value['vpa_state']==2 ){
                            $value['icon_class'] = 'icon-remove-sign';
                            $value['step_state'] = $value['vpa_name'].'审批未通过';
                            $value['label_text'] = '拒绝';
                            $value['label_class'] = 'label label-danger';
                        }
                    }
                    $value['create_time'] = date('Y-m-d H:i:s',$value['create_time']);
                    $value['type_name'] = '报销';
                    break;
                case 'leave':
                    $value['type_name'] = '请假';
                    break;
            }
        }
        $this->ajaxReturn($result);exit;
    }

    //ajax分页(我审批的)
    public function approval_page(){
        if( !IS_POST || !I('post.pagenumber') || empty(I('post.pagenumber')) ) return;
        $ApprovalCenter = M('ApprovalCenter');
        $map['has_many'] = array('like','%'.session('user')['id'].'%');
        $likedata = $ApprovalCenter->where($map)->order('time DESC')->limit((I('post.pagenumber')-1)*C('LIMIT_SIZE'),C('LIMIT_SIZE'))->select();
        //$likedataCount = $ApprovalCenter->where($map)->count();
        //$page_approval_total = $ApprovalCenter->where($map)->order('time DESC')->count();
        //$this->assign('page_approval_total',ceil($page_approval_total / C('LIMIT_SIZE')));
        if(!empty($likedata)){
            foreach($likedata as $key=>&$value){
                $initiatedata = M('User')->field('nickname,face')->find($value['initiate']);
                switch($value['type']){
                    case 'expense':
                        $value['type_name'] = '报销';
                        $value['time'] = date('Y-m-d H:i:s',$value['time']);
                        $value['face'] = $initiatedata['face'];
                        $value['nickname'] = $initiatedata['nickname'];
                        break;
                }
            }
            //$this->assign('likedataCount',$likedataCount);
            $this->ajaxReturn($likedata);
            //$this->assign('like',$likedata);
        }
    }

    //报销
    public function expense(){
        $user = M('User');
        $user_id = session('user')['id'];
        //获取当前登录用户的部门id和汇报人id
        $user_data = $user->field('id,department,position,report,level,face,nickname')->where('id='.$user_id)->select();
        //$this->assign('userdata',$user_data[0]);
        //获取到财务部副总及总监
        $financial['director'] = $user->field('id,nickname')->where('department=7 AND position=20')->select()[0];
        $financial['vicepresident'] = $user->field('id,nickname')->where('department=7 AND position=21')->select()[0];
        //print_r($financial);
        $this->assign('financial',$financial);
        //获取当前登录用户的部门总监数据/避免admin账号部门为空的情况
        if($user_data[0]['department']!=''){
            $director = $user->where('department='.$user_data[0]['department'].' AND level=5')->select();
        }
        //如果总监人数超过超过1人则递归该用户汇报关系
        if( count($director) > 1 ){
            if($user_data[0]['report']!=''){
                //兼容报销发起人自身就是总监
                if( $user_data[0]['level']==5 ){
                    $this->director = $user_data;
                }else{
                    $this->getDepartmentDirector($user,$user_data);
                }
                $this->assign('director',$this->director);
            }
        }else{
            $this->assign('director',$director);
        }
        $this->display();
    }

    //根据参数递归获取指定用户的部门总监
    private function getDepartmentDirector($obj,&$user_data){
        if($user_data[0]['level']==5){
            $this->director = $user_data;
        }else{
            $arr = $obj->field('id,department,report,level,face,nickname')->where('id='.$user_data[0]['report'])->select();
            $this->getDepartmentDirector($obj,$arr);
        }
    }

    //上传附件
    public function uploadAttachment(){
        $subName = I('post.SUB_NAME');
        // 如果需要按id为子目录则必须填写第二个参数，否则直接保存在当前目录
        if( $subName != '' ){
            $result = upload( '/Approval/', $subName );
            $this->ajaxReturn( $result );
        }
   /*     if(!IS_POST) return;
        $info = FileUpload('/Approval/',0,1);
        if(!info){ return false; }
        $file['flag'] = 1;
        $file['name'] = $info['Filedata']['name'];
        $file['savepath'] = '/Uploads'.$info['Filedata']['savepath'].$info['Filedata']['name'];
        $file['ext'] = $info['Filedata']['ext'];
        $this->ajaxReturn($file);
        exit;*/
    }

    //删除附件
    public function removeAttachment(){
        if( !IS_POST || !I('post.filepath') ) return;
        $filepath = checkZH_CN(I('post.filepath'));
        $filepath = getcwd().$filepath;
        if(file_exists($filepath)){
            unlink($filepath);
        }
        if(!file_exists($filepath)){
            echo 1;
        }
        exit;
    }

    //添加报销主表数据
    public function addApproval(){
        if(!IS_POST) return;
        $postdata = I('post.');
        if( $postdata['type']=='expense' ){
            $approvalData['title'] = $postdata['title'];
            $approvalData['person_id'] = session('user')['id'];
            $approvalData['person_name'] = session('user')['nickname'];
            $approvalData['type'] = $postdata['type'];
            $approvalData['approvals'] = $postdata['dda_id'].','.$postdata['fda_id'].','.$postdata['vpa_id'];
            $approvalData['approvals_name'] = $postdata['dda_name'].','.$postdata['fda_name'].','.$postdata['vpa_name'];
            $approvalData['attachment'] = $postdata['attachment'];
            $approvalData['create_time'] = time();
            $Approval = M('Approval');
            $id = $Approval->add($approvalData);
        }
        if( $id ){
            $state['state_text'] = '等待'.$postdata['dda_name'].'审批';
            $state['subjection'] = $id;
            M('ApprovalState')->add($state);
            //审批添加成功后推送邮件
            if( $postdata['type']=='expense' ){
                $userdata = M('User')->find($postdata['dda_id']);
                $subject = session('user')['nickname'].'的报销需要您审批';
                $body = '<p>Dear '.$userdata['nickname'].',</p><br><p>'.session('user')['nickname'].'的报销需要您审批，详情请点击链接：<a href="http://'.$_SERVER['HTTP_HOST'].'/Approval/details/id/'.$id.'">http://'.$_SERVER['HTTP_HOST'].'/Approval/details/id/'.$id.'</a></p>';
                send_Email($userdata['email'],$userdata['nickname'],$subject,$body);
                $noticedata['who'] = $postdata['dda_id'];
                $noticedata['message'] = session('user')['nickname'].'的报销需要您审批';
                $noticedata['link'] = '/Approval/details/id/'.$id;
                $noticedata['pushtime'] = time();
                $noticedata['approval'] = 'expense';
                $noticedata['approval_id'] = $id;
                if( M('Notice')->add($noticedata) ) echo $id; exit;
            }
        }
    }

    //添加报销从表数据
    public function addApprovalAffiliated(){
        if(!IS_POST) return;
        $postdata = I('post.');
        $ApprovalExpenseExtend = M('ApprovalAffiliated');
        $ApprovalExpenseExtend->add($postdata);
    }

    //审批详情
    public function details(){
        if( !I('get.id') || I('get.id')=='' ) return;
        $approval = D('Approval');
        $notice = M('Notice');
        $approvalResult = $approval->relation(true)->find(I('get.id'));
        //将审批人id和姓名拆分为数组便于查询
        $approvalResult['approvals'] = explode(',',$approvalResult['approvals']);
        $approvalResult['approvals_name'] = explode(',',$approvalResult['approvals_name']);
        //如果审批类型是报销则计算报销总额
        switch( $approvalResult['type'] ){
            case 'expense':
                foreach($approvalResult['affiliated'] as $key=>&$value){
                    $approvalResult['total_money'] += $value['money'];
                }
                $map['approval'] = 'expense';
                break;
            default:
                echo 'error';
        }
        //审批创建时间和审批日志添加时间格式化
        $approvalResult['create_time'] = conversiontime($approvalResult['create_time']);
        if( !empty($approvalResult['log']) ){
            foreach($approvalResult['log'] as $key=>&$value){
                $value['log_time'] = conversiontime($value['log_time']);
            }
        }
        //如果附件不为空则拆分为数组
        if( !empty($approvalResult['attachment']) ){
            $approvalResult['attachment'] = explode(',',$approvalResult['attachment']);
        }
        //合并两个数组 ( 审批人id=>审批人姓名 )
        $approvalResult['merge'] = array_combine($approvalResult['approvals'],$approvalResult['approvals_name']);
        //修改通知查看状态及查看时间
        $map['who'] = session('user')['id'];
        $map['approval_id'] = I('get.id');
        $map['status'] = 0;
        $noticelist = $notice->field('id')->where($map)->select();
        if( $noticelist ){
            foreach($noticelist as $key=>&$value){
                $notice->save(array('id'=>$value['id'],'status'=>1,'viewtime'=>time()));
            }
        }
        # print_r($approvalResult);
        $this->assign('approval',$approvalResult);
        $this->display();
    }

    //审批备注添加
    public function addApprovalLog(){
        if(!IS_POST) return;
        //收集用户提交数据
        $state = I('post.state');
        $data['belongs_to'] = I('post.belongs_to');
        $data['comment'] = I('post.comment');
        $data['log_time'] = time();
        $data['audit_id'] = session('user')['id'];
        $data['audit_name'] = session('user')['nickname'];
        //获取审批数据
        $approval = D('Approval');
        $approvalResult = $approval->relation(true)->find(I('post.belongs_to'));
        $approvalResult['approvals'] = explode(',',$approvalResult['approvals']);
        $approvalResult['approvals_name'] = explode(',',$approvalResult['approvals_name']);
        # print_r($approvalResult);
        //添加备注日志
        $approvalLog = M('ApprovalLog');
        $logID = $approvalLog->add($data);
        if( $logID ){
            $approvalState = M('ApprovalState');
            $stateStr = $approvalState->field('state')->where('subjection='.I('post.belongs_to'))->select()[0]['state'];
            $editData = $this->modifyState($stateStr,$state,$approvalResult);
            $approvalState->where('subjection='.I('post.belongs_to'))->save( array('state'=>$editData['state'],'state_text'=>$editData['statetext']) );
            $this->readySendEmailData($approvalResult,$state,I('post.comment'));
            echo $logID; exit;
        }
    }

    //修改状态
    private function modifyState($state,$status,$result){
        $arr = array();
        if( $status==1 ){
            $state .= 'Y';
            //根据状态长度修改状态文本内容
            $statelen = strlen($state);
            if( $statelen < count($result['approvals']) ){
                $stateText = "等待{$result['approvals_name'][$statelen]}审批";
            }else{
                $stateText = '审批已完成';
            }
        }else{
            $state .= 'N';
            //根据状态长度修改状态文本内容
            $statelen = strlen($state)-1;
            if( $statelen <= count($result['approvals']) ){
                $stateText = "{$result['approvals_name'][$statelen]}审批未通过";
            }else{
                $stateText = '审批已完成';
            }
        }
        $arr['state'] = $state;
        $arr['statetext'] = $stateText;
        return $arr;
    }

    //准备邮件发送数据
    private function readySendEmailData(&$result,$state,$comment){
        if( is_array($result) && !empty($result) ){
            $sessionUserName = session('user')['nickname'];
            $pushPersonName = $result['person_name'];
            switch($result['type']){
                case 'expense':
                    $result['type_name'] = '报销';
                    break;
                default:
                    exit;
            }
            //邮件推送链接
            $link = "http://{$_SERVER['HTTP_HOST']}/Approval/details/id/{$result['id']}";
            //获取当前审核状态
            $stateText = '';
            $state == 1 ? $stateText = '通过' : $stateText = '拒绝';
            //拼装标题
            $subject = "{$sessionUserName}{$stateText}了您的{$result['type_name']}。";
            //获取收件人的邮件
            $address = M('User')->field('email')->find($result['person_id'])['email'];
            $body = <<<BODY
<p>Dear {$pushPersonName},</p>
<br>
<p>{$subject}</p>
<p>备注：{$comment}</p>
<p>详情请点击链接：<a href='{$link}'>{$link}</a></p>
BODY;
            //给发起人推送站内信
            $noticedata['who'] = $result['person_id'];
            $noticedata['message'] = $subject;
            $noticedata['link'] = "/Approval/details/id/{$result['id']}";
            $noticedata['pushtime'] = time();
            $noticedata['approval'] = 'expense';
            $noticedata['approval_id'] = $result['id'];
            M('Notice')->add($noticedata);

            # print_r($address.' + '.$pushPersonName.' + '.$subject.' + '.$body);
            if( session('user')['id'] != $result['person_id'] ){
                send_Email($address,$pushPersonName,$subject,$body);
            }
            //获取状态长度（根据状态长度判断下一步推送给谁）
            $stateLen = strlen($result['state'])+1;
            //最后一个审批人不需要再继续往下面推送
            if( $stateLen <= count($result['approvals']) ){
                $recipient_id = $result['approvals'][$stateLen];
                //如果下一步推送人的id和当前登录用户的id相等则不推送邮件,但是推送站内信
                if( $recipient_id != session('user')['id'] ){
                    $recipient_name = $result['approvals_name'][$stateLen];
                    //获取到下一步推送人的邮箱
                    $address2 = M('User')->field('email')->find($recipient_id)['email'];
                    //拼装标题
                    $subject2 = "{$pushPersonName}的{$result['type_name']}需要您审批。";
                    $body2 = <<<BODY2
<p>Dear {$recipient_name},</p>
<br>
<p>{$subject2}</p>
<p>详情请点击链接：<a href='{$link}'>{$link}</a></p>
BODY2;
                    send_Email($address2,$recipient_name,$subject2,$body2);
                }
                //给下一步审批人推送站内信
                $noticedata2['who'] = $recipient_id;
                $noticedata2['message'] = $subject2;
                $noticedata2['link'] = "/Approval/details/id/{$result['id']}";
                $noticedata2['pushtime'] = time();
                $noticedata2['approval'] = 'expense';
                $noticedata2['approval_id'] = $result['id'];
                M('Notice')->add($noticedata2);

                # print_r($address2.' + '.$recipient_name.' + '.$subject2.' + '.$body2);
            }
        }
    }


}