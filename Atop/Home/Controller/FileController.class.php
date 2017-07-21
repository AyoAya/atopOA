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
                               ->where('(a.id = b.n_id AND b.action = "apply" AND a.file_no LIKE "%'.I('get.search').'%") OR (a.id = b.n_id AND b.action = "apply" AND b.nickname LIKE "%'.I('get.search').'%")')
                               ->order('a.file_no ASC')
                               ->group('a.id')
                               ->limit($page->firstRow.','.$page->listRows)
                               ->select();
        }else{
            $indexData = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'file_log b,'.C('DB_PREFIX').'user c')
                               ->field('a.file_no,a.status,c.nickname,c.face,a.id,b.time')
                               ->where('a.id = b.n_id AND b.person = c.id AND b.action ="apply"')
                               ->limit($page->firstRow.','.$page->listRows)
                               ->order('a.file_no ASC')
                               ->group('a.id')
                               ->select();
        }

        $pageShow = $page->show();

        if(I('get.p')){
            $this->assign('pageNumber',I('get.p')-1);
        }

        # print_r($count);
        # print_r($indexData);

        $this->assign('indexData',$indexData);
        $this->assign('page',$pageShow);
        $this->display();
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
                             ->field('a.file_no,b.time,b.person,a.id,a.status')
                             ->where("a.id = b.n_id AND c.state = 1 AND b.action = 'apply'")
                             ->limit($page->firstRow.','.$page->listRows)
                             ->order('b.time DESC')
                             ->group('a.id')
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

        if(IS_POST) {

            $post = I('post.');

            # 获取评审人信息
            $post['cc'] = json_decode(html_entity_decode($post['cc']), true);
            # 日志表数据
            $logData['log'] = $post['data']['content'];
            $logData['action'] = 'initiate';
            $logData['n_id'] = $post['data']['file_no'];
            $logData['person'] = session('user')['id'];
            $logData['nickname'] = session('user')['nickname'];
            $logData['time'] = time();
            $logData['count'] = $post['num']+1;
            # 修改当前进度(已发起)
            $numData['status'] = 2;
            $numData['num'] = $post['num']+1;
            # 收集所选择的操作人
            foreach ($post['cc'] as $key => &$value) {
                $review['person'] = $value['id'];
                $review['n_id'] = $post['data']['file_no'];
                $review['count'] = $post['num']+1;
                $arr[] = $review;
            }
            # 收件人邮箱
            foreach ($post['cc'] as $key=>$val){
                $email[] = $val['email'];
                $nickname[] = $val['name'];
            }

            # 发起评审的邮件数据
            $emailData = $model->table(C('DB_PREFIX').'file_number')->where('id ='.$post['data']['file_no'])->find();
            $emailData['content'] = $post['data']['content'];
            $emailData['nickname'] = $nickname;
            $emailData['userCount'] = count($emailData['nickname']);


            # 写入日志表
            $log_id = $model->table(C('DB_PREFIX') . 'file_log')->add($logData);
            # 修改number表的状态
            $num_id = $model->table(C('DB_PREFIX').'file_number')->where('id =' . $post['data']['file_no'])->save($numData);
            # 写入评审表评审人数据
            $review_id = $model->table(C('DB_PREFIX') . 'file_review')->addAll($arr);

            if ($review_id && $num_id && $log_id ) {

                $this->pushEmail('INITIATE',$email,$emailData);
                $model->commit();
                $this->ajaxReturn(['flag' => $post['data']['file_no'], 'msg' => '发起成功！','num'=>$post['num']+1]);


            } else {
                $model->rollback();
                $this->ajaxReturn(['flag' => 0, 'msg' => '发起失败！']);
            }


        }else{
            # 默认加载的文件号
            $numberData = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'file_log b')
                ->field('a.file_no,a.id,a.num')
                ->where('b.person ='.session('user')['id'].' AND a.status <=1  AND b.n_id=a.id AND b.action = "apply"')
                ->order('file_no ASC')
                ->group('file_no')
                ->select();

            # print_r($numberData);
            //调用父类注入部门和人员信息(cc)
            $this->getAllUsersAndDepartments();
            $this->assign('numberData',$numberData);
            $this->display();
        }

    }

    /**
     * 文件详情
     */
    public function fileDetail(){
        $model = new model();
        $model->startTrans();

        $id = I('get.id');
        # number详情
        $numData = $model->table(C('DB_PREFIX').'file_number')
                         ->field('id,file_no,attachment,status,num')
                         ->where('id='.$id)
                         ->find();
        $numData['attachment'] = json_decode($numData['attachment'],true);
        # number的操作记录
        $numData['log'] = $model->table(C('DB_PREFIX').'file_log a,'.C('DB_PREFIX').'user b')
                                ->field('a.n_id,a.action,a.time,a.person,a.count,a.log,b.face,b.nickname')
                                ->where('a.n_id='.$numData['id'].' AND a.person =b.id')
                                ->order('a.time ASC')
                                ->select();

        # print_r($numData);
        $this->assign('numData',$numData);
        $this->display();

    }

    /**
     * 审批详情
     */
    public function reviewDetail(){
        $model = new model();

        $id = I('get.id');
        $num = I('get.num');

        if($num){
            # 发起的数据
            $person = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'file_log b,'.C('DB_PREFIX').'user c,'.C('DB_PREFIX').'file_attachment d')
                            ->field('a.id ,a.file_no,b.log,c.nickname,a.status,c.face,b.time,a.num,c.id user_id')
                            ->where('a.id='.$id.' AND b.n_id = a.id AND c.id = b.person AND b.action = "initiate" AND b.count ='.$num)
                            ->find();
            $person['attachment'] = $model->table(C('DB_PREFIX').'file_attachment')->field('attachment')->where('n_id ='.$id.' AND count='.$num)->find();
            $person['attachment'] = json_decode($person['attachment']['attachment'],true);

            # 评审人数据
            $numData = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'file_review c,'.C('DB_PREFIX').'user d')
                             ->field('a.id,c.person,c.status,d.email,d.nickname,d.face')
                             ->where('a.id='.$id.' AND c.n_id = a.id AND c.n_id = a.id AND c.person = d.id AND c.count ='.$num)
                             ->group('c.person')
                            ->select();

            foreach($numData as $key=>&$val){
                $val['log'] = $model->table(C('DB_PREFIX').'file_log')
                                    ->where('n_id ='.$id.' AND person ='.$val['person'].' AND count ='.$num)
                                    ->find();

            }
            $tmpNumData = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'file_log b,'.C('DB_PREFIX').'file_review c,'.C('DB_PREFIX').'user d')
                ->field('a.id,c.person,c.status,d.email,b.log,d.nickname,d.face')
                ->where('a.id='.$id.' AND c.n_id = a.id AND c.n_id = a.id AND c.person = d.id AND c.person ='.session('user')['id'].' AND b.count ='.$num.' AND c.count ='.$num)
                ->group('c.person')
                ->find();
            # 统计评审人
            foreach ($numData as $key=>&$value){
                $arr[] = $value['person'];
            }

            # print_r($tmpNumData);
            # print_r($numData);
            # print_r($person);
            # print_r($num);
            $this->assign('person',$person);
            $this->assign('num',$num);
            $this->assign('tmpNumData',$tmpNumData);
            $this->assign('arr',$arr);
            $this->assign('numData',$numData);
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

        if( IS_POST ){
            $post = I('post.');

            $a = $post['numberNum'];

            # 邮件推送数据
            $emailData = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'file_log b,'.C('DB_PREFIX').'user c')
                               ->field('a.id,a.file_no,a.status,a.attachment,a.num,b.log,b.person,c.nickname')
                               ->where('a.id='.$post['numberId'].' AND a.id =b.n_id AND a.num = '.$post['numberNum'].' AND b.count ='.$post['numberNum'].' AND b.action ="initiate" AND c.id = b.person')
                               ->find();

            $emailData['user'] = $emailData['nickname'];
            $emailData['content'] = $emailData['log'];
            $emailData['info'] = $post['context'];
            $email = $model->table(C('DB_PREFIX').'file_log a,'.C('DB_PREFIX').'user b')
                ->where('a.n_id ='.$post['numberId'].' AND a.count ='.$post['numberNum'].' AND a.action ="initiate" AND a.person = b.id')
                ->field('b.email')
                ->find();



            $cc = $model->table(C('DB_PREFIX').'file_review a,'.C('DB_PREFIX').'user c')
                        ->field('c.email')
                        ->where('a.n_id ='.$post['numberId'].' AND a.count ='.$post['numberNum'].' AND a.person = c.id')
                        ->select();
            foreach ($cc as $key=>&$val){
                $ccs[] = $val['email'];
            }

            /*print_r($emailData);
            print_r($email);
            print_r($post);
            print_r($ccs);

            die();*/

            # 如果审批人提交的是通过
            if($post['type'] == 'pass'){
                $review['status'] = 2;

                $log['time'] = time();
                $log['action'] = 'pass';
                $log['log'] = $post['context'];
                $log['person'] = session('user')['id'];
                $log['n_id'] = $post['numberId'];
                $log['count'] = $post['numberNum'];
                $log['nickname'] = session('user')['nickname'];

                /*print_r($emailData);
                print_r($post);
                print_r($email);
                die();*/


                # 修改审批人审批的状态
                $review_id = $model->table(C('DB_PREFIX').'file_review')->where('n_id ='.$post['numberId'].' AND person ='.session('user')['id'].' AND count='.$post['numberNum'])->save($review);
                # 写入审批时留下的数据
                $log_id = $model->table(C('DB_PREFIX').'file_log')->add($log);

                if($review_id && $log_id){
                    # 如果操作成功 统计操作人数量
                    $i = $model->table(C('DB_PREFIX').'file_review')->where('n_id ='.$post['numberId'].' AND count ='.$post['numberNum'])->select();
                    $count = $model->table(C('DB_PREFIX').'file_review')->where('n_id ='.$post['numberId'].' AND count ='.$post['numberNum'])->count();

                    $num = 0;

                    foreach($i as $key=>&$value){
                        if( $value['status'] != 1){
                            $num += 1;
                        }

                    }
                    # 如果所有操作人完成操作
                    if($num == $count){
                        foreach ($i as $key=>&$val){
                            $tmpArr[] = $val['status'];
                        }
                        # 如果所有评审人评审完成后有某个评审是拒绝的状体 修改number表的状态
                        if(in_array(0,$tmpArr)){

                            $tmpNum['num'] = $a;
                            $tmpNum['status'] = 0;

                            $number_id = $model->table(C('DB_PREFIX').'file_number')->where('id ='.$post['numberId'])->save($tmpNum);
                            if($number_id){

                                $this->pushEmail('PASS',$email,$emailData,$ccs);
                                $model->commit();
                                $this->ajaxReturn(['flag'=>1,'msg'=>'操作成功！']);
                            }else{
                                $model->rollback();
                                $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败！']);
                            }
                        }else{
                            # 如果所有评审人评审完成后有所有评审是成功的状体 修改number表的状态
                            $tmpNum['status'] = 3;
                            $number_id = $model->table(C('DB_PREFIX').'file_number')->where('id ='.$post['numberId'])->save($tmpNum);
                            if($number_id){

                                $this->pushEmail('PASS_S',$email,$emailData,$ccs);
                                $model->commit();
                                $this->ajaxReturn(['flag'=>1,'msg'=>'操作成功！']);
                            }else{
                                $model->rollback();
                                $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败！']);
                            }
                        }
                    }else{
                        $this->pushEmail('PASS',$email,$emailData,$ccs);
                        $model->commit();
                        $this->ajaxReturn(['flag'=>1,'msg'=>'操作成功！']);
                    }

                }else{
                    $model->rollback();
                    $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败！']);
                }

            }else{
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
                $review_id = $model->table(C('DB_PREFIX').'file_review')->where('n_id ='.$post['numberId'].' AND person ='.session('user')['id'].' AND count ='.$post['numberNum'])->save($review);
                $log_id = $model->table(C('DB_PREFIX').'file_log')->add($log);

                if($review_id && $log_id){
                    # 统计评审人总数
                    $i = $model->table(C('DB_PREFIX').'file_review')->where('n_id ='.$post['numberId'].' AND count ='.$post['numberNum'])->select();
                    $count = $model->table(C('DB_PREFIX').'file_review')->where('n_id ='.$post['numberId'].' AND count ='.$post['numberNum'])->count();
                    $num = 0;

                    foreach($i as $key=>$value){
                        if( $value['status'] != 1){
                            $num += 1;
                        }

                    }
                    # 如果所有评审人都操作完成
                    if($num == $count){
                        # 修改number表数据
                        $tmpNum['status'] = 0;
                        $tmpNum['num'] = $a;
                        $number_id = $model->table(C('DB_PREFIX').'file_number')->where('id ='.$post['numberId'])->save($tmpNum);
                        if($number_id){

                            $this->pushEmail('REFUSE',$email,$emailData,$ccs);
                            $model->commit();
                            $this->ajaxReturn(['flag'=>1,'msg'=>'操作成功！']);
                        }else{
                            $model->rollback();
                            $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败！']);
                        }
                    }else{

                        $this->pushEmail('REFUSE',$email,$emailData,$ccs);
                        $model->commit();
                        $this->ajaxReturn(['flag'=>1,'msg'=>'操作成功！']);
                    }

                }else{
                    $model->rollback();
                    $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败！']);
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

        # print_r(I('post.'));
        if(IS_POST){
            $tmpData['num'] = $num+1;
            $tmpData['status'] = 1;
            $tmpId = $model->table(C('DB_PREFIX').'file_number')->where('id ='.$id)->save($tmpData);

            $tmpLog['n_id'] = $id;
            $tmpLog['action'] = 'rollback';
            $tmpLog['time'] = time();
            $tmpLog['person'] = session('user')['id'];
            $tmpLog['count'] = $num;
            $logData['nickname'] = session('user')['nickname'];

            $tmp_id = $model->table(C('DB_PREFIX').'file_log')->add($tmpLog);

            if($tmpId && $tmp_id){
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

        $users = session('user')['nickname'];

        $http_host = $_SERVER['HTTP_HOST'];
        extract($data);

        # 如果是单人则显示收件人姓名否则群发显示All
        if( isset($data['user']) ){
            $call = $data['user'];
        }else{
            $call = 'All';
        }


        $subject = '[文件管理] ' .$data['file_no']. ' 文件评审';

        $title = '<p>Dear '.$call.', </p>';

        $order_basic = '
                        <span>描述 ：'.$data['content'].'</span><br>
                        <p>详情请点击链接：<a href="http://'.$http_host.'/File/reviewDetail/id/'.$data['id'].'/num/'.$data['num'].'" target="_blank">http://'.$http_host.'/File/reviewDetail/id/'.$data['id'].'/num/'.$data['num'].'</a></p>
';


        switch( $type ) {

            # 通过
            case 'PASS':

                    $body = '<p>[ '.$users.' ] 通过了您发起的编号为 <b>'.$data['file_no'].'</b> 的文件评审请求。</p>
                             <p>备注：'.$data['info'].'</p>
 ';

                break;
            # 通过
            case 'PASS_S':

                    $body = '<p>[ '.$users.' ] 通过了您发起的编号为 <b>'.$data['file_no'].'</b> 的文件评审，此文件已归档。</p>
                             <p>备注：'.$data['info'].'</p>
';

                break;
            # 完成
            case 'INITIATE':
                $body = '<p>[ '.$users.' ] 发起了编号为 <b>'.$data['file_no'].'</b> 的文件评审，请及时处理。</p>
                ';
                break;

            # 撤回
            /*case 'ROLLBACK':
                $body = '<p>['.$data['middle'][0]['nickname'].'] 将 <b>'.$data['title'].'</b> 由 <span style="padding: 3px 5px; border-radius:1px;border: 1px solid #555;">Step'.$data['nowData'][0]['step'].' - '.$data['nowData'][0]['name'].'</span> 回退到 <span style="padding: 3px 5px; border-radius:1px;border: 1px solid #555;">Step'.$data['before_data']['step'].' - '.$data['before_data']['name'].'</span>，请及时处理！</p>
                <span><b>内容 ： </b>'.htmlspecialchars_decode($data['save_log']['log']).'</span>
                ';
                break;*/

            #拒绝
            case 'REFUSE':

                $body = '<p>[ '.$users.' ] 拒绝了您发起的编号为 <b>'.$data['file_no'].'</b> 的文件评审，请知悉。</p>
                         <p>备注：'.$data['info'].'</p>
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