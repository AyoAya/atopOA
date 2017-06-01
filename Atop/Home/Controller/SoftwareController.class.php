<?php
namespace Home\Controller;
use Think\Model;

class SoftwareController extends AuthController {


    /**
     * 首页
     */
    public function index(){

        $model = new model();

        $softData =  $model->table(C('DB_PREFIX').'software')
                           ->select();

        foreach($softData as $key=>&$value){

            $value['content'] = $model->table(C('DB_PREFIX').'software_log')
                ->where('soft_asc ='.$value['id'])
                ->order('id DESC')
                ->field('version,save_time,soft_asc')
                ->select();

        }

        //print_r($softData);

        $this->assign('softData',$softData);
        $this->display();
    }


    /**
     * 添加页面
     */
    public function add(){

        if( IS_POST ){
            $software = M('Software');
            $software_log = M('SoftwareLog');

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

                    $softLog['soft_asc'] = $software_add_id;
                    $softLog['version'] = 'v1.0';
                    $softLog['save_time'] = time();
                    $softLog['log'] = '新软件发布';
                    $softLog['save_person'] = session('user')['id'];

                    $software_log_id = $software_log->add($softLog);

                    if( $software_log_id ){

                        $this->ajaxReturn(['flag'=>1,'msg'=>'添加项目成功!']);

                    }else{

                        $this->ajaxReturn(['flag'=>0,'msg'=>'添加项目失败!']);
                        exit();

                    }

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

        $model = new model();

        $softRel = $model->table(C('DB_PREFIX').'software')->find(I('get.id'));

        $softRel['child'] = $model->table(C('DB_PREFIX').'software_log a,'.C('DB_PREFIX').'software b,'.C('DB_PREFIX').'user c')
            ->field('a.log,a.save_time,a.version,a.attachment,c.face')
            ->where('b.id ='.I('get.id').' AND b.id=a.soft_asc AND a.save_person=c.id')
            ->order('a.id DESC')
            ->select();

        foreach ($softRel['child'] as $key=>&$value){
            $value['attachment'] = json_decode($value['attachment'],true);
            foreach($value['attachment'] as $k=>&$v){
                $v['ext'] = strtolower($v['ext']);
            }
        }

        $this->assign('softData',$softRel['child']);
        $this->assign('softwareData',$softRel);

        $this->display();
    }

    /**
     * 页面展示
     */

    public function addLog(){
        #添加信息跟新版本
        if( IS_POST ){
            $arr = I('post.addRel','',false);

            $logData['soft_asc'] = $arr['subName'];
            $logData['version'] = $arr['version'];
            $logData['log'] = replaceEnterWithBr($arr['context']);
            $logData['save_time'] = time();
            $logData['save_person'] = session('user')['id'];
            $logData['attachment'] = $arr['attachment'];


            $add_saftlog_id = M('software_log')->add($logData);
            if( $add_saftlog_id ){
                $this->ajaxReturn(['flag'=>1,'msg'=>'更新成功!']);
            }else{
                $this->ajaxReturn(['flag'=>0,'msg'=>'更新失败!']);
            }


        }
    }

    /**
     * 附件上传
     */
    # 上传附件
    public function upload(){
        $subName = I('post.SUB_NAME');

        #  如果需要按id为子目录则必须填写第二个参数，否则直接保存在当前目录
        if( $subName != '' ){
            $result = upload( I('post.PATH'), $subName );

            $this->ajaxReturn( $result );
        }

    }


}
