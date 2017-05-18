<?php
namespace Home\Controller;
use Think\Model;

class SoftwareController extends AuthController {


    /**
     * 首页
     */
    public function index(){
        $software = M('Software');
        $softData =  $software->select();

        $this->assign('softData',$softData);
        $this->display();
    }


    /**
     * 添加页面
     */
    public function add(){

        if( IS_POST ){
            $software = M('Software');

            $soft['type'] = I('post.type');
            $soft['number'] = I('post.number');
            $soft['name'] = I('post.name');
            $soft['mcu'] = I('post.mcu');
            $soft['log'] = I('post.log');
            $soft['person'] = session('user')['nickname'];
            $soft['create_time'] = time();
            #判断用户是否选择ATE
            if(I('post.type') == 'ATE'){
                $soft['mcu'] = '';
            }
            # 判断项目单号是否存在
            $rel = $software->where('number="'.I('post.number').'"')->select();

            if(!empty($rel)){
                $this->ajaxReturn(['flag'=>0,'msg'=>'该项目已存在!']);
                exit();
            }else{
                $software_add_id = $software->add($soft);
                if( $software_add_id ){

                    $this->ajaxReturn(['flag'=>1,'msg'=>'添加项目成功!']);
                }else{
                    $this->ajaxReturn(['flag'=>0,'msg'=>'添加项目失败!']);
                    exit();
                }
            }
        }
        $this->display();
    }

    /**
     * 详情页面
     */
    public function detail(){
        $software = M('software');
        $softwareData = $software->where('id ='.$_GET['id'])->select();

        //print_r($software);
        $this->assign('softwareData',$softwareData);
        $this->display();
    }


}
