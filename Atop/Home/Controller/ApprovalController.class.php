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
                $tmpArr = $model->table(C('DB_PREFIX').'approval')->order('id ASC')->select();

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


                $ongoingData = $model->table(C('DB_PREFIX').'approval_detail a,'.C('DB_PREFIX').'user b')
                    ->where('a.create_person = b.id')
                    ->field('a.id,a.title,a.set_id,a.attachment,a.info,a.create_person,a.create_time,a.current_step,a.state,b.email,b.nickname')
                    ->select();

                foreach ($ongoingData as $key=>&$value) {
                    $value['step'] = $model->table(C('DB_PREFIX').'approval_step a,'.C('DB_PREFIX').'approval_middle b,'.C('DB_PREFIX').'user c,'.C('DB_PREFIX').'approval d')
                                           ->field('d.name t,c.id,c.nickname,c.face,a.id,a.name,a.step,a.type,a.position,a.a_id,a.transfer,a.refuse,b.middle_detail,b.middle_person,b.middle_step,b.middle_refuse')
                                           ->where('a.a_id ='.$value['set_id'].' AND a.step ='.$value['current_step']
                                               .' AND a.id =b.middle_step AND b.middle_detail ='.$value['id'].
                                               ' AND b.middle_refuse = "Y" AND b.middle_person = c.id AND c.id='.session('user')['id'].' AND d.id ='.$value['set_id'])
                                           ->find();

                    /*if(empty($value['step'])){
                        unset($value);
                    }*/


                }




              #  print_r($ongoingData);

                $this->assign('ongoingData', $ongoingData);
                break;
            case 'already':


                break;
            case 'belongtome':
                $belongtome_data = $model->table(C('DB_PREFIX').'approval_detail a,'.C('DB_PREFIX').'approval b')
                                         ->field('a.id detail_id,a.title,a.create_time,b.name,a.state')
                                         ->where( 'a.set_id = b.id AND a.create_person ='.session('user')['id'])
                                         ->select();
                //print_r($belongtome_data);
                foreach( $belongtome_data as $key=>&$value ){
                    $value['current_data'] = $model->table(C('DB_PREFIX').'approval_middle a,'.C('DB_PREFIX').'approval_detail b,'.C('DB_PREFIX').'approval_step c,'.C('DB_PREFIX').'user d')
                                                       ->field('c.name,d.nickname,d.face')
                                                       ->where('a.middle_detail='.$value['detail_id'].' AND a.middle_step=c.id AND a.middle_person = d.id')
                                                       ->order('a.middle_step DESC')
                                                       ->limit(1)
                                                       ->find();
                }

                //print_r($belongtome_data);

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
            $model->startTrans();

            //print_r(I('post.'));

            /*# 判断登录用户是否可以使用当前选择类型
            $tmpStep = $model->table(C('DB_PREFIX').'approval_step')->where('a_id ='.I('post.set_id').' AND step = 1')->find();

            if($tmpStep['type'] == 'department'){
                $tmpFlag = $model->table(C('DB_PREFIX').'department a,'.C('DB_PREFIX').'user b')->field('b.id')->where('a.id = b.department AND a.id ='.$tmpStep['position'])->select();
            }else{
                $tmpFlag = $model->table(C('DB_PREFIX').'position a,'.C('DB_PREFIX').'user b')->field('b.id')->where('a.id = b.position AND a.id ='.$tmpStep['position'])->select();
            }

            $arr = [];
            foreach ($tmpFlag as $key=>&$val){
                array_push($arr,$val['id']);
            }

            if(in_array(session('user')['id'],$arr)){
                $offOn = 1;
            }else{
                $offOn = 2;
            }*/
            //print_r($tmpFlag);

            //exit();
            //die();


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
                $approval_middle_data['middle_person'] = session('user')['id'];
                $approval_middle_data['middle_step'] = $step_id;
                $approval_middle_data['middle_time'] = time();
                $middle_id = $model->table(C('DB_PREFIX').'approval_middle')->add( $approval_middle_data );

                # 获取到当前选中集的第二个步骤的id
                $step2_id = $model->table(C('DB_PREFIX').'approval_step')->field('id')->where( ['a_id'=>I('post.set_id'), 'step'=>2] )->find()['id'];
                $approval_middle2_data['middle_detail'] = $id;
                $approval_middle2_data['middle_person'] = I('post.person');
                $approval_middle2_data['middle_step'] = $step2_id;

                $middle2_id = $model->table(C('DB_PREFIX').'approval_middle')->add( $approval_middle2_data );

                if($middle_id && $middle2_id){

                    $model->commit();
                    $this->ajaxReturn( ['flag'=>$id, 'msg'=>'添加成功'] );

                }else{
                    $model->rollback();
                    $this->ajaxReturn( ['flag'=>0, 'msg'=>'添加失败'] );
                }


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
                $value['step'] = $model->table(C('DB_PREFIX').'approval_step')->where('a_id ='.$value['id'])->order('step ASC')->select();
            }

        }

        /*if($rel[0]['step'][0]['type'] == 'department'){
            $tmpFlag = $model->table(C('DB_PREFIX').'department a,'.C('DB_PREFIX').'user b')->field('b.id')->where('a.id = b.department AND a.id ='.$rel[0]['step'][0]['position'])->select();
        }else{
            $tmpFlag = $model->table(C('DB_PREFIX').'position a,'.C('DB_PREFIX').'user b')->field('b.id')->where('a.id = b.position AND a.id ='.$rel[0]['step'][0]['position'])->select();
        }

        $arr = [];
        foreach ($tmpFlag as $key=>&$val){
            array_push($arr,$val['id']);
        }*/
        //print_r($arr);

       /* if(in_array(session('user')['id'],$arr)){
            $this->ajaxReturn(['flag'=>1]);
        }else{
            $this->ajaxReturn(['flag'=>0,'msg'=>'您无法选择此类别！']);
        }*/

        #print_r(session('user')['id']);
        #print_r($tmpFlag);

        //print_r($rel);

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
        $tmpPerson = $model->table(C('DB_PREFIX').'approval a,'.C('DB_PREFIX').'approval_step b')->where('a.id = b.a_id AND a.id='.$tmpSe.' AND b.step=2')->order('b.id ASC')->limit(1)->select();

        if($tmpPerson[0]['type'] == 'department') {
            $person = $model->table(C('DB_PREFIX') . 'user a,' . C('DB_PREFIX') . 'department b')->where(' a.department= b.id AND b.id='.$tmpPerson[0]['position'].' AND a.state = 1')->field('a.nickname,a.id,a.email')->select();
        }else{
            $person = $model->table(C('DB_PREFIX') . 'user a,' . C('DB_PREFIX') . 'position b')->where(' a.position= b.id AND b.id='.$tmpPerson[0]['position'].' AND a.state = 1')->field('a.nickname,a.id,a.email')->select();
        }

        $this->ajaxReturn($person);
    }

    /**
     * 添加步骤集页面
     */
    public function add(){

        $model = new model();

        if (IS_POST){

            $postBasic = I('post.basic');
            $postStep = I('post.step');

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


        # 所有岗位和部门数据
        $position['department'] = $model->table(C('DB_PREFIX').'department')->field('id,name')->select();
        $position['position'] = $model->table(C('DB_PREFIX').'position')->field('id,name')->select();

# print_r($position);
        $this->assign('position',$position);
        $this->display();
    }

    /**
     * 审批详情页
     */
    public function detail(){
        # 判断地址栏参数正确性
        if( I('get.id') && I('get.id') != '' && preg_match('/^[1-9]([0-9]+)?$/', I('get.id'))){

            $model = new Model();
            # 当前参数提交数据
            $detail_data = $model->table(C('DB_PREFIX').'approval_detail')->find( I('get.id') );
            # 当前参数附件
            $detail_data['attachment'] = json_decode($detail_data['attachment'], true);
            # 当前参数主数据
            $detail_data['set'] = $model->table(C('DB_PREFIX').'approval')->where(['id'=>$detail_data['set_id']])->find();
            # 步骤表数据
            $detail_data['steps'] = $model->table(C('DB_PREFIX').'approval_step')->where(['a_id'=>$detail_data['set']['id']])->select();
            # 记录总步骤
            $detail_data['count'] = $model->table(C('DB_PREFIX').'approval_step')->where(['a_id'=>$detail_data['set']['id']])->count();
            # middle表数据
            $detail_data['middle'] = $model->table(C('DB_PREFIX').'approval_middle')->where(['middle_detail'=>I('get.id')])->order('middle_time ASC')->select();
            # 日志表数据
            $detail_data['log'] = $model->table(C('DB_PREFIX').'approval_log a,'.C('DB_PREFIX').'user b,'.C('DB_PREFIX').'approval_step c')
                                        ->field('b.id,b.nickname,a.log,a.log_time,a.person,a.step,c.name')
                                        ->where('d_id='.I('get.id').' AND a.person = b.id AND c.id=a.s_id')
                                        ->order('log_time ASC')
                                        ->select();

            foreach ($detail_data['log'] as $key=>&$val){
                $val['log'] = htmlspecialchars_decode($val['log']);
            }

            # 判断当前审批进到的步骤
            if( !empty($detail_data['middle']) ){
                foreach( $detail_data['middle'] as $key=>&$value){
                    if( !empty($value['middle_time']) ){
                        if( $value['middle_refuse'] == 'N' ){
                            foreach($detail_data['steps'] as $k=>&$v){
                                if($value['middle_step'] == $v['id']){
                                    $v['state'] = 'refuse';
                                    $v['operator'] = $model->table(C('DB_PREFIX').'user')->field('nickname')->find( $value['middle_person'] )['nickname'];
                                    $v['complete_time'] = $value['middle_time'];
                                }
                            }
                        }else{
                            foreach($detail_data['steps'] as $k=>&$v){
                                if($value['middle_step'] == $v['id']){
                                    $v['state'] = 'completed';
                                    $v['operator'] = $model->table(C('DB_PREFIX').'user')->field('nickname')->find( $value['middle_person'] )['nickname'];
                                    $v['complete_time'] = $value['middle_time'];
                                }
                            }
                        }

                    }else{
                        foreach($detail_data['steps'] as $k=>&$v) {
                            $v['state'] = 'processing';
                            if( $v['id'] == $value['middle_step'] ){
                                $detail_data['current'] = [
                                    'middle_detail'=>$value['middle_detail'],
                                    'middle_person'=>$value['middle_person'],
                                    'middle_step'=>$value['middle_step'],
                                    'middle_time'=>$value['middle_time']
                                ];
                            }

                        }

                    }
                }
            }


            $detail_data['user'] = $model->table(C('DB_PREFIX').'user')->field('nickname,face')->find( $detail_data['create_person'] );

            # 判断用户是否有操作权
            $tmpTr = '';
            if( $detail_data['middle'][0]['middle_detail'] == I('get.id') && $detail_data['middle'][0]['middle_person'] == session('user')['id']){
                $tmpTr = 'true';
            }else{
                $tmpTr = 'false';
            }

            # 获取最大步骤
            $tmpStep= $model->table(C('DB_PREFIX').'approval_step')->where(['a_id'=>$detail_data['set']['id']])->count();
            # 下一步步骤
            $tmpNum = $detail_data['current_step']+1;


            # 获取下一步操作信息
            if($tmpNum > $tmpStep){
                $next_data = $model->table(C('DB_PREFIX').'approval_step')->where('a_id = '.$detail_data['set_id'].' AND step ='.($detail_data['current_step']))->select();
            }else{
                $next_data = $model->table(C('DB_PREFIX').'approval_step')->where('a_id = '.$detail_data['set_id'].' AND step ='.($detail_data['current_step']+1))->select();
            }
            # 判断部门还是岗位
            if($next_data[0]['type'] == 'position'){
                $next_data['person'] = $model->table(C('DB_PREFIX').'user a,'.C('DB_PREFIX').'position b')->field('a.nickname,a.id,a.email')->where('a.position = b.id AND b.id='.$next_data['0']['position'].' AND a.state = 1')->select();
            }else{
                $next_data['person'] = $model->table(C('DB_PREFIX').'user a,'.C('DB_PREFIX').'department b')->field('a.nickname,a.id,a.email')->where('a.department = b.id AND b.id='.$next_data['0']['position'].' AND a.state = 1')->select();
            }

            # 获取当前步骤信息
            $nowData = $model->table(C('DB_PREFIX').'approval_step')->where('a_id = '.$detail_data['set_id'].' AND step ='.($detail_data['current_step']))->select();
            if($nowData[0]['type'] == 'department'){
                $nowData['nickname'] = $model->table(C('DB_PREFIX').'user a,'.C('DB_PREFIX').'department b')
                                             ->field('a.email,a.nickname,a.id')
                                             ->where('a.department = b.id AND b.id ='.$nowData[0]['position'].' AND a.id <>'.session('user')['id'].' AND a.state = 1')
                                             ->select();
            }else{
                $nowData['nickname'] = $model->table(C('DB_PREFIX').'user a,'.C('DB_PREFIX').'position b')
                                             ->field('a.email,a.nickname,a.id')
                                             ->where('a.position = b.id AND b.id ='.$nowData[0]['position'].' AND a.id <>'.session('user')['id'].' AND a.state = 1')
                                             ->select();
            }

            # 获取上一步信息

            $fastData = $model->table(C('DB_PREFIX').'approval_step a,'.C('DB_PREFIX').'user b,'.C('DB_PREFIX').'approval_middle c')
                              ->where('a.a_id ='.$detail_data['set_id'].' AND a.step ='.($detail_data['current_step']-1).' AND c.middle_detail ='.I('get.id').' AND b.id=c.middle_person AND c.middle_step = a.id')
                              ->field('b.id,b.email,b.face,b.nickname')
                              ->find();




            //调用父类注入部门和人员信息
            $this->getAllUsersAndDepartments();

            # print_r($detail_data);
            $this->assign('DetailResult', $detail_data);
            $this->assign('next_data', $next_data);
            $this->assign('fastData', $fastData);
            $this->assign('nowData', $nowData);
            $this->assign('tmpTr', $tmpTr);


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

    }

    public function step(){

        $model = new model();

        # 提交数据
        if(IS_POST){
            $post = I('post.');
            $id = I('post.id');

            # 当前参数提交数据
            $detail_data = $model->table(C('DB_PREFIX').'approval_detail')->find($id);

            # 当前参数附件
            $detail_data['attachment'] = json_decode($detail_data['attachment'], true);
            # 当前参数主数据
            $detail_data['set'] = $model->table(C('DB_PREFIX').'approval')->where(['id'=>$detail_data['set_id']])->find();
            # 步骤表数据
            $detail_data['steps'] = $model->table(C('DB_PREFIX').'approval_step')->where(['a_id'=>$detail_data['set']['id']])->select();
            # middle表数据
            $detail_data['middle'] = $model->table(C('DB_PREFIX').'approval_middle a,'.C('DB_PREFIX').'user b')->where('middle_detail='.$id.' AND a.middle_person = b.id')->field('b.email,b.nickname,a.middle_person,a.middle_detail,a.middle_step,a.middle_refuse,a.middle_time')->order('middle_time ASC')->select();
            # 记录总步骤
            $detail_data['count'] = $model->table(C('DB_PREFIX').'approval_step')->where(['a_id'=>$detail_data['set']['id']])->count();
            # 获取当前操作信息
            $nowData = $model->table(C('DB_PREFIX').'approval_step')->where('a_id = '.$detail_data['set_id'].' AND step ='.($detail_data['current_step']))->select();
            # 下一步操作信息
            $next_data = $model->table(C('DB_PREFIX').'approval_step')->where('a_id = '.$detail_data['set_id'].' AND step ='.($detail_data['current_step']+1))->select();
            $detail_data['nowData'] = $nowData;
            $detail_data['nextData'] = $next_data;

            switch ($post['data']['apr_action']){
                # 通过
                case 'pass' :
                    # 开启事物
                    $model->startTrans();
                    $middle['middle_time'] = time();
                    $detail_data['middle_time'] = time();

                    # 获取提交的抄送人
                    $ccEmail = html_entity_decode($post['email']);
                    $cc = json_decode($ccEmail,true);
                    $ccArr = [];
                    if(!empty($cc)){
                        foreach ($cc as $key=>&$value) {
                            array_push($ccArr,$value['email']);
                        }
                    }

                    # 完成后写入完成的时间
                    $save_time_id = $model->table(C('DB_PREFIX').'approval_middle')->where('middle_detail ='.$id.' AND middle_step ='.$nowData[0]['id'])->save($middle);
                    # 写入第二步的数据
                    $middleNext['middle_detail'] = $id;
                    $middleNext['middle_person'] = $post['data']['next_person'];
                    $middleNext['nickname'] = $model->table(C('DB_PREFIX').'user')->field('nickname,email')->where('id='.$post['data']['next_person'])->find();
                    # 抄送人邮箱
                    $email = $model->table(C('DB_PREFIX').'user')->field('email')->where('id='.$post['data']['next_person'])->find();
                    $middleNext['middle_step'] = $next_data[0]['id'];
                    $detail_data['middleNext'] = $middleNext;
                    $save_next_id = $model->table(C('DB_PREFIX').'approval_middle')->where('middle_id ='.$id.' AND middle_step ='.$next_data[0]['id'])->add($middleNext);
                    # 完成时留下的log
                    $save_log['log'] = $post['dit'];
                    $save_log['log_time'] = time();
                    $save_log['person'] = session('user')['id'];
                    $save_log['step'] = $nowData[0]['step'];
                    $save_log['d_id'] = $id;
                    $save_log['s_id'] = $nowData[0]['id'];
                    $detail_data['save_log'] = $save_log;
                    $pass_log_id = $model->table(C('DB_PREFIX').'approval_log')->add($save_log);
                    # 完成后修改当前步骤
                    if( $next_data[0]['step'] == $detail_data['count'] ){
                        $now_step['current_step'] = $nowData[0]['step'];
                    }else{
                        $now_step['current_step'] = $next_data[0]['step'];
                    }
                    $now_step['current_step'] = $next_data[0]['step'];
                    $save_now_step_id = $model->table(C('DB_PREFIX').'approval_detail')->where('id ='.$id)->save($now_step);


                    foreach ($detail_data['middle'] as $key=>&$val){
                        array_push($ccArr,$val['email']);
                    }

                    if($save_time_id && $save_next_id && $pass_log_id && $save_now_step_id){
                        $model->commit();
                        $this->pushEmail('PASS',$email,$detail_data,$ccArr);
                        $this->ajaxReturn(['flag'=>1,'msg'=>'操作成功','action'=>'通过']);
                    }else{
                        $model->rollback();
                        $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败']);
                    }

                    break;
                # 完成
                case 'success' :
                    # 开启事物
                    $model->startTrans();
                    $middle['middle_time'] = time();

                    # 获取提交的抄送人
                    $ccEmail = html_entity_decode($post['email']);
                    $cc = json_decode($ccEmail,true);
                    $ccArr = [];
                    if(!empty($cc)){
                        foreach ($cc as $key=>&$value) {
                            array_push($ccArr,$value['email']);
                        }
                    }

                    # 完成后写入完成的时间
                    $save_time_id = $model->table(C('DB_PREFIX').'approval_middle')->where('middle_detail ='.$id.' AND middle_step ='.$nowData[0]['id'])->save($middle);
                    # 完成时留下的log
                    $save_log['log'] = $post['dit'];
                    $save_log['log_time'] = time();
                    $save_log['person'] = session('user')['id'];
                    $save_log['step'] = $nowData[0]['step'];
                    $save_log['d_id'] = $id;
                    $save_log['s_id'] = $nowData[0]['id'];
                    $pass_log_id = $model->table(C('DB_PREFIX').'approval_log')->add($save_log);
                    $detail_data['save_log'] = $save_log;

                    $save_state['state'] = 'C';
                    $save_state_id = $model->table(C('DB_PREFIX').'approval_detail')->where('id ='.$id)->save($save_state);


                    # 获取创建人信息
                    $detail_data['first_person'] = $model->table(C('DB_PREFIX').'user a,'.C('DB_PREFIX').'approval_middle b')
                        ->where('a.id = b.middle_person AND b.middle_detail ='.$detail_data['id'])
                        ->field('a.nickname,a.id,a.email')
                        ->order('b.middle_step')
                        ->limit(1)
                        ->find();
                    $detail_data['middleNext']['nickname']['nickname'] = $detail_data['first_person']['nickname'];
                    $email = $detail_data['first_person']['email'];
                    # 获取抄送人信息
                    $detail_data['before_data'] = $model->table(C('DB_PREFIX').'approval_middle a,'.C('DB_PREFIX').'user b,'.C('DB_PREFIX').'approval_step c')
                        ->field('c.name,b.email,b.nickname,a.middle_detail,a.middle_person,a.middle_step,a.middle_refuse,a.middle_time,c.step')
                        ->where('middle_detail='.$post['id'].' AND middle_step ='.($nowData[0]['id']-1).' AND b.id = a.middle_person AND a.middle_step = c.id')
                        ->find();
                    //$cc = $detail_data['before_data']['email'];


                    foreach ($detail_data['middle'] as $key=>&$val){
                        array_push($ccArr,$val['email']);
                    }


/*//print_r($persons);
print_r($detail_data);
//print_r($emails);
die();
exit();*/

                    if( $save_time_id && $pass_log_id && $save_state_id){
                        $this->pushEmail('SUCCESS',$email,$detail_data,$ccArr);
                        $model->commit();
                        $this->ajaxReturn(['flag'=>3,'msg'=>'操作成功','action'=>'完成']);
                    }else{
                        $model->rollback();
                        $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败']);
                    }

                    break;
                # 转交
                case 'transfer' :

                    $model->startTrans();

                    # 获取提交的抄送人
                    $ccEmail = html_entity_decode($post['email']);
                    $cc = json_decode($ccEmail,true);
                    $ccArr = [];
                    if(!empty($cc)){
                        foreach ($cc as $key=>&$value) {
                            array_push($ccArr,$value['email']);
                        }
                    }

                    $person = $model->table(C('DB_PREFIX').'user')->field('id,nickname,email')->where('id='.$post['data']['transfer_user'])->find();
                    $nowPerson = $model->table(C('DB_PREFIX').'approval_middle')->where(['middle_detail'=>$id])->order('middle_time ASC')->find();
                    $persons['middle_person'] = $person['id'];
                    $detail_data['transfer_person'] = $model->table(C('DB_PREFIX').'user')->where('id ='.$person['id'])->field('nickname,id,email')->find();
                    $detail_data['middleNext']['nickname']['nickname'] = $detail_data['transfer_person']['nickname'];
                    # 写入被转交的人
                    $save_person_id = $model->table(C('DB_PREFIX').'approval_middle')->where('middle_detail ='. $id .' AND middle_step ='.$nowData[0]['id'])->save($persons);
                    # 完成时留下的log
                    $save_log['log'] = $post['dit'];
                    $save_log['log_time'] = time();
                    $save_log['person'] = session('user')['id'];
                    $save_log['step'] = $nowData[0]['step'];
                    $save_log['d_id'] = $id;
                    $save_log['s_id'] = $nowData[0]['id'];

                    $detail_data['save_log'] = $save_log;
                    $transfer_log_id = $model->table(C('DB_PREFIX').'approval_log')->add($save_log);

                    $email = $detail_data['transfer_person']['email'];

                    foreach ($detail_data['middle'] as $key=>&$val){
                        array_push($ccArr,$val['email']);
                    }

                    if($save_person_id && $transfer_log_id ){
                        $this->pushEmail('TRANSFER',$email,$detail_data,$ccArr);
                        $model->commit();
                        $this->ajaxReturn(['flag'=>1,'msg'=>'操作成功','action'=>'转交']);
                    }else{
                        $model->rollback();
                        $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败!']);
                    }

                    break;
                # 回退
                case 'rollback' :

                    # 获取提交的抄送人
                    $ccEmail = html_entity_decode($post['email']);
                    $cc = json_decode($ccEmail,true);
                    $ccArr = [];
                    if(!empty($cc)){
                        foreach ($cc as $key=>&$value) {
                            array_push($ccArr,$value['email']);
                        }
                    }
                    if($post['data']['rollback_user']){
                        $model->startTrans();
                        # 删除当前进度middle数据
                        $del_middle_id = $model->table(C('DB_PREFIX').'approval_middle')->where('middle_detail='.$post['id'].' AND middle_step ='.$nowData[0]['id'])->delete();
                        # 清空上一步middle时间
                        $fast_middle['middle_time'] = null;
                        $save_middle_time = $model->table(C('DB_PREFIX').'approval_middle')->where('middle_detail='.$post['id'].' AND middle_step ='.($nowData[0]['id']-1))->save($fast_middle);
                        # 获取上一步信息
                        $detail_data['before_data'] = $model->table(C('DB_PREFIX').'approval_middle a,'.C('DB_PREFIX').'user b,'.C('DB_PREFIX').'approval_step c')
                                                            ->field('c.name,b.email,b.nickname,a.middle_detail,a.middle_person,a.middle_step,a.middle_refuse,a.middle_time,c.step')
                                                            ->where('middle_detail='.$post['id'].' AND middle_step ='.($nowData[0]['id']-1).' AND b.id = a.middle_person AND a.middle_step = c.id')
                                                            ->find();
                        # 修改当前步骤
                        $nowCurrent['current_step'] = $nowData[0]['step']-1;
                        $now_current_id = $model->table(C('DB_PREFIX').'approval_detail')->where('id ='.$post['id'])->save($nowCurrent);
                        # 回退是留下的日志
                        $save_log['log'] = $post['dit'];
                        $save_log['log_time'] = time();
                        $save_log['person'] = session('user')['id'];
                        $save_log['step'] = $nowData[0]['step'];
                        $save_log['d_id'] = $id;
                        $save_log['s_id'] = $nowData[0]['id'];

                        $detail_data['save_log'] = $save_log;
                        $transfer_log_id = $model->table(C('DB_PREFIX').'approval_log')->add($save_log);

                        # 收件人邮箱
                        $emails = $detail_data['before_data']['email'];
                        $detail_data['middleNext']['nickname']['nickname'] = $email = $detail_data['before_data']['nickname'];

                        foreach ($detail_data['middle'] as $key=>&$val){
                            array_push($ccArr,$val['email']);
                        }

                        if( $del_middle_id && $save_middle_time && $now_current_id && $transfer_log_id){
                            $this->pushEmail('ROLLBACK',$emails,$detail_data,$ccArr);
                            $model->commit();
                            $this->ajaxReturn(['flag'=>1,'msg'=>'操作成功!']);
                        }else{
                            $model->rollback();
                            $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败!']);
                            exit();
                        }

                    }else{
                        $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败!']);
                        exit();
                    }

                    break;
                # 拒绝
                case 'refuse':

                    $model->startTrans();

                    # 获取提交的抄送人
                    $ccEmail = html_entity_decode($post['email']);
                    $cc = json_decode($ccEmail,true);
                    $ccArr = [];
                    if(!empty($cc)){
                        foreach ($cc as $key=>&$value) {
                            array_push($ccArr,$value['email']);
                        }
                    }
                    $nowData[0]['state'] = 'refuse';
                    # 修改状态
                    $detail_state['state'] = 'N';
                    $saveStateId_id = $model->table(C('DB_PREFIX').'approval_detail')->where('id ='.$post['id'])->save($detail_state);
                    # 留下日志
                    $save_log['log'] = $post['dit'];
                    $save_log['log_time'] = time();
                    $save_log['person'] = session('user')['id'];
                    $save_log['step'] = $nowData[0]['step'];
                    $save_log['d_id'] = $id;
                    $save_log['s_id'] = $nowData[0]['id'];

                    $detail_data['save_log'] = $save_log;
                    $transfer_log_id = $model->table(C('DB_PREFIX').'approval_log')->add($save_log);

                    $middle['middle_time'] = time();
                    $middle['middle_refuse'] = 'N';
                    # 拒绝的时间
                    $save_refuseTime_id = $model->table(C('DB_PREFIX').'approval_middle')->where('middle_detail ='.$id.' AND middle_step ='.$nowData[0]['id'])->save($middle);

                    # 获取创建人信息
                    $detail_data['first_person'] = $model->table(C('DB_PREFIX').'user a,'.C('DB_PREFIX').'approval_middle b')
                                                         ->where('a.id = b.middle_person AND b.middle_detail ='.$detail_data['id'])
                                                         ->field('a.nickname,a.id,a.email')
                                                         ->order('b.middle_step')
                                                         ->limit(1)
                                                         ->find();
                    $detail_data['middleNext']['nickname']['nickname'] = $detail_data['first_person']['nickname'];
                    $email = $detail_data['first_person']['email'];

                    # 获取抄送人信息
                    $detail_data['before_data'] = $model->table(C('DB_PREFIX').'approval_middle a,'.C('DB_PREFIX').'user b,'.C('DB_PREFIX').'approval_step c')
                        ->field('c.name,b.email,b.nickname,a.middle_detail,a.middle_person,a.middle_step,a.middle_refuse,a.middle_time,c.step')
                        ->where('middle_detail='.$post['id'].' AND middle_step ='.($nowData[0]['id']-1).' AND b.id = a.middle_person AND a.middle_step = c.id')
                        ->find();
                    //$cc = $detail_data['before_data']['email'];

                    foreach ($detail_data['middle'] as $key=>&$val){
                        array_push($ccArr,$val['email']);
                    }

                    if($saveStateId_id && $transfer_log_id && $save_refuseTime_id){
                        $this->pushEmail('REFUSE',$email,$detail_data,$ccArr);
                        $model->commit();
                        $this->ajaxReturn(['flag'=>4,'msg'=>'操作成功!','action'=>'拒绝']);

                    }else{
                        $model->rollback();
                        $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败!']);
                    }


                    break;
            }
        }
    }


    # 邮件推送
    public function pushEmail( $type, $address, $data,$cc){

        $http_host = $_SERVER['HTTP_HOST'];
        extract($data);

        # 如果是单人则显示收件人姓名否则群发显示All
        if( isset($data['middleNext']['nickname']['nickname']) ){
            $call = $data['middleNext']['nickname']['nickname'];
        }else{
            $call = 'All';
        }


        $subject = '【通用评审】 ' .$data['title']. ' 步骤更新';

        $order_basic = '<p>Dear '.$call.', </p>
                        <p>评审 <b>[ '. $data['title'] .' ]</b> 有步骤更新，请及时处理！</p>
                        <span>描述 ：'.$data['info'].'</span><br>
                        <p>详情请点击链接：<a href="http://'.$http_host.'/Approval/detail/id/'.$data['id'].'" target="_blank">http://'.$http_host.'/Approval/detail/id/'.$data['id'].'</a></p>
';


        switch( $type ) {

            # 通过
            case 'PASS':

                $body = '<p>['.$data['middle'][0]['nickname'].'] 将 <b>'.$data['title'].'</b> 由 <span style="padding: 3px 5px; border-radius:1px;border: 1px solid #555;">Step'.$data['nowData'][0]['step'].' - '.$data['nowData'][0]['name'].'</span> 推送到 <span style="padding: 3px 5px; border-radius:1px;border: 1px solid #555;">Step'.$data['nextData'][0]['step'].' - '.$data['nextData'][0]['name'].'</span>，请及时处理！</p>
                <span><b>内容 ： </b>'.htmlspecialchars_decode($data['save_log']['log']).'</span>
                ';

                break;
            # 完成
            case 'SUCCESS':
                $body = '<p>['.$call.'] 您发起的 <b>'.$data['title'].'</b> 评审已完成！评审人 ['.$data['middle'][0]['nickname'].']</p>
                <span><b>内容 ： </b><br>'.htmlspecialchars_decode($data['save_log']['log']).'</span>
                ';
                break;

            # 回退
            case 'ROLLBACK':

                $body = '<p>['.$data['middle'][0]['nickname'].'] 将 <b>'.$data['title'].'</b> 由 <span style="padding: 3px 5px; border-radius:1px;border: 1px solid #555;">Step'.$data['nowData'][0]['step'].' - '.$data['nowData'][0]['name'].'</span> 回退到 <span style="padding: 3px 5px; border-radius:1px;border: 1px solid #555;">Step'.$data['before_data']['step'].' - '.$data['before_data']['name'].'</span>，请及时处理！</p>
                <span><b>内容 ： </b>'.htmlspecialchars_decode($data['save_log']['log']).'</span>
                ';
                break;

            # 转交
            case 'TRANSFER':
                $order_basic = '<p>Dear '.$call.', </p>
                        <p>评审 <b>[ '. $data['title'] .' ]</b> 有转交信息，请及时处理！</p>
                        <span>描述 ：'.$data['info'].'</span><br>
                ';
                $body = '<p>['.$data['middle'][0]['nickname'].'] 将 <b>'.$data['title'].'</b> 转交给您，请您及时处理！当前步骤 <span style="padding: 3px 5px; border-radius:1px;border: 1px solid #555;">Step'.$data['nowData'][0]['step'].' - '.$data['nowData'][0]['name'].'</span> </p>
                <span><b>内容 ： </b><br>'.htmlspecialchars_decode($data['save_log']['log']).'</span>
                ';

                break;

            #拒绝
            case 'REFUSE':

                $body = '<p>['.$data['middle'][0]['nickname'].'] 拒绝了您发起的 <b>'.$data['title'].'</b> 评审！当前步骤 <span style="padding: 3px 5px; border-radius:1px;border: 1px solid #555;">Step'.$data['nowData'][0]['step'].' - '.$data['nowData'][0]['name'].'</span> </p>
                <span><b>内容 ： </b><br>'.htmlspecialchars_decode($data['save_log']['log']).'</span>
                ';
                break;
        }

        # 检查邮件发送结果
        if( $cc == '' ){
            $result = send_Email( $address, '', $subject, $order_basic.$body );
            if( $result != 1 ){
                $this->ajaxReturn( ['flag'=>0,'msg'=>'邮件发送失败'] );
            }
        }else{
            $result = send_Email( $address, '', $subject, $order_basic.$body, $cc);   # $cc
            if( $result != 1 ){
                $this->ajaxReturn( ['flag'=>0,'msg'=>'邮件发送失败'] );
            }
        }



    }













}