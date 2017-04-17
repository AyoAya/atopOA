<?php
namespace Home\Controller;
use Think\Controller;
/**
 * 后台登陆
 * @author Fulwin
 * 2016-10-9
 */
class LoginController extends Controller {
    //登录界面
    public function index(){
        //如果您已经是登录状态则禁止访问登录页
        if(session('user')){
            echo '<script>history.back();</script>';
        }
        $this->display();
    }
    //验证码
    public function code(){
        verifycode();
    }
    //登录
    public function login(){
        if(!IS_POST){
            $this->error('错误');
            exit;
        }
        $login = D('User');
        if(C('LOGIN_VERIFY')){
            if(!check_verify(I('post.verify'),1)){
                $this->error('验证码错误');
                exit;
            }
        }
        $map['account'] = I('account');
        $map['password'] = sha1(I('password'));
        $result = $login->where($map)->select();
        $userData = array('id'=>$result[0]['id'],'account'=>$result[0]['account'],'nickname'=>$result[0]['nickname'],'face'=>$result[0]['face']);
        if($result){
            //如果用户点击了一月免登陆则将登陆信息保存到cookie
            if(I('post.remember')){
                if (is_array($userData) && !empty($userData)){
                    //将cookie进行加密
                    cookie('UserLoginInfo', encrypt(serialize($userData), C('MD5_KEY')),86400*30);      //user是一个数据然后序列化成字符串
                    session('user',$userData);
                }
            }else{
                if (is_array($userData) && !empty($userData)){
                    //将数据保存到session
                    session('user',$userData);
                }
            }
            if(session('user')){
                $data['id'] = $result[0]['id'];
                $data['lasttime'] = time();
                $data['lastip'] = $_SERVER['REMOTE_ADDR'];
                if($login->save($data)){
                    //登陆成功后访问用户之前的页面
                    if(session('source')){
                        $url = session('source');
                        session('source',null);
                        header("location:$url");exit;
                    }else{
                        $this->redirect(__ROOT__.'/Index');
                    }
                }
            }
        }else{
            $this->error('账号或密码错误');
        }
    }

}