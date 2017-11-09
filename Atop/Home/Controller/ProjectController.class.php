<?php
namespace Home\Controller;
use Think\Page;
use Think\Model;

class ProjectController extends AuthController
{
    # 默认抄送人列表
    private $defaultCClist = [
        ['id'=>54, 'email'=>'dingzheng@atoptechnology.com', 'nickname'=>'丁征']
    ];

    # 抄送人列表
    private $ccList = array(
        'dingzheng@atoptechnology.com'
    );

    # 初始化页面
    public function index()
    {
        $user = M('User');
        $userInfo = $user->find(session('user')['id']);
        $this->assign('userInfo',$userInfo);
        # print_r($userInfo);
        $project = M('Project');
        $plan = M('ProjectPlan');
        $count = $project->where('pj_child=0 AND pj_show=1')->count();
        //数据分页
        $page = new Page($count,C('LIMIT_SIZE'));
        $page->setConfig('prev','<span aria-hidden="true">上一页</span>');
        $page->setConfig('next','<span aria-hidden="true">下一页</span>');
        $page->setConfig('first','<span aria-hidden="true">首页</span>');
        $page->setConfig('last','<span aria-hidden="true">尾页</span>');
        if(C('PAGE_STATUS_INFO')){
            $page->setConfig ( 'theme', '<li><a href="javascript:void(0);">当前%NOW_PAGE%/%TOTAL_PAGE%</a></li>  %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%' );
        }
        $projects = $project->where('pj_child=0 AND pj_show=1')->order('pj_create_time DESC')->limit($page->firstRow.','.$page->listRows)->select();
        $pageShow = $page->show();
        if(I('get.p')){
            $this->assign('pagenumber',I('get.p')-1);
        }
        $this->assign('limitsize',C('LIMIT_SIZE'));
        $this->assign('pageShow',$pageShow);
        foreach($projects as $k=>&$v){
            $plans = $plan->where('plan_project='.$v['id'])->select();
            $completeResult = array();  //准备存放已完成节点数据
            foreach($plans as $key=>&$value){
                if( !empty($value['complete_time']) ){   //查看子节点结束时间是否为空，为空则表示该进度未完
                    array_push($completeResult,$value);
                }
            }
            $getLastId = $plan->where('plan_project='.$v['id'])->max('gate'); //获取最后一个步骤
            $nums = array();
            foreach($completeResult as $key=>&$value){
                array_push($nums,$value['gate']);
            }
            if( !empty($nums) ){
                $maxNum = max($nums);   //获取到当前已完成gate的最后一个节点
                //$gateRes = $plan->find($maxNum);
            }else{
                $maxNum = 0;
            }
            $nodeSize = $plan->where('plan_project='.$v['id'].' AND gate='.$maxNum)->count();  //获取gate的子节点数量
            if($maxNum){
                $countNode = $plan->where('plan_project='.$v['id'].' AND gate='.$maxNum.' AND complete_time<>""')->count();   //获取结束时间不为空的节点个数
            }else{
                $countNode = $plan->where('plan_project='.$v['id'].' AND gate='.$maxNum.' AND complete_time<>""')->count();   //获取结束时间不为空的节点个数
            }
            if( $nodeSize == $countNode ){  //如果已完成的节点和items数量一致则说明当前gate已经完成
                if( $maxNum == $getLastId ){
                    $progress['progress'] = '已完成';
                    $progress['class'] = 'label-success';
                }else{
                    $gateNode = $maxNum+1;
                    $nowNodeData = $plan->where('plan_project='.$v['id'].' AND gate='.$gateNode)->group('gate')->select()[0];
                    $progress['progress'] = 'Gate'.$nowNodeData['gate'].' - '.$nowNodeData['mile_stone'];
                    $progress['class'] = 'label-primary';
                }
            }else{
                $gateNode = $maxNum;
                $nowNodeData = $plan->where('plan_project='.$v['id'].' AND gate='.$gateNode)->group('gate')->select()[0];
                $progress['progress'] = 'Gate'.$nowNodeData['gate'].' - '.$nowNodeData['mile_stone'];
                $progress['class'] = 'label-primary';
            }
            $v['node'] = $progress;
        }
        # print_r($projects);

        $this->assign('projects',$projects);
        $this->display();
    }

    # 添加页面
    public function add()
    {
        if(IS_POST)
        {
            $dataArr = array();
            $planArr = json_decode(html_entity_decode(I('post.planData')),true);    //将项目计划数据转换成数组
            //print_r($planArr);die;
            $idList = I('post.idlist');
            $idstr = '';
            foreach($idList as $key=>&$value){
                if( $value['name'] != 'pj_management' ){
                    $idstr .= $value['value'].',';//参与该项目的人员id(不包括添加人/项目管理人自己)
                }
            }
            $idstr = substr($idstr,0,-1);
            //接收用户提交数据
            $dataArr['pj_num'] = I('post.pj_num');
            $dataArr['pj_name'] = I('post.pj_name');
            $dataArr['pj_responsible'] = I('post.pj_responsible');
            $dataArr['pj_add_pro_time'] = I('post.pj_add_pro_time');
            $dataArr['pj_management'] = I('post.pj_management');
            $dataArr['pj_design'] = I('post.pj_design');
            $dataArr['pj_family'] = I('post.pj_family');
            $dataArr['pj_fw_development'] = I('post.pj_fw_development');
            $dataArr['pj_type'] = I('post.pj_type');
            $dataArr['pj_structure_design'] = I('post.pj_structure_design');
            $dataArr['pj_standard_name'] = I('post.pj_standard_name');
            $dataArr['pj_light_design'] = I('post.pj_light_design');
            $dataArr['pj_platform'] = I('post.pj_platform');
            $dataArr['pj_ate_design'] = I('post.pj_ate_design');
            $dataArr['pj_market_demand'] = I('post.pj_market_demand');
            $dataArr['pj_dvt_test'] = I('post.pj_dvt_test');
            $dataArr['pj_target_customer'] = I('post.pj_target_customer');
            $dataArr['pj_market'] = I('post.pj_market');
            $dataArr['pj_target_cost'] = I('post.pj_target_cost');
            $dataArr['pj_describe'] = I('post.pj_describe');
            $dataArr['pj_create_person'] = session('user')['nickname'];
            $dataArr['pj_create_person_id'] = session('user')['id'];
            $dataArr['pj_participate'] = $idstr;
            $dataArr['pj_create_time'] = time();
            $dataArr['pj_child'] = I('post.pj_child');
            $dataArr['pj_belong'] = I('post.pj_belong');
            $project = M('Project');
            $plan = M('ProjectPlan');
            $id = $project->add($dataArr);
            if( $id )
            {
                foreach($planArr['planData'] as $key=>&$value){
                    $value['plan_project'] = $id;
                }
                if( $plan->addAll($planArr['planData']) ){
                    $this->pushEmail('add',$idstr.','.session('user')['id'],$dataArr,$id);//项目新增成功后，给参与该项目的所有人推送邮件
                    $this->ajaxReturn( array('flag' => $id,'msg' => '添加成功') );
                }
            }else{
                $this->ajaxReturn( array('flag' => 0,'msg' => '添加失败') );
            }
        }else{
            # 检测进入该页面的用户是否是项目管理员角色，不是则提示没有权限访问该页面
            $user = M('User');
            $userInfo = $user->find(session('user')['id']);
            if( $userInfo['position'] != 1780 ){
                $this->error('你没有权限');
                exit;
            }
            if( I('get.child') && I('get.child') == 'true' ){//检测添加的项目是否为子项目
                if( I('get.belong') && preg_match('/^[1-9]([0-9])?$/',I('get.belong')) ){//如果是子项目则必须包含父项目的id
                    $param['pj_child'] = 1;
                    $param['pj_belong'] = I('get.belong');
                    $param['title'] = '子项目信息录入';
                    //print_r('该项目属于'.I('get.belong').'下的子项目');
                }else{
                    $this->error('参数错误');
                }
            }else{
                $param['pj_child'] = 0;
                $param['pj_belong'] = 0;
                $param['title'] = '项目信息录入';
            }
            $this->assign('param',$param);
            $users = M('User')->where('id<>1 AND state=1')->select();
            $this->assign('users',$users);
            $this->display();
        }
    }

    # 编辑
    public function edit(){
        # 检测进入该页面的用户是否是项目管理员角色，不是则提示没有权限访问该页面
        $user = M('User');
        $userInfo = $user->find(session('user')['id']);
        if( $userInfo['position'] != 1780 ){
            $this->error('你没有权限');
            exit;
        }
        if( IS_POST ){
            if( I('post._type') == 'plan' ){
                $saveData = I('post.');
                $id = M('ProjectPlan')->save($saveData);
                if( $id !== false ){
                    $this->ajaxReturn( array('flag'=>1, 'msg'=>'修改成功') );
                }else{
                    $this->ajaxReturn( array('flag'=>0, 'msg'=>'修改失败') );
                }
            }else{
                $idList = I('post.idlist');
                $idstr = '';
                foreach($idList as $key=>&$value){
                    if( $value['name'] != 'pj_management' ){
                        $idstr .= $value['value'].',';//参与该项目的人员id(不包括添加人/项目管理人自己)
                    }
                }
                $idstr = substr($idstr,0,-1);
                //接收用户提交数据
                $dataArr['id'] = I('post.id');
                $dataArr['pj_num'] = I('post.pj_num');
                $dataArr['pj_name'] = I('post.pj_name');
                $dataArr['pj_responsible'] = I('post.pj_responsible');
                $dataArr['pj_management'] = I('post.pj_management');
                $dataArr['pj_design'] = I('post.pj_design');
                $dataArr['pj_family'] = I('post.pj_family');
                $dataArr['pj_fw_development'] = I('post.pj_fw_development');
                $dataArr['pj_type'] = I('post.pj_type');
                $dataArr['pj_structure_design'] = I('post.pj_structure_design');
                $dataArr['pj_standard_name'] = I('post.pj_standard_name');
                $dataArr['pj_light_design'] = I('post.pj_light_design');
                $dataArr['pj_platform'] = I('post.pj_platform');
                $dataArr['pj_ate_design'] = I('post.pj_ate_design');
                $dataArr['pj_market_demand'] = I('post.pj_market_demand');
                $dataArr['pj_dvt_test'] = I('post.pj_dvt_test');
                $dataArr['pj_target_customer'] = I('post.pj_target_customer');
                $dataArr['pj_market'] = I('post.pj_market');
                $dataArr['pj_target_cost'] = I('post.pj_target_cost');
                $dataArr['pj_describe'] = I('post.pj_describe');
                $dataArr['pj_create_person'] = session('user')['nickname'];
                $dataArr['pj_create_person_id'] = session('user')['id'];
                $dataArr['pj_participate'] = $idstr;
                $id = M('Project')->save($dataArr);
                if( $id !== false ){
                    $this->ajaxReturn( array('flag'=>1, 'msg'=>'修改成功') );
                }else{
                    $this->ajaxReturn( array('flag'=>0, 'msg'=>'修改失败') );
                }
            }
        }else{
            if( I('get.id') && preg_match('/^[1-9]([0-9]{1,})?$/',I('get.id'))){
                $projects = M('Project')->find(I('get.id'));
                //print_r($projects);
                $partivipates = explode(',',$projects['pj_participate']);
                $this->assign('participates',$partivipates);
                $this->assign('projects',$projects);
                $plans = M('ProjectPlan')->field('id,gate,mile_stone,items,plan_start_time,plan_stop_time')->where('plan_project='.I('get.id'))->order('id ASC')->select();
                //print_r($plans);
                $this->assign('plans',$plans);
                $users = M('User')->where('id<>1 AND state=1')->select();
                $this->assign('users',$users);
                $this->display();
            }else{
                $this->error('参数错误');
                exit;
            }
        }
    }

    # 邮件推送
    public function pushEmail($category, $pushIdList, $emailData, $iid=''){
        $user = M('user');
        $map['id'] = array('in',$pushIdList);//查询所有参与该项目人员的信息
        $pushPersonList = $user->field('nickname,email')->where($map)->select();
        foreach($pushPersonList as $key=>$value){
            $emailList[] = $value['email'];//打包推送人列表邮件
        }
        $httpHost = $_SERVER['HTTP_HOST'];
        if( $category == 'add' ){//如果类别的是add，则说明是项目新增的时候推送的邮件，如果是update则是修改的时候推送邮件
            extract($emailData);
            $pj_create_time = date('Y-m-d H:i:s');
            $body = <<<HTML
<style>
.table {
    color: #333;
    font-size: 14px;
    width: 500px;
    border-right: solid 1px #ddd;
    border-bottom: solid 1px #ddd;
    margin-bottom: 10px;
}
.table td {
    border-top: solid 1px #ddd;
    border-left: solid 1px #ddd;
}
.table td.key {
    font-weight: bold;
    padding: 10px 20px 10px 10px;
}
.table td.value {
    padding: 10px 10px 10px 20px;
}
.table .key {
    text-align: right;
    background: #f5f5f5;
}
</style>
<p>Dear All,</p>
<p>项目管理员 [ $pj_management ] 在系统中添加了新的项目</p>
<table class="table" cellspacing="0" cellpadding="0">
    <tbody>
        <tr>
            <td class="key">项目编号</td>
            <td class="value">$pj_num</td>
        </tr>
        <tr>
            <td class="key">项目名称</td>
            <td class="value">$pj_name</td>
        </tr>
        <tr>
            <td class="key">项目负责人</td>
            <td class="value">$pj_responsible</td>
        </tr>
        <tr>
            <td class="key">硬件设计</td>
            <td class="value">$pj_design</td>
        </tr>
        <tr>
            <td class="key">FW开发</td>
            <td class="value">$pj_fw_development</td>
        </tr>
        <tr>
            <td class="key">结构件设计</td>
            <td class="value">$pj_structure_design</td>
        </tr>
        <tr>
            <td class="key">光器件设计</td>
            <td class="value">$pj_light_design</td>
        </td>
        <tr>
            <td class="key">ATE设计</td>
            <td class="value">$pj_ate_design</td>
        </tr>
        <tr>
            <td class="key">DVT测试</td>
            <td class="value">$pj_dvt_test</td>
        </tr>
        <tr>
            <td class="key">市场部</td>
            <td class="value">$pj_market</td>
        </td>
        <tr>
            <td class="key">项目管理员</td>
            <td class="value">$pj_management</td>
        </tr>
        <tr>
            <td class="key">立项时间</td>
            <td class="value" colspan="4">$pj_add_pro_time</td>
        </tr>
    </tbody>
</table>
<div>详情请查看：<a href="http://$httpHost/Project/details/id/$iid">http://$httpHost/Project/details/id/$iid</a></div>
HTML;
            if( $emailData['pj_child'] == 1 ){
                $subject = '[项目管理] 新项目立项，'.$pj_num.' / '.$pj_name.' [子项目]';
            }else{
                $subject = '[项目管理] 新项目立项，'.$pj_num.' / '.$pj_name;
            }
        }elseif( $category == 'update' ){
            extract($emailData);
            $pj_create_time = date('Y年m月d日 H:i:s');
            if( $emailData['pj_child'] == 1 ){
                $subject = '[项目管理] 项目计划更新，'.$pj_num.' / '.$pj_name.' [子项目]';
            }else{
                $subject = '[项目管理] 项目计划更新，'.$pj_num.' / '.$pj_name;
            }
            extract($emailData['gate']);
            $body = <<<HTML
<style>
p {
    font-size: 14px;
    color: #333;
}
.main {
    padding: 15px;
    margin: 15px 0;
    border: dashed 1px #ddd;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
    box-sizing: border-box;
}
</style>
<p>Dear All,</p>
<p>项目管理员 [ $pj_create_person ] 更新了 [ $pj_num / $pj_name ] 的项目计划。</p>
<p class="main">更新项：Gate$gate [ $mile_stone ] / $items</p>
<p>详情请查看：<a href="http://$httpHost/Project/details/tab/plan/id/$iid">http://$httpHost/Project/details/tab/plan/id/$iid</a></p>
HTML;
        }elseif( $category == 'document' ){
            extract($emailData);
            $pj_create_time = date('Y年m月d日 H:i:s');
            if( $emailData['pj_child'] == 1 ){
                $subject = '[项目管理] 归档文件更新，'.$pj_num.' / '.$pj_name.' [子项目]';
            }else{
                $subject = '[项目管理] 归档文件更新，'.$pj_num.' / '.$pj_name;
            }
            extract($emailData['gate']);
            $body = <<<HTML
<style>
p {
    font-size: 14px;
    color: #333;
}
.main {
    padding: 15px;
    margin: 15px 0;
    border: dashed 1px #ddd;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
    box-sizing: border-box;
}
</style>
<p>Dear All,</p>
<p>项目管理员 [ $pj_management ] 在 [ $pj_num / $pj_name ] 的 [ Gate$gate / $mile_stone ] 上传了新的文件</p>
<p class="main">文件：$filename</p>
<p>详情请查看：<a href="http://$httpHost/Project/details/tab/document/id/$iid">http://$httpHost/Project/details/tab/document/id/$iid</a></p>
HTML;
        }elseif( $category == 'assoc' ){

        }else{
            extract($emailData);
            $pj_create_time = date('Y年m月d日 H:i:s');
            if( $emailData['pj_child'] == 1 ){
                $subject = '[项目管理] 讨论区更新，'.$pj_num.' / '.$pj_name.' [子项目]';
            }else{
                $subject = '[项目管理] 讨论区更新，'.$pj_num.' / '.$pj_name;
            }
            extract($emailData['gate']);
            $context = htmlspecialchars_decode($context);//编辑器内容转换
            $body = <<<HTML
<style>
p {
    font-size: 14px;
    color: #333;
}
.context {
    padding: 15px;
    margin: 15px 0;
    border: dashed 1px #ddd;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
    box-sizing: border-box;
}
.prompt {
    color: #d9534f;
    margin-top: 10px;
}
</style>
<p>Dear All,</p>
<div>[ $nickname ] 在 [ $pj_num / $pj_name ] 的讨论区发表了新内容 ( $floor # )</div>
<div class="context">$discuss_context</div>
<div>详情请查看：<a href="http://$httpHost/Project/details/tab/discuss/id/$iid/floor/$floor">http://$httpHost/Project/details/tab/discuss/id/$iid/floor/$floor</a></div>
<p class="prompt">注：内容由程序进行过滤，部分标签（如：图像/文件/表情）无法显示</p>
HTML;
        }
        send_Email($emailList,'',$subject,$body,$this->ccList);
    }

    # 回复邮件推送
    public function pushEmails($category, $pushIdList, $emailData, $cc){

        $map['id'] = array('in',$pushIdList);//查询所有参与该项目人员的信息

        $httpHost = $_SERVER['HTTP_HOST'];

        if( $category == 'REPLY'){

            $subject = '[项目管理] 项目讨论回复，'.$emailData['pj_num'].' / '.$emailData['pj_name'];


            $body ='<p>Dear '.$emailData['nickname'].'，</p>
<p>['.session('user')['nickname'].'] 在 '.$emailData['pj_num'].'/'.$emailData['pj_name'].' 的讨论区下回复了您发表的评论： </p>
<p>'.$emailData['discuss_context'].'</p><br/>
<p><b>回复内容如下：</b></p>
<p>'.$emailData['content'].'</p>
<p>详情请查看：<a href="http://'.$httpHost.'/Project/details/tab/discuss/id/'.$emailData['id'].'">http://'.$httpHost.'/Project/details/tab/discuss/id/'.$emailData['id'].'</a></p>';

            send_Email($pushIdList,'',$subject,$body,$cc);
        }

    }

    # 详情页
    public function details()
    {
        # 根据tab参数判断用户访问的版块，如果为空则默认访问首个模块
        $get = I('get.tab') ? I('get.tab') : 'overview';
        # 将获取的id注入模板
        if( I('get.id') && I('get.id') != '' )
        {
            $getID = I('get.id');
            $this->assign('getID',$getID);
            $project = M('Project');
            $auth = $project->field('pj_create_person_id,pj_participate')->find($getID);



            # print_r($auth);

            //$this->assign('auth',$auth['pj_participate']);
            $this->assign('auth',$auth['pj_create_person_id']);
            $this->assign('participate',$auth['pj_create_person_id'].','.$auth['pj_participate']);
        }else{
            $this->error('错误');
        }
        $project = M('Project');
        $basicInfo = $project->field('id,pj_num,pj_name,pj_create_time,pj_create_person,pj_child,pj_belong')->find($getID);
        if( $basicInfo['pj_child'] == 1 ){
            $subjectsData = $project->field('id,pj_num,pj_name')->find($basicInfo['pj_belong']);
            $basicInfo['label_info'] = '隶属于 [ <a href="http://'.$_SERVER['HTTP_HOST'].'/Project/details/id/'.$subjectsData['id'].'">'.$subjectsData['pj_num'].' / '.$subjectsData['pj_name'].'</a> ]';
            $basicInfo['label_class'] = 'label label-warning';
            $basicInfo['label_text'] = '子项目';
        }
        $this->assign('basicInfo',$basicInfo);
        switch( $get )
        {
            case 'overview':    // 项目概览
                $project = M('Project');
                $plan = M('ProjectPlan');
                $projects = $project->find($getID);
                $plans = $plan->where('plan_project='.$getID)->select();
                $completeResult = array();  //准备存放已完成节点数据
                foreach($plans as $key=>&$value){
                    if( !empty($value['complete_time']) ){   //查看子节点结束时间是否为空，为空则表示该进度未完
                        array_push($completeResult,$value);
                    }
                }
                $getLastId = $plan->where('plan_project='.$getID)->max('gate'); //获取最后一个步骤
                $nums = array();
                foreach($completeResult as $key=>&$value){
                    array_push($nums,$value['gate']);
                }
                if( !empty($nums) ){
                    $maxNum = max($nums);   //获取到当前已完成gate的最后一个节点
                    $gateRes = $plan->find($maxNum);
                }else{
                    $maxNum = 0;
                }
                $nodeSize = $plan->where('plan_project='.$getID.' AND gate='.$maxNum)->count();  //获取gate的子节点数量
                //print_r($plan->getLastSql());
                if($maxNum){
                    $countNode = $plan->where('plan_project='.$getID.' AND gate='.$maxNum.' AND complete_time<>""')->count();   //获取结束时间不为空的节点个数
                }else{
                    $countNode = $plan->where('plan_project='.$getID.' AND gate='.$maxNum.' AND complete_time<>""')->count();   //获取结束时间不为空的节点个数
                }
                if( $nodeSize == $countNode ){  //如果已完成的节点和items数量一致则说明当前gate已经完成
                    if( $maxNum == $getLastId ){
                        $progress['progress'] = '已完成';
                        $progress['class'] = 'label-success';
                    }else{
                        $gateNode = $maxNum+1;
                        $nowNodeData = $plan->where('plan_project='.$getID.' AND gate='.$gateNode)->group('gate')->select()[0];
                        $progress['progress'] = 'Gate'.$nowNodeData['gate'].' - '.$nowNodeData['mile_stone'];
                        $progress['class'] = 'label-primary';
                    }
                }else{
                    $gateNode = $maxNum;
                    $nowNodeData = $plan->where('plan_project='.$getID.' AND gate='.$gateNode)->group('gate')->select()[0];
                    $progress['progress'] = 'Gate'.$nowNodeData['gate'].' - '.$nowNodeData['mile_stone'];
                    $progress['class'] = 'label-primary';
                }
                $this->assign('progress',$progress);
                $this->assign('projects',$projects);
                break;

            case 'plan':    // 项目计划

                $plan = M('ProjectPlan');
                $planResult = $plan->field('id,gate,mile_stone,items,plan_start_time,plan_stop_time,status')->where('plan_project='.$getID)->order('gate,id ASC')->select();    //获取项目计划信息

                $steps = array();   // 存放步骤节点
                $step = 1;

                foreach($planResult as $key=>&$value){
                    array_push($steps,$value['gate']);
                    $num = 0;
                    foreach($planResult as $k=>&$v){
                        if($value['gate'] == $v['gate']){
                            $num++;
                        }
                    }
                    # 统计需合并的行数
                    if( $step == $value['gate'] ){
                        $value['rowspan'] = $num;
                        $step++;
                    }
                    # 获取该节点的计划信息
                    $nodeResult = $plan->field('start_time,complete_time,comments')->where('plan_project='.$getID.' AND id='.$value['id'])->order('gate,id ASC')->select()[0];
                    $value['plan'] = $nodeResult;
                }
                # 获得所有节点步骤
                $uniqueStep = array_unique($steps);
                $emptyPlan = array();
                foreach($planResult as $key=>&$value){
                    if( empty($value['plan']) ){
                        array_push($emptyPlan,$value['gate']);  //如果数据为空则表示该步骤未完成
                    }else{  //如果数据存在但结束时间为空也应表示未完成
                        if( empty($value['plan']['complete_time']) ){   //查看结束时间是够为空，为空则表示该步骤尚未完成便无法编辑下一步
                            array_push($emptyPlan,$value['gate']);
                        }
                    }
                }
                # 获取已完成步骤和下一个步骤
                $uniquePlan = array_unique($emptyPlan);
                $uniqueResult = array_diff($uniqueStep,$uniquePlan);
                array_push($uniqueResult,max($uniqueResult)+1);     //get next step.
                $uniqueResult = array_values($uniqueResult);

                # 根据show键控制显示隐藏编辑按钮
                foreach($planResult as $key=>&$value){
                    # 当用户完成某一项节点时，添加class为succ，未完成则标识为acti

                    if( !empty($value['plan']) ){
                        if( $value['plan']['start_time'] != '' ){
                            $value['plan']['class'] = 'succ';
                        }else{
                            $value['plan']['class'] = 'acti';
                        }
                        //$value['plan']['comments_br'] = replaceEnterWithBr($value['plan']['comments']);
                        $value['plan']['comments_entity'] = htmlspecialchars($value['plan']['comments']);   # 为保留用户撤销后维持原有的格式
                        if( $value['plan']['start_time'] != '' && $value['plan']['complete_time'] != '' ){
                            $value['plan']['state_class'] = 'label-success';
                            $value['plan']['state_text'] = 'success';
                        }elseif( $value['plan']['start_time'] == '' && $value['plan']['complete_time'] == '' ){
                            $value['plan']['state_class'] = 'label-default';
                            $value['plan']['state_text'] = 'noStart';
                        }elseif( $value['plan']['start_time'] != '' && $value['plan']['complete_time'] == '' ){
                            $value['plan']['state_class'] = 'label-primary';
                            $value['plan']['state_text'] = 'start';
                        }

                        if( strtotime($value['plan']['complete_time']) && strtotime($value['plan_stop_time']) >= strtotime($value['plan']['complete_time']) ){
                            $value['plan']['status'] = 'ok';
                        }elseif( strtotime($value['plan']['complete_time']) && strtotime($value['plan_stop_time']) < strtotime($value['plan']['complete_time']) ){
                            $value['plan']['status'] = 'no';
                        }elseif( strtotime($value['plan_stop_time']) && !strtotime($value['plan']['complete_time']) && (strtotime($value['plan_stop_time'])+86400) < time()){
                            $value['plan']['status'] = 'no';
                        }elseif( strtotime($value['plan_stop_time']) && !strtotime($value['plan']['complete_time']) && (strtotime($value['plan_stop_time'])+86400) > time()){
                            $value['plan']['status'] = 'ok';
                        }else{
                            $value['plan']['status'] = 'default';
                        }

                    }else{
                        $value['plan']['class'] = 'acti';
                    }
                    # 控制节点是否显示编辑按钮，当其中一项存在完成时间时则表明该节点已完成并下一步可操作
                    if( in_array($value['gate'],$uniqueResult) ){
                        $value['show'] = 'true';
                    }
                }
                # print_r($planResult);
                $this->assign('gateResult',$planResult);
                break;

            case 'subprojects':    // 子项目开发
                $project = M('Project');
                $plan = M('ProjectPlan');
                $projects = $project->where('pj_child=1 AND pj_belong='.$getID)->order('pj_create_time DESC')->select();
                foreach($projects as $k=>&$v){
                    $plans = $plan->where('plan_project='.$v['id'])->select();
                    $completeResult = array();  //准备存放已完成节点数据
                    foreach($plans as $key=>&$value){
                        if( !empty($value['complete_time']) ){   //查看子节点结束时间是否为空，为空则表示该进度未完
                            array_push($completeResult,$value);
                        }
                    }
                    $getLastId = $plan->where('plan_project='.$v['id'])->max('gate'); //获取最后一个步骤
                    $nums = array();
                    foreach($completeResult as $key=>&$value){
                        array_push($nums,$value['gate']);
                    }
                    if( !empty($nums) ){
                        $maxNum = max($nums);   //获取到当前已完成gate的最后一个节点
                        $gateRes = $plan->find($maxNum);
                    }else{
                        $maxNum = 0;
                    }
                    //$gateRes = $plan->find($maxNum);
                    $nodeSize = $plan->where('plan_project='.$v['id'].' AND gate='.$maxNum)->count();  //获取gate的子节点数量
                    if($maxNum){
                        $countNode = $plan->where('plan_project='.$v['id'].' AND gate='.$maxNum.' AND complete_time<>""')->count();   //获取结束时间不为空的节点个数
                    }else{
                        $countNode = $plan->where('plan_project='.$v['id'].' AND gate='.$maxNum.' AND complete_time<>""')->count();   //获取结束时间不为空的节点个数
                    }
                    if( $nodeSize == $countNode ){  //如果已完成的节点和items数量一致则说明当前gate已经完成
                        if( $maxNum == $getLastId ){
                            $progress['progress'] = '已完成';
                            $progress['class'] = 'label-success';
                        }else{
                            $gateNode = $maxNum+1;
                            $nowNodeData = $plan->where('plan_project='.$v['id'].' AND gate='.$gateNode)->group('gate')->select()[0];
                            $progress['progress'] = 'Gate'.$nowNodeData['gate'].' - '.$nowNodeData['mile_stone'];
                            $progress['class'] = 'label-primary';
                        }
                    }else{
                        $gateNode = $maxNum;
                        $nowNodeData = $plan->where('plan_project='.$v['id'].' AND gate='.$gateNode)->group('gate')->select()[0];
                        $progress['progress'] = 'Gate'.$nowNodeData['gate'].' - '.$nowNodeData['mile_stone'];
                        $progress['class'] = 'label-primary';
                    }
                    $v['node'] = $progress;
                }
                # print_r($projects);
                $this->assign('projects',$projects);
                break;
            case 'sample':    // 样品状况
                $sample = M('sample');
                $project = M('Project');
                $standard = $project->field('pj_standard_name')->find($getID);//获取到标准品名
                if( strpos($standard['pj_standard_name'],'/') ){    // 检测标准品名中是否包含‘/’符号，如果包含则根据该符号分割
                    $standard['pj_standard_name'] = explode('/',$standard['pj_standard_name']);
                    foreach($standard['pj_standard_name'] as $key=>&$value){    // 根据分割后的标准品名获取属于该品名的样品订单信息
                        $result = $sample->field('a.order_num,a.create_person_name,b.id,b.pn,b.count,b.customer,b.brand,b.model')
                                         ->table(C('DB_PREFIX').'sample a,'.C('DB_PREFIX').'sample_detail b')
                                         ->where('b.pn="'.trim($value).'" AND a.id=b.detail_assoc')
                                         ->order('a.id DESC')
                                         ->select();
                        if( !empty($result) ){  // 如果没有该品名的订单信息则不注入模板
                            $samples[] = $result;
                        }
                    }
                }else{
                    //$samples = $sample->field('totalorder,product,order,saleperson,number,customer,brand,model')->where('product="'.$standard['pj_standard_name'].'"')->group('totalorder')->select();
                    $samples = $sample->field('a.order_num,a.create_person_name,b.id,b.pn,b.count,b.customer,b.brand,b.model')
                        ->table(C('DB_PREFIX').'sample a,'.C('DB_PREFIX').'sample_detail b')
                        ->where('b.pn="'.trim($standard['pj_standard_name']).'" AND a.id=b.detail_assoc')
                        ->order('a.id DESC')
                        ->select();
                }
                # print_r($samples);
                $this->assign('samples',$samples);
                break;
            case 'document':    // 归档文件
                $plan = M('ProjectPlan');
                $document = M('ProjectDocument');
                $fileModel = M('FileNumber');
                $gates = $plan->field('plan_project,gate,mile_stone')->where('plan_project='.$getID)->group('gate')->select();
                $documentResult = $document->where(['project_id'=>$getID])->order('gate_num ASC')->select();
                foreach( $documentResult as $key=>&$value ){
                    $value['assoc_file'] = json_decode($value['assoc_file'], true);
                    $value['FileNumbers'] = $fileModel->where(['filenumber'=>['in', $value['assoc_file']]])->having('upgrade <> "Y"')->select();
                    foreach( $value['FileNumbers'] as $k=>&$v ){
                        $v['attachment'] = json_decode($v['attachment'], true);
                    }
                    $value['Plan'] = $plan->where(['plan_project'=>$getID, 'gate'=>$value['gate_num']])->find();
                }
                $filesData = $fileModel->where(['state'=>'Archiving'])->order('createtime DESC')->select();
                foreach( $filesData as $key=>&$value ){
                    $value['attachment'] = json_decode($value['attachment'], true);
                }
                $this->assign('files', $filesData);
                $this->assign('documents', $documentResult);
                $this->assign('gates',$gates);
                break;

            case 'discuss':    // 讨论区

                $model = new model();

                //$users = M()->table('atop_department a,atop_user b')->field('a.id dpmt_id,a.name dpmt_name,b.id user_id,b.nickname,b.email')->where('a.id=b.department')->select();
                //$this->assign('users',$users);
                $departments = M('Department')->field('id,name')->select();
                $this->assign('departments',$departments);
                $user = M('User');
                $project = M('Project');
                $users = $user->field('id,nickname,email,department')->where('id<>1 AND state=1 AND department='.$departments[0]['id'])->select();
                $this->assign('users',$users);
                $recipients = $project->field('pj_participate,pj_create_person_id')->find($getID);  // 获取到参与该项目的人员
                $map['id'] = array('in',$recipients['pj_participate'].','.$recipients['pj_create_person_id']);
                $recipientUserinfo = $user->field('nickname,email')->where($map)->select();
                $this->assign('recipientUserinfo',$recipientUserinfo);
                $nameList = '';
                foreach($recipientUserinfo as $key=>&$value){
                    $nameList .= $value['nickname'].' / ';    // 拼接所有收件人
                }
                $this->assign('namelist',substr($nameList,0,-2));
                $discussResult = M()->table('atop_project_discuss a,atop_user b')
                                    ->field('a.discuss_id,a.discuss_context,a.reply_time,a.cc_list,b.nickname,b.face,b.email,a.id')
                                    ->where('a.discuss_id='.$getID.' AND a.discuss_user = b.id AND Reply_id is null')
                                    ->select();
                // 获取所有参与该项目的人员
                //$recipients = $project->field('pj_responsible,pj_management,pj_fw_development,pj_design,pj_structure_design,pj_dvt_test,pj_light_design,pj_ate_design,pj_market')->find($getID);
                foreach($discussResult as $key=>&$value){
                    $value['discuss_context'] = htmlspecialchars_decode($value['discuss_context']);
                    if(!empty($value['cc_list'])){  // 如果抄送列表不为空则获取所有抄送人列表信息
                        $condition['id'] = ['in',$value['cc_list']];
                        $userInfo = $user->field('nickname')->where($condition)->select();
                        $nicknameList = '';
                        foreach($userInfo as $k=>&$val){
                            $nicknameList .= $val['nickname'].' / ';  // 拼接所有抄送人
                        }
                        $value['cc_person_list_info'] = substr($nicknameList,0,-2);
                    }

                    $value['reply'] = $model->table(C('DB_PREFIX').'project_discuss a,'.C('DB_PREFIX').'user b')
                                            ->field('a.discuss_context,a.reply_time,b.nickname,b.face')
                                            ->where('a.discuss_id = '.$getID.' AND a.Reply_id = '.$value['id'].' AND a.reply_id is not null AND b.id = a.discuss_user')
                                            ->select();

                    $countVv = $model->table(C('DB_PREFIX').'project_discuss a,'.C('DB_PREFIX').'user b')
                                     ->field('a.discuss_context,a.reply_time,b.nickname,b.face')
                                     ->where('a.discuss_id = '.$getID.' AND a.Reply_id = '.$value['id'].' AND a.reply_id is not null AND b.id = a.discuss_user')
                                     ->count();

                    foreach ($value['reply'] as $kk=>&$vv){
                        if($kk+1 == $countVv){
                            $vv['class'] = 'hasClass';
                        }
                    }

                }
                # print_r($discussResult);
                $this->assign('defaultCClist',$this->defaultCClist);    // 将默认抄送人注入模板
                $this->assign('discuss',$discussResult);
                break;

            default:
                $this->error('错误');
        }
        $this->assign('tab',$get);
        $this->display();
    }

    // 关联文件
    public function assoc(){
        if( I('get.id') && is_numeric(I('get.id')) ){
            $getID = I('get.id');
            $plan = M('ProjectPlan');
            $gates = $plan->field('plan_project,gate,mile_stone')->where('plan_project='.$getID)->group('gate')->select();
            $page = I('post.page') ? I('post.page') : 1;
            $limit = C('LIMIT_SIZE');
            $model = new Model();
            $temporary = $model->table(C('DB_PREFIX').'project_document')->where(['project_id'=>$getID])->select();
            if( $temporary ){
                $mergeArr = [];
                foreach($temporary as $key=>&$value){
                    $tmpArr = json_decode($value['assoc_file']);
                    foreach($tmpArr as $k=>&$v){
                        array_push($mergeArr, $v);
                    }
                }
                $total = $model->table(C('DB_PREFIX').'file_number')->where(['state'=>'Archiving', 'id'=>['notin', $mergeArr]])->count();
                $filesData = $model->table(C('DB_PREFIX').'file_number')
                    ->where(['state'=>'Archiving', 'id'=>['notin', $mergeArr]])  // 过滤掉已经添加过的filenumber
                    ->order('createtime DESC')
                    ->limit((($page-1)*$limit), $limit)
                    ->select();
            }else{
                $total = $model->table(C('DB_PREFIX').'file_number')->where(['state'=>'Archiving'])->count();
                $filesData = $model->table(C('DB_PREFIX').'file_number')
                    ->where(['state'=>'Archiving'])  // 过滤掉已经添加过的filenumber
                    ->order('createtime DESC')
                    ->limit((($page-1)*$limit), $limit)
                    ->select();
            }
            foreach( $filesData as $key=>&$value ){
                $value['attachment'] = json_decode($value['attachment'], true);
                $value['description'] = strip_tags($value['description']);
            }
            $data['limit'] = $limit;
            $data['total'] = (int)$total;
            $data['count'] = ceil($total / $limit);
            $data['data'] = $filesData;
            $this->assign('result', $data);
            $this->assign('gates', $gates);
            $this->display();
        }
    }

    // 渲染关联文件
    public function renderAssocFileData(){
        if( IS_POST ){
            $getID = I('post.id');
            $page = I('post.page') ? I('post.page') : 1;
            if( I('post.search','',false) ){
                $map['state'] = 'Archiving';
                $map['filenumber'] = ['like', '%'. I('post.search','',false) .'%'];
            }else{
                $map['state'] = 'Archiving';
            }
            $limit = C('LIMIT_SIZE');
            $model = new Model();
            $temporary = $model->table(C('DB_PREFIX').'project_document')->where(['project_id'=>$getID])->select();
            if( $temporary ){
                $mergeArr = [];
                foreach($temporary as $key=>&$value){
                    $tmpArr = json_decode($value['assoc_file']);
                    foreach($tmpArr as $k=>&$v){
                        array_push($mergeArr, $v);
                    }
                }
                $map['id'] = ['notin', $mergeArr];
                $total = $model->table(C('DB_PREFIX').'file_number')->where($map)->count();
                $filesData = $model->table(C('DB_PREFIX').'file_number')
                    ->where($map)  // 过滤掉已经添加过的filenumber
                    ->order('createtime DESC')
                    ->limit((($page-1)*$limit), $limit)
                    ->select();
            }else{
                $total = $model->table(C('DB_PREFIX').'file_number')->where($map)->count();
                $filesData = $model->table(C('DB_PREFIX').'file_number')
                    ->where($map)
                    ->order('createtime DESC')
                    ->limit((($page-1)*$limit), $limit)
                    ->select();
            }
            foreach( $filesData as $key=>&$value ){
                $value['attachment'] = json_decode($value['attachment'], true);
                $value['description'] = strip_tags($value['description']);
            }
            $data['limit'] = $limit;
            $data['total'] = (int)$total;
            $data['count'] = ceil($total / $limit);
            $data['data'] = $filesData;
            $data['search'] = I('post.search');
            $this->ajaxReturn($data);
        }
    }

    public function getProjectAllGates(){
        if( IS_POST ){
            $getID = I('post.getID');
            $plan = M('ProjectPlan');
            $gates = $plan->field('plan_project,gate,mile_stone')->where('plan_project='.$getID)->group('gate')->select();
            $this->ajaxReturn($gates);
        }
    }

    # 保存文件关联
    public function saveAssocFile(){
        if( IS_POST ){
            $postData = I('post.');
            $model = new Model();
            $temporary = $model->table(C('DB_PREFIX').'project_document')->where(['project_id'=>$postData['project_id'], 'gate_num'=>$postData['gate']])->select();
            $assoc_file = [];
            foreach($postData['selecteds'] as $key=>&$value){
                array_push($assoc_file, $value['filenumber']);
            }
            $data['project_id'] = $postData['project_id'];
            $data['gate_num'] = $postData['gate'];
            $data['assoc_file'] = json_encode($assoc_file);
            $data['assoc_time'] = time();
            $projectData = $model->table(C('DB_PREFIX').'project')->find($postData['project_id']);
            $projectData['Plan'] = $model->table(C('DB_PREFIX').'project_plan')->where(['plan_project'=>$postData['project_id'], 'gate'=>$postData['gate']])->select();
            $projectData['Assoc'] = $postData['selecteds'] ? $postData['selecteds'] : null;
            $projectData['Recipient'] = $model->table(C('DB_PREFIX').'user')->field('nickname name, email')->where(['id'=>['in', $projectData['pj_participate']]])->select();
            $projectData['CC'] = [['name'=>'丁征', 'email'=>'dingzheng@atoptechnology.com'], ['name'=>session('user')['nickname'], 'email'=>session('user')['email']]];
            $this->newPushEmail('assoc', $projectData['Recipient'], $projectData['CC'], $projectData);  // 推送邮件
            if( $temporary ){   // 在相同的id和相同的gate是否已经存在关联文件，如果存在则合并后复写，不存在则新增
                $merge_arr = array_merge($assoc_file, json_decode($temporary[0]['assoc_file'], true));
                $merge_arr = json_encode($merge_arr);
                $affectRow = $model->table(C('DB_PREFIX').'project_document')->where(['project_id'=>$postData['project_id'], 'gate_num'=>$postData['gate']])->save(['assoc_file'=>$merge_arr]);
                $affectRow !== false ? $this->ajaxReturn(['flag'=>1, 'msg'=>'保存成功', 'id'=>$postData['project_id']]) : $this->ajaxReturn(['flag'=>0, 'msg'=>'保存失败']);
            }else{
                $id = $model->table(C('DB_PREFIX').'project_document')->add($data);
                $id ? $this->ajaxReturn(['flag'=>1, 'msg'=>'保存成功', 'id'=>$postData['project_id']]) : $this->ajaxReturn(['flag'=>0, 'msg'=>'保存失败']);
            }
        }
    }

    # 推送邮件
    public function newPushEmail($type, $address, $cc, $data){
        if( $type == 'assoc' ){     // 关联文件号
            $subject = '[项目管理]归档文件更新，'.$data['pj_num'].'/'.$data['pj_name'];
            $body = '<p>Dear All,<p>';
            $body .= '<p>项目管理员['.session('user')['nickname'].']在['.$data['pj_num'].'/'.$data['pj_name'].'] 的 [Gate'.$data['Plan'][0]['gate'].'/'.$data['Plan'][0]['mile_stone'].']关联了新文件。<p>';
            $body .= '<table width="100%" cellspacing="0" cellpadding="15" border="1">';
            $body .= '<tr><th style="text-align: left;">文件号</th><th style="text-align: left;">版本</th><th style="text-align: left;">附件</th></tr>';
            foreach($data['Assoc'] as $key=>&$value){
                $body .= '<tr>';
                $body .= '<td><a href="http://'.$_SERVER['HTTP_HOST'].'/File/detail/'.$value['filenumber'].'" target="_blank">'.$value['filenumber'].'</a></td>';
                $body .= '<td>'.$value['version'].'</td>';
                $body .= '<td><a href="http://'.$_SERVER['HTTP_HOST'].$value['attachment']['path'].'" target="_blank">'.$value['attachment']['name'].'</a></td>';
                $body .= '</tr>';
            }
            $body .= '</table>';
            $body .= '<p>详情请查看链接：<a href="http://'.$_SERVER['HTTP_HOST'].'/Project/details/tab/document/id/'.$data['id'].'" target="_blank">http://'.$_SERVER['HTTP_HOST'].'/Project/details/tab/document/id/'.$data['id'].'</a></p>';
            send_Email($address, '', $subject, $body, $cc);
        }
    }

    # 获取filenumber数据
    public function getFilenumberData(){
        if( IS_POST ){
            $id = I('post.id');
            $page = I('post.page') ? I('post.page') : 1;
            $limit = 1;
            $model = new Model();
            $temporary = $model->table(C('DB_PREFIX').'project_document')->where(['project_id'=>$id])->select();
            if( $temporary ){
                $mergeArr = [];
                foreach($temporary as $key=>&$value){
                    $tmpArr = json_decode($value['assoc_file']);
                    foreach($tmpArr as $k=>&$v){
                        array_push($mergeArr, $v);
                    }
                }
                $total = $model->table(C('DB_PREFIX').'file_number')->where(['state'=>'Archiving', 'id'=>['notin', $mergeArr]])->count();
                $filesData = $model->table(C('DB_PREFIX').'file_number')
                    ->where(['state'=>'Archiving', 'id'=>['notin', $mergeArr]])  // 过滤掉已经添加过的filenumber
                    ->order('createtime DESC')
                    ->limit((($page-1)*$limit), $limit)
                    ->select();
            }else{
                $total = $model->table(C('DB_PREFIX').'file_number')->where(['state'=>'Archiving'])->count();
                $filesData = $model->table(C('DB_PREFIX').'file_number')
                    ->where(['state'=>'Archiving'])  // 过滤掉已经添加过的filenumber
                    ->order('createtime DESC')
                    ->limit((($page-1)*$limit), $limit)
                    ->select();
            }
            foreach( $filesData as $key=>&$value ){
                $value['attachment'] = json_decode($value['attachment'], true);
                $value['description'] = strip_tags($value['description']);
            }
            $data['limit'] = $limit;
            $data['total'] = (int)$total;
            $data['count'] = ceil($total / $limit);
            $data['data'] = $filesData;
            $this->ajaxReturn($data);
        }
    }

    # 关联文件
    public function assocFile(){
        if( IS_POST ){
            $postData = I('post.', '', false);
            $postData['assoc_time'] = time();
            $PDmodel = M('ProjectDocument');
            $temporary = $PDmodel->where(['project_id'=>$postData['project_id'], 'gate_num'=>$postData['gate_num']])->select();
            $alreadyExistingData = json_decode($temporary[0]['assoc_file'], true);
            if( $temporary ){   // 如果已经存在关于当前项目id并且gate相等的数据则合并assoc_file
                $currentAssoc = json_decode($postData['assoc_file']);
                foreach($currentAssoc as $key=>&$value){
                    array_push($alreadyExistingData, $value);
                }
                $postData['id'] = $temporary[0]['id'];
                $postData['assoc_file'] = json_encode($alreadyExistingData);
                $affect = $PDmodel->save($postData);
                $affect !== false ? $this->ajaxReturn(['flag'=>1, 'msg'=>'成功']) : $this->ajaxReturn(['flag'=>0, 'msg'=>'失败']);
            }else{  // 如果不存在关于当前项目id并且gate不相等的数据则直接新增
                $pdId = $PDmodel->add($postData);
                $pdId ? $this->ajaxReturn(['flag'=>1, 'msg'=>'成功']) : $this->ajaxReturn(['flag'=>0, 'msg'=>'失败']);
            }
        }
    }

    # 回复评论
    public function reply(){

        if(IS_POST){

            $model = new model();

            $post = I('post.');

            $emailPerson = $model->table(C('DB_PREFIX').'project')->where('id ='.$post['id'])->find();

            $tmpUser = explode(',',$emailPerson['pj_participate']);

            $uid['id'] = array('in',$tmpUser) ;
            $userEmail = $model->table(C('DB_PREFIX').'user')->field('email')->where($uid)->select();

            foreach ($userEmail as $key=>&$value){
                $tmpArr[] = $value['email'];
            }
            $dz = $model->table(C('DB_PREFIX').'project_discuss')->where('id = '.$post['discuss'])->find();

            $tmpCC = explode(',',$dz['cc_list']);

            $eid['id'] = array('in',$tmpCC) ;
            $userEmails = $model->table(C('DB_PREFIX').'user')->field('email')->where($eid)->select();

            foreach ($userEmails as $key=>&$value){
                $tmpArrs[] = $value['email'];
            }

            $nowPerson = session('user')['email'];
            $cc = array_merge($tmpArr,$tmpArrs);
            array_push($cc,$nowPerson);

            $discuss['discuss_id'] = $post['id'];
            $discuss['discuss_context'] = $post['context'];
            $discuss['discuss_user'] = session('user')['id'];
            $discuss['reply_time'] = time();

            $rel = $model->table(C('DB_PREFIX').'project a,'.C('DB_PREFIX').'project_discuss b')
                         ->field('a.pj_num,b.discuss_context,a.pj_name,a.pj_create_person,a.id')
                         ->where('a.id = b.discuss_id AND a.id ='.$post['id'].' AND b.id ='.$post['discuss'])
                         ->find();

            $user = $model->table(C('DB_PREFIX').'project_discuss a,'.C('DB_PREFIX').'user b')->field('b.nickname')->where('a.id ='.$post['discuss'].' AND b.id = a.discuss_user')->find();

            $rel['content'] = $post['context'];
            $rel['discuss_context'] = html_entity_decode($rel['discuss_context']);
            $rel['nickname'] = $user['nickname'];

            $discuss_id = M('ProjectDiscuss')->add($discuss);
            if( $discuss_id ){
                $dis['Reply_id'] = $post['discuss'];
                $discussId = M('projectDiscuss')->where('id = '.$discuss_id)->save($dis);
                if( $discussId ){
                    $this->pushEmails('REPLY',$post['email'],$rel,$cc);
                    $this->ajaxReturn(['flag'=>1,'msg'=>'操作成功！']);
                }else{
                    $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败！']);
                }
            }else{
                $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败！']);
            }
        }else{
            $this->error('非法提交！');
        }

    }

    # 切换部门
    public function ajaxChangeDepartment(){
        if(IS_POST){
            if( I('post.dpmt') ){
                $result = M('User')->field('id,nickname,email')->where('id<>1 AND state=1 AND department='.I('post.dpmt'))->select();
                if(!empty($result)){
                    $this->ajaxReturn(array('flag'=>1,'data'=>$result));exit;
                }else{
                    $this->ajaxReturn(array('flag'=>0,'msg'=>'没有数据'));exit;
                }
            }
        }
    }

    # 删除文档
    public function removeDocument(){
        if( IS_POST ){
            $path = I('post.path');
            $id = I('post.id');
            $res = M('ProjectDocument')->delete($id);
            if( $res ){
                if(file_exists('.'.$path)){
                    unlink('.'.$path);//删除该文件
                    $this->ajaxReturn( array('flag'=>1,'msg'=>'删除成功') );
                }else{
                    $this->ajaxReturn( array('flag'=>0,'msg'=>'文件不存在') );
                }
            }else{
                $this->ajaxReturn( array('flag'=>0,'msg'=>'错误') );
            }
        }
    }

    # 讨论区发表提交
    public function addDiscussContext(){
        if( IS_POST ){
            $data['discuss_id'] = I('post.discuss_id');
            $data['discuss_context'] = I('post.discuss_context');
            $data['discuss_user'] = session('user')['id'];
            $data['reply_time'] = time();
            $data['cc_list'] = I('post.cc_list');
            $map['id'] = array('in',I('post.cc_list'));
            $ccList = M('User')->field('email')->where($map)->select(); // 获取用户选择的抄送人员
            foreach($ccList as $key=>&$value){
                $ccListEmail[] = $value['email'];   // 替换抄送列表人员
            }
            $this->ccList = $ccListEmail;
            $id = M('ProjectDiscuss')->add($data);
            if( $id ){
                $project = M('Project');
                $participate = $project->find(I('post.discuss_id')); //获取到参与该项目的人员id
                $participate['context'] = I('post.discuss_context');
                $participate['nickname'] = session('user')['nickname'];
                $participate['floor'] = I('post.floor');
                $participate['discuss_context'] = strip_tags(I('post.discuss_context','',false),'<br><p><strong><sup><sub><em><b><ul><ol><li><span><i>');    // 将回复的内容取消转义/添加至邮件并过滤掉html标签【但不过滤指定标签】
                $this->pushEmail('discuss',$participate['pj_participate'].','.$participate['pj_create_person_id'],$participate,I('post.discuss_id'));  //上传归档文件后给所有参与该项目的人员推送邮件
                $this->ajaxReturn( array('flag'=>1,'msg'=>'发表成功') );
            }else{
                $this->ajaxReturn( array('flag'=>0,'msg'=>'发表失败') );
            }
        }
    }

    # Ajax修改项目计划数据
    public function plan()
    {
        if(IS_POST)
        {

            $data = I('post.');
            # print_r($data);

            $plan = M('ProjectPlan');

            if(I('post.steps') == 0){
                $tmp['plan_start_time'] = $data['start_time'];
                $tmp['plan_stop_time'] = $data['plan_stop_time'];
                $tmp['comments'] = $data['comments'];
                $tmp['status'] = 0;
                $tmp['save_time'] = time();
                $tmp['start_time'] = '';
                $tmp['complete_time'] = '';

                $savePlan = $plan->where('id ='.$data['plan_node'])->save($tmp);

                if( $savePlan ){
                    $pj_save_data['id'] = $data['plan_project'];
                    $pj_save_data['pj_update_time'] = time();
                    $pj_save_id = M('Project')->save($pj_save_data);

                    if($pj_save_id){
                        $projectResult = M('Project')->find($data['plan_project']);
                        $planNode = $plan->find( $data['plan_node'] );
                        $planNode['pj_num'] = $projectResult['pj_num'];
                        $planNode['pj_name'] = $projectResult['pj_name'];
                        $planNode['pj_create_person'] = $projectResult['pj_management'];

                        $this->pushEmail('update', $projectResult['pj_participate'] . ',' . $projectResult['pj_create_person_id'], $planNode, $projectResult['id']);
                        $this->ajaxReturn(array('flag' => 1, 'msg' => '保存成功'));
                        exit;
                    }else{
                        $this->ajaxReturn(array('flag' => 0, 'msg' => '保存失败'));
                        exit;
                    }
                }else{
                    $this->ajaxReturn(array('flag' => 0, 'msg' => '保存失败'));
                    exit;
                }

            }elseif( I('post.steps') == 1 ){

                $tmp['plan_start_time'] = $data['start_time'];
                $tmp['plan_stop_time'] = $data['plan_stop_time'];
                $tmp['start_time'] = $data['start_time'];
                $tmp['comments'] = $data['comments'];
                $tmp['status'] = 1;
                $tmp['save_time'] = time();

                $savePlan = $plan->where('id ='.$data['plan_node'])->save($tmp);

                if( $savePlan ){
                    $pj_save_data['id'] = $data['plan_project'];
                    $pj_save_data['pj_update_time'] = time();
                    $pj_save_id = M('Project')->save($pj_save_data);

                    if($pj_save_id){
                        $projectResult = M('Project')->find($data['plan_project']);
                        $planNode = $plan->find( $data['plan_node'] );
                        $planNode['pj_num'] = $projectResult['pj_num'];
                        $planNode['pj_name'] = $projectResult['pj_name'];
                        $planNode['pj_create_person'] = $projectResult['pj_management'];

                        $this->pushEmail('update', $projectResult['pj_participate'] . ',' . $projectResult['pj_create_person_id'], $planNode, $projectResult['id']);
                        $this->ajaxReturn(array('flag' => 1, 'msg' => '保存成功'));
                        exit;
                    }else{
                        $this->ajaxReturn(array('flag' => 0, 'msg' => '保存失败'));
                        exit;
                    }
                }else{
                    $this->ajaxReturn(array('flag' => 0, 'msg' => '保存失败'));
                    exit;
                }

            }else{
                $tmp['plan_start_time'] = $data['start_time'];
                $tmp['plan_stop_time'] = $data['plan_stop_time'];
                $tmp['start_time'] = $data['start_time'];
                $tmp['complete_time'] = date('Y-m-d', time());
                $tmp['comments'] = $data['comments'];
                $tmp['status'] = 2;
                $tmp['save_time'] = time();

                $savePlan = $plan->where('id ='.$data['plan_node'])->save($tmp);

                if( $savePlan ){
                    $pj_save_data['id'] = $data['plan_project'];
                    $pj_save_data['pj_update_time'] = time();
                    $pj_save_id = M('Project')->save($pj_save_data);

                    if($pj_save_id){
                        $projectResult = M('Project')->find($data['plan_project']);
                        $planNode = $plan->find( $data['plan_node'] );
                        $planNode['pj_num'] = $projectResult['pj_num'];
                        $planNode['pj_name'] = $projectResult['pj_name'];
                        $planNode['pj_create_person'] = $projectResult['pj_management'];
                        
                        $this->pushEmail('update', $projectResult['pj_participate'] . ',' . $projectResult['pj_create_person_id'], $planNode, $projectResult['id']);
                        $this->ajaxReturn(array('flag' => 1, 'msg' => '保存成功'));
                        exit;
                    }else{
                        $this->ajaxReturn(array('flag' => 0, 'msg' => '保存失败'));
                        exit;
                    }
                }else{
                    $this->ajaxReturn(array('flag' => 0, 'msg' => '保存失败'));
                    exit;
                }
            }
        }
    }

    # Ajax修改项目状态
    public function mark(){

        if(IS_POST){

            $post = I('post.');
            $asd['plan_stop_time'] = '';
            $rel = M('Project')->select();
            foreach ($rel as $key=>&$val){
                $val['plan'] = M('ProjectPlan')->where('plan_project ='.$val['id'])->select();
                foreach ($val['plan'] as $k=>&$v){
                    if($v['start_time'] == ''){
                        M('ProjectPlan')->where('id ='.$v['id'])->save($asd);
                    }
                }
            }

            $plan['complete_time'] = date('Y-m-d', time());
            $plan['id'] = $post['plan_node'];

            $plan_id = M('ProjectPlan')->save($plan);

            if($plan_id){
                $this->ajaxReturn(['flag'=>1,'msg'=>'操作成功！']);
                exit();
            }else{
                $this->ajaxReturn(['flag'=>0,'msg'=>'操作失败！']);
                exit();
            }

        }else {

            $this->ajaxReturn(['flag' => 0, 'msg' => '操作失败！']);
            exit();

        }
    }

    # 上传归档文件
    public function document(){
        if( IS_POST && I('post.project_id') && !empty(I('post.project_id')) ){
            # 获取到上传文档关联的项目和gate
            $projectId = I('post.project_id');
            $gateNum = I('post.gate_num');
            $info = FileUpload('/ProjectDocument/',1,0,'ProDoc_');
            if(!$info){
                $arr['flag'] = 0;
                $arr['msg'] = $info;
                $this->ajaxReturn($arr);
                exit;
            };
            $document = M('ProjectDocument');
            $ext = $info['Filedata']['ext'];
            $oldname = $info['Filedata']['name'];
            $newname = $info['Filedata']['savename'];
            $filePath = '/Uploads'.$info['Filedata']['savepath'].$info['Filedata']['savename'];
            $data['gate_num'] = $gateNum;
            $data['project_id'] = $projectId;
            $data['document_name'] = $oldname;
            $data['document_savename'] = $newname;
            $data['document_ext'] = $ext;
            $data['document_path'] = $filePath;
            $data['upload_time'] = time();
            $id = $document->add($data);
            if( $id ){
                $arr['flag'] = 1;
                $arr['ext'] = $ext;
                $arr['msg'] = '上传成功';
                $arr['NewFileName'] = $oldname;
                $arr['FileSavePath'] = $filePath;
                $project = M('Project');
                $participate = $project->find($projectId); //获取到参与该项目的人员id
                $participate['filename'] = $oldname;
                $planResult = M('ProjectPlan')->field('mile_stone')->where(array('plan_project'=>$projectId,'gate'=>$gateNum))->group('gate')->select()[0]; //获取到当前上传的文件是属于的里程碑
                $participate['mile_stone'] = $planResult['mile_stone'];
                $participate['gate'] = $gateNum;
                $this->pushEmail('document',$participate['pj_participate'],$participate,$projectId);  //上传归档文件后给所有参与该项目的人员推送邮件
                $this->ajaxReturn($arr);
                exit;
            }else{
                $arr['flag'] = 0;
                $arr['msg'] = '上传失败';
                $this->ajaxReturn($arr);
                exit;
            }
        }
    }

}