<?php
namespace Home\Controller;
use Think\Page;

/**
 * Created by PhpStorm.
 * User: Fulwin
 * Date: 2017/9/26
 * Time: 17:34
 */
class TodolistController extends AuthController {

    # 初始化页面
    public function index(){
        $model = M('Todolist');
        if( I('get.state') == 'todo' ){
            $count = $model->where(['state'=>'todo'])->count();
            //数据分页
            $page = new Page($count,C('LIMIT_SIZE'));
            $page->setConfig('prev','<span aria-hidden="true">上一页</span>');
            $page->setConfig('next','<span aria-hidden="true">下一页</span>');
            $page->setConfig('first','<span aria-hidden="true">首页</span>');
            $page->setConfig('last','<span aria-hidden="true">尾页</span>');
            if(C('PAGE_STATUS_INFO')){
                $page->setConfig ( 'theme', '<li><a href="javascript:void(0);">当前%NOW_PAGE%/%TOTAL_PAGE%</a></li>  %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%' );
            }
            $result = $model->where(['who'=>session('user')['id'], 'state'=>'todo'])->limit($page->firstRow.','.$page->listRows)->order('generate_time DESC')->select();
        }else{
            $count = $model->where(['state'=>'done'])->count();
            //数据分页
            $page = new Page($count,C('LIMIT_SIZE'));
            $page->setConfig('prev','<span aria-hidden="true">上一页</span>');
            $page->setConfig('next','<span aria-hidden="true">下一页</span>');
            $page->setConfig('first','<span aria-hidden="true">首页</span>');
            $page->setConfig('last','<span aria-hidden="true">尾页</span>');
            if(C('PAGE_STATUS_INFO')){
                $page->setConfig ( 'theme', '<li><a href="javascript:void(0);">当前%NOW_PAGE%/%TOTAL_PAGE%</a></li>  %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%' );
            }
            $result = $model->where(['who'=>session('user')['id'], 'state'=>'done'])->limit($page->firstRow.','.$page->listRows)->order('complete_time DESC')->select();
        }
        $pageShow = $page->show();
        $this->assign('pageShow',$pageShow);
        $this->assign('result', $result);
        $this->display();
    }

    // 获取待办事项数量
    public function getTodolistCount(){
        if( IS_POST ){
            $model = M('Todolist');
            $count = $model->where(['who'=>session('user')['id'], 'state'=>'todo'])->count();
            if( $count !== 0 && $count < 99 ){
                $countTxt = $count;
            }elseif( $count !== 0 && $count > 99 ){
                $countTxt = '99+';
            }else{
                $countTxt = 0;
            }
            echo $countTxt;
        }
    }

}