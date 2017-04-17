<?php
namespace Home\Controller;
/**
 * 后台首页（初始化数据）
 * @author Fulwin
 * 2016-10-8
 */
class IndexController extends AuthController {
    
    //初始化页面
    public function index(){
        $user = M('User');
        $result = $user->find(session('user')['id']);
        $result2 = $user->field('lasttime,lastip')->find(session('user')['id']);
        //将数据注入模板
        $this->assign('lasttime',conversiontime($result2['lasttime']));
        $this->assign('lastip',$result2['lastip']);
        $this->assign('browser',determinebrowser($_SERVER['HTTP_USER_AGENT']));
        $this->assign('version',apache_get_version());
        $this->assign('day',date('Y-m-d',time()));
        $this->assign('week',week());
        $this->assign('userinfo',$result);
        $this->display();
    }

    //退出登录
    public function logout(){
        //删除cookie
        cookie('UserLoginInfo',null);
        //删除session
        session('[destroy]');
        //跳转了到登陆页
        $this->redirect('Login/index');
    }
    
}