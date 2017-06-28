<?php
/**
 * Created by PhpStorm.
 * User: Fulwin
 * Date: 2017/1/10
 * Time: 11:02
 */
namespace Home\Controller;
use Think\Model;

class ApprovalController extends AuthController  {
    private $director = array();

    /**
     * 初始化审批首页数据
     * 注入默认显示数据
     */
    public function index(){

        // 检查地址栏tab参数的正确性，如果不存在则默认访问 “我发起的“ 页面，如果tab参数不存在指定的数据中也默认访问 ”我发起的“ 页面
        $tab = I('get.tab') ? I('get.tab') : 'belongtome';
        if( !in_array($tab,['initiated', 'ongoing', 'already', 'belongtome','statistics']) ) $tab = 'belongtome';

        $model = new model();

        switch( $tab ){
            case 'initiated':
                # 所有数据
                $tmpArr = $model->table(C('DB_PREFIX').'approval')->select();

                foreach ($tmpArr as $key=>&$value){
                    $value['step'] = $model->table(C('DB_PREFIX').'approval_step')->where('a_id ='.$value['id'])->select();
                }

                # 默认加载的集数据
                $tmpSeData = $model->table(C('DB_PREFIX').'approval')->field('name aName,id')->order('id ASC')->limit(1)->select();
                foreach ($tmpSeData as $key=>&$value){
                    $value['step'] = $model->table(C('DB_PREFIX').'approval_step')->where('a_id ='.$value['id'])->select();
                    foreach ($value['step'] as $k=>&$v){
                        if($v['type'] == 'department'){
                            $v['person'] = $model->table(C('DB_PREFIX').'user a,'.C('DB_PREFIX').'department b')
                                ->where('b.id ='.$v['position'].' AND a.department = b.id AND a.state = 1')
                                ->field('a.nickname,a.email,a.id')
                                ->select();
                        }else{
                            $v['person'] = $model->table(C('DB_PREFIX').'user a,'.C('DB_PREFIX').'position b')
                                ->where('b.id ='.$v['position'].' AND a.position = b.id AND a.state = 1')
                                ->field('a.nickname,a.email,a.id')
                                ->select();
                        }
                    }
                }

                $this->assign('approvalData',$tmpArr);
                $this->assign('tmpSeData',$tmpSeData);
                break;
            case 'ongoing':

                break;
            case 'already':

                break;
            case 'belongtome':
                $belongtome_data = $model->table(C('DB_PREFIX').'approval_detail a,'.C('DB_PREFIX').'approval b')
                                         ->field('a.id detail_id,a.title,a.create_time,b.name')
                                         ->where( 'a.set_id = b.id' )
                                         ->select();
                foreach( $belongtome_data as $key=>&$value ){
                    $value['current_data'] = $model->table(C('DB_PREFIX').'approval_middle a,'.C('DB_PREFIX').'approval_detail b,'.C('DB_PREFIX').'approval_step c,'.C('DB_PREFIX').'user d')
                                                       ->field('c.name,d.nickname,d.face')
                                                       ->where('a.middle_detail=b.id AND a.middle_step=c.id AND a.middle_person=d.id')
                                                       ->order('a.middle_time DESC')
                                                       ->limit(1)
                                                       ->find();
                }
                print_r($belongtome_data);
                $this->assign('BelongtomeResult', $belongtome_data);
                break;
            case 'statistics':

                break;
        }

        $this->assign('tab', $tab);
        $this->display();
    }

    /**
     * 写入发起审批的数据
     * 返回写入的结果，成功返回添加成功的id，失败则返回0
     */
    public function SaveInitiatedApproval(){

        if( IS_POST ){

            $model = new Model();

            $approval_detail_data['title'] = I('post.title');
            $approval_detail_data['set_id'] = I('post.set_id');
            $approval_detail_data['info'] = I('post.info');
            $approval_detail_data['create_person'] = session('user')['id'];
            $approval_detail_data['create_time'] = time();

            $id = $model->table(C('DB_PREFIX').'approval_detail')->add( $approval_detail_data );

            if( $id ){  # 当审批基本数据写入成功后，将该审批对应的集数据和审批人数据写入中间表

                # 获取到当前选中集的第一个步骤的id
                $step_id = $model->table(C('DB_PREFIX').'approval_step')->field('id')->where( ['a_id'=>I('post.set_id'), 'step'=>1] )->find()['id'];

                $approval_middle_data['middle_detail'] = $id;
                $approval_middle_data['middle_person'] = I('post.person');
                $approval_middle_data['middle_step'] = $step_id;

                $middle_id = $model->table(C('DB_PREFIX').'approval_middle')->add( $approval_middle_data );

                $middle_id ? $this->ajaxReturn( ['flag'=>$id, 'msg'=>'添加成功'] ) : $this->ajaxReturn( ['flag'=>0, 'msg'=>'添加失败'] );

            }else{

                $this->ajaxReturn( ['flag'=>0, 'msg'=>'添加失败'] );

            }

        }
    }

    /**
     * select 首页监听数据
     */
    public function category(){

        $model = new model();

        $tmpSe = I('post.id');
        # 监听选择集数据
        if(IS_POST){
            $rel = $model->table(C('DB_PREFIX').'approval')->field('name aName,id')->where('id ='.$tmpSe)->select();
            foreach ($rel as $key=>&$value){
                $value['step'] = $model->table(C('DB_PREFIX').'approval_step')->where('a_id ='.$value['id'])->select();
            }

        }

        $tmpRel = json_encode($rel);
        $this->ajaxReturn($tmpRel);

    }

    /**
     * 第一步开始操作人
     */
    public function person(){

        $model = new model();
        $tmpSe = I('post.id');

        # 选择后的部门或岗位人员
        $tmpPerson = $model->table(C('DB_PREFIX').'approval a,'.C('DB_PREFIX').'approval_step b')->where('a.id = b.a_id AND a.id='.$tmpSe)->order('b.id ASC')->limit(1)->select();

        if($tmpPerson[0]['type'] == 'department') {
            $person = $model->table(C('DB_PREFIX') . 'user a,' . C('DB_PREFIX') . 'department b')->where(' a.department= b.id AND b.id='.$tmpPerson[0]['position'].' AND a.state = 1')->field('a.nickname,a.id,a.email')->select();
        }else{
            $person = $model->table(C('DB_PREFIX') . 'user a,' . C('DB_PREFIX') . 'position b')->where(' a.position= b.id AND b.id='.$tmpPerson[0]['position'].' AND a.state = 1')->field('a.nickname,a.id,a.email')->select();
        }

        $this->ajaxReturn($person);
    }

    /**
     * select add监听数据
     */
    public function aprType(){

        $model = new model();

        $type = I('post.type');

        if(IS_POST){
            if($type === '部门'){

                $position = $model->table(C('DB_PREFIX').'department')->field('id,name')->select();

            }else{

                $position = $model->table(C('DB_PREFIX').'position')->field('id,name')->select();

            }

            $this->ajaxReturn($position);
        }

    }

    /**
     * 添加步骤集页面
     */
    public function add(){

        if (IS_POST){

            $postBasic = I('post.basic');
            $postStep = I('post.step');

            $model = new model();
            $model->startTrans();

            # 集表数据
            $approval['name'] = $postBasic['set_name'];
            $approval['create_person'] = session('user')['id'];
            $approval['archive_dir'] = $postBasic['archive_dir'];

            $approval_id = $model->table(C('DB_PREFIX').'approval')->add($approval);

            if( $approval_id ){
                # 收集步骤表数据
                foreach ($postStep as $key=>&$value){
                    $tmpStep['name'] = $value['step_name'];
                    $tmpStep['step'] = $key+1;
                    $tmpStep['type'] = $value['type'];
                    $tmpStep['transfer'] = $value['transfer'];
                    $tmpStep['refuse'] = $value['refuse'];
                    $tmpStep['a_id'] = $approval_id;

                    if(!empty($value['department'])){
                        $tmpStep['position'] =$value['department'];
                    }else{
                        $tmpStep['position'] =$value['position'];
                    }

                    $a_step[] = array_merge($tmpStep);
                }

                $step_id = $model->table(C('DB_PREFIX').'approval_step')->addAll($a_step);

                if($step_id){
                    $model->commit();
                    $this->ajaxReturn( ['flag'=>1,'msg'=>'添加成功!'] );

                }else{
                    $model->rollback();
                    $this->ajaxReturn( ['flag'=>0,'msg'=>'添加失败!'] );
                }
            }
        }


        $this->display();
    }

    /**
     * 审批详情页
     */
    public function detail(){
        if( I('get.id') && I('get.id') != '' && preg_match('/^[1-9]([0-9]+)?$/', I('get.id'))){

            $model = new Model();

            $detail_data = $model->table(C('DB_PREFIX').'approval_detail')->find( I('get.id') );

            $detail_data['attachment'] = json_decode($detail_data['attachment'], true);

            $detail_data['set'] = $model->table(C('DB_PREFIX').'approval')->where(['id'=>$detail_data['set_id']])->find();

            $detail_data['steps'] = $model->table(C('DB_PREFIX').'approval_step')->where(['a_id'=>$detail_data['set']['id']])->select();

            //$detail_data['current'] = $detail_data['steps'][count($detail_data['steps'])-1];

            $detail_data['middle'] = $model->table(C('DB_PREFIX').'approval_middle')->where(['middle_detail'=>I('get.id')])->order('middle_time ASC')->select();

            if( !empty($detail_data['middle']) ){
                foreach( $detail_data['steps'] as $key=>&$value){
                    foreach($detail_data['middle'] as $k=>&$v){
                        if( $value['id'] == $v['middle_step'] && $v['middle_time'] != '' ){
                            $value['state'] = 'completed';
                            $value['operator'] = $model->table(C('DB_PREFIX').'user')->field('nickname')->find( $v['middle_person'] )['nickname'];
                            $value['complete_time'] = $v['middle_time'];
                        }elseif( $value['id'] == $v['middle_step'] && $v['middle_time'] == '' ){
                            $detail_data['current'] = [
                                'middle_detail'=>$v['middle_detail'],
                                'middle_person'=>$v['middle_person'],
                                'middle_step'=>$v['middle_step'],
                                'middle_time'=>$v['middle_time']
                            ];
                            $value['state'] = 'processing';
                        }else{
                            $value['state'] = 'processing';
                        }
                    }
                }
            }

            $detail_data['user'] = $model->table(C('DB_PREFIX').'user')->field('nickname,face')->find( $detail_data['create_person'] );

            //print_r($detail_data);

            $this->assign('DetailResult', $detail_data);

            $this->display();
        }else{
            $this->error('参数错误');
        }
    }

    /**
     * 插入附件数据
     */
    public function insertAttachment(){
        if( IS_POST ){
            $model = new Model();
            $id = $model->table(C('DB_PREFIX').'approval_detail')->save( ['id'=>I('post.sub_name'), 'attachment'=>I('post.attachments','',false)] );
            $id ? $this->ajaxReturn( ['flag'=>I('post.sub_name'), 'msg'=>'添加成功'] ) : $this->ajaxReturn( ['flag'=>0, 'msg'=>'添加失败'] );
        }
    }

    /**
     * 发起审批页附件上传
     */
    public function uploadAttachment(){
        $subName = I('post.SUB_NAME');
        // 如果需要按id为子目录则必须填写第二个参数，否则直接保存在当前目录
        if( $subName != '' ){
            $result = upload( '/Approval/', $subName );
            $this->ajaxReturn( $result );
        }
   /*     if(!IS_POST) return;
        $info = FileUpload('/Approval/',0,1) ;
        if(!info){ return false; }
        $file['flag'] = 1;
        $file['name'] = $info['Filedata']['name'];
        $file['savepath'] = '/Uploads'.$info['Filedata']['savepath'].$info['Filedata']['name'];
        $file['ext'] = $info['Filedata']['ext'];
        $this->ajaxReturn($file);
        exit;*/
    }


}