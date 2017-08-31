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

        $model = new model();

        /*if(I('get.search')){
            $count = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'file_log b,'.C('DB_PREFIX').'file_ecn c')
                           ->where('(c.id = b.n_id AND b.action = "initiate" AND a.file_no LIKE "%'.I('get.search').'%") OR (c.id = b.n_id AND b.action = "initiate" AND b.nickname LIKE "%'.I('get.search').'%")')
                           ->count();

            $this->assign( 'search' , I('get.search') );

        }else{

            $count = $model->table(C('DB_PREFIX').'file_ecn')->count();

        }*/

        $count = $model->table(C('DB_PREFIX').'file_ecn')->count();
        # 数据分页
        $page = new Page($count,10);
        $page->setConfig('prev','<span aria-hidden="true">上一页</span>');
        $page->setConfig('next','<span aria-hidden="true">下一页</span>');
        $page->setConfig('first','<span aria-hidden="true">首页</span>');
        $page->setConfig('last','<span aria-hidden="true">尾页</span>');

        if(C('PAGE_STATUS_INFO')){
            $page->setConfig ( 'theme', '<li><a href="javascript:void(0);">当前%NOW_PAGE%/%TOTAL_PAGE%</a></li>  %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%' );
        }

        # 根据条件筛选数据
        if(I('get.search')){
            /*$indexData = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'file_log b')
                               ->where('(a.id = b.n_id AND b.action = "apply" AND a.file_no LIKE "%'.I('get.search').'%") OR (a.id = b.n_id AND (b.action = "apply" OR b.action = "upgrade") AND b.nickname LIKE "%'.I('get.search').'%")')
                               ->order('a.file_no ASC')
                               ->limit($page->firstRow.','.$page->listRows)
                               ->select();*/
            $indexData = $model->table(C('DB_PREFIX').'file_ecn')
                               ->limit($page->firstRow.','.$page->listRows)
                               ->select();
        }else{

            $indexData = $model->table(C('DB_PREFIX').'file_ecn')
                               ->limit($page->firstRow.','.$page->listRows)
                               ->select();


            foreach ($indexData as $key=>&$value){
                $value['tmpArr'] = explode(',',$value['n_id']);
                $value['log'] = $model->table(C('DB_PREFIX').'file_log')
                                      ->where('n_id ='.$value['id'].' AND action = "initiate"')
                                      ->field('time,nickname')
                                      ->find();
            }
             #   print_r($indexData);
            foreach ($indexData as $key=>&$value){
                foreach ($value['tmpArr'] as $k=>&$v){
                    $value['number'][] = $model->table(C('DB_PREFIX').'file_number')->where('id ='.$v)->find();
                }
                $value['colSpan'] = count($value['number']);
                $len = 10;
                $type = 'ECN';
                $ecnId = $value['id'];
                $bit = $len-strlen($type);
                $num_len = strlen($ecnId);
                $zero = '';
                for($i=$num_len; $i<$bit; $i++){
                    $zero .= "0";
                }

                $value['ecn'] = $type.$zero.$ecnId;
                # echo $real_num;
            }

            $tmpIndexData = [];
            foreach ($indexData as $kk=>&$vv){
                foreach ($vv['number'] as $ke=>&$va){
                    $tmpIndex = [];
                    $tmpIndex['eid'] = $vv['id'];
                    $tmpIndex['id'] = $va['id'];
                    $tmpIndex['file_no'] = $va['file_no'];
                    $tmpIndex['status'] = $vv['status'];
                    $tmpIndex['attachment'] = $va['attachment'];
                    $tmpIndex['num'] = $va['num'];
                    $tmpIndex['version'] = $va['version'];
                    $tmpIndex['content'] = $va['content'];
                    $tmpIndex['step'] = $va['step'];
                    $tmpIndex['time'] = $vv['log']['time'];
                    $tmpIndex['nickname'] = $vv['log']['nickname'];
                    if($ke < 1 ){
                        $tmpIndex['colSpan'] = $vv['colSpan'];
                    }
                    $tmpIndex['ecn'] = $vv['ecn'];
                    array_push($tmpIndexData,$tmpIndex);
                }
            }


        }

        $pageShow = $page->show();

        if(I('get.p')){
            $this->assign('pageNumber',I('get.p')-1);
        }

        # print_r($indexData);
        # print_r($tmpIndexData);

        $this->assign('indexData',$indexData);
        $this->assign('tmpIndexData',$tmpIndexData);
        $this->assign('page',$pageShow);
        $this->display();
    }


    /**
     * 添加类型
     */
    public function add(){

        $position = M('Position')->field('id,name')->select();

        if(IS_POST){

            $post = I('post.');

            $rule['type'] = $post['type'];
            $rule['length'] = $post['length'];
            $rule['info'] = $post['info'];

            $tmpRel = M('FileRule')->where("type = '".$post['type']."'")->select();

            if($tmpRel){
                $this->ajaxReturn(['flag'=>0,'msg'=>'已有此类型文件']);
                exit();
            }else{

                $rule_id =  M('FileRule')->add($rule);

                if($rule_id){
                    $this->ajaxReturn(['flag'=>1,'msg'=>'添加成功']);
                }else{

                    $this->ajaxReturn(['flag'=>0,'msg'=>'添加失败']);
                }
            }

        }

        $this->assign('position',$position);
        $this->display();

    }

    /**
     * 评审规则
     */
    public function ecn(){

        if(IS_POST){

            $model = new model();

            $positionData = '';
            $reviewData = '';
            $post = I('post.');
            # 抄送职位
            foreach($post['position'] as $key=>&$value){
                $positionData .= $value.',';
            }
            $tmpArr = [];
            # 评审职位
            foreach ($post['revData'] as $key=>&$value){
                $arr = [];
                foreach($value as $ke=>&$val){
                    $arr[] = $val['rev'];
                }
                array_push($tmpArr, $arr);
            }

            foreach ($tmpArr as $value){
                $tmepArr[] = implode(',',$value);
            }

            $box = '';
            foreach ($tmepArr as $k=>&$v){
                $tmpData = ($k+1).'|'.$v.'@';
                $data = json_encode($tmpData);
                $box .= $tmpData;
            }

            # 抄送职位
            $ecnRule['notice'] = substr($positionData,0,-1);
            $ecnRule['review'] = substr($box,0,-1);
            $ecnRule['name'] = $post['data']['name'];

            $name = $model->table(C('DB_PREFIX').'file_ecn_rule')->where('name ="'.$post['data']['name'].'"')->find();

            if($name){
                $this->ajaxReturn(['flag'=>0,'msg'=>'名称已存在！']);
                exit();
            }else{
                $ruleId = $model->table(C('DB_PREFIX').'file_ecn_rule')->add($ecnRule);
                if($ruleId){
                    $this->ajaxReturn(['flag'=>1,'msg'=>'添加成功!']);
                }else{
                    $this->ajaxReturn(['flag'=>0,'msg'=>'添加失败!']);
                }
            }
            # print_r($ecnRule);



        }else{

            $model = new model();

            $department = M('department')->field('id,name')->select();
            $position =  M('position')->field('id,name')->select();


            foreach ($department as $key=>&$value){
                $value['position'] = $model->table(C('DB_PREFIX').'position')->where('belongsto ='.$value['id'])->select();
            }

            $this->assign('department',$department);
            $this->assign('position',$position);
            $this->display();
        }


    }

    /**
     * 编号申请
     */
    public function apply(){

        $model = new model();
        $model->startTrans();

        $ruleData = $model->table(C('DB_PREFIX').'file_rule')->select();

        $count = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'file_log b')
                        ->field('a.file_no,b.time,b.person,a.id,a.status')
                        ->where("a.id = b.n_id AND b.action = 'apply'")
                        ->count();

        # 数据分页
        $page = new Page($count,10);
        $page->setConfig('prev','<span aria-hidden="true">上一页</span>');
        $page->setConfig('next','<span aria-hidden="true">下一页</span>');
        $page->setConfig('first','<span aria-hidden="true">首页</span>');
        $page->setConfig('last','<span aria-hidden="true">尾页</span>');

        if(C('PAGE_STATUS_INFO')){
            $page->setConfig ( 'theme', '<li><a href="javascript:void(0);">当前%NOW_PAGE%/%TOTAL_PAGE%</a></li>  %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%' );
        }


        if(IS_POST){

            $post = I('post.');

            # 生成一个编号
            $ruleRel = $model->table(C('DB_PREFIX').'file_rule')->where("type =".'"'.$post['type'].'"')->find();
            $num = $ruleRel['current_id'];
            $len = $ruleRel['length'];
            $type = $ruleRel['type'];

            # 自动填充编号
            $num = $num+1;
            $bit = $len-strlen($type);
            $num_len = strlen($num);
            $zero = '';
            for($i=$num_len; $i<$bit; $i++){
                $zero .= "0";
            }
            $real_num = $type.$zero.$num;
            # echo $real_num;

            $current['current_id'] = $num;

            #生成编号后修改rule表
            $up_busy_id = $model->table(C('DB_PREFIX').'file_rule')->where("type =".'"'.$post['type'].'"')->save($current);

            if( $up_busy_id ){

                $numberData['file_no'] = $real_num;
                $number_id = $model->table(C('DB_PREFIX').'file_number')->add($numberData);

                if($number_id){
                    # 日志表数据
                    $logData['n_id'] = $number_id;
                    $logData['action'] = 'apply';
                    $logData['time'] = time();
                    $logData['log'] = '';
                    $logData['person'] = session('user')['id'];
                    $logData['nickname'] = session('user')['nickname'];
                    $log_id = $model->table(C('DB_PREFIX').'file_log')->add($logData);

                    if($log_id){
                        $model->commit();
                        $this->ajaxReturn(['flag'=>$number_id,'msg'=>'操作成功！','num'=>$real_num]);
                    }else{
                        $model->rollback();
                        $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败！']);
                    }

                }else{
                    $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败！']);
                }
            }else{
                $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败！']);
            }

        }else{

            # 默认加载的apply页面数据
            $numData = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'file_log b,'.C('DB_PREFIX').'user c')
                             ->field('a.file_no,b.time,b.person,a.id,a.status,a.version,c.nickname')
                             ->where("a.id = b.n_id AND c.state = 1 AND (b.action = 'apply' OR b.action = 'upgrade') AND c.id = b.person AND b.person =".session('user')['id'])
                             ->limit($page->firstRow.','.$page->listRows)
                             ->order('b.time DESC')
                             ->select();

            # print_r($numData);
            $pageShow = $page->show();
            $this->assign('page', $pageShow);
            $this->assign('numData',$numData);
            $this->assign('ruleData',$ruleData);
            $this->display();

        }

    }

    /**
     * 发起审批
     */
    public function review(){

        $model = new model();
        $model->startTrans();

        if( IS_POST ){

            $post = I('post.','',true);

            if($post['type'] == 'file_info'){

                # 评审
                $numberData = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'file_log b')
                    ->field('a.file_no,a.id,a.num,a.status,a.content,a.version,a.attachment')
                    ->where('b.person ='.session('user')['id'].' AND (a.status =2 OR a.status=0)  AND b.n_id=a.id')
                    ->select();

                $revRule = $model->table(C('DB_PREFIX').'file_ecn_rule')->order('id ASC')->select();

                $DCC = $model->table(C('DB_PREFIX').'user a,'.C('DB_PREFIX').'position b')->where()->select();

                foreach ($numberData as $key=>&$value){
                    $value['attachment'] = json_decode($value['attachment'],true);
                }

                $this->ajaxReturn($numberData);

            }else{

                $post = I('post.','',true);
                $fileId = '';

                foreach ($post['data']['selected'] as $key=>&$value){
                    $fileId .= $value['id'].',';
                }


                # ecn 表数据
                $ecn['review_id'] = $post['data']['revRules'];
                $ecn['n_id'] = substr($fileId,0,-1);

                # 将字符串转换成数组

                $tmpFileId = explode(',',$ecn['n_id']);

                foreach ($tmpFileId as $key=>&$val){
                    # 获取当前是第几次评审
                    //$tmpNumData = $model->table(C('DB_PREFIX').'file_number')->where('id ='.$val)->find();
                    //$numData['num'] = $tmpNumData['num']+1;

                    $numData['status'] = 3;

                    $numId = $model->table(C('DB_PREFIX').'file_number')->where('id ='.$val)->save($numData);

                    if( !$numId ){

                        $model->rollback();
                        $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败!']);
                        return;
                    }

                }

                //die();

                $ecn_id = $model->table(C('DB_PREFIX').'file_ecn')->add($ecn);

                # log 表数据
                $log['n_id'] = $ecn_id;
                $log['action'] = 'initiate';
                $log['time'] = time();
                $log['log'] = $post['data']['title'];
                $log['person'] = session('user')['id'];
                $log['nickname'] = session('user')['nickname'];

                if( $ecn_id ){

                    $tmpReview = [];
                    foreach ($post['data']['allUserItem'] as $key=>&$val){
                        $arr[] = json_decode($val,true);
                    }

                    foreach ($arr as $k=>&$v){

                        $review['step'] = $k+1;
                        foreach ($v as $kk=>&$vv){
                            $review['person'] = $vv['userId'];
                            $review['r_id'] = $ecn_id;
                            array_push($tmpReview,$review);
                        }
                    }

                    # DCC 评审人
                    $tmpDcc['step'] = 0;
                    $tmpDcc['person'] = $post['data']['DCC'];
                    $tmpDcc['r_id'] = $ecn_id;
                    array_push($tmpReview,$tmpDcc);

                    $review_id = $model->table(C('DB_PREFIX').'file_review')->addAll($tmpReview);
                    $log_id = $model->table(C('DB_PREFIX').'file_log')->add($log);

                    # 收集初级评审人邮件
                    $emails = $model->table(C('DB_PREFIX').'file_review a,'.C('DB_PREFIX').'user b')->field('b.email,b.nickname')->where('a.r_id ='.$ecn_id.' AND a.person =b.id AND a.step = 1')->select();
                    foreach ($emails as $key=>&$value){
                        $email[] = $value['email'];
                        $emailPersons['nickname'] = $value['nickname'];
                    }
                    $emailData = $model->table(C('DB_PREFIX').'file_ecn')->where('id ='.$ecn_id)->find();
                    # 自动填充
                    $len = 10;
                    $n = 'ECN';
                    $bit = $len-strlen($n);
                    $num_len = strlen($ecn_id);
                    $zero = '';
                    for($i=$num_len; $i<$bit; $i++){
                        $zero .= "0";
                    }
                    $real_num = $n.$zero.$ecn_id;  # 自动填充
                    $len = 10;
                    $n = 'ECN';
                    $bit = $len-strlen($n);
                    $num_len = strlen($ecn_id);
                    $zero = '';
                    for($i=$num_len; $i<$bit; $i++){
                        $zero .= "0";
                    }
                    $real_num = $n.$zero.$ecn_id;

                    $emailData['ecn'] = $real_num;
                    $emailData['firstNickname'] = $emailPersons['nickname'];
                    # print_r($emailData);
                    # print_r($email);

                    # 抄送此规则的抄送职位
                    $ccData = $model->table(C('DB_PREFIX').'file_ecn_rule')->where('id ='.$emailData['review_id'])->find();

                    $ccArr = explode(',',$ccData['notice']);
                    foreach ($ccArr as $key=>&$value){
                        $ccPerson[] = $model->table(C('DB_PREFIX').'user')->field('nickname,email')->where('post REGEXP "^'.$value.'," OR post REGEXP ",'.$value.'$" OR post REGEXP ",'.$value.'," OR post REGEXP "^'.$value.'$"')->select();
                    }

                    $ccArr = [];
                    foreach ($ccPerson as $k=>&$v){
                        foreach ($v as $kk=>&$vv){
                            $ccs['email'] = $vv['email'];
                            $ccs['nickname'] = $vv['nickname'];
                            array_push($ccArr,$ccs);
                        }
                    }

                    foreach ($ccArr as $key=>&$val){
                        $cc[] = $val['email'];
                    }

                    array_push($cc,session('user')['email']);

                    if( $review_id && $log_id ){

                        $this->pushEmail('INITIATE',$email,$emailData,session('user')['email']);
                        $model->commit();
                        $this->ajaxReturn(['flag'=>$ecn_id,'msg'=>'操作成功！']);

                    }
                }else{

                    $model->rollback();
                    $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败！']);

                }

            }


        }else{

            # 评审
            $numberData = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'file_log b')
                                ->field('a.file_no,a.id,a.num,a.status,a.content,a.version,a.attachment')
                                ->where('b.person ='.session('user')['id'].' AND (a.status =2 OR a.status=0)  AND b.n_id=a.id')
                                ->select();

            $revRule = $model->table(C('DB_PREFIX').'file_ecn_rule')->order('id ASC')->select();

            $DCC = $model->table(C('DB_PREFIX').'user a,'.C('DB_PREFIX').'position b')->where()->select();

            foreach ($numberData as $key=>&$value){
                $value['attachment'] = json_decode($value['attachment'],true);
            }

             $this->assign('type',I('get.type'));


            //print_r($numberData);
            //调用父类注入部门和人员信息(cc)
            $this->getAllUsersAndDepartments();
            $this->getDccPostUsers();
            $this->assign('revRule',$revRule);
            $this->assign('numberData',$numberData);
            $this->display();
        }

    }


    /**
     * 监听评审规则
     */

    public function selectReview(){
        if(IS_POST){
            $id = I('post.id');

            #print_r($id);

            $selData = M('FileEcnRule')->where('id ='.$id)->find();


            $tmpPost = M('User')->field('post')->where()->select();

            # print_r($tmpPost);

            //将用户多职位转换成数组
            foreach ($tmpPost as $key=>&$value){
                $tmpP[$key] = explode(',',$value['post']);
            }

             # print_r($tmpP);

            // 多级评审职位
            $arr = explode('@',$selData['review']);
            foreach ($arr as $key=>&$value){
                $arr[$key] = explode('|',$value);
                foreach ($value as $k=>&$v){
                    if($k == 1){
                        $value[$k] = explode(',',$v);
                    }else{
                        $value[$k] = $v;
                    }
                }
            }
            // 根据职位赛选评审人
            foreach ($arr as $key=>&$value){
                $where['id'] = array('in',$value[1]);
                $value['rel'] = M('Position')->field('id,name')->where($where)->select();
                foreach ($value[1] as $k=>&$v){

                    $value['user'][] = M('User')->field('email,id,nickname')->where('(post regexp ",'.$v.'&" OR post regexp "^'.$v.'," OR post regexp "^'.$v.'$") AND state = 1')->select();

                }
            }

             # print_r($arr);

            $this->ajaxReturn(['flag'=>1,'arr'=>$arr]);


        }else{
            return;
        }
    }

    /**
     * 文件详情
     */
    public function fileDetail(){
        $model = new model();
        $model->startTrans();

        $no = I('get.no');
        $version = I('get.version');

        if(IS_POST){

            $post = I('post.');
            $firstVersion = $post['firstVersion'];
            $id = $post['fileId'];
            $no = $post['getNo'];

            if( $post['type'] == 'upgrade' ){

                $rel = $model->table(C('DB_PREFIX').'file_number')->where('file_no ="'.$post['getNo'].'" AND version = "'.$post['version'].'"')->select();

                if( $rel ){
                    $this->ajaxReturn(['flag'=>0,'msg'=>'版本号已存在，请重新输入！']);

                }else{

                    $number['status'] = 2;
                    $number['version'] = $post['version'];
                    $number['content'] = $post['info-content'];
                    $number['file_no'] = $post['getNo'];

                    $oldNumber['status'] = 6;

                    $saveOldNumberId = $model->table(C('DB_PREFIX').'file_number')->where('id ='.$id)->save($oldNumber);
                    $saveNumberId = $model->table(C('DB_PREFIX').'file_number')->add($number);

                    if( $saveOldNumberId !== false && $saveNumberId){

                        $log['action'] = 'upgrade';
                        $log['n_id'] = $saveNumberId;
                        $log['time'] = time();
                        $log['person'] = session('user')['id'];
                        $log['nickname'] = session('user')['nickname'];
                        $log['log'] = $post['info-content'];

                        $log_id = $model->table(C('DB_PREFIX').'file_log')->add($log);

                        if( $log_id ){
                            $model->commit();
                            $this->ajaxReturn(['flag'=>$saveNumberId,'msg'=>'操作成功！','no'=>$no,'version'=>$post['version']]);
                        }else{
                            $model->rollback();
                            $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败！']);
                        }

                    }else{
                        $model->rollback();
                        $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败！']);
                    }
                }

            }else{
                $number['status'] = 2;
                $number['version'] = $post['version'];
                $number['content'] = $post['info-content'];

                $saveNumberId = $model->table(C('DB_PREFIX').'file_number')->where('id ='.$id)->save($number);

                if( $saveNumberId !== false ){
                    $model->commit();
                    $this->ajaxReturn(['flag'=>$id,'msg'=>'操作成功！','no'=>$no,'version'=>$post['version']]);
                }else{
                    $model->rollback();
                    $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败！']);
                }

            }

        }else{

            if($version){
                $numData = $model->table(C('DB_PREFIX').'file_number')
                                 ->field('id,version,file_no,attachment,version,status,num,content')
                                 ->where('file_no ="'.$no.'"'.' AND version = "'.$version.'"' )
                                 ->find();

            }else{
                $numData = $model->table(C('DB_PREFIX').'file_number')
                                 ->field('id,version,file_no,attachment,version,status,num')
                                 ->where('file_no ="'.$no.'"')
                                 ->find();
            }

            # print_r($numData);

            $numData['attachment'] = json_decode($numData['attachment'],true);
            # number的操作记录
            $numData['log'] = $model->table(C('DB_PREFIX').'file_log a,'.C('DB_PREFIX').'user b')
                                    ->field('a.n_id,a.action,a.time,a.person,a.count,a.log,b.face,b.nickname')
                                    ->where('a.n_id='.$numData['id'].' AND a.person = b.id')
                                    ->order('a.time ASC')
                                    ->find();

            $content = $model->table(C('DB_PREFIX').'file_log a,'.C('DB_PREFIX').'user b')
                             ->field('a.n_id,a.action,a.time,a.person,a.count,a.log,b.face,b.nickname')
                             ->where('a.n_id='.$numData['id'].' AND a.person =b.id AND a.action = "apply"')
                             ->order('a.time DESC')
                             ->limit(1)
                             ->find();

            $context = $model->table(C('DB_PREFIX').'file_log a,'.C('DB_PREFIX').'user b')
                             ->field('a.n_id,a.action,a.time,a.person,a.count,a.log,b.face,b.nickname')
                             ->where('a.n_id='.$numData['id'].' AND a.person =b.id AND a.action = "initiate"')
                             ->order('a.time DESC')
                             ->limit(1)
                             ->find();
            # 历史版本
            $history = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'file_log b,'.C('DB_PREFIX').'user c')
                             ->field('a.version,a.num,a.status,b.time,c.nickname,a.id,a.file_no')
                             ->where('a.id = b.n_id AND b.person = c.id AND b.action = "initiate" AND a.file_no ="'.I('get.no').'"')
                             ->group('a.version')
                             ->select();

            # print_r($content);
            # print_r($numData);
            # print_r($content);
            # print_r($context);
            $this->assign('numData',$numData);
            $this->assign('history',$history);
            $this->assign('content',$content);
            $this->assign('context',$context);
            $this->display();

        }

    }


    /**
     * 审批详情
     */

    public function reviewDetail(){

        $model = new model();

        if(IS_POST){

            $model->startTrans();

            $post = I('post.');
            $id = $post['rId'];



            # 评审文件数据
            $ecnData = $model->table(C('DB_PREFIX').'file_ecn')->where('id ='.$id)->find();
            $ecnData['tmpArr'] = explode(',',$ecnData['n_id']);
            $ecnData['log'] = $model->table(C('DB_PREFIX').'file_log')
                                    ->where('n_id ='.$ecnData['id'].' AND action = "initiate"')
                                    ->field('time,nickname')
                                    ->find();

            foreach ($ecnData['tmpArr'] as $k=>&$v){
                $ecnData['number'][] = $model->table(C('DB_PREFIX').'file_number')->where('id ='.$v)->find();
            }

            # 当前步骤操作人数据
            $nowEcnData = $model->table(C('DB_PREFIX').'file_ecn')->where('id ='.$id)->find();

            $nowReviewData = $model->table(C('DB_PREFIX').'file_review a,'.C('DB_PREFIX').'user b')
                                   ->field('a.person,a.count,a.status,b.nickname,b.email,a.step')
                                   ->where('r_id ='.$nowEcnData['id'].' AND step ='.$nowEcnData['step'].' AND a.person =b.id')
                                   ->select();

            $count = $model->table(C('DB_PREFIX').'file_review a,'.C('DB_PREFIX').'user b')
                           ->field('a.person,a.count,a.status,b.nickname,b.email,a.step')
                           ->where('r_id ='.$nowEcnData['id'].' AND step ='.$nowEcnData['step'].' AND a.person =b.id')
                           ->count();

            # 当前步骤的所有操作人数据
            foreach ($nowReviewData as $key=>&$value){
                $tmpNowPerson[] = $value['person'];
            }

            # 下一步步骤的操作人数据
            $nextReviewData = $model->table(C('DB_PREFIX').'file_review a,'.C('DB_PREFIX').'user b')
                                    ->field('a.person,a.count,a.status,b.nickname,b.email,a.step')
                                    ->where('r_id ='.$nowEcnData['id'].' AND step ='.($nowEcnData['step']+1).' AND a.person =b.id')
                                    ->select();
            # DCC评审人数据
            $DCCReviewData = $model->table(C('DB_PREFIX').'file_review a,'.C('DB_PREFIX').'user b')
                                   ->field('a.person,a.count,a.status,b.nickname,b.email,a.step')
                                   ->where('r_id ='.$nowEcnData['id'].' AND step = 0 AND a.person =b.id')
                                   ->select();
            # 发起人数据
            $firstPerson = $model->table(C('DB_PREFIX').'file_log a,'.C('DB_PREFIX').'user b')->where('n_id ='.$id.' AND action = "initiate" AND a.person = b.id')->field('b.email,b.nickname')->find();

            $email[] = $firstPerson['email'];
            $nowEcnData['firstNickname'] = $firstPerson['nickname'];

            # 自动填充
            $len = 10;
            $n = 'ECN';
            $bit = $len-strlen($n);
            $num_len = strlen($nowEcnData['id']);
            $zero = '';
            for($i=$num_len; $i<$bit; $i++){
                $zero .= "0";
            }
            $real_num = $n.$zero.$nowEcnData['id'];
            $nowEcnData['ecn'] = $real_num;
            $nowEcnData['info'] = $post['context'];

            # 抄送此规则的抄送职位
            $ccData = $model->table(C('DB_PREFIX').'file_ecn_rule')->where('id ='.$nowEcnData['review_id'])->find();

            $ccArr = explode(',',$ccData['notice']);
            foreach ($ccArr as $key=>&$value){
                $ccPerson[] = $model->table(C('DB_PREFIX').'user')->field('nickname,email')->where('post REGEXP "^'.$value.'," OR post REGEXP ",'.$value.'$" OR post REGEXP ",'.$value.'," OR post REGEXP "^'.$value.'$"')->select();
            }
            # 当前职位需要抄送人
            $ccArr = [];
            foreach ($ccPerson as $k=>&$v){
                foreach ($v as $kk=>&$vv){
                    $ccs['email'] = $vv['email'];
                    $ccs['nickname'] = $vv['nickname'];
                    array_push($ccArr,$ccs);
                }
            }

            foreach ($ccArr as $key=>&$val){
                $cc[] = $val['email'];
            }

            foreach ($nowReviewData as $k=>&$v){
                $tmpArr[] = $v['email'];
            }

            # 抄送人
            array_merge($cc,$tmpArr);


            foreach ($nextReviewData as $key=>&$value){
                $tmpCc[] = $value['email'];
                $tmpNickname[] = $value['nickname'];
            }

            $dcc[] = $DCCReviewData[0]['email'];
            $nowEcnData['dcc'] = $DCCReviewData[0]['nickname'];
            $nowEcnData['tmpNickname'] = $tmpNickname;

            /*print_r($nowEcnData);
            die();*/

            if($post['type'] == 'pass'){
                # 通过操作

                # review表数据
                $reviewStatus['status'] = 2;

                #log表数据
                $log['log'] = $post['context'];
                $log['action'] = $post['type'];
                $log['time'] = time();
                $log['nickname'] = session('user')['nickname'];
                $log['person'] = session('user')['id'];
                $log['n_id'] = $post['rId'];
                $log['step'] = $nowReviewData[0]['step'];

                $reviewStatus_Id = $model->table(C('DB_PREFIX').'file_review')->where('r_id ='.$post['rId'].' AND step ='.$post['step'].' AND person ='.$post['uId'])->save($reviewStatus);
                $log_id = $model->table(C('DB_PREFIX').'file_log')->add($log);

                if($reviewStatus_Id && $log_id) {
                    $num = 1;
                    $int = 0;
                    foreach ($nowReviewData as $key => &$value) {
                        if ($value['status'] != 1) {
                            $num++;
                        }
                    }


                    if($nowReviewData[0]['step'] > 0){
                        # 判断是否此步骤全部操作完成
                        if ($num == $count) {
                            # 判断是否有拒绝
                            foreach ($nowReviewData as $key => &$value) {
                                if ($value['status'] == 0) {
                                    $int++;
                                }
                            }

                            if ( $int ) {
                                # 此步骤全部完成且有拒绝

                                $ecnStatus['status'] = 0;

                                $number['status'] = 0;
                                foreach ($ecnData['tmpArr'] as $key=>&$value){
                                    $number_id = $model->table(C('DB_PREFIX').'file_number')->where('id ='.$value)->save($number);
                                    if( !$number_id ){
                                        $model->rollback();
                                        $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败！']);
                                        exit();
                                    }
                                }

                                $ecnStatus_id = $model->table(C('DB_PREFIX').'file_ecn')->where('id ='.$id)->save($ecnStatus);
                                if( $ecnStatus_id ){
                                    $model->commit();
                                    $this->pushEmail('PASS',$email,$nowEcnData,$tmpArr);
                                    $this->ajaxReturn(['flag'=>1,'msg'=>'操作成功！']);
                                }else{
                                    $model->rollback();
                                    $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败！']);
                                }

                            } else {
                                # 此步骤全部完成且全部通过

                                if( $nextReviewData ){
                                    $ecnStep['step'] = $nextReviewData[0]['step'];
                                    /*foreach ( $nextReviewData as $ke=>&$val){
                                        $email[] = $val['email'];
                                    }
                                    array_merge($cc,$email);*/

                                    $ecnStep_id = $model->table(C('DB_PREFIX').'file_ecn')->where('id ='.$id)->save($ecnStep);

                                    if($ecnStep_id){
                                        $model->commit();
                                        $this->pushEmail('INITIATE_OVER',$tmpCc,$nowEcnData,$email);
                                        $this->pushEmail('PASS',$email,$nowEcnData,$tmpArr);
                                        $this->ajaxReturn(['flag'=>2,'msg'=>'操作成功！']);
                                    }else{
                                        $model->rollback();
                                        $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败！']);
                                    }

                                }else{

                                    $ecnStep['step'] = 0;
                                    $ecnStep_id = $model->table(C('DB_PREFIX').'file_ecn')->where('id ='.$id)->save($ecnStep);

                                    if( $ecnStep_id ){
                                        $model->commit();
                                        $this->pushEmail('INITIATE_DCC',$dcc,$nowEcnData,$email);
                                        $this->pushEmail('PASS',$email,$nowEcnData,$tmpArr);
                                        $this->ajaxReturn(['flag'=>3,'msg'=>'操作成功！']);
                                    }else{
                                        $model->rollback();
                                        $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败！']);
                                    }

                                }

                            }

                        }else{

                            $model->commit();
                            $this->pushEmail('PASS',$email,$nowEcnData,$tmpArr);
                            $this->ajaxReturn(['flag'=>4,'msg'=>'操作成功！']);
                        }
                    }else{
                        # dcc 评审

                        $ecnStatus['status'] = 2;

                        $number['status'] = 5;

                        foreach ( $ecnData['tmpArr'] as $key=>&$value ){
                            $number_id = $model->table(C('DB_PREFIX').'file_number')->where('id ='.$value)->save($number);
                            if( !$number_id ){
                                $model->rollback();
                                $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败！']);
                                exit();
                            }
                        }

                        $ecnStatus_id = $model->table(C('DB_PREFIX').'file_ecn')->where('id ='.$id)->save($ecnStatus);
                        if( $ecnStatus_id ){
                            $model->commit();
                            $this->pushEmail('PASS_S',$email,$nowEcnData,$cc);
                            $this->ajaxReturn(['flag'=>5,'msg'=>'操作成功！']);
                        }else{
                            $model->rollback();
                            $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败！']);
                        }

                    }
                }

            }else{
                # 拒绝操作

                # print_r($post);

                # review表数据
                $reviewStatus['status'] = 0;
                # ecn表数据
                $ecnStatus['status'] = 0;

                #log表数据
                $log['log'] = $post['context'];
                $log['action'] = $post['type'];
                $log['time'] = time();
                $log['nickname'] = session('user')['nickname'];
                $log['person'] = session('user')['id'];
                $log['n_id'] = $post['rId'];
                $log['step'] = $nowReviewData[0]['step'];


                $number['status'] = 0;

                foreach ($ecnData['tmpArr'] as $key=>&$value){
                    $number_id = $model->table(C('DB_PREFIX').'file_number')->where('id ='.$value)->save($number);
                    if( !$number_id ){
                        $model->rollback();
                        $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败！']);
                        exit();
                    }
                }

                $reviewStatus_Id = $model->table(C('DB_PREFIX').'file_review')->where('r_id ='.$post['rId'].' AND step ='.$post['step'].' AND person ='.$post['uId'])->save($reviewStatus);
                $log_id = $model->table(C('DB_PREFIX').'file_log')->add($log);
                $ecnStatus_id = $model->table(C('DB_PREFIX').'file_ecn')->where('id ='.$id)->save($ecnStatus);

                if($reviewStatus_Id && $log_id && $ecnStatus_id){

                    $model->commit();
                    $this->pushEmail('REFUSE',$email,$nowEcnData,$tmpArr);
                    $this->ajaxReturn(['flag'=>1,'msg'=>'操作成功！']);
                }else{
                    $model->rollback();
                    $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败！']);
                }



            }


        }else{

            $id = I('get.id');
            if($id){

                # 评审文件数据
                $ecnData = $model->table(C('DB_PREFIX').'file_ecn')->where('id = '.$id)->find();


                $ecnData['tmpArr'] = explode(',',$ecnData['n_id']);
                $ecnData['log'] = $model->table(C('DB_PREFIX').'file_log')
                                        ->where('n_id ='.$ecnData['id'].' AND action = "initiate"')
                                        ->field('time,nickname')
                                        ->find();

                foreach ($ecnData['tmpArr'] as $k=>&$v){
                    $ecnData['number'][] = $model->table(C('DB_PREFIX').'file_number')->where('id ='.$v)->find();
                }
                foreach ($ecnData['number'] as $key=>&$value){
                    $value['attachment'] = json_decode($value['attachment'],true);
                }

                # 当前步骤操作人数据
                $nowEcnData = $model->table(C('DB_PREFIX').'file_ecn')->where('id ='.$id)->find();

                $nowReviewData = $model->table(C('DB_PREFIX').'file_review a,'.C('DB_PREFIX').'user b')
                                       ->field('a.person,a.count,a.status,b.nickname,b.email,a.step')
                                       ->where('r_id ='.$nowEcnData['id'].' AND step ='.$nowEcnData['step'].' AND a.person =b.id')
                                       ->select();

                # 当前步骤的所有操作人数据
                foreach ($nowReviewData as $key=>&$value){
                    $tmpNowPerson[] = $value['person'];
                    if(session('user')['id'] == $value['person']){
                        $status = $value['status'];
                    }
                }

                # 下一步步骤的操作人数据
                $nextReviewData = $model->table(C('DB_PREFIX').'file_review a,'.C('DB_PREFIX').'user b')
                                        ->field('a.person,a.count,a.status,b.nickname,b.email,a.step')
                                        ->where('r_id ='.$nowEcnData['id'].' AND step ='.($nowEcnData['step']+1).' AND a.person =b.id')
                                        ->select();

                # 所有评审人数据
                $reviewData = $model->table(C('DB_PREFIX').'file_review a,'.C('DB_PREFIX').'user b')
                                    ->field('a.person,a.status,a.step,b.nickname,b.email')
                                    ->where('r_id = '.$id.' AND b.id = a.person')
                                    ->order('a.id ASC')
                                    ->select();

                foreach ($reviewData as $Key=>&$value){

                    $fileLog = $model->table(C('DB_PREFIX').'file_log')->where('n_id ='.$id.' AND person ='.$value['person'].' AND step ='.$value['step'])->field('log')->find();
                    $value['log'] = $fileLog['log'];
                    $fileTime = $model->table(C('DB_PREFIX').'file_log')->where('n_id ='.$id.' AND person ='.$value['person'].' AND step ='.$value['step'])->field('time')->find();
                    $value['time'] = $fileTime['time'];
                    # 为每一个节点计算出合并行
                    $count = 0;
                    foreach($reviewData as $k=>&$v){
                        if( $value['step'] == $v['step'] ){
                            $count++;
                            $value['rowSpan'] = $count;
                        }else{
                            $count = 0;
                        }
                    }
                }

                # 检查当前节点的步骤和上一节点的步骤和rowSpan是否相同，如果相同则保留第一节点，反之则删除后续节点相同的rowSpan
                foreach ($reviewData as $Key=>&$value){
                    if($value['step'] == $reviewData[$Key-1]['step']  ){
                        if( isset($reviewData[$Key-1]['rowSpan']) ){    # 避免重复步骤出现两次以上，每次遍历时检查上一节点是否存在rowSpan，如果存在并且rowSpan相等则移除
                            if( $value['rowSpan'] == $reviewData[$Key-1]['rowSpan'] ){  # 排除掉不等于情况
                                unset($reviewData[$Key]['rowSpan']);
                            }
                        }else{  # 如果上一节点不存在rowSpan也移除
                            unset($reviewData[$Key]['rowSpan']);
                        }
                    }
                }

                $ecn_Data = $model->table(C('DB_PREFIX').'file_ecn a,'.C('DB_PREFIX').'file_log b')
                                  ->field('b.time,b.log,b.nickname,a.status,b.person,a.step')
                                  ->where('a.id ='.$id.' AND a.id = b.n_id AND b.action = "initiate" AND b.n_id ='.$id)
                                  ->find();
                # 自动填充
                $len = 10;
                $n = 'ECN';
                $bit = $len-strlen($n);
                $num_len = strlen($id);
                $zero = '';
                for($i=$num_len; $i<$bit; $i++){
                    $zero .= "0";
                }
                $real_num = $n.$zero.$id;
                $ecn_Data['ecn'] = $real_num;

                print_r($ecnData);
                $this->assign('ecnData',$ecnData);
                $this->assign('ecn_Data',$ecn_Data);
                $this->assign('nextReviewData',$nextReviewData);
                $this->assign('status',$status);
                $this->assign('nowEcnData',$nowEcnData);
                $this->assign('nowReviewData',$nowReviewData);
                $this->assign('tmpNowPerson',$tmpNowPerson);
                $this->assign('reviewData',$reviewData);
                $this->display();

            }else{

                $this->error('参数错误!');

            }

        }

    }

    /**
     * 审批人操作
     */
    public function saveDetail(){

        $model = new model();
        $model->startTrans();

        if( IS_POST ) {
            $post = I('post.');

            $tmpNumber = $post['numberNum'];
            $tmpId = $post['numberId'];

            # 邮件推送数据
            $emailData = $model->table(C('DB_PREFIX') . 'file_number a,' . C('DB_PREFIX') . 'file_log b,' . C('DB_PREFIX') . 'user c')
                               ->field('a.id,a.file_no,a.version,a.status,a.attachment,a.num,b.log,b.person')
                               ->where('a.id=' . $post['numberId'] . ' AND a.id =b.n_id AND a.num = ' . $post['numberNum'] . ' AND b.count =' . $post['numberNum'] . ' AND b.action ="initiate" AND c.id = b.person')
                               ->find();

            $emailData['user'] = $emailData['nickname'];
            $emailData['content'] = $emailData['log'];
            $emailData['info'] = $post['context'];
            $emailD = $model->table(C('DB_PREFIX') . 'file_number a,' . C('DB_PREFIX') . 'file_log b,' . C('DB_PREFIX') . 'user c')
                            ->field('c.nickname')
                            ->where('a.id=' . $post['numberId'] . ' AND a.id =b.n_id AND a.num = ' . $post['numberNum'] . ' AND b.count =' . $post['numberNum'] . ' AND b.action ="initiate" AND c.id = b.person')
                            ->select();

            foreach ($emailD as $key=>&$value){

                $names[] = $value['nickname'];
            }

            $emailData['nickname'] = $names;

            # 发起人
            $email = $model->table(C('DB_PREFIX') . 'file_log a,' . C('DB_PREFIX') . 'user b')
                           ->where('a.n_id =' . $post['numberId'] . ' AND a.count =' . $post['numberNum'] . ' AND a.action ="initiate" AND a.person = b.id')
                           ->field('b.email')
                           ->select();

            # 当前评审人数据
            $now = $model->table(C('DB_PREFIX') . 'file_review a,' . C('DB_PREFIX') . 'user c')
                         ->field('c.email')
                         ->where('a.n_id =' . $post['numberId'] . ' AND a.count =' . $post['numberNum'] . ' AND a.person = c.id AND a.step =' . $post['step'])
                         ->select();

            # 下一级评审人数据
            $next = $model->table(C('DB_PREFIX') . 'file_review a,' . C('DB_PREFIX') . 'user c')
                          ->field('c.email,c.nickname')
                          ->where('a.n_id =' . $post['numberId'] . ' AND a.count =' . $post['numberNum'] . ' AND a.person = c.id AND a.step =' . ($post['step'] + 1))
                          ->select();

            $dcc = $model->table(C('DB_PREFIX') . 'file_review a,' . C('DB_PREFIX') . 'user c')
                         ->field('c.email')
                         ->where('a.n_id =' . $post['numberId'] . ' AND a.count =' . $post['numberNum'] . ' AND a.person = c.id AND a.step = 0')
                         ->select();

            # 不包含发起人和dcc的评审人
            $all = $model->table(C('DB_PREFIX') . 'file_review a,' . C('DB_PREFIX') . 'user c')
                         ->field('c.email')
                         ->where('a.n_id =' . $post['numberId'] . ' AND a.count =' . $post['numberNum'] . ' AND a.person = c.id AND a.step <> 0')
                         ->select();

            # 当前步骤以及之前的所有评审人
            $reviewCurrentStep = $model->table(C('DB_PREFIX') . 'file_review a,' . C('DB_PREFIX') . 'user c')
                         ->field('c.email')
                         ->where('a.n_id =' . $post['numberId'] . ' AND a.count =' . $post['numberNum'] . ' AND a.person = c.id AND a.step <> 0 AND a.step <='. $post['step'])
                         ->select();

            $allReceive = array_merge($email,$all);     //文件归档时收件人列表
            $DCCRefuseAllCClist = array_merge($now,$all);     //DCC拒绝时抄送人列表

            foreach( $DCCRefuseAllCClist as $key=>&$value ){
                $DCCRefuse_AllCClist[] = $value['email'];
            }

            $allReceive_Refuse = $reviewCurrentStep;    //拒绝时的抄送列表(拒绝时发送给发起人)

            $allReceive_Pass = array_merge($email,$reviewCurrentStep);      //通过时的抄送列表(通过是发送给下一级评审人)

            foreach( $allReceive as $key=>&$value ){
                $All_Receive[] = $value['email'];
            }

            foreach( $allReceive_Refuse as $key=>&$value ){
                $All_Receive_Refuse[] = $value['email'];
            }

            foreach( $allReceive_Pass as $key=>&$value ){
                $All_Receive_Pass[] = $value['email'];
            }

            # 当前评审人邮箱
            foreach ($now as $key => &$val) {
                $nowEmail[] = $val['email'];
            }
            # 发起人邮箱
            foreach ($email as $key => &$val) {
                $ccs[] = $val['email'];
            }
            # 下级评审人邮箱
            foreach ($next as $key => &$va) {
                $nextEmail[] = $va['email'];
                $nextName[] = $va['nickname'];
            }
            # dcc评审人邮箱
            foreach ($dcc as $key => &$va) {
                $dccEmail[] = $va['email'];
            }


            $cc = array_merge($ccs,$nowEmail);
            $nextCc = array_merge($ccs,$nextEmail);

            $emailData['nextPerson'] = $nextName;

            # 判断用户提交操作
            if ($post['step'] >= 1) {
                # 判断用户提交的是否为通过
                if ($post['type'] == 'pass') {

                    $review['status'] = 2;

                    $log['time'] = time();
                    $log['action'] = 'pass';
                    $log['log'] = $post['context'];
                    $log['person'] = session('user')['id'];
                    $log['n_id'] = $post['numberId'];
                    $log['count'] = $post['numberNum'];
                    $log['nickname'] = session('user')['nickname'];

                    # 修改审批人审批的状态
                    $review_id = $model->table(C('DB_PREFIX') . 'file_review')
                                       ->where('n_id =' . $post['numberId'] . ' AND person =' . session('user')['id'] . ' AND count=' . $post['numberNum'] . ' AND step =' . $post['step'])
                                       ->save($review);
                    # 写入审批时留下的数据
                    $log_id = $model->table(C('DB_PREFIX') . 'file_log')->add($log);

                    if ($review_id && $log_id) {
                        # 如果操作成功 统计初级操作人数量
                        $i = $model->table(C('DB_PREFIX') . 'file_review')->where('n_id =' . $post['numberId'] . ' AND count =' . $post['numberNum'] . ' AND step =' . $post['step'])->select();
                        $count = $model->table(C('DB_PREFIX') . 'file_review')->where('n_id =' . $post['numberId'] . ' AND count =' . $post['numberNum'] . ' AND step =' . $post['step'])->count();

                        $num = 0;
                        foreach ($i as $key => &$value) {
                            if ($value['status'] != 1) {
                                $num += 1;
                            }
                        }

                        # 如果所有操作人完成操作
                        if ($num == $count) {

                            foreach ($i as $key => &$val) {
                                $tmpArr[] = $val['status'];
                            }

                            if (in_array(0, $tmpArr)) {

                                $tmpNum['num'] = $tmpNumber;
                                $tmpNum['status'] = 0;

                                $number_id = $model->table(C('DB_PREFIX') . 'file_number')->where('id =' . $post['numberId'])->save($tmpNum);

                                if ($number_id) {

                                    $this->pushEmail('PASS', $ccs, $emailData, $allReceive_Pass);
                                    $model->commit();
                                    $this->ajaxReturn(['flag' => 1, 'msg' => '操作成功！']);
                                } else {
                                    $model->rollback();
                                    $this->ajaxReturn(['flag' => 0, 'msg' => '操作失败！']);
                                }

                            } else {

                                # 判断是否有下级评审
                                if ( $next ) {
                                    $number['step'] = $post['step']+1;
                                    # 修改num表当前评审步骤
                                    $number_saveId = $model->table(C('DB_PREFIX') . 'file_number')->where('id ='.$tmpId)->save($number);
                                    if ( $number_saveId ) {
                                        //$this->pushEmail('PASS', $ccs, $emailData, $cc);
                                        $this->pushEmail('INIT', $nextEmail, $emailData, $cc);  //$nextCc
                                        $model->commit();
                                        $this->ajaxReturn(['flag' => 1, 'msg' => '操作成功！']);
                                    }else{
                                        $model->rollback();
                                        $this->ajaxReturn(['flag' => 0, 'msg' => '操作失败！']);
                                    }

                                } else {

                                    $number['step'] = 0;
                                    # 修改num表当前评审步骤
                                    $number_saveId = $model->table(C('DB_PREFIX') . 'file_number')->where('id ='.$tmpId)->save($number);

                                    if ( $number_saveId ) {
                                        //$this->pushEmail('PASS', $ccs, $emailData, $cc);
                                        $this->pushEmail('DCC', $dccEmail, $emailData, $All_Receive_Pass);  //$dccEmail
                                        $model->commit();
                                        $this->ajaxReturn(['flag' => 1, 'msg' => '操作成功！']);
                                    }else{
                                        $model->rollback();
                                        $this->ajaxReturn(['flag' => 0, 'msg' => '操作失败！']);
                                    }

                                }

                            }

                        }else{

                            $this->pushEmail('PASS', $ccs, $emailData, $cc);
                            $model->commit();
                            $this->ajaxReturn(['flag' => 1, 'msg' => '操作成功！']);

                        }
                    } else {

                        $model->rollback();
                        $this->ajaxReturn(['flag' => 0, 'msg' => '操作失败！']);
                    }

                } else {

                    # 如果评审人提交的数据是拒绝
                    $review['status'] = 0;

                    $log['time'] = time();
                    $log['action'] = 'refuse';
                    $log['log'] = $post['context'];
                    $log['person'] = session('user')['id'];
                    $log['n_id'] = $post['numberId'];
                    $log['nickname'] = session('user')['nickname'];
                    $log['count'] = $post['numberNum'];
                    # 写入日志 修改当前评审人的评审状态
                    $review_id = $model->table(C('DB_PREFIX') . 'file_review')
                                       ->where('n_id =' . $post['numberId'] . ' AND person =' . session('user')['id'] . ' AND count =' . $post['numberNum'] . ' AND step ='.$post['step'])
                                       ->save($review);

                    $log_id = $model->table(C('DB_PREFIX') . 'file_log')->add($log);

                    if ($review_id && $log_id) {
                        # 统计评审人总数
                        $i = $model->table(C('DB_PREFIX') . 'file_review')->where('n_id =' . $post['numberId'] . ' AND count =' . $post['numberNum'] . ' AND step = 1')->select();
                        $count = $model->table(C('DB_PREFIX') . 'file_review')->where('n_id =' . $post['numberId'] . ' AND count =' . $post['numberNum'] . ' AND step =1')->count();
                        $num = 0;

                        foreach ($i as $key => $value) {
                            if ($value['status'] != 1) {
                                $num += 1;
                            }

                        }
                        # 如果所有评审人都操作完成
                        if ($num == $count) {
                            # 修改number表数据
                            $tmpNum['status'] = 0;
                            $tmpNum['num'] = $tmpNumber;
                            $tmpNum['step'] = 1;
                            $number_id = $model->table(C('DB_PREFIX') . 'file_number')->where('id =' . $post['numberId'])->save($tmpNum);
                            if ($number_id) {

                                $this->pushEmail('REFUSE', $ccs, $emailData, $All_Receive_Refuse);
                                $model->commit();
                                $this->ajaxReturn(['flag' => 1, 'msg' => '操作成功！']);
                            } else {
                                $model->rollback();
                                $this->ajaxReturn(['flag' => 0, 'msg' => '操作失败！']);
                            }
                        } else {

                            $this->pushEmail('REFUSE', $ccs, $emailData, $All_Receive_Refuse);
                            $model->commit();
                            $this->ajaxReturn(['flag' => 1, 'msg' => '操作成功！']);
                        }

                    } else {
                        $model->rollback();
                        $this->ajaxReturn(['flag' => 0, 'msg' => '操作失败！']);
                    }

                }

            }else{

                # 所有评审人评审完成后dcc评审 修改number表的状态

                if( $post['type'] == 'pass' ){

                    $tmpNum['status'] = 3;

                    $review['status'] = 2;

                    $log['time'] = time();
                    $log['action'] = 'pass';
                    $log['log'] = $post['context'];
                    $log['person'] = session('user')['id'];
                    $log['n_id'] = $post['numberId'];
                    $log['count'] = $post['numberNum'];
                    $log['nickname'] = session('user')['nickname'];

                    # 修改审批人审批的状态
                    $review_id = $model->table(C('DB_PREFIX') . 'file_review')
                                       ->where('n_id =' . $post['numberId'] . ' AND person =' . session('user')['id'] . ' AND count=' . $post['numberNum'] . ' AND step =' . $post['step'])
                                       ->save($review);
                    # 写入审批时留下的数据
                    $log_id = $model->table(C('DB_PREFIX') . 'file_log')->add($log);
                    # 修改number表状态
                    $number_saveId = $model->table(C('DB_PREFIX') . 'file_number')->where('id =' . $post['numberId'])->save($tmpNum);

                    if($review_id && $log_id && $number_saveId){

                        $this->pushEmail('PASS_S', $All_Receive, $emailData, $dccEmail);
                        $model->commit();
                        $this->ajaxReturn(['flag' => 1, 'msg' => '操作成功！']);
                    }else{
                        $model->rollback();
                        $this->ajaxReturn(['flag' => 0, 'msg' => '操作失败！']);
                    }
                }else{

                    $tmpNum['status'] = 0;
                    $tmpNum['step'] = 1;

                    $review['status'] = 0;

                    $log['time'] = time();
                    $log['action'] = 'refuse';
                    $log['log'] = $post['context'];
                    $log['person'] = session('user')['id'];
                    $log['n_id'] = $post['numberId'];
                    $log['count'] = $post['numberNum'];
                    $log['nickname'] = session('user')['nickname'];

                    # 修改审批人审批的状态
                    $review_id = $model->table(C('DB_PREFIX') . 'file_review')
                        ->where('n_id =' . $post['numberId'] . ' AND person =' . session('user')['id'] . ' AND count=' . $post['numberNum'] . ' AND step =' . $post['step'])
                        ->save($review);
                    # 写入审批时留下的数据
                    $log_id = $model->table(C('DB_PREFIX') . 'file_log')->add($log);
                    # 修改number表状态
                    $number_saveId = $model->table(C('DB_PREFIX') . 'file_number')->where('id =' . $post['numberId'])->save($tmpNum);

                    if($review_id && $log_id && $number_saveId){

                        $this->pushEmail('REFUSE', $ccs, $emailData, $DCCRefuse_AllCClist);
                        $model->commit();
                        $this->ajaxReturn(['flag' => 1, 'msg' => '操作成功！']);
                    }else{

                        $model->rollback();
                        $this->ajaxReturn(['flag' => 0, 'msg' => '操作失败！']);
                    }

                }

            }
        }

    }



    /**
     * 撤回一个评审
     */
    public function rollbackDetail(){

        if(IS_POST){
            $post = (I('post.'));


            $model = new model();
            $model->startTrans();

            $ecn = I('post.ecn');
            $step = I('post.step');

            # 当前需要通知的人邮箱
            if( $step == 0 ){
                $email = $model->table(C('DB_PREFIX').'file_review a,'.C('DB_PREFIX').'user b')
                               ->field('b.email,b.nickname')
                               ->where('a.r_id ='.$ecn.' AND a.person = b.id AND a.step <= '.$step)
                               ->select();
            }else{
                $email = $model->table(C('DB_PREFIX').'file_review a,'.C('DB_PREFIX').'user b')
                               ->field('b.email,b.nickname')
                               ->where('a.r_id ='.$ecn.' AND a.person = b.id AND a.step <= '.$step.' AND a.step != 0')
                               ->select();
            }

            # 收件人
            foreach ($email as $key=>&$val){
                $allReceive[] = $val['email'];
            }
            foreach ($email as $key=>&$val){
                $allPerson[] = $val['nickname'];
            }


            # 发起人邮箱

            $emailData = $model->table(C('DB_PREFIX').'file_ecn')->where('id ='.$ecn)->find();

            # 自动填充
            $len = 10;
            $n = 'ECN';
            $bit = $len-strlen($n);
            $num_len = strlen($ecn);
            $zero = '';
            for($i=$num_len; $i<$bit; $i++){
                $zero .= "0";
            }
            $real_num = $n.$zero.$ecn;
            $emailData['ecn'] = $real_num;
            $emailData['nickname'] = $allPerson;

            $cc = session('user')['email'];

            $tmpData['status'] = 3;

            $tmpLog['n_id'] = $ecn;
            $tmpLog['action'] = 'rollback';
            $tmpLog['time'] = time();
            $tmpLog['person'] = session('user')['id'];
            $tmpLog['nickname'] = session('user')['nickname'];

            $numberData['status'] = 2;
            foreach ($post['flNum'] as $key=>&$val){
                $num_id = $model->table(C('DB_PREFIX').'file_number')->where('id ='.$val)->save($numberData);
                if( !$num_id ){
                    $model->rollback();
                    $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败！']);
                    exit();
                }
            }

            $tmpId = $model->table(C('DB_PREFIX').'file_ecn')->where('id ='.$ecn)->save($tmpData);

            $tmp_id = $model->table(C('DB_PREFIX').'file_log')->add($tmpLog);

            if($tmpId && $tmp_id){
                $this->pushEmail('ROLLBACK',$allReceive,$emailData,$cc);
                $model->commit();
                $this->ajaxReturn(['flag'=>1,',msg'=>'撤回成功']);
            }else {
                $model->rollback();
                $this->ajaxReturn(['flag' => 0, ',msg' => ' 操作失败']);
            }

        }else{

            $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败!']);
        }


    }

    /**
     * 附件
     */

    public function saveDetailAttachment(){

        $att['id'] = I('post.id');
        $att['attachment'] = I('post.attachments', '', false);

        $ats['n_id'] = I('post.id');
        $ats['attachment'] = I('post.attachments', '', false);
        $ats['count'] = I('post.num');

        $attId = M('FileNumber')->save($att);
        $att_id = M('FileAttachment')->add($ats);
        if($attId && $att_id){
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
        $no = I('post.NO');
        $version = I('post.VERSION');

        // 如果需要按id为子目录则必须填写第二个参数，否则直接保存在当前目录
        if( $subName != '' ){
            $result = upload( '/File/'.$no.'/'.$version.'/');
            $this->ajaxReturn( $result );
        }

    }

    /**
     * ecn评审规则列表页
     */
    public function EcnList(){
        $this->display();
    }

    /**
     * ecn评审规则编辑页
     */
    public function EcnEdit(){
        $this->display();
    }

    /**
     * 文件号规则列表页
     */
    public function FilenumberList(){
        $this->display();
    }

    /**
     * 文件号规则编辑页
     */
    public function FilenumberEdit(){
        $this->display();
    }


    /**
     * 邮件推送
     */
    public function pushEmail( $type, $address, $data,$cc){

        $model = new Model();

        if( count($address) > 1 ){
            $call = 'ALL';
        }else{
            $call = $data['firstNickname'];
        }

        $users = session('user')['nickname'];


        $http_host = $_SERVER['HTTP_HOST'];
        extract($data);

        $count = count($data['nickname']);

        $count_s = count($data['nextPerson']);
        if( $count_s == 1 ){
            $next_name = $data['nextPerson'][0];
        }else{
            $next_name = 'All';
        }


        $subject = '[文件管理] ' .$data['file_no']. ' '.$data['version'].' 文件评审';

        $title = '<p>Dear '.$call.', </p>';

        $order_basic = '<p>详情请点击链接：<a href="http://'.$http_host.'/File/reviewDetail/id/'.$data['id'].'" target="_blank">http://'.$http_host.'/File/reviewDetail/id/'.$data['id'].'</a></p>';

        switch( $type ) {

            # 通过
            case 'PASS':

                    $body = '<p>['.$users.'] 通过了您发起的编号为 <b>'.$data['ecn'].'</b> '.$data['version'].' 的文件评审请求。</p>
                             <p>备注：'.$data['info'].'</p>';

                break;
            # 所有评审完成
            case 'PASS_S':

                    $body = '<p>['.$data['dcc'].'] 通过了你发起的编号为 <b>'.$data['ecn'].'</b> 的文件评审，此文件已归档。</p>
                             <p>备注：'.$data['info'].'</p>';

                break;
            # 发起
            case 'INITIATE':

                $body = '<p>['.$users.'] 发起了编号为 <b>'.$data['ecn'].'</b> '.$data['version'].' 的文件评审，请及时处理。</p>';

                $order_basic = '<p>详情请点击链接：<a href="http://'.$http_host.'/File/reviewDetail/id/'.$data['id'].'" target="_blank">http://'.$http_host.'/File/reviewDetail/id/'.$data['id'].'</a></p>
';

                break; # 发起
            case 'INITIATE_OVER':

                if( count($address) > 1 ){
                    $calls = 'ALL';
                }else{
                    $calls = $data['tmpNickname'][0];
                }


                $title = '<p>Dear '.$calls.', </p>';

                $body = '<p>['.$data['firstNickname'].'] 发起了编号为 <b>'.$data['ecn'].'</b> '.$data['version'].' 的文件评审，请及时处理。</p>';

                $order_basic = '<p>详情请点击链接：<a href="http://'.$http_host.'/File/reviewDetail/id/'.$data['id'].'" target="_blank">http://'.$http_host.'/File/reviewDetail/id/'.$data['id'].'</a></p>
';

                break;
            # 发起
            case 'INITIATE_DCC':

                $call = $data['dcc'];

                $title = '<p>Dear '.$call.', </p>';

                $body = '<p>['.$data['firstNickname'].'] 发起了编号为 <b>'.$data['ecn'].'</b> '.$data['version'].' 的文件评审，请及时处理。</p>';

                $order_basic = '<p>详情请点击链接：<a href="http://'.$http_host.'/File/reviewDetail/id/'.$data['id'].'" target="_blank">http://'.$http_host.'/File/reviewDetail/id/'.$data['id'].'</a></p>
';

                break;
            # 二次评审自动发起

            case 'INIT':
                $nnn = $data['num'];

                $title = '<p>Dear '.$next_name.', </p>';

                $body = '<p>[' . session('user')['nickname'] . '] 通过了 [' . $data['nickname'][0] . '] 发起的编号为 <b>'.$data['file_no'].'</b> '.$data['version'].' 的文件评审，请及时处理。</p>
                        <p>备注：'.$data['info'].'</p>';

                break;

            #拒绝
            case 'REFUSE':

                $body = '<p>['.$users.'] 拒绝了您发起的编号为 <b>'.$data['ecn'].'</b> 的文件评审，请知悉。</p>
                         <p>备注：'.$data['info'].'</p>';
                break;
            #回退
            case 'ROLLBACK':

                if( count($address) > 1 ){
                    $calls = 'ALL';
                }else{
                    $calls = $data['nickname'][0];
                }

                $title = '<p>Dear '.$calls.', </p>';

                $body = '<p>['.$users.'] 撤回了编号为 <b>'.$data['ecn'].'</b> 的文件评审，请知悉。</p>
                
';
                break;


        }

        # 检查邮件发送结果
        if( $cc == '' ){
            $result = send_Email( $address, '', $subject, $title.$body.$order_basic );
            if( $result != 1 ){
                $this->ajaxReturn( ['flag'=>0,'msg'=>'邮件发送失败'] );
            }
        }else{
            $result = send_Email( $address, '', $subject, $title.$body.$order_basic, $cc);   # $cc
            if( $result != 1 ){
                $this->ajaxReturn( ['flag'=>0,'msg'=>'邮件发送失败'] );
            }
        }



    }


}