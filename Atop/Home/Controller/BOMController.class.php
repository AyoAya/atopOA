<?php
/**
 * Created by PhpStorm.
 * User: Fulwin
 * Date: 2017/1/10
 * Time: 11:02
 */
namespace Home\Controller;
use Think\Model;
use Think\Page;

class BOMController extends AuthController  {

    /**
     * 初始化审批首页数据
     * 注入默认显示数据
     */
    public function index(){

        $this->display();
    }




}