<?php
namespace Home\Controller;
use Think\Page;

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
        $count = $project->where('pj_child=0')->count();
        //数据分页
        $page = new Page($count,C('LIMIT_SIZE'));
        $page->setConfig('prev','<span aria-hidden="true">上一页</span>');
        $page->setConfig('next','<span aria-hidden="true">下一页</span>');
        $page->setConfig('first','<span aria-hidden="true">首页</span>');
        $page->setConfig('last','<span aria-hidden="true">尾页</span>');
        if(C('PAGE_STATUS_INFO')){
            $page->setConfig ( 'theme', '<li><a href="javascript:void(0);">当前%NOW_PAGE%/%TOTAL_PAGE%</a></li>  %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%' );
        }
        $projects = $project->where('pj_child=0')->order('pj_create_time DESC')->limit($page->firstRow.','.$page->listRows)->select();
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
                $subject = '新项目立项，'.$pj_num.' / '.$pj_name.' [子项目]';
            }else{
                $subject = '新项目立项，'.$pj_num.' / '.$pj_name;
            }
        }elseif( $category == 'update' ){
            extract($emailData);
            $pj_create_time = date('Y年m月d日 H:i:s');
            if( $emailData['pj_child'] == 1 ){
                $subject = '项目计划更新，'.$pj_num.' / '.$pj_name.' [子项目]';
            }else{
                $subject = '项目计划更新，'.$pj_num.' / '.$pj_name;
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
                $subject = '归档文件更新，'.$pj_num.' / '.$pj_name.' [子项目]';
            }else{
                $subject = '归档文件更新，'.$pj_num.' / '.$pj_name;
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
        }else{
            extract($emailData);
            $pj_create_time = date('Y年m月d日 H:i:s');
            if( $emailData['pj_child'] == 1 ){
                $subject = '讨论区更新，'.$pj_num.' / '.$pj_name.' [子项目]';
            }else{
                $subject = '讨论区更新，'.$pj_num.' / '.$pj_name;
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
        die;
        //send_Email($emailList,'',$subject,$body,$this->ccList);
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
                $planResult = $plan->field('id,gate,mile_stone,items,plan_start_time,plan_stop_time')->where('plan_project='.$getID)->order('gate,id ASC')->select();    //获取项目计划信息
                //print_r($plan->getLastSql());
                $steps = array();   // 存放步骤节点
                $step = 1;
                //print_r($planResult);
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
                            $value['plan']['state_text'] = '已完成';
                        }elseif( $value['plan']['start_time'] == '' && $value['plan']['complete_time'] == '' ){
                            $value['plan']['state_class'] = 'label-default';
                            $value['plan']['state_text'] = '未开始';
                        }elseif( $value['plan']['start_time'] != '' && $value['plan']['complete_time'] == '' ){
                            $value['plan']['state_class'] = 'label-primary';
                            $value['plan']['state_text'] = '进行中';
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
                $gates = $plan->field('plan_project,gate,mile_stone')->where('plan_project='.$getID)->group('gate')->select();
                foreach($gates as $key=>&$value){
                    $value['document'] = $document->where('project_id='.$value['plan_project'].' AND '.'gate_num='.$value['gate'])->select();
                }
                $num = 0;   // 该变量作用于存放没有document的数据个数
                foreach($gates as $key=>&$value){
                    if( empty($value['document']) ){    // 统计document为空的数组的个数
                        $num++;
                    }
                }
                if( count($gates) == $num ){
                    $this->assign('empty',true);    //如果检测到为空的数组和源数据的长度相等则表明所有数组都不存在附件，则向模板传递empty表示不遍历数据
                }
                # print_r($gates);
                $this->assign('gates',$gates);
                break;
            case 'discuss':    // 讨论区
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
                                    ->field('a.discuss_id,a.discuss_context,a.reply_time,a.cc_list,b.nickname,b.face')
                                    ->where('a.discuss_id='.$getID.' AND a.discuss_user=b.id')
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
            if( !empty($data) )
            {
                $data['id'] = I('post.plan_node');
                $data['comments'] = replaceEnterWithBr(strip_tags($data['comments']));
                $data['save_time'] = time();
                $plan = M('ProjectPlan');
                $id = $plan->save($data);  // 修改
                if( $id )
                {
                    # 更新项目最近更新时间
                    $pj_id = $data['plan_project'];
                    $pj_save_data['id'] = $pj_id;
                    $pj_save_data['pj_update_time'] = time();
                    $pj_save_id = M('Project')->save($pj_save_data);
                    if( $pj_save_id )
                    {
                        $projectResult = M('Project')->find($data['plan_project']);
                        $planNode = $plan->find( I('post.plan_node') );
                        $planNode['pj_num'] = $projectResult['pj_num'];
                        $planNode['pj_name'] = $projectResult['pj_name'];
                        $planNode['pj_create_person'] = $projectResult['pj_management'];
                        //print_r($planNode);
                        $this->pushEmail('update',$projectResult['pj_participate'].','.$projectResult['pj_create_person_id'],$planNode,$projectResult['id']);
                        $this->ajaxReturn( array('flag' => 1, 'msg' => '保存成功') );
                        exit;
                    }else{
                        $this->ajaxReturn( array('flag' => 0, 'msg' => '保存失败') );
                        exit;
                    }
                }else{
                    $this->ajaxReturn( array('flag' => 0, 'msg' => '错误') );
                    exit;
                }
            }
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