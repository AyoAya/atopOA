<?php
/**
 * Created by PhpStorm.
 * User: Fulwin
 * Date: 2017/6/16
 * Time: 10:19
 */
namespace Home\Controller;

use Think\Controller;
use Think\Model;
use Think\Upload;

class ApiController extends Controller
{

    /**
     * 检查用户权限
     */
    public function checkUserAuth(){
        $rules = $_REQUEST['rules'];
        $currentRouterPath = $_REQUEST['currentRouterPath'];
        $valid = false;
        $modules = $this->getAllModules($rules);
        if( !$modules ) $this->ajaxReturn(['flag'=>0, 'EN_msg'=>'You do not have permission to access the page', 'CN_msg'=>'你没有权限访问该页面']);
        $currentRouterPathArr = explode('/',$currentRouterPath);    // 拆分当前路由指定[2]则可获取到主模块
        $verifyFilter = ['RD', 'PLM'];  // 如果访问的页面存在子栏目则按3级目录判断
        if( in_array($currentRouterPathArr[2], $verifyFilter) ){    // 如果当前访问的页面在指定的栏目中
            foreach( $modules as $key=>&$value ){   // 规则中最多包含一级子栏目，如果需要更多则需手动添加
                $tmpPath = '/'.$currentRouterPathArr[1].'/'.$currentRouterPathArr[2].'/'.$currentRouterPathArr[3];
                if( strstr($value['module_router'], $tmpPath) ){
                    $valid = true;
                }
            }
        }else{
            foreach( $modules as $key=>&$value ){
                $tmpPath = '/'.$currentRouterPathArr[1].'/'.$currentRouterPathArr[2];
                if( strstr($value['module_router'], $tmpPath) ){
                    $valid = true;
                }
            }
        }
        if( $valid ){
            $this->ajaxReturn(['flag'=>1, 'EN_msg'=>'Verification successful', 'CN_msg'=>'验证成功']);
        }else{
            $this->ajaxReturn(['flag'=>0, 'EN_msg'=>'You do not have permission to access the page', 'CN_msg'=>'你没有权限访问该页面']);
        }
    }

    /**
     * 获取用户可访问的模块
     * @return mixed
     */
    private function getAllModules($rules){
        $model = new Model();
        $map['id'] = ['in', $rules];
        $result = $model->table(C('DB_PREFIX').'modules')->where($map)->select();
        return $result;
    }

    /**
     * 登录后台
     */
    public function login()
    {
        $model = new Model();
        $data = I('get.data', '', false);
        $map['account'] = $data['account'];
        $map['password'] = sha1($data['password']);
        $UserInfoData = $model->table(C('DB_PREFIX') . 'user')->field('id,account,nickname,email,createtime,lasttime,lastip,face,theme,department,position,report,level,state,sex,rules')->where($map)->find();
        if ($UserInfoData) {
            $this->ajaxReturn(['flag' => 1, 'userdata' => $UserInfoData]);
        } else {
            $this->ajaxReturn(['flag' => 0, 'msg' => '账号或密码错误!']);
        }
    }

    /**
     * 添加用户
     * 1: 添加用户时同时写入用户权限
     */
    public function addUser(){
        $postData = I('get.');
        $postData['position'] = implode(',', $postData['position']);
        $postData['authrule'] = implode(',', $postData['authrule']);
        // user表数据
        $userData['account'] = $postData['account'];
        $userData['password'] = $postData['password'];
        $userData['nickname'] = $postData['nickname'];
        $userData['createtime'] = time();
        $userData['email'] = $postData['email'];
        $userData['sex'] = $postData['sex'];
        $userData['department'] = $postData['department'];
        $userData['level'] = $postData['userlevel'];
        $userData['position'] = $postData['position'];
        print_r($userData);
    }

    /**
     * 获取所有用户
     * @param string $query_id
     */
    public function getUsers($query_id = '')
    {
        $model = new Model();
        if ($query_id == '') {
            $pagesize = 15;
            $page = I('get.page') > 0 ? I('get.page') : 1;
            $total = $model->table(C('DB_PREFIX') . 'user')->where('id<>1')->count();
            $result = $model->table(C('DB_PREFIX') . 'user a,' . C('DB_PREFIX') . 'department b,' . C('DB_PREFIX') . 'position c,' . C('DB_PREFIX') . 'userlevel d')
                ->field('a.id,a.account,a.nickname,a.state,a.email,a.createtime,a.sex,a.lasttime,a.lastip,a.face,b.name department_name,c.name position_name,d.levelname,a.report')
                ->where('a.id<>1 AND a.department=b.id AND a.position=c.id AND a.level=d.id')
                ->order('id ASC')
                ->limit((($pagesize * ($page - 1))) . ',' . $pagesize)
                ->select();
            foreach ($result as $key => &$value) {
                $value['report_name'] = $model->table(C('DB_PREFIX') . 'user')->field('nickname')->find($value['report'])['nickname'];
                $value['createtime'] = date('Y-m-d H:i:s', $value['createtime']);
                $value['lasttime'] = date('Y-m-d H:i:s', $value['lasttime']);
                switch ($value['state']) {
                    case 1:
                        $value['state_name'] = '正常';
                        $value['tag'] = 'success';
                        break;
                    case 2:
                        $value['state_name'] = '禁用';
                        $value['tag'] = 'danger';
                        break;
                    case 3:
                        $value['state_name'] = '离职';
                        $value['tag'] = 'warning';
                        break;
                    default:
                        $value['state_name'] = 'UNKNOW';
                }
            }
            $data['usersdata'] = $result;
            $data['total'] = (int)$total;
            $data['pagesize'] = $pagesize;
            $this->ajaxReturn($data);
        } else {
            $result = $model->table(C('DB_PREFIX') . 'user a,' . C('DB_PREFIX') . 'department b,' . C('DB_PREFIX') . 'position c,' . C('DB_PREFIX') . 'userlevel d')
                ->field('a.id,a.account,a.nickname,a.password,a.email,a.sex,a.state,b.id dpmt_id,b.name department_name,c.id pst_id,c.name position_name,d.id usl_id,d.levelname,a.report')
                ->where('a.id<>1 AND a.id=' . $query_id . ' AND a.department=b.id AND a.position=c.id AND a.level=d.id')
                ->select();
            foreach ($result as $key => &$value) {
                foreach ($result as $k => &$v) {
                    if ($v['report'] == $value['id']) {
                        $value['report_name'] = $v['nickname'];
                    } else {
                        $value['report_name'] = null;
                    }
                }
                $value['pst_id'] = explode(',', $value['pst_id']);
                switch ($value['state']) {
                    case 1:
                        $value['state_name'] = '正常';
                        $value['tag'] = 'success';
                        break;
                    case 2:
                        $value['state_name'] = '禁用';
                        $value['tag'] = 'danger';
                        break;
                    case 3:
                        $value['state_name'] = '离职';
                        $value['tag'] = 'warning';
                        break;
                    default:
                        $value['state_name'] = 'UNKNOW';
                }
            }
            $result['rules'] = $this->getUserAuthRule($query_id);
            $this->ajaxReturn($result);
        }
    }

    /**
     * 获取添加用户时需要配备的属性
     * @return mixed
     */
    public function getUserAttributes()
    {
        $userAttributes['departments'] = $this->getAllDepartment();
        $userAttributes['userlevels'] = $this->getAllUserLevel();
        $userAttributes['positions'] = $this->getAllPosition();
        $userAttributes['authrule'] = $this->getAllModule();
        $userAttributes['DepartmentGroupUsers'] = $this->getDepartmentGroupUsers();
        $this->ajaxReturn($userAttributes);
    }

    /**
     * 用户添加页面，选择汇报关系时需要的数据
     */
    public function getDepartmentalPersonnel(){
        $this->ajaxReturn( $this->getDepartmentGroupUsers() );
    }

    /**
     * 获取部门分组用户
     * @return mixed
     */
    public function getDepartmentGroupUsers()
    {
        $userModel = M('User');
        $result = M('Department')->field('id,name')->select();
        foreach( $result as $key=>&$value ){
            $value['users'] = $userModel->field('id,nickname,state')->where( ['department'=>$value['id']] )->order('id ASC')->select();
        }
        return $result;
    }


    /**
     * 获取产品属性及默认数据
     */
    public function getProductAttributeData()
    {
        $this->ajaxReturn($this->getProductAttribute());
    }

    /**
     * 获取所有部门数据
     * @return mixed
     */
    private function getAllDepartment()
    {
        return M('Department')->select();
    }

    /**
     * 获取所有用户级别数据
     * @return mixed
     */
    private function getAllUserLevel()
    {
        return M('Userlevel')->select();
    }

    /**
     * 获取所有职位数据
     * @return mixed
     */
    private function getAllPosition()
    {
        return M('Position')->select();
    }

    /**
     * 获取所有模块数据
     * @return mixed
     */
    private function getAllModule()
    {
        return M('AuthRule')->where('title<>"系统"')->select();
    }

    /**
     * 获取用户的权限组
     * @param $id 用户id
     * @return array
     */
    private function getUserAuthRule($id)
    {
        $user_account = M('User')->field('account')->find($id);
        $map['title'] = $user_account['account'];
        $result = M('AuthGroup')->where($map)->find();
        $rules = explode(',', $result['rules']);
        return $rules;
    }

    /**
     * 获取产品属性
     * @return mixed
     */
    private function getProductAttribute($condition = [])
    {
        if (is_array($condition) && !empty($condition)) {
            $pro_rel_ships = M('Productrelationships');
            $productResult['types'] = $pro_rel_ships->field('type')->where($condition)->order('type ASC')->group('type')->select();   //类型
            $productResult['wavelengths'] = $pro_rel_ships->field('wavelength,pn')->where($condition)->order('wavelength ASC')->group('wavelength')->select();   //波长
            $productResult['reachs'] = $pro_rel_ships->field('reach')->where($condition)->order('reach ASC')->group('reach')->select();   //距离
            $productResult['connectors'] = $pro_rel_ships->field('connector')->where($condition)->order('connector ASC')->group('connector')->select();   //接口
            $productResult['casetemps'] = $pro_rel_ships->field('casetemp')->where($condition)->order('casetemp ASC')->group('casetemp')->select();   //环境
            $productResult['defaultData'] = $pro_rel_ships->field('pn')->where($condition)->group('pn')->order('pn ASC')->select();   //筛选数据
            //针对环境类型显示不同的名称
            foreach ($productResult['casetemps'] as $key => &$value) {
                if ($value['casetemp'] == 'C') {
                    $value['casetemp_as_name'] = 'C档（0-70°）';
                } else {
                    $value['casetemp_as_name'] = 'I 档（-40°-85°）';
                }
            }
            //针对于DWDM系列产品显示不同的名称
            if (isset($condition['type']) && !empty($condition['type'])) {
                if ($condition['type'] == 'XFP DWDM' || $condition['type'] == 'SFP DWDM') {
                    foreach ($productResult['wavelengths'] as $key => &$value) {
                        $value['wavelength_as_name'] = 'CH' . substr($value['pn'], 4, 2);
                    }
                } elseif ($condition['type'] == 'SFP+ DWDM') {
                    foreach ($productResult['wavelengths'] as $key => &$value) {
                        $value['wavelength_as_name'] = 'CH' . substr($value['pn'], 5, 2);
                    }
                } else {
                    foreach ($productResult['wavelengths'] as $key => &$value) {
                        $value['wavelength_as_name'] = $value['wavelength'];
                    }
                }
            }
            return $productResult;
        } else {
            $pro_rel_ships = M('Productrelationships');
            $productResult['types'] = $pro_rel_ships->field('type')->order('type ASC')->group('type')->select();   //类型
            $productResult['wavelengths'] = $pro_rel_ships->field('wavelength')->where('wavelength not like "%.%"')->order('wavelength ASC')->group('wavelength')->select();   //波长
            $productResult['reachs'] = $pro_rel_ships->field('reach')->order('reach ASC')->group('reach')->select();   //距离
            $productResult['connectors'] = $pro_rel_ships->field('connector')->order('connector ASC')->group('connector')->select();   //接口
            $productResult['casetemps'] = $pro_rel_ships->field('casetemp')->order('casetemp ASC')->group('casetemp')->select();   //环境
            foreach ($productResult['casetemps'] as $key => &$value) {
                if ($value['casetemp'] == 'C') {
                    $value['casetemp_as_name'] = 'C档（0-70°）';
                } else {
                    $value['casetemp_as_name'] = 'I档（-40°-85°）';
                }
            }
            $productResult['defaultData'] = $pro_rel_ships->field('id,pn,manager')->group('pn')->limit('0,32')->select();   //默认加载30条数据
            $user = M('User');
            foreach ($productResult['defaultData'] as $key => &$value) {
                $value['nickname'] = $user->field('nickname')->find($value['manager'])['nickname'];
            }
            return $productResult;
        }
    }

    /**
     * 产品筛选
     */
    public function filterProducts()
    {
        $model = new Model();
        $filterParams = I('get.');
        foreach ($filterParams as $key => &$value) {
            if (!$value) {
                unset($filterParams[$key]);
            }
        }
        if (!empty($filterParams)) {
            $result = $this->getProductAttribute($filterParams);
        } else {
            $result = $this->getProductAttribute();
        }
        $this->ajaxReturn($result);
    }

    /**
     * 产品搜索
     */
    public function productSearch()
    {
        $model = new Model();
        if (trim(I('get.search')) != '') {
            $searchResult = $model->table(C('DB_PREFIX') . 'productrelationships')->field('pn')->where('pn like "%' . I('get.search') . '%"')->order('pn ASC')->select();
            $this->ajaxReturn($searchResult);
        }
    }

    /**
     * 获取缩略词首页数据
     */
    public function getAcronymListData(){
        $acronymModel = D('Acronym');
        $result = $acronymModel->relation(true)->group('acronym')->order('acronym ASC')->select();
        $letter = range('A','Z');
        $arr = array();
        foreach($letter as &$value){
            foreach($result as $k=>&$v){
                if( substr($v['acronym'],0,1)==$value ){
                    $arr[$value][] = $v;
                }
            }
        }
        $this->ajaxReturn($arr);
    }

    /**
     * 获取指定简称的缩略词数据
     */
    public function getAcronymDetailData($acronym){
        if( $acronym ){
            $acronymModel = D('Acronym');
            $map['acronym'] = $acronym;
            $result = $acronymModel->relation(true)->where($map)->order('acronym ASC')->select();
            $this->ajaxReturn($result);
        }
    }

    /**
     * 获取产品列表
     */
    public function getProductList(){
        $model = new Model();
        $pagesize = 15;
        $page = I('get.page') > 0 ? I('get.page') : 1;
        $total = $model->table(C('DB_PREFIX').'productrelationships')->count();
        $result = $model->table(C('DB_PREFIX').'productrelationships a,'.C('DB_PREFIX').'user b')
                        ->field('a.id,a.type,a.stage,a.pn,a.rate,a.wavelength,a.component,a.txpwr,a.rxsens,a.connector,a.casetemp,a.reach,a.multirate,b.nickname')
                        ->where('a.manager=b.id')
                        ->order('a.id ASC')
                        ->limit((($pagesize * ($page - 1))) . ',' . $pagesize)
                        ->select();
        foreach( $result as $key=>&$value ){    // 标明各个阶段颜色
            if( $value['stage'] == 'Gate1' || $value['stage'] == 'Gate7' ){
                $value['tag'] = 'gray';
            }elseif( $value['stage'] == 'Gate2' || $value['stage'] == 'Gate3' || $value['stage'] == 'Gate4' ){
                $value['tag'] = 'danger';
            }elseif( $value['stage'] == 'Gate5' ){
                $value['tag'] = 'warning';
            }else{
                $value['tag'] = 'success';
            }
        }
        $products['result'] = $result;
        $products['pagesize'] = $pagesize;
        $products['total'] = (int)$total;
        $this->ajaxReturn($products);
    }

    /**
     * 获取产品参数
     */
    public function getProductParams(){
        $productModel = M('Productrelationships');
        $result = $productModel->field('type')->group('type')->select();
        $this->ajaxReturn($result);
    }

    /**
     * 获取所有软件列表
     */
    public function getSoftwareList(){
        $model = new Model();
        $type = I('get.type') ? I('get.type') : '';
        $pagesize = 15;
        $page = I('get.page') > 0 ? I('get.page') : 1;
        $Result['pagesize'] = $pagesize;
        if( $type ){    // 如果type不为空则筛选指定type的数据，反之则所有type都查
            $count = $model->table(C('DB_PREFIX').'software')->where('type="'.$type.'"')->count();
            $TmpResult = $model->table(C('DB_PREFIX').'software')->where('type="'.$type.'"')->order('create_time DESC')->limit((($pagesize * ($page - 1))) . ',' . $pagesize)->select();
            $Result[$type] = $this->getSoftwareLastUpdateData($TmpResult);
            $Result[$type.'Total'] = (int)$count;
            $this->ajaxReturn($Result);
        }else{
            $FmCount = $model->table(C('DB_PREFIX').'software')->where('type="firmware"')->count();
            $AteCount = $model->table(C('DB_PREFIX').'software')->where('type="ATE"')->count();
            $FmResult = $model->table(C('DB_PREFIX').'software')->where('type="firmware"')->order('create_time DESC')->limit((($pagesize * ($page - 1))) . ',' . $pagesize)->select();
            $AteResult = $model->table(C('DB_PREFIX').'software')->where('type="ATE"')->order('create_time DESC')->limit((($pagesize * ($page - 1))) . ',' . $pagesize)->select();
            $Result['firmware'] = $this->getSoftwareLastUpdateData($FmResult);
            $Result['ate'] = $this->getSoftwareLastUpdateData($AteResult);
            $Result['firmwareTotal'] = (int)$FmCount;
            $Result['ateTotal'] = (int)$AteCount;
            $this->ajaxReturn($Result);
        }
    }

    /**
     * 根据软件id获取到对应的最后更新时间以及最新版本号
     * @param $res  传入获取到的结果
     * @return mixed    返回追加最后更新时间及最新版本的结果
     */
    private function getSoftwareLastUpdateData($res){
        $model = new Model();
        foreach($res as $key=>&$value){
            $tmpRes = $model->table(C('DB_PREFIX').'software_log')->field('save_time,version')->where('soft_asc='.$value['id'])->order('save_time DESC')->find();
            $value['save_time'] = date('Y-m-d', $tmpRes['save_time']);
            $value['version'] = $tmpRes['version'];
        }
        return $res;
    }

    /**
     * 验证项目编号的唯一性
     */
    public function checkSoftwareNumberUnique($number){
        $model = new Model();
        $result = $model->table(C('DB_PREFIX').'software')->where('number="'.$number.'"')->select();
        if( $result ){
            $this->ajaxReturn( ['flag'=>1, 'msg'=>'项目编号已存在'] );
        }
    }

    /**
     * 获取兼容表列表数据
     */
    public function getCompatibilityList(){
        $model = new Model();
        $pagesize = 15;
        $page = I('get.page') > 0 ? I('get.page') : 1;
        $Result['pagesize'] = $pagesize;
        $Result['total'] = (int)$model->table(C('DB_PREFIX').'compatibility')->count();
        $Result['compatibilitys'] = $model->table(C('DB_PREFIX').'compatibility')->order('createtime DESC')->limit((($pagesize * ($page - 1))) . ',' . $pagesize)->select();
        foreach($Result['compatibilitys'] as $key=>&$value){
            $value['createtime'] = date('Y-m-d H:i', $value['createtime']);
            switch( $value['state'] ){
                case 1:
                    $value['state_name'] = '完全兼容';
                    $value['tag'] = 'success';
                    break;
                case 2:
                    $value['state_name'] = '能够使用';
                    $value['tag'] = 'warning';
                    break;
                case 3:
                    $value['state_name'] = '不能兼容';
                    $value['tag'] = 'danger';
                    break;
                default:
                    $value['state_name'] = 'UNKNOW';
            }
        }
        $this->ajaxReturn($Result);
    }

    /**
     * 获取按部门分组的角色(职位)列表
     */
    public function getDepartmentGroupPostList(){
        $model = new Model();
        $result = $model->table(C('DB_PREFIX').'department')->select();
        foreach( $result as $key=>&$value ){
            $positions = $model->table(C('DB_PREFIX').'position')->where( ['belongsto'=>$value['id']] )->select();
            if( $positions ) $value['positions'] = $positions;
        }
        $this->ajaxReturn($result);
    }

    /**
     * 获取项目列表
     */
    public function getProjectList(){
        $model = new Model();
        $pagesize = 15;
        $page = I('get.page') > 0 ? I('get.page') : 1;
        $Result['pagesize'] = $pagesize;
        $Result['total'] = (int)$model->table(C('DB_PREFIX').'project')->where('pj_child = 0')->count();
        $Result['data'] = $model->table(C('DB_PREFIX').'project')->where('pj_child = 0')->order('pj_create_time DESC')->limit((($pagesize * ($page - 1))) . ',' . $pagesize)->select();
        foreach( $Result['data'] as $key=>&$value ){
            $value['pj_update_time'] = date('Y-m-d H:i', $value['pj_update_time']);
            $state = $model->table(C('DB_PREFIX').'project_plan')->field('gate,mile_stone')->where('plan_project='.$value['id'])->order('save_time DESC')->find();
            $value['progress'] = 'Gate'.$state['gate'].'-'.$state['mile_stone'];
        }
        $this->ajaxReturn($Result);
    }

    /**
     * 文件上传
     */
    public function upload()
    {
        if (I('post.path') && I('post.path') != '') {
            $savePath = I('post.path');
        }
        $subName = '';
        $upload = new Upload();
        // 设置上传路径
        $upload->savePath = $savePath;
        // 限制上传文件大小为20mb
        $upload->maxSize = 20971520;
        // 开启子目录保存 并以指定参数为子目录
        $upload->autoSub = true;
        if ($subName != '') {
            $upload->subName = $subName;
        }
        // 保持上传文件名不变
        $upload->saveName = uniqid() . '_' . time();
        // 存在同名文件是否是覆盖
        $upload->replace = true;
        // 上传并返回结果
        $fileinfo = $upload->upload();
        if ($fileinfo) {
            $path = './Uploads' . $fileinfo['file']['savepath'] . $fileinfo['file']['savename'];
            $name = $fileinfo['file']['name'];
            $savename = $fileinfo['file']['savename'];
            $ext = strtolower($fileinfo['file']['ext']);
            $mime = $fileinfo['file']['type'];
            $size = $fileinfo['file']['size'];
            $time = time();
            $this->ajaxReturn(['flag' => 1, 'path' => $path, 'name' => $name, 'savename' => $savename, 'ext' => $ext, 'mime' => $mime, 'size' => $size, 'time' => $time]);
        } else {
            $this->ajaxReturn(['flag' => 0, 'msg' => $upload->getError()]);
        }
    }


}