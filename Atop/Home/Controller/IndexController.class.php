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