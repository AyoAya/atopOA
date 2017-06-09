<?php
namespace Home\Controller;
use Think\Auth;
use Think\Page;
/**
 * 管理员
 * @author Fulwin
 * 2016-10-9
 */
class ManageController extends AuthController {
    
    //用户管理
    public function index(){
        $person = D('User');
        $count = $person->where('id<>1 AND state=1')->count();
        //数据分页
        $page = new Page($count,C('LIMIT_SIZE'));
        $page->setConfig('prev','<span aria-hidden="true">上一页</span>');
        $page->setConfig('next','<span aria-hidden="true">下一页</span>');
        $page->setConfig('first','<span aria-hidden="true">首页</span>');
        $page->setConfig('last','<span aria-hidden="true">尾页</span>');
        if(C('PAGE_STATUS_INFO')){
            $page->setConfig ( 'theme', '<li><a href="javascript:void(0);">当前%NOW_PAGE%/%TOTAL_PAGE%</a></li>  %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%' );
        }
        $result = $person->where('id<>1 AND state=1')->relation(true)->order('id DESC')->limit($page->firstRow.','.$page->listRows)->select();
        //echo $count.' | '.count($result);
        $pageShow = $page->show();
        $auth = new Auth();
        foreach($result as $key=>&$value){
            if($value['lasttime']!=''){
                $value['lasttime'] = conversiontime($value['lasttime']);
            }
            $value['createtime'] = conversiontime($value['createtime']);
            if($value['report']){
                $value['report_name'] = $person->field('nickname')->find($value['report'])['nickname'];
            }
            switch($value['state']){
                case 1:
                    $value['state_text'] = '正常';
                    $value['state_class'] = 'tag-success';
                    break;
                case 2:
                    $value['state_text'] = '禁用';
                    $value['state_class'] = 'tag-danger';
                    break;
                case 3:
                    $value['state_text'] = '离职';
                    $value['state_class'] = 'tag-warning';
                    break;
                default:
                    $value['state_text'] = 'UNKNOW';
                    $value['state_class'] = 'tag-danger';
            }
        }
        # print_r($result);
        if(I('get.p')){
            $this->assign('pagenumber',I('get.p')-1);
        }
        $this->assign('limitsize',C('LIMIT_SIZE'));
        $authgroup = M('AuthGroup');
        $role = $authgroup->field('id,title')->select();
        $this->assign('role',$role);
        $this->assign('personList',$result);
        $this->assign('pageShow',$pageShow);
        $this->display();
    }

    //非正常状态人员名单
    public function abnormal(){
        $person = D('User');
        $count = $person->where('id<>1 AND state>1')->count();
        //数据分页
        $page = new Page($count,C('LIMIT_SIZE'),'','Manage/page');
        $page->setConfig('prev','<span aria-hidden="true">上一页</span>');
        $page->setConfig('next','<span aria-hidden="true">下一页</span>');
        $page->setConfig('first','<span aria-hidden="true">首页</span>');
        $page->setConfig('last','<span aria-hidden="true">尾页</span>');
        if(C('PAGE_STATUS_INFO')){
            $page->setConfig ( 'theme', '<li><a href="javascript:void(0);">当前%NOW_PAGE%/%TOTAL_PAGE%</a></li>  %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%' );
        }
        $result = $person->where('id<>1 AND state>1')->relation(true)->order('id DESC')->limit($page->firstRow.','.$page->listRows)->select();
        //echo $count.' | '.count($result);
        $pageShow = $page->show();
        $auth = new Auth();
        foreach($result as $key=>&$value){
            if($value['lasttime']!=''){
                $value['lasttime'] = conversiontime($value['lasttime']);
            }
            $value['createtime'] = conversiontime($value['createtime']);
            if($value['report']){
                $value['report_name'] = $person->field('nickname')->find($value['report'])['nickname'];
            }
            switch($value['state']){
                case 1:
                    $value['state_text'] = '正常';
                    $value['state_class'] = 'tag-success';
                    break;
                case 2:
                    $value['state_text'] = '禁用';
                    $value['state_class'] = 'tag-danger';
                    break;
                case 3:
                    $value['state_text'] = '离职';
                    $value['state_class'] = 'tag-warning';
                    break;
                default:
                    $value['state_text'] = 'UNKNOW';
                    $value['state_class'] = 'tag-danger';
            }
        }
        # print_r($result);
        if(I('get.p')){
            $this->assign('pagenumber',I('get.p')-1);
        }
        $this->assign('limitsize',C('LIMIT_SIZE'));
        $authgroup = M('AuthGroup');
        $role = $authgroup->field('id,title')->select();
        $this->assign('role',$role);
        $this->assign('personList',$result);
        $this->assign('pageShow',$pageShow);
        $this->display();
    }
    
    //添加管理员
    public function add(){
        if(IS_AJAX){
            $person = D('User');
            if(!$person->create()){
                $this->ajaxReturn(array('flag'=>'-1','msg'=>'数据创建失败'));
            }
            //收集权限分组数据
            $data['title'] = I('post.account');
            $data['rules'] = I('post.rules');
            //开启事务支持
            M()->startTrans();
            $rulesid = M('AuthGroup')->add($data);
            $id = $person->add();
            if($id && $rulesid){
                M()->commit();
                $access['uid'] = $id;
                $access['group_id'] = $rulesid;
                //添加用户角色
                if(M('AuthGroupAccess')->add($access)){
                    $this->ajaxReturn(array('flag'=>'1','msg'=>'添加成功'));
                }else{
                    $this->ajaxReturn(array('flag'=>'0','msg'=>'添加失败'));
                }
            }else{
                M()->rollback();
                $this->ajaxReturn(array('flag'=>'-1','msg'=>'错误'));
            }
        }else{
            $this->display();
        }
    }

    //邮件通知
   public function EmailNotice(){
        if(!IS_POST) reutrn;
        $address = I('post.sendEmail');
        $result = send_Email($address,'这是邮件的标题','这是邮件的正文');
        if($result['error']){
            echo 'true';exit;
        }else{
            echo $result['message'];exit;
        }
    }

    //部门职位联动
    public function changeDepartment(){
        if(!IS_POST) return;
        if(!I('post.id')) return;
        $position = M('Position');
        $result = $position->where('belongsto='.I('post.id'))->order('id DESC')->select();
        if($result){
            $this->ajaxReturn(array('flag'=>1,'data'=>$result));
        }else{
            $this->ajaxReturn(array('flag'=>0,'msg'=>'数据获取失败'));
        }
    }
    
    //新增用户页面初始化
    public function addManage(){
        $person = D('User');
        $alluser = $person->field('id,nickname,department')->select();
        $person = D('Department');
        $departmentList = $person->field('id,name')->select();
        $department = M('Department');
        $position = M('Position');
        $authrule = M('AuthRule');
        $level = M('Userlevel');
        $allLevel = $level->select();
        $authruledata = $authrule->field('id,title')->select();
        $positionData = $position->field('id,name')->where('belongsto=1')->order('id DESC')->select();
        $departmentData = $department->field('id,name')->select();
        $this->assign('level',$allLevel);
        $this->assign('departmentList',$departmentList);
        $this->assign('alluser',$alluser);
        $this->assign('authrule',$authruledata);
        $this->assign('position',$positionData);
        $this->assign('department',$departmentData);
        $this->display();
    }

    //重置密码
    public function resetPassword(){
        if(!IS_POST) return;
        $id = I('post.id');
        $resetPassword = I('post.password');
        $user = M('User');
        $userInfo = $user->find($id);
        $address = $userInfo['email'];
        $nickname = $userInfo['nickname'];
        $subject = '您的密码已重置';
        $body = <<<HTML
<p>Dear $nickname,</p>
<p>您的密码已重置</p>
<p>新密码：$resetPassword</p>
HTML;
        $result = $user->save(array('id'=>$id,'password'=>sha1($resetPassword)));
        if($result!==false){
            send_Email($address,$nickname,$subject,$body);
            $this->ajaxReturn(array('flag'=>1,'newpassword'=>$resetPassword,'sha1'=>sha1($resetPassword)));exit;
        }
    }

    //验证账号的唯一性
    public function checkUniqueAccount(){
        if(!IS_POST) return;
        $person = D('User');
        $account = I('post.account');
        $map['account'] = I('post.account');
        $result = $person->where($map)->select();
        if(!$result){
            echo 'true';exit;
        }else{
            echo 'false';exit;
        }
    }
    
    //添加用户
    public function addManageData(){
        if(!IS_POST) return;
        $person = D('User');
        $rules = '';
        foreach(I('post.permissions') as $key=>&$value){
            $rules .= $value.',';
        }
        //收集用户数据
        $rules = substr($rules,0,-1);
        $userData['account'] = I('post.account');
        $userData['password'] = sha1(I('post.password'));
        $userData['nickname'] = I('post.nickname');
        $userData['email'] = I('post.email');
        $userData['department'] = I('post.department');
        $userData['position'] = I('post.position');
        $userData['level'] = I('post.level');
        $userData['sex'] = I('post.sex');

        if($userData['sex'] == '女'){
            $userData['face'] = '/Public/home/img/face/face_01.png';
        }

        if(I('post.report')!=''){
            $userData['report'] = I('post.report');
        }
        $userData['createtime'] = time();
        //收集权限分组数据
        $data['title'] = I('post.account');
        $data['rules'] = $rules;
        //开启事务支持
        M()->startTrans();  
        $rulesid = M('AuthGroup')->add($data);
        $id = $person->add($userData);
        if($id && $rulesid){
            M()->commit();
            $access['uid'] = $id;
            $access['group_id'] = $rulesid;
            //添加用户角色
            if(M('AuthGroupAccess')->add($access)){
                $address = I('post.email');
                $nickname = I('post.nickname');
                $subject = '您的OA系统账号已开通!';
                $body = '<p>Dear '.I('post.nickname').',</p><p>您的OA系统账号已开通!<br><br>账号：'.I('post.account').'<br>密码：'.I('post.password').'<br><br>收到邮件后请及时登录 <a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'">http://'.$_SERVER['HTTP_HOST'].'</a> 修改密码。</p>';
                send_Email($address, $nickname, $subject, $body);
                $this->ajaxReturn(array('flag'=>'1','msg'=>'添加成功'));
            }else{
                $this->ajaxReturn(array('flag'=>'0','msg'=>'添加失败'));
            }
        }else{
            M()->rollback();
            $this->ajaxReturn(array('flag'=>'-1','msg'=>'错误'));
        }
    }
    
    //编辑用户页面初始化
    public function edit(){
        if(!I('get.id') || I('get.id')==1) {
            $this->error('参数错误');exit;
        };
        $person = D('User');
        $personData = $person->field('u.id,u.account,u.password,u.nickname,u.email,u.department,u.position,u.report,u.level,u.state,d.name department_name,p.id position_id,p.name position_name')
                             ->table('__DEPARTMENT__ d,__POSITION__ p,__USER__ u')
                             ->where('u.department=d.id AND u.position=p.id AND u.id='.I('get.id'))
                             ->select()[0];
        $level = M('Userlevel');
        $allLevel = $level->select();
        $alluser = $person->field('id,nickname,department')->select();
        $person = D('Department');
        $departmentList = $person->field('id,name')->select();
        $department = M('Department');
        $position = M('Position');
        $authrule = M('AuthRule');
        $auth = new Auth();
        $authgroup = $auth->getGroups(I('get.id'))[0];
        $authruledata = $authrule->field('id,title')->select();
        //print_r($personData);
        //$positionid = $person->field('u.position')->table('__USER__ u')->find(I('get.id'))['position'];
        //echo $personData['position_id'];
        $belongsto = $position->field('belongsto')->find($personData['position_id'])['belongsto'];
        $positionData = $position->field('id,name')->where('belongsto='.$belongsto)->order('id DESC')->select();
        $departmentData = $department->field('id,name')->select();
        $this->assign('level',$allLevel);
        $this->assign('authgroup',$authgroup);
        $this->assign('personData',$personData);
        $this->assign('departmentList',$departmentList);
        $this->assign('alluser',$alluser);
        $this->assign('authrule',$authruledata);
        $this->assign('position',$positionData);
        $this->assign('department',$departmentData);
        $this->display();
    }
    
    //保存编辑
    public function saveEdit(){
        if(!IS_POST) return;
        $person = D('User');
        $rules = '';
        foreach(I('post.permissions') as $key=>&$value){
            $rules .= $value.',';
        }
        //收集用户数据
        $rules = substr($rules,0,-1);
        $userData['id'] = I('post.id');
        $userData['account'] = I('post.account');
        $userData['nickname'] = I('post.nickname');
        $userData['email'] = I('post.email');
        $userData['department'] = I('post.department');
        $userData['position'] = I('post.position');
        $userData['level'] = I('post.level');
        $userData['state'] = I('post.state');
        if(I('post.report')!=''){
            $userData['report'] = I('post.report');
        }
        //收集权限分组数据
        $data['rules'] = $rules;
        //开启事务支持
        M()->startTrans();
        $rulesid = M('AuthGroup')->where('title="'.I('post.account').'"')->save($data);
        $id = $person->save($userData);
        if($id!==false && $rulesid!==false){
            M()->commit();
            $this->ajaxReturn(array('flag'=>'1','msg'=>'修改成功'));exit;
        }else{
            M()->rollback();
            $this->ajaxReturn(array('flag'=>'0','msg'=>'修改失败'));exit;
        }
    }
    
    //删除用户
    public function del(){
        if(!I('post.id')) return;
        $user = M('User');
        $authGroupAccess = M('AuthGroupAccess');
        $groupArr = $authGroupAccess->field('group_id')->where('uid='.I('post.id'))->select()[0];
        $group_id = $groupArr['group_id'];
        M()->startTrans();
        $delAuthGroup = M('AuthGroup')->delete($group_id);
        $delAuthGroupAccess = $authGroupAccess->where('uid='.I('post.id'))->delete();
        $delUserData = $user->delete(I('post.id'));
        if($delAuthGroup && $delAuthGroupAccess && $delUserData){
            echo 'true';exit;
        }else{
            echo 'false';exit;
        }
    }
    
    //角色管理
    public function role(){
        $role = M('AuthGroup');
        $auth = new Auth();
        $rules = $auth->getGroups(session('user')['id']);
        $result = $role->field('id,title,status')->select();
        $this->assign('authgroup',$result);
        $this->display();
    }
    
    //ajax获取角色分组数据
    public function getRole(){
        if(!IS_AJAX) return;
        if(!I('post.id')) return;
        $role = M('AuthGroup');
        $map['id'] = I('post.id');
        $result = $role->where($map)->find();
        $rules = $result['rules'];
        $authrule = M('AuthRule');
        $res = $authrule->select();
        $data = array();
        $data['authrule'] = $res;
        $data['rules'] = $rules;
        $this->ajaxReturn($data);
    }
    
    //ajax修改角色权限
    public function editrule(){
        if(!IS_AJAX) return;
        if(!I('post.id')) return;
        $rule = M('AuthGroup');
        $data['id'] = I('post.id');
        $data['rules'] = I('post.rules');
        if(!$rule->save($data)){
            $this->ajaxReturn('false');
        }else{
            $this->ajaxReturn('true');
        }
    }
    
    //ajax控制角色状态开关
    public function flag(){
        if(!IS_AJAX) return;
        if(!I('post.id')) return;
        $authgroup = M('AuthGroup');
        $authgroup->id = I('post.id');
        if(I('post.flag')=='on'){
            $authgroup->status = 1;
        }else{
            $authgroup->status = 0;
        }
        if($authgroup->save()){
            $this->ajaxReturn('true');
        }else{
            $this->ajaxReturn('false');
        }
    }
    
    //ajax获取用户信息
    public function getuserinfo(){
        if(!IS_AJAX) return;
        if(!I('post.id')) return;
        $person = D('User');
        $auth = new Auth();
        $authgroup = $auth->getGroups(I('post.id'))[0];
        $result = $person->relation(true)->find(I('post.id'));
        $result['report_name'] = $person->field('nickname')->find($result['report'])['nickname'];
        $result['authgroup'] = $authgroup['title'];
        $result['rules'] = $authgroup['rules'];
        $result['groupid'] = $authgroup['group_id'];
        $this->ajaxReturn($result);
    }
    
    //ajax删除用户
    public function deluser(){
        if(!IS_AJAX) return;
        if(!I('post.id')) return;
        $user = M('User');
        if($user->delete(I('post.id'))){
            $this->ajaxReturn('true');
        }else{
            $this->ajaxReturn('false');
        }
    }
    
    //ajax保存角色修改
    public function changerole(){
        if(!IS_AJAX) return;
        if(!I('post.id') || !I('groupid')) return;
        //print_r($_POST);die;
        $map['id'] = I('groupid');
        //开启事务支持
        M()->startTrans();
        $authgroup = M('AuthGroup');
        $flag = $authgroup->where($map)->save(array('rules'=>I('rules')));
        $user = M('User');
        $where['id'] = I('id');
        $data['department'] = I('department');
        $data['position'] = I('position');
        $data['report'] = I('report');
        $_flag = $user->where($where)->save($data);
        if($flag!==false && $_flag!==false){
            M()->commit();
            $this->ajaxReturn('true');
        }else{
            M()->rollback();
            $this->ajaxReturn('false');
        }
    }

    //组织架构图
    public function organization(){
        $generalManager = M()->table('__USER__ u,__POSITION__ p')->field('u.id,u.nickname name,p.name title')->where('level=7 AND u.position=p.id')->select();
        self::organizationalTraverse($generalManager);
        //print_r(json_encode($generalManager));
        $orgchart = json_encode($generalManager);
        $orgchart = substr($orgchart,1);
        $orgchart = substr($orgchart,0,-1);
        $this->assign('orgchart',$orgchart);
        $this->display();
    }

    //递归
    static function organizationalTraverse(&$general){
        if( is_array($general) && !empty($general) ){
            foreach($general as $key=>&$value){
                $child = M()->table('__USER__ u,__POSITION__ p')->field('u.id,u.nickname name,p.name title')->where('u.report='.$value['id'].' AND u.position=p.id AND state=1')->select();
                if( is_array($child) && !empty($child) ){
                    $value['children'] = $child;
                    self::organizationalTraverse($value['children']);
                }
            }
        }
    }
    
}