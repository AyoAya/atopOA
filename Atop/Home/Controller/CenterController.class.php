<?php
namespace Home\Controller;
/**
 * 个人中心
 * @author Fulwin
 * 2016-10-10
 */
class CenterController extends AuthController {

    //个人中心（资料修改）
    public function index(){
        $this->display();
    }

    //修改头像页面初始化
    public function face(){
        $user = M('User');
        $result = $user->find(session('user')['id']);
        $this->assign('userinfo',$result);
        $this->display();
    }

    //保存头像
    public function saveFace(){
        if(!IS_AJAX) return;
        $face = I('post.face');
        $user = M('User');
        $oldFace = $user->field('face')->find(session('user')['id']);
        if($oldFace){
            if(substr($oldFace['face'],1,7)=='Uploads'){
                unlink(getcwd().$oldFace['face']);
            }
        }
        $user->id = session('user')['id'];
        $user->face = I('post.face');
        session('user')['face'] = I('post.face');
        if($user->save()!==false){
            $this->ajaxReturn(array('flag'=>1,'msg'=>'修改成功'));exit;
        }else{
            $this->ajaxReturn(array('flag'=>0,'msg'=>'修改失败'));exit;
        }
    }

    //上传头像
    public function uploadFace(){
        $info = FileUpload('/UserFace/',1,0);
        if(!$info) return false;
        $oldname = $info['Filedata']['name'];
        $newname = $info['Filedata']['savename'];
        $filePath = '/Uploads'.$info['Filedata']['savepath'].$info['Filedata']['savename'];
        $arr['flag'] = 1;
        $arr['OldFileName'] = $oldname;
        $arr['NewFileName'] = $newname;
        $arr['FileSavePath'] = $filePath;
        $this->ajaxReturn($arr);
        exit;
    }

    //保存上传的头像
    public function saveUploadFace(){
        if(!IS_AJAX) return;
        $path = I('post.path');
        $name = 'cut_'.I('post.name');
        $x = I('post.x');
        $y = I('post.y');
        $w = I('post.w');
        $h = I('post.h');
        $cutImg = cutImage($path,$name,$x,$y,$w,$h);
        $thumbFacePath = thumbFace($cutImg['path'],'thumb_'.$cutImg['name']);
        unlink(getcwd().$cutImg['path']);
        unlink(getcwd().$path);
        $user = M('User');
        $oldFace = $user->field('face')->find(session('user')['id']);
        if($oldFace){
            if(substr($oldFace['face'],1,7)=='Uploads'){
                unlink(getcwd().$oldFace['face']);
            }
        }
        $user->id = session('user')['id'];
        $user->face = $thumbFacePath;
        if($user->save()!==false){
            $this->ajaxReturn(array('flag'=>1,'msg'=>'保存成功','facepath'=>$thumbFacePath));
        }else{
            $this->ajaxReturn(array('flag'=>1,'msg'=>'保存失败'));
        }
    }

    //取消上传头像
    public function cancelUploadFace(){
        if(!IS_AJAX) return;
        unlink(getcwd().I('post.path'));
        echo 'true';exit;
    }

    //初始化修改资料页面
    public function modify(){
        $manage = M('User');
        $map['id'] = session('user')['id'];
        $result = $manage->where($map)->find();
        foreach($result as $key=>&$value){
            if($key=='lasttime'){
                $value = conversiontime($value);
            }
        }
        $this->assign('userinfo',$result);
        $this->display();
    }
    
    //ajax修改密码
    public function editPassword(){
        if(!IS_AJAX) return;
        $user = M('User');
        $password = sha1(I('post.password'));
        $map['password'] = $password;
        if($user->where('id='.session('user')['id'])->save($map)){
            $this->ajaxReturn(array('flag'=>1,'msg'=>'保存成功'));
        }else{
            $this->ajaxReturn(array('flag'=>0,'msg'=>'保存失败'));
        }
    }

    //验证原密码
    public function checkOldpassword(){
        if(!IS_AJAX) return;
        $oldpassword = sha1(I('post.oldpassword'));
        $user = M('User');
        $result = $user->where('id='.session('user')['id'].' AND password="'.$oldpassword.'"')->select();
        if($result){
            echo 'true';exit();
        }else{
            echo 'false';exit();
        }
    }
    
    //ajax修改姓名
    public function changeEameEmail(){
        if(!IS_AJAX) return;
        $center = M('User');
        $data['nickname'] = I('post.nickname');
        $data['email'] = I('post.email');
        $result = $center->where('id='.session('user')['id'])->save($data);
        if($result!==false){
            $this->ajaxReturn(array('flag'=>1,'msg'=>'保存成功'));
        }else{
            $this->ajaxReturn(array('flag'=>1,'msg'=>'保存失败'));
        }
    }
    
    
}