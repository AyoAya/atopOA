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

class FileController extends AuthController  {
    /**
     * 初始化审批首页数据
     * 注入默认显示数据
     */
    public function index(){

        $this->display();
    }

    /**
     * 编号申请
     */
    public function apply(){

        $model = new model();

        $ruleData = $model->table(C('DB_PREFIX').'file_rule')->select();

        if(IS_POST){

            $post = I('post.');

            print_r($post);


            $rule = $model->table(C('DB_PREFIX').'file_rule')->where("type =".'"'.$post['type'].'"')->find();
            print_r($rule);
            /*
                            $save_busy_id = $model->table(C('DB_PREFIX').'file_rule')->where("type =".'"'.$post['type'].'"')->save($busy);

                            if( $save_busy_id ){
                                # 生成一个编号
                                $ruleRel = $model->table(C('DB_PREFIX').'file_rule')->where("type =".'"'.$post['type'].'"')->find();
                                $num = $ruleRel['current_id'];
                                $len = $ruleRel['length'];
                                $type = $ruleRel['type'];

                                $num = $num;
                                $bit = $len-strlen($type);
                                $num_len = strlen($num);
                                $zero = '';
                                for($i=$num_len; $i<$bit; $i++){
                                    $zero .= "0";
                                }
                                $real_num = $type.$zero.($num+1);
                                # echo $real_num;

                                $current['current_id'] = $num+1;


                                #生成编号后修改rule表
                                $up_busy_id = $model->table(C('DB_PREFIX').'file_rule')->where("type =".'"'.$post['type'].'"')->save($current);

                                if( $up_busy_id ){
                                    $up['busy'] = 'Y';
                                    $save_buyId = $model->table(C('DB_PREFIX').'file_rule')->where("type =".'"'.$post['type'].'"')->save($up);

                                    if( $save_buyId ){
                                        $numberData['file_no'] = $real_num;
                                        $numberData['person'] = session('user')['id'];
                                        $numberData['rule_time'] = time();
                                        $number_id = $model->table(C('DB_PREFIX').'file_number')->add($numberData);

                                        if($number_id){
                                            $this->ajaxReturn(['flag'=>1,'msg'=>'操作成功！']);
                                        }else{
                                            $this->ajaxReturn(['flag'=>0,'msg'=>'失败！']);
                                        }
                                    }else{
                                        $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败！']);
                                    }
                                }else{
                                    $this->ajaxReturn(['flag'=>0,'msg'=>'生成编号失败！']);
                                }

                                #生成编号后写进number表



                            }


                        }*/


        }


        $this->assign('ruleData',$ruleData);
        $this->display();

    }


    public function review(){

        $model = new model();
        $model->startTrans();
        # 收集文件号
        $numberData = $model->table(C('DB_PREFIX').'file_number')->where('person ='.session('user')['id'].' AND status =1')->order('file_no ASC')->select();

        if(IS_POST) {

            $post = I('post.');
            $post['cc'] = json_decode(html_entity_decode($post['cc']), true);
            # print_r($post);
            $numData['description'] = $post['data']['desc'];
            $numData['create_time'] = time();
            $numData['status'] = 2;

            $num_id = $model->table(C('DB_PREFIX') . 'file_number')->where('id =' . $post['data']['file_no'])->save($numData);

            foreach ($post['cc'] as $key => &$value) {
                $review['person'] = $value['id'];
                $review['n_id'] = $post['data']['file_no'];
                $arr[] = $review;
            }

            $review_id = $model->table(C('DB_PREFIX') . 'file_review')->addAll($arr);

            if ($review_id && $num_id) {
                $model->commit();
                $this->ajaxReturn(['flag' => $post['data']['file_no'], 'msg' => '发起成功！']);
            } else {
                $model->rollback();
                $this->ajaxReturn(['flag' => 0, 'msg' => '发起失败！']);
            }


        }else{
            //调用父类注入部门和人员信息
            $this->getAllUsersAndDepartments();
            $this->assign('numberData',$numberData);
            $this->display();
        }

    }


    public function fileDetail(){

        $this->display();

    }


    public function reviewDetail(){
        $model = new model();
        $id = I('get.id');
        if($id){

            $person = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'user b')
                            ->field('a.id,a.file_no,a.status,a.person create_person,a.attachment,a.description,a.rule_time,b.nickname,b.email')
                            ->where('a.id ='.$id.' AND a.person =b.id')
                            ->find();
            $person['attachment'] = json_decode($person['attachment'],true);

            $reviewData = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'file_review b,'.C('DB_PREFIX').'user c')
                ->field('a.id,a.file_no,a.status,a.person create_person,a.attachment,a.description,a.rule_time,b.id,b.person,b.status state,b.time,b.log,c.nickname,c.email,c.face')
                ->where('a.id=b.n_id AND a.person ='.session('user')['id'].' AND a.id ='.$id.' AND c.id = b.person')
                ->select();





            print_r($person);
            print_r($reviewData);
            $this->assign('person',$person);
            $this->assign('reviewData',$reviewData);
            $this->display();

        }else{
            $this->error('参数错误！');
        }


    }

    /**
     * 附件
     */

    public function saveDetailAttachment(){
        $att['id'] = I('post.id');
        $att['attachment'] = I('post.attachments', '', false);
        $attId = M('FileNumber')->save($att);
        if($attId){
            $this->ajaxReturn(['flag'=>1,'msg'=>'操作成功!']);
        }else{
            $this->ajaxReturn(['flag'=>0,'msg'=>'上传文件失败!']);
        }
    }

    /**
     * 发起审批页附件上传
     */
    public function uploadAttachment(){
        $subName = I('post.SUB_NAME');

        //print_r($subName);
        // 如果需要按id为子目录则必须填写第二个参数，否则直接保存在当前目录
        if( $subName != '' ){
            $result = upload( '/File/', $subName );
            $this->ajaxReturn( $result );
        }

    }




    /**
     * 写入发起审批的数据
     * 返回写入的结果，成功返回添加成功的id，失败则返回0
     */




}