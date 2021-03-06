<?php
namespace Home\Controller;
use Think\Model;
use Think\Page;

class SoftwareController extends AuthController {


    /**
     * 首页
     */
    public function index(){


        $model = new model();


        if(I('get.search')){

            $softData = $model->table(C('DB_PREFIX') . 'software')->where('number LIKE "%'.I('get.search').'%" OR name LIKE "%'.I('get.search').'%" OR person LIKE "%'.I('get.search').'%"')->select();
            $count = $model->table(C('DB_PREFIX') . 'software')->where('number LIKE "%'.I('get.search').'%" OR name LIKE "%'.I('get.search').'%" OR person LIKE "%'.I('get.search').'%"')->count();
            $tab = I('get.tab') ? I('get.tab') : 'software';
            if( !in_array($tab,['software', 'ate'])) $tab = 'software';

            # 数据分页
            $page = new Page($count,15);
            $page->setConfig('prev','<span aria-hidden="true">上一页</span>');
            $page->setConfig('next','<span aria-hidden="true">下一页</span>');
            $page->setConfig('first','<span aria-hidden="true">首页</span>');
            $page->setConfig('last','<span aria-hidden="true">尾页</span>');

            if(C('PAGE_STATUS_INFO')){
                $page->setConfig ( 'theme', '<li><a href="javascript:void(0);">当前%NOW_PAGE%/%TOTAL_PAGE%</a></li>  %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%' );
            }

        }else{

            $tab = I('get.tab') ? I('get.tab') : 'software';
            if( !in_array($tab,['software', 'ate'])) $tab = 'software';


            switch ( $tab ){

                case 'software' :

                    $count = $model->table(C('DB_PREFIX') . 'software')
                        ->where("type='firmware'")
                        ->count();

                    # 数据分页
                    $page = new Page($count,15);
                    $page->setConfig('prev','<span aria-hidden="true">上一页</span>');
                    $page->setConfig('next','<span aria-hidden="true">下一页</span>');
                    $page->setConfig('first','<span aria-hidden="true">首页</span>');
                    $page->setConfig('last','<span aria-hidden="true">尾页</span>');

                    if(C('PAGE_STATUS_INFO')){
                        $page->setConfig ( 'theme', '<li><a href="javascript:void(0);">当前%NOW_PAGE%/%TOTAL_PAGE%</a></li>  %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%' );
                    }

                    $softData = $model->table(C('DB_PREFIX') . 'software')
                                      ->where("type='firmware'")
                                      ->limit($page->firstRow.','.$page->listRows)
                                      ->order('number ASC')
                                      ->select();

                    foreach ($softData as $key => &$value) {

                        $value['content'] = $model->table(C('DB_PREFIX') . 'software_log')
                                                  ->where('soft_asc =' . $value['id'])
                                                  ->order('save_time DESC')
                                                  ->field('version,save_time,soft_asc')
                                                  ->select();
                    }

                    break;

                case  'ate' :

                    $count = $model->table(C('DB_PREFIX') . 'software')
                                   ->where("type='ATE'")
                                   ->count();

                    # 数据分页
                    $page = new Page($count,15);
                    $page->setConfig('prev','<span aria-hidden="true">上一页</span>');
                    $page->setConfig('next','<span aria-hidden="true">下一页</span>');
                    $page->setConfig('first','<span aria-hidden="true">首页</span>');
                    $page->setConfig('last','<span aria-hidden="true">尾页</span>');

                    if(C('PAGE_STATUS_INFO')){
                        $page->setConfig ( 'theme', '<li><a href="javascript:void(0);">当前%NOW_PAGE%/%TOTAL_PAGE%</a></li>  %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%' );
                    }

                    $softData = $model->table(C('DB_PREFIX') . 'software')
                                      ->where("type='ATE'")
                                      ->limit($page->firstRow.','.$page->listRows)
                                      ->order('number ASC')
                                      ->select();

                    foreach ($softData as $key => &$value) {

                        $value['content'] = $model->table(C('DB_PREFIX') . 'software_log')
                                                  ->where('soft_asc ='.$value['id'])
                                                  ->order('save_time DESC')
                                                  ->field('version,save_time,soft_asc')
                                                  ->select();
                    }


                    break;
            }
        }

        if(I('get.p')){
            $this->assign('pageNumber',I('get.p')-1);
        }


        $pageShow = $page->show();

        # print_r($softData);

        $this->assign('softData', $softData);
        $this->assign('page', $pageShow);
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
            $soft['comment'] = str_replace("\n","<br>",I('post.log'));
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

                if( $software_add_id ) {

                    $this->ajaxReturn(['flag' => 1, 'msg' => '添加项目成功!']);

                }

            }
        }else{

            $model = new model();

            $pizza = session('user')['post'];
            $pieces = explode(",", $pizza);

            $pos = $model->table(C('DB_PREFIX').'position')->where('id = 8 OR id = 10')->select();

            $num = 0;

            foreach ($pos as $key=>&$val){
                foreach ($pieces as $k=>&$v){
                    if($v == $val['id']){
                        $num +=1;
                    }
                }
            }

            if($num > 0){
                $this->display();
            }else{
                $this->error('没有权限');
            }

        }
    }

    /**
     * 详情页面
     */
    public function detail(){

        $model = new model();

        $softRel = $model->table(C('DB_PREFIX').'software')->find(I('get.id'));

        $softRel['child'] = $model->table(C('DB_PREFIX').'software_log a,'.C('DB_PREFIX').'software b,'.C('DB_PREFIX').'user c')
                                  ->field('a.log,a.save_time,a.version,a.attachment,c.face,a.push_email,a.cc_email,c.nickname')
                                  ->where('b.id ='.I('get.id').' AND b.id=a.soft_asc AND a.save_person=c.id')
                                  ->order('a.save_time DESC')
                                  ->select();


        # print_r($softRel);

        foreach ($softRel['child'] as $key=>&$value){
            $value['attachment'] = json_decode($value['attachment'],true);
            $value['push_email'] = json_decode($value['push_email'],true);
            $value['cc_email'] = json_decode($value['cc_email'],true);
            foreach($value['attachment'] as $k=>&$v){
                $v['ext'] = strtolower($v['ext']);
            }
        }

        # 邮件推送抄送人

        $ccList = $model->table(C('DB_PREFIX').'department')->select();
        foreach ($ccList as $key=>&$value){
            $value['users'] = $model->table(C('DB_PREFIX').'user')->where('department ='.$value['id'])->select();
        }

        //调用父类注入部门和人员信息
        $this->getAllUsersAndDepartments();

        # print_r($softRel);

        $this->assign('softData',$softRel['child']);
        $this->assign('ccList',$ccList);
        $this->assign('softwareData',$softRel);

        $this->display();
    }

    /**
     * 页面展示
     */

    public function addLog(){


        $model = new model();

        #添加信息跟新版本
        if( IS_POST ) {
            $arr = I('post.addRel', '', false);
            $arrData = I('post.', '', false);

            $emails = json_decode($arrData['email'], true);

            $logData['soft_asc'] = $arr['subName'];
            $logData['version'] = $arr['version'];
            $logData['log'] = replaceEnterWithBr($arr['context']);
            $logData['save_time'] = time();
            $logData['save_person'] = session('user')['id'];
            $logData['attachment'] = $arr['attachment'];
            $logData['push_email'] = !empty($emails['recipient']) ? json_encode($emails['recipient'], JSON_UNESCAPED_UNICODE) : '';
            $logData['cc_email'] = !empty($emails['cc']) ? json_encode($emails['cc'], JSON_UNESCAPED_UNICODE) : '';

            $pushArr = [];
            if( !empty($logData['push_email']) ){
                foreach( $emails['recipient'] as $key=>&$value ){
                    array_push($pushArr, $value['email']);
                }
            }

            $ccArr = [];
            if( !empty($logData['cc_email']) ){
                foreach( $emails['cc'] as $key=>&$value ){
                    array_push($ccArr, $value['email']);
                }
            }

            $add_saftlog_id = M('software_log')->add($logData);
            if ($add_saftlog_id) {
                #获取当前写入的数据
                $data = $model->table(C('DB_PREFIX').'software a,'.C('DB_PREFIX').'software_log b')->where('a.id = b.soft_asc AND a.id = '.$arr['subName'].' AND '.$add_saftlog_id.'=b.id')->order('save_time DESC')->select();
                $data['push_email'] = json_decode($data[0]['push_email'],true);
                $data['cc_email'] = json_decode($data[0]['cc_email'],true);

                if( !empty($pushArr) ) $this->pushEmail($pushArr,$data[0],$ccArr);

                $this->ajaxReturn(['flag' => 1, 'msg' => '更新成功!']);
            } else {
                $this->ajaxReturn(['flag' => 0, 'msg' => '更新失败!']);
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

    /***
     * 邮件推送
     */
    # 邮件推送
    public function pushEmail( $address, $data, $cc=[] ){

        $http_host = $_SERVER['HTTP_HOST'];
        extract($data);

        # 如果是单人则显示收件人姓名否则群发显示All
        if( isset($recipient_name) ){
            $call = $recipient_name;
        }else{
            $call = 'All';
        }

        $user = session('user')['nickname'];

        $subject = ' [软件发布] '.$number.' ( '.$name.' ) 发布更新'.$version.'';
        $body = <<<HTML
<style>
.step {
    padding: 2px 5px;
    -webkit-border-radius: 2px;
    -moz-border-radius: 2px;
    border-radius: 2px;
    border: solid 1px #000;
}
.title {
    font-size: 16px;
    font-weight: 600;
}
a{
    color: #428bca;
}
</style>
<p>Dear $call,</p>
<p>[$user] 发布了<b>$number</b>( $name )的 $version 版本，请登录<a href="http://$http_host/Software/detail/id/$soft_asc" target="_blank">http://$http_host/Software/detail/id/$soft_asc</a>下载使用</p>
<p class="title">更新记录：</p>
<span>
    $log
</span>
HTML;


        # 检查邮件发送结果
        if( empty($cc) ){
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
