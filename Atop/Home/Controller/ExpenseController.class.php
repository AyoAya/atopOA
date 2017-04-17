<?php
/**
 * Created by PhpStorm.
 * User: Fulwin
 * Date: 2017/1/6
 * Time: 16:49
 */
namespace Home\Controller;

class ExpenseController extends AuthController {



    //初始化页面
    public function index(){
        $this->display();
    }

    //报销发起
    public function add(){
        $this->display();
    }



}