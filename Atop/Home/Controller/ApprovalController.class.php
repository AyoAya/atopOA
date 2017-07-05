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

                break;
            case 'already':

                break;
            case 'belongtome':
                $belongtome_data = $model->table(C('DB_PREFIX').'approval_detail a,'.C('DB_PREFIX').'approval b')
                                         ->field('a.id detail_id,a.title,a.create_time,b.name,a.state')
                                         ->where( 'a.set_id = b.id' )
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
            $detail_data['middle'] = $model->table(C('DB_PREFIX').'approval_middle')->where(['middle_detail'=>$id])->order('middle_time ASC')->select();
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
                    # 完成后写入完成的时间
                    $save_time_id = $model->table(C('DB_PREFIX').'approval_middle')->where('middle_detail ='.$id.' AND middle_step ='.$nowData[0]['id'])->save($middle);
                    # 写入第二步的数据
                    $middleNext['middle_detail'] = $id;
                    $middleNext['middle_person'] = $post['data']['next_person'];
                    $middleNext['middle_step'] = $next_data[0]['id'];
                    $save_next_id = $model->table(C('DB_PREFIX').'approval_middle')->where('middle_id ='.$id.' AND middle_step ='.$next_data[0]['id'])->add($middleNext);
                    # 完成时留下的log
                    $save_log['log'] = $post['dit'];
                    $save_log['log_time'] = time();
                    $save_log['person'] = session('user')['id'];
                    $save_log['step'] = $nowData[0]['step'];
                    $save_log['d_id'] = $id;
                    $save_log['s_id'] = $nowData[0]['id'];
                    $pass_log_id = $model->table(C('DB_PREFIX').'approval_log')->add($save_log);
                    # 完成后修改当前步骤
                    if( $next_data[0]['step'] == $detail_data['count'] ){
                        $now_step['current_step'] = $nowData[0]['step'];
                    }else{
                        $now_step['current_step'] = $next_data[0]['step'];
                    }
                    $now_step['current_step'] = $next_data[0]['step'];
                    $save_now_step_id = $model->table(C('DB_PREFIX').'approval_detail')->where('id ='.$id)->save($now_step);

                    if($save_time_id && $save_next_id && $pass_log_id && $save_now_step_id){
                        $model->commit();
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

                    $save_state['state'] = 'C';
                    $save_state_id = $model->table(C('DB_PREFIX').'approval_detail')->where('id ='.$id)->save($save_state);

                    if( $save_time_id && $pass_log_id && $save_state_id){
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

                    $person = $model->table(C('DB_PREFIX').'user')->field('id,nickname,email')->where('id='.$post['data']['transfer_user'])->find();
                    $nowPerson = $model->table(C('DB_PREFIX').'approval_middle')->where(['middle_detail'=>$id])->order('middle_time ASC')->find();
                    $persons['middle_person'] = $person['id'];
                    # 写入被转交的人
                    $save_person_id = $model->table(C('DB_PREFIX').'approval_middle')->where('middle_detail ='. $id .' AND middle_step ='.$nowData[0]['id'])->save($persons);
                    # 完成时留下的log
                    $save_log['log'] = $post['dit'];
                    $save_log['log_time'] = time();
                    $save_log['person'] = session('user')['id'];
                    $save_log['step'] = $nowData[0]['step'];
                    $save_log['d_id'] = $id;
                    $save_log['s_id'] = $nowData[0]['id'];
                    $transfer_log_id = $model->table(C('DB_PREFIX').'approval_log')->add($save_log);

                    if($save_person_id && $transfer_log_id ){
                        $model->commit();
                        $this->ajaxReturn(['flag'=>1,'msg'=>'操作成功','action'=>'转交']);
                    }else{
                        $model->rollback();
                        $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败!']);
                    }

                    break;
                # 回退
                case 'rollback' :

                    if($post['data']['rollback_user']){
                        $model->startTrans();
                        # 删除当前进度middle数据
                        $del_middle_id = $model->table(C('DB_PREFIX').'approval_middle')->where('middle_detail='.$post['id'].' AND middle_step ='.$nowData[0]['id'])->delete();
                        # 清空上一步middle时间
                        $fast_middle['middle_time'] = null;
                        $save_middle_time = $model->table(C('DB_PREFIX').'approval_middle')->where('middle_detail='.$post['id'].' AND middle_step ='.($nowData[0]['id']-1))->save($fast_middle);
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
                        $transfer_log_id = $model->table(C('DB_PREFIX').'approval_log')->add($save_log);

                        if( $del_middle_id && $save_middle_time && $now_current_id && $transfer_log_id){
                            $model->commit();
                            $this->ajaxReturn(['flag'=>1,'msg'=>'操作成功','action'=>'回退']);
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
                    $transfer_log_id = $model->table(C('DB_PREFIX').'approval_log')->add($save_log);

                    $middle['middle_time'] = time();
                    $middle['middle_refuse'] = 'N';
                    # 拒绝的时间
                    $save_refuseTime_id = $model->table(C('DB_PREFIX').'approval_middle')->where('middle_detail ='.$id.' AND middle_step ='.$nowData[0]['id'])->save($middle);
                    if($saveStateId_id && $transfer_log_id && $save_refuseTime_id){

                        $this->ajaxReturn(['flag'=>4,'msg'=>'操作成功!','action'=>'拒绝']);

                    }else{
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
        if( isset($recipient_name) ){
            $call = $recipient_name;
        }else{
            $call = 'All';
        }

        $order_basic = <<<BASIC
BASIC;


        switch( $type ) {
            case 'PASS':

                break;
            case 'SUCCESS':
                $body = <<<HTML
HTML;
                break;
            case 'ROLLBACK':

                $body = <<<HTML
HTML;

                break;
            case 'TRANSFER':

                $body = <<<HTML

HTML;

                break;
            case 'REFUSE':

                $body = <<<HTML
HTML;
                break;
        }

        # 检查邮件发送结果
        if( $cc == '' ){
            $result = send_Email( $address, '', $subject, $body.$order_basic );
            if( $result != 1 ){
                $this->ajaxReturn( ['flag'=>0,'msg'=>'邮件发送失败'] );
            }
        }else{
            $result = send_Email( $address, '', $subject, $body.$order_basic, $cc);   # $cc
            if( $result != 1 ){
                $this->ajaxReturn( ['flag'=>0,'msg'=>'邮件发送失败'] );
            }
        }



    }













}