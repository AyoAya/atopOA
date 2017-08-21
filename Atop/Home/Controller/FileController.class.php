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


        if(I('get.search')){
            $count = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'file_log b')
                           ->where('(a.id = b.n_id AND b.action = "apply" AND a.file_no LIKE "%'.I('get.search').'%") OR (a.id = b.n_id AND b.action = "apply" AND b.nickname LIKE "%'.I('get.search').'%")')
                           ->count();

            $this->assign( 'search' , I('get.search') );

        }else{

            $count = $model->table(C('DB_PREFIX').'file_number')->count();

        }

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
            $indexData = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'file_log b')
                               ->where('(a.id = b.n_id AND b.action = "apply" AND a.file_no LIKE "%'.I('get.search').'%") OR (a.id = b.n_id AND (b.action = "apply" OR b.action = "upgrade") AND b.nickname LIKE "%'.I('get.search').'%")')
                               ->order('a.file_no ASC')
                               ->limit($page->firstRow.','.$page->listRows)
                               ->select();
        }else{

            $indexData = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'file_log b,'.C('DB_PREFIX').'user c')
                               ->field('a.id,a.file_no,a.status,c.nickname,c.face,a.id,b.time,a.version')
                               ->where('a.id = b.n_id AND b.person = c.id AND (b.action = "apply" OR b.action = "upgrade")')
                               ->limit($page->firstRow.','.$page->listRows)
                               ->order('a.id DESC')
                               ->select();

            # echo $model->getLastSql();
        }

        $pageShow = $page->show();

        if(I('get.p')){
            $this->assign('pageNumber',I('get.p')-1);
        }

        # print_r($indexData);

        $this->assign('indexData',$indexData);
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
                             ->where("a.id = b.n_id AND c.state = 1 AND b.action = 'apply' AND c.id = b.person AND b.person =".session('user')['id'])
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

            $id = $post['data']['file_no'];

            if( $post['data']['type'] == 'upgrade' ){

                # 升版
                # 收件人数据
                foreach ( $post['data']['allUserItem'] as $key=>&$value ){
                    $post['email'][] = json_decode($value,true);
                }

                # 当前number数据(已发起)
                $numData['file_no'] = $post['file_no'];
                $numData['version'] = $post['data']['version'];
                $numData['content'] = $post['data']['content'];
                $numData['num'] = $post['num']+1;
                $numData['status'] = 2;

                # 修改上个版本的数据
                $save_num['status'] = 4;

                # 写入number表
                $num_id = $model->table(C('DB_PREFIX').'file_number')->add($numData);
                # 修改上个版本的状态
                $save_num_id = $model->table(C('DB_PREFIX').'file_number')->where('id ='.$id)->save($save_num);

                if($num_id && $save_num_id ){

                    # 日志表数据
                    $logData['log'] = $post['data']['content'];
                    $logData['action'] = 'initiate';
                    $logData['n_id'] = $num_id;
                    $logData['person'] = session('user')['id'];
                    $logData['nickname'] = session('user')['nickname'];
                    $logData['time'] = time();
                    $logData['count'] = $post['num']+1;
                    # 日志表数据
                    $logDa['log'] = $post['data']['content'];
                    $logDa['action'] = 'upgrade';
                    $logDa['n_id'] = $num_id;
                    $logDa['person'] = session('user')['id'];
                    $logDa['nickname'] = session('user')['nickname'];
                    $logDa['time'] = time();
                    $logDa['count'] = $post['num']+1;

                    # DCC 数据(review表)
                    $dcc['n_id'] = $num_id;
                    $dcc['person'] = $post['data']['dcc'];
                    $dcc['count'] = $post['num']+1;
                    $dcc['step'] = 0;
                    # 评审人数据 review表
                    foreach ($post['email'] as $key=>&$val){
                        foreach ($val as $k=>&$v){
                            $review['n_id'] = $num_id;
                            $review['person'] = $v['userId'];
                            $review['count'] = $post['num']+1;
                            $review['step'] = $key+1;

                            $tmpArr[] = $review;
                        }

                        $review_id = $model->table(C('DB_PREFIX').'file_review')->addAll($tmpArr);

                        if(!$review_id){
                            $model->rollback();
                            $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败！']);
                            exit();
                        }

                        $tmpArr = '';
                    }

                    # 写入日志表
                     $logDa_id = $model->table(C('DB_PREFIX').'file_log')->add($logDa);

                    # 写入日志表
                    $log_id = $model->table(C('DB_PREFIX').'file_log')->add($logData);

                    # 写入review表
                    $dcc_id = $model->table(C('DB_PREFIX').'file_review')->add($dcc);

                    if( $log_id && $dcc_id && $logDa_id){

                        # 初次评审人邮箱
                        $email = $model->table(C('DB_PREFIX').'file_review a,'.C('DB_PREFIX').'user b')
                                       ->field('b.email,b.nickname')
                                       ->where('a.person = b.id AND a.n_id ='.$num_id.' AND a.count ='.($post['num']+1).' AND a.step = 1')
                                       ->select();
                        foreach ( $email as $k=>&$v){
                            $emails[] = $v['email'];
                            $names[] = $v['nickname'];
                        }

                        # 操作人
                        $persons = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'file_log b,'.C('DB_PREFIX').'user c')
                                        ->field('c.email')
                                        ->where('a.id = b.n_id AND a.id ='.$num_id.' AND b.action = "initiate" AND c.id = b.person')
                                        ->select();
                        foreach ( $persons as $k=>&$v){
                            $ccs[] = $v['email'];
                            $cc_names[] = $v['nickname'];
                        }

                        $emailData = $model->table(C('DB_PREFIX').'file_number')->where('id ='.$num_id)->find();
                        $emailData['nickname'] = $names;

                        # print_r($emailData);
                        $this->pushEmail('UPGRADE',$emails,$emailData,$ccs);
                        $model->commit();$this->ajaxReturn(['flag'=>$emailData['id'],'msg'=>'操作成功！','file_no'=>$emailData['file_no'],'version'=>$emailData['version'],'num'=>$emailData['num']]);

                    }else {

                        $model->rollback();
                        $this->ajaxReturn(['flag' => 0, 'msg' => '操作失败！']);

                    }

                }else{

                    $model->rollback();
                    $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败！']);
                }


            }else{

                print_r($post);

                foreach ($post['data']['file_no'] as $k=>&$y){
                    $arr[] = $k;
                }

                $arr = '';
                //多职位
                foreach($post['file_no'] as $key=>&$val){
                    $arr .= $key.',';
                }

                $fileEcn['review_id'] = substr($arr,0,-1);


                print_r($fileEcn);

                die();

                $a[] = session('user')['email'];

                # 评审
                # 收件人数据
                foreach ( $post['data']['allUserItem'] as $key=>&$value ){
                    $post['email'][] = json_decode($value,true);
                }

                # 日志表数据
                $logData['log'] = $post['data']['content'];
                $logData['action'] = 'initiate';
                $logData['n_id'] = $post['data']['file_no'];
                $logData['person'] = session('user')['id'];
                $logData['nickname'] = session('user')['nickname'];
                $logData['time'] = time();
                $logData['count'] = $post['num']+1;
                # 当前number数据(已发起)
                $numData['status'] = 2;
                $numData['version'] = $post['data']['version'];
                $numData['content'] = $post['data']['content'];
                $numData['num'] = $post['num']+1;
                # DCC 数据(review表)
                $dcc['n_id'] = $post['data']['file_no'];
                $dcc['person'] = $post['data']['dcc'];
                $dcc['count'] = $post['num']+1;
                $dcc['step'] = 0;
                # 评审人数据 review表
                foreach ($post['email'] as $key=>&$val){
                    foreach ($val as $k=>&$v){
                        $review['n_id'] = $post['data']['file_no'];
                        $review['person'] = $v['userId'];
                        $review['count'] = $post['num']+1;
                        $review['step'] = $key+1;

                        $tmpArr[] = $review;
                    }

                    $review_id = $model->table(C('DB_PREFIX').'file_review')->addAll($tmpArr);

                    if(!$review_id){
                        $model->rollback();
                        $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败！']);
                        exit();
                    }

                    $tmpArr = '';
                }

                # 写入日志表
                $log_id = $model->table(C('DB_PREFIX').'file_log')->add($logData);

                # 写入number表
                $num_id = $model->table(C('DB_PREFIX').'file_number')->where('id ='.$id)->save($numData);

                # 写入review表
                $dcc_id = $model->table(C('DB_PREFIX').'file_review')->add($dcc);

                if( $log_id && $num_id && $dcc_id ){

                    # 初次评审人邮箱
                    $email = $model->table(C('DB_PREFIX').'file_review a,'.C('DB_PREFIX').'user b')
                                   ->field('b.email,b.nickname')
                                   ->where('a.person = b.id AND a.n_id ='.$id.' AND a.count ='.($post['num']+1).' AND a.step = 1')
                                   ->select();
                    foreach ( $email as $k=>&$v){
                        $emails[] = $v['email'];
                        $names[] = $v['nickname'];
                    }

                    $person = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'file_log b,'.C('DB_PREFIX').'user c')
                        ->field('c.email')
                        ->where('a.id = b.n_id AND a.id ='.$num_id.' AND b.action = "initiate" AND c.id = b.person')
                        ->select();
                    foreach ( $person as $k=>&$v){
                        $ccs[] = $v['email'];
                        $cc_names[] = $v['nickname'];
                    }

                    $cc = array_merge($emails,$ccs);

                    $emailData = $model->table(C('DB_PREFIX').'file_number')->where('id ='.$id)->find();
                    $emailData['nickname'] = $names;

                    $this->pushEmail('INITIATE',$emails,$emailData,$a);
                    $model->commit();
                    $this->ajaxReturn(['flag'=>$emailData['id'],'msg'=>'操作成功！','file_no'=>$emailData['file_no'],'version'=>$emailData['version'],'num'=>$emailData['num']]);

                }else{

                    $model->rollback();
                    $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败！']);

                }

                # print_r($numData);

            }

        }else{

            if( I('get.type') == 'upgrade' ){

                # 升版操作
                $numberData = $model->table(C('DB_PREFIX').'file_number')
                                    ->field('id,file_no')
                                    ->where('file_no = "'.I('get.no').'"')
                                    ->select();
                $revRule = $model->table(C('DB_PREFIX').'file_ecn_rule')->select();
                # print_r($revRule);
                $this->assign('type',I('get.type'));

            }else{

                # 评审
                $numberData = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'file_log b')
                                    ->field('a.file_no,a.id,a.num')
                                    ->where('b.person ='.session('user')['id'].' AND a.status <=1  AND b.n_id=a.id AND b.action = "apply"')
                                    ->select();
                 $revRule = $model->table(C('DB_PREFIX').'file_ecn_rule')->order('id ASC')->select();

                 $DCC = $model->table(C('DB_PREFIX').'user a,'.C('DB_PREFIX').'position b')->where()->select();

                 # print_r($revRule);
                 $this->assign('type',I('get.type'));


            }
        }

        # print_r($numberData);

        //调用父类注入部门和人员信息(cc)
        $this->getAllUsersAndDepartments();
        $this->getDccPostUsers();
        $this->assign('revRule',$revRule);
        $this->assign('numberData',$numberData);
        $this->display();

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
            foreach ($arr as $key=>&$value){
                $where['id'] = array('in',$value[1]);
                $value['rel'] = M('Position')->field('id,name')->where($where)->select();
                foreach ($value[1] as $k=>&$v){

                    $in['post'] = array('like','%'.$v.'%');
                    $value['user'][] = M('User')->field('email,id,nickname')->where($in)->select();

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
        # number详情
        if( $version ){
            $numData = $model->table(C('DB_PREFIX').'file_number')
                             ->field('id,version,file_no,attachment,version,status,num')
                             ->where('file_no ="'.$no.'"'.' AND version = "'.$version.'"' )
                             ->find();
        }else{
            $numData = $model->table(C('DB_PREFIX').'file_number')
                             ->field('id,version,file_no,attachment,version,status,num')
                             ->where('file_no ="'.$no.'"')
                             ->find();
        }

        $numData['attachment'] = json_decode($numData['attachment'],true);
        # number的操作记录
        $numData['log'] = $model->table(C('DB_PREFIX').'file_log a,'.C('DB_PREFIX').'user b')
                                ->field('a.n_id,a.action,a.time,a.person,a.count,a.log,b.face,b.nickname')
                                ->where('a.n_id='.$numData['id'].' AND a.person = b.id')
                                ->order('a.time ASC')
                                ->select();

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


    /**
     * 审批详情
     */
    public function reviewDetail(){

        $model = new model();

        $version = I('get.version');
        $id = I('get.id');
        $num = I('get.num');

        if($num){
            # 发起的数据
            $person = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'file_log b,'.C('DB_PREFIX').'user c,'.C('DB_PREFIX').'file_attachment d')
                            ->field('a.id,a.step,a.file_no,a.version,b.log,c.nickname,a.status,c.face,b.time,a.num,c.id user_id')
                            ->where('a.id='.$id.' AND b.n_id = a.id AND c.id = b.person AND b.action = "initiate" AND a.version = "'.$version.'" AND b.count ='.$num)
                            ->find();
            $person['attachment'] = $model->table(C('DB_PREFIX').'file_attachment')->field('attachment')->where('n_id ='.$id.' AND count='.$num)->find();
            $person['attachment'] = json_decode($person['attachment']['attachment'],true);

            # 初级评审人数据
            $numData = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'file_review c,'.C('DB_PREFIX').'user d')
                             ->field('a.id,c.person,c.status,c.step,d.email,d.nickname,d.face')
                             ->where('a.id='.$id.' AND c.n_id = a.id AND c.n_id = a.id AND c.person = d.id AND a.version = "'.$version.'" AND c.count ='.$num.' AND c.step =1')
                             ->group('c.person')
                             ->select();


            foreach($numData as $key=>&$val){
                $val['log'] = $model->table(C('DB_PREFIX').'file_log')
                                    ->where('n_id ='.$id.' AND person ='.$val['person'].' AND count ='.$num)
                                    ->find();

            }

            # 所有步骤评审人数据
            $allData = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'file_review c,'.C('DB_PREFIX').'user d')
                             ->field('a.id,c.person,c.step,c.status,c.step,d.email,d.nickname')
                             ->where('a.id='.$id.' AND c.n_id = a.id AND c.person = d.id AND a.version = "'.$version.'" AND c.count ='.$num)
                             ->group('c.id')
                             ->order('c.step ASC')
                             ->select();

            foreach($allData as $key=>&$val){
                $val['log'] = $model->table(C('DB_PREFIX').'file_log')
                                    ->where('n_id ='.$id.' AND person ='.$val['person'].' AND count ='.$num)
                                    ->find();
            }

            # 当前步骤评审人数据
            $nowData = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'file_review c,'.C('DB_PREFIX').'user d')
                             ->field('a.id,c.person,c.status,d.email,c.step,d.nickname')
                             ->where('a.id='.$id.' AND c.n_id = a.id AND c.person = d.id AND c.count ='.$num.' AND c.step ='.$person['step'])
                             ->select();

            # 下一级评审人数据
            $nextData = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'file_review c,'.C('DB_PREFIX').'user d')
                              ->field('a.id,c.person,c.status,d.email,c.step,d.nickname')
                              ->where('a.id='.$id.' AND c.n_id = a.id AND c.person = d.id AND c.count ='.$num.' AND c.step ='.($person['step']+1))
                              ->select();

            # DCC 评审人数据
            $DCCData  = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'file_review c,'.C('DB_PREFIX').'user d')
                              ->field('a.id,c.person,c.status,d.email,d.nickname')
                              ->where('a.id='.$id.' AND c.n_id = a.id AND c.person = d.id AND c.count ='.$num.' AND c.step = 0')
                              ->select();

            foreach($DCCData as $key=>&$val){
                $val['log'] = $model->table(C('DB_PREFIX').'file_log')
                                    ->where('n_id ='.$id.' AND person ='.$val['person'].' AND count ='.$num.' AND (action="pass" OR action="refuse")')
                                    ->find();
            }

            foreach($nowData as $key=>&$val){
                $val['log'] = $model->table(C('DB_PREFIX').'file_log')
                                    ->where('n_id ='.$id.' AND person ='.$val['person'].' AND count ='.$num)
                                    ->find();
            }

            $tmpNumData = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'file_log b,'.C('DB_PREFIX').'file_review c,'.C('DB_PREFIX').'user d')
                                ->field('a.id,c.person,c.status,d.email,b.log,d.nickname,c.step')
                                ->where('a.id='.$id.' AND c.n_id = a.id AND c.n_id = a.id AND c.person = d.id AND c.person ='.session('user')['id'].' AND b.count ='.$num.' AND c.count ='.$num)
                                ->group('c.person')
                                ->find();
            # 统计评审人
            foreach ($numData as $key=>&$value){
                $arr[] = $value['person'];
            }
            foreach ($nowData as $key=>&$value){
                $arr_s[] = $value['person'];
            }

            # 判断当前评审是第几步
            $nowStep = $model->table(C('DB_PREFIX').'file_review')->where('n_id ='.$id.' AND count ='.$num.' AND step ='.($person['step']-1))->select();
            $count = $model->table(C('DB_PREFIX').'file_review')->where('n_id ='.$id.' AND count ='.$num.' AND step ='.($person['step']-1))->count();

            $tmpNum = 0;

            foreach ($nowStep as $key=>&$v){
                if($v['status'] != 1){
                    $tmpNum += 1;
                }
            }

            if($count == $tmpNum){
                $tmpStep = 1;
            }else{
                $tmpStep = 0;
            }

            # 统计每个步骤出现的次数
            foreach ($allData as $key=>&$value){
                $tmpNum = 1;
                foreach ($allData as $k=>$v){
                    if( $v['step'] != $currentStep ){   // 对比是否和上一次的步骤相同，如果相同则只会在第一次生效
                        if( $value['step'] == $v['step'] ){
                            $value['count'] = $tmpNum++;
                        }
                    }
                }
                $currentStep = $value['step'];  // 记录上一次的步骤
            }

            print_r($person);

            $this->assign('person',$person);
            $this->assign('allData',$allData);
            $this->assign('tmpStep',$tmpStep);
            $this->assign('num',$num);
            $this->assign('tmpNumData',$tmpNumData);
            $this->assign('arr',$arr);
            $this->assign('arr_s',$arr_s);
            $this->assign('numData',$numData);
            $this->assign('nextData',$nextData);
            $this->assign('nowData',$nowData);
            $this->assign('DCCData',$DCCData);
            $this->display();

        }else{
            $this->error('参数错误！');
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

        $model = new model();
        $model->startTrans();
        $num = I('post.num');
        $id = I('post.id');
        $version = I('post.version');
        $step = I('post.step');

        # print_r(I('post.'));

        # 当前需要通知的人邮箱
        if( $step == 0 ){
            $email = $model->table(C('DB_PREFIX').'file_review a,'.C('DB_PREFIX').'user b')
                ->field('b.email,b.nickname')
                ->where('a.n_id ='.$id.' AND a.person = b.id AND a.step <= '.$step.' AND a.count ='.$num)
                ->select();
        }else{
            $email = $model->table(C('DB_PREFIX').'file_review a,'.C('DB_PREFIX').'user b')
                ->field('b.email,b.nickname')
                ->where('a.n_id ='.$id.' AND a.person = b.id AND a.step <= '.$step.' AND a.step != 0 AND a.count ='.$num)
                ->select();
        }

        foreach ($email as $key=>&$val){
            $allReceive[] = $val['email'];
        }

        # 发起人邮箱
        $ccEmail = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'file_log b,'.C('DB_PREFIX').'user c')
                         ->field('c.email,c.nickname')
                         ->where('a.id ='.$id.' AND b.person = c.id AND a.num ='.$num.' AND b.action = "initiate" AND a.id = b.n_id')
                         ->select();


        $emailData = $model->table(C('DB_PREFIX').'file_number')->where('id ='.$id)->find();


        foreach ($email as $key=>&$val){
            $emails[] = $val['email'];
        }
        foreach ($email as $key=>&$val){
            $names[] = $val['nickname'];
        }
        foreach ($ccEmail as $key=>&$val){
            $ccs[] = $val['email'];
        }

        /*print_r($email);
        print_r($ccEmail);*/

        $cc = array_merge($emails,$ccs);

        $emailData['nickname'] = $names;

        if(IS_POST){
            $tmpData['num'] = $num;
            $tmpData['status'] = 1;
            $tmpData['step'] = 1;

            $tmpLog['n_id'] = $id;
            $tmpLog['action'] = 'rollback';
            $tmpLog['time'] = time();
            $tmpLog['person'] = session('user')['id'];
            $tmpLog['count'] = $num;
            $tmpLog['nickname'] = session('user')['nickname'];

            $tmpId = $model->table(C('DB_PREFIX').'file_number')->where('id ='.$id)->save($tmpData);

            $tmp_id = $model->table(C('DB_PREFIX').'file_log')->add($tmpLog);

            if($tmpId && $tmp_id){
                $this->pushEmail('ROLLBACK',$allReceive,$emailData,$ccs);
                $model->commit();
                $this->ajaxReturn(['flag'=>1,',msg'=>'撤回成功']);
            }else{
                $model->rollback();
                $this->ajaxReturn(['flag'=>0,',msg'=>' 操作失败']);
            }
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
        $numId = I('post.NUM_ID');

        // 如果需要按id为子目录则必须填写第二个参数，否则直接保存在当前目录
        if( $subName != '' ){
            $result = upload( '/File/'.$subName.'/' , $numId);
            $this->ajaxReturn( $result );
        }

    }




    /**
     * 邮件推送
     */

    public function pushEmail( $type, $address, $data,$cc){

        $model = new Model();

        if( is_array($address) ){
            if( count($address) == 1 ){
                $map['email'] = $address[0];
                $userData = $model->table( C('DB_PREFIX'). 'user' )->field('nickname')->where($map)->find();
                $call = $userData['nickname'];
            }elseif( count($address) > 1 ){
                $call = 'All';
            }
        }else{
            $map['email'] = $address;
            $userData = $model->table( C('DB_PREFIX'). 'user' )->field('nickname')->where($map)->find();
            $call = $userData['nickname'];
        }

        $users = session('user')['nickname'];


        $http_host = $_SERVER['HTTP_HOST'];
        extract($data);

        $count = count($data['nickname']);

        # 如果是单人则显示收件人姓名否则群发显示All
        /*if( $count == 1 ){
        }else{
            $call = 'All';
        }*/

        $count_s = count($data['nextPerson']);
        if( $count_s == 1 ){
            $next_name = $data['nextPerson'][0];
        }else{
            $next_name = 'All';
        }


        $subject = '[文件管理] ' .$data['file_no']. ' '.$data['version'].' 文件评审';

        $title = '<p>Dear '.$call.', </p>';

        $order_basic = '<p>详情请点击链接：<a href="http://'.$http_host.'/File/reviewDetail/id/'.$data['id'].'/version/'.$data['version'].'/num/'.$data['num'].'" target="_blank">http://'.$http_host.'/File/reviewDetail/id/'.$data['id'].'/version/'.$data['version'].'/num/'.$data['num'].'</a></p>';

        switch( $type ) {

            # 通过
            case 'PASS':

                    $body = '<p>['.$users.'] 通过了您发起的编号为 <b>'.$data['file_no'].'</b> '.$data['version'].' 的文件评审请求。</p>
                             <p>备注：'.$data['info'].'</p>';

                break;
            # 所有评审完成
            case 'PASS_S':

                    $body = '<p>['.$users.'] 通过了 ['. $data['nickname'][0] .'] 发起的编号为 <b>'.$data['file_no'].'</b> '.$data['version'].' 的文件评审，此文件已归档。</p>
                             <p>备注：'.$data['info'].'</p>';

                break;
            # 发起
            case 'INITIATE':
                $nnn = $data['num'];

                $body = '<p>['.$users.'] 发起了编号为 <b>'.$data['file_no'].'</b> '.$data['version'].' 的文件评审，请及时处理。</p>';

                $order_basic = '<span>描述：'.$data['content'].'</span><br>
                        <p>详情请点击链接：<a href="http://'.$http_host.'/File/reviewDetail/id/'.$data['id'].'/version/'.$data['version'].'/num/'.$data['num'].'" target="_blank">http://'.$http_host.'/File/reviewDetail/id/'.$data['id'].'/version/'.$data['version'].'/num/'.$data['num'].'</a></p>
';

                break;
            # 二次评审自动发起

            case 'INIT':
                $nnn = $data['num'];

                $title = '<p>Dear '.$next_name.', </p>';

                $body = '<p>[' . session('user')['nickname'] . '] 通过了 [' . $data['nickname'][0] . '] 发起的编号为 <b>'.$data['file_no'].'</b> '.$data['version'].' 的文件评审，请及时处理。</p>
                        <p>备注：'.$data['info'].'</p>';

                break;
            # DCC
            case 'DCC':
                $nnn = $data['num'];

                $body = '<p>[' . session('user')['nickname'] . '] 通过了 [' . $data['nickname'][0] . '] 发起的编号为 <b>'.$data['file_no'].'</b> '.$data['version'].' 的文件评审，请及时处理。</p>
                        <p>备注：'.$data['info'].'</p>';

                break;

            # 升版
            case 'UPGRADE':
                $body = '<p>['.$users.'] 发起了编号为 <b>'.$data['file_no'].'</b> 的文件升版评审，新版本号为 ' . $data['version'] . '，请及时处理。</p>';
                break;

            #拒绝
            case 'REFUSE':

                $body = '<p>['.$users.'] 拒绝了您发起的编号为 <b>'.$data['file_no'].'</b> '.$data['version'].' 的文件评审，请知悉。</p>
                         <p>备注：'.$data['info'].'</p>';
                break;
            #回退
            case 'ROLLBACK':

                $body = '<p>['.$users.'] 撤回了编号为 <b>'.$data['file_no'].'</b> '.$data['version'].' 的文件评审，请知悉。</p>
                
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