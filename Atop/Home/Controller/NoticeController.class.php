<?php
namespace Home\Controller;
/**
 * 通知中心
 * @author Fulwin
 * 2016-11-2
 */
class NoticeController extends AuthController {
    
    //初始化页面
    public function index(){
        $notice = M('Notice');
        $map['who'] = session('user')['id'];
        $map['status'] = 0;
        $newMessage = $notice->where($map)->order('id DESC')->select();
        foreach($newMessage as $key=>&$value){
            $value['time'] = mdate($value['pushtime']);
        }
        $map2['who'] = session('user')['id'];
        $map2['status'] = 1;
        $oldMessage = $notice->where($map2)->order('id DESC')->select();
        foreach($oldMessage as $key=>&$value){
            if($value['viewtime']){
                $value['view'] = mdate($value['viewtime']);
            }
        }
        $this->assign('newMessage',$newMessage);
        $this->assign('oldMessage',$oldMessage);
        $this->assign('empty','<div class="empty-box"><i class="icon-info-sign"></i><p class="first">没有数据</p><p class="last">There is no data</p></div>');
        $this->display();
    }
    
    
    //ajax请求最新通知
    public function getNotice(){
        if(!IS_AJAX)return;
        $notice = M('Notice');
        $map['who'] = session('user')['id'];
        $map['status'] = 0;
        $result = $notice->where($map)->count();
        if($result){
            if($result>99){
                $this->ajaxReturn(array('flag'=>1,'number'=>99));exit;
            }else{
                $this->ajaxReturn(array('flag'=>1,'number'=>$result));exit;
            }
        }
    }
    
    
    
}