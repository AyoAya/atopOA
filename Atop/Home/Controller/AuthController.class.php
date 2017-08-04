<?php
namespace Home\Controller;
use Think\Controller;
use Think\Auth;
/**
 * 权限认证
 * @author Fulwin
 * 2016-10-10
 */
class AuthController extends Controller {
    protected function _initialize(){

        if(cookie('UserLoginInfo') && !session('user')){
            //当cookie存在而session不存在时，将cookie进行解密并将数据写入到session
            $userInfo = unserialize(decrypt(cookie('UserLoginInfo'),C('MD5_KEY')));
            //array('id'=>$result[0]['id'],'account'=>$result[0]['account'],'nickname'=>$result[0]['nickname'],'face'=>$result[0]['face']);
            session('user',$userInfo);
        }


        //如果session不存在就跳转到登录页
        if(!session('user')){
            //如果session不存在则将当前页面url记录在session中
            session('source',getUrl());
            $this->redirect('Login/index');
            exit;
        }else{
            $face = M('User')->field('account,face,theme,state,department,position')->find(session('user')['id']);
            switch( $face['state'] ){   //检查登录用户的状态
                case 2:
                    $this->error('当前账户已被禁用!');
                    exit;
                    break;
                case 3:
                    $this->error('当前账户已被禁用!');
                    exit;
                    break;
            }
            $this->assign('face',$face);
        }

        //如果是超级管理员就给予所有权限
        if(session('user')['id']==1) return true;

        //检查权限
        $auth = new Auth();
        if(!$auth->check(MODULE_NAME.'/'.CONTROLLER_NAME.'/', session('user')['id'])){
            # 如果验证失败则将该信息注入模板
            $this->assign('PERMISSION_DENIED',true);
        }

    }

    # 获取所有部门和人员信息
    protected function getAllUsersAndDepartments(){
        $result['departments'] = M('Department')->select();
        $result['users'] = M('User')->where('id<>1 AND state=1')->select();     //只获取状态为正常的人员
        # 统计每个部门的人数
        foreach($result['departments'] as $key=>&$value){
            $num = 0;
            foreach( $result['users'] as $k=>&$v ){
                if( $value['id'] == $v['department'] ){
                    $num++;
                }
            }
            $value['summary'] = $num;
        }
        $this->assign('AUAD', $result);
    }

    #获取DCC专员人员数据
    protected function getDccPostUsers(){
        $user = M('User');
        $map['state'] = 1;
        $result = $user->field('id,nickname,email,post')->where( $map )->select();
        foreach( $result as $key=>&$value ){
            if( !in_array(1788, explode(',', $value['post'])) ){
                unset($result[$key]);
            }
        }
        $this->assign('DccPostUsers', $result);
    }



    // 获取产品信息
    public function getProductData($condition = []){
        if( is_array($condition) && !empty($condition) ){
            $pro_rel_ships = M('Productrelationships');
            $productResult['types'] = $pro_rel_ships->field('type')->where($condition)->order('type ASC')->group('type')->select();   //类型
            $productResult['wavelengths'] = $pro_rel_ships->field('wavelength,pn')->where($condition)->order('wavelength ASC')->group('wavelength')->select();   //波长
            $productResult['reachs'] = $pro_rel_ships->field('reach')->where($condition)->order('reach ASC')->group('reach')->select();   //距离
            $productResult['connectors'] = $pro_rel_ships->field('connector')->where($condition)->order('connector ASC')->group('connector')->select();   //接口
            $productResult['casetemps'] = $pro_rel_ships->field('casetemp')->where($condition)->order('casetemp ASC')->group('casetemp')->select();   //环境
            //针对环境类型显示不同的名称
            foreach($productResult['casetemps'] as $key=>&$value){
                if( $value['casetemp'] == 'C' ){
                    $value['casetemp_as_name'] = 'C档（0-70°）';
                }else{
                    $value['casetemp_as_name'] = 'I 档（-40°-85°）';
                }
            }
            //针对于DWDM系列产品显示不同的名称
            if( isset($condition['type']) && !empty($condition['type']) ){
                if( $condition['type'] == 'XFP DWDM' || $condition['type'] == 'SFP DWDM' ){
                    foreach($productResult['wavelengths'] as $key=>&$value){
                        $value['wavelength_as_name'] = 'CH'.substr($value['pn'],4,2);
                    }
                }elseif( $condition['type'] == 'SFP+ DWDM' ){
                    foreach($productResult['wavelengths'] as $key=>&$value){
                        $value['wavelength_as_name'] = 'CH'.substr($value['pn'],5,2);
                    }
                }else{
                    foreach($productResult['wavelengths'] as $key=>&$value){
                        $value['wavelength_as_name'] = $value['wavelength'];
                    }
                }
            }
            return $productResult;
        }else{
            $pro_rel_ships = M('Productrelationships');
            $productResult['types'] = $pro_rel_ships->field('type')->order('type ASC')->group('type')->select();   //类型
            $productResult['wavelengths'] = $pro_rel_ships->field('wavelength')->order('wavelength ASC')->group('wavelength')->select();   //波长
            foreach($productResult['wavelengths'] as $key=>&$value){    //过滤所有距离包含小数点的数据
                if( strpos($value['wavelength'], '.') ){
                    unset($productResult['wavelengths'][$key]);
                }
            }
            $productResult['reachs'] = $pro_rel_ships->field('reach')->order('reach ASC')->group('reach')->select();   //距离
            $productResult['connectors'] = $pro_rel_ships->field('connector')->order('connector ASC')->group('connector')->select();   //接口
            $productResult['casetemps'] = $pro_rel_ships->field('casetemp')->order('casetemp ASC')->group('casetemp')->select();   //环境
            foreach($productResult['casetemps'] as $key=>&$value){
                if( $value['casetemp'] == 'C' ){
                    $value['casetemp_as_name'] = 'C档（0-70°）';
                }else{
                    $value['casetemp_as_name'] = 'I 档（-40°-85°）';
                }
            }
            $productResult['defaultData'] = $pro_rel_ships->field('id,pn,manager')->limit('0,30')->select();   //默认加载30条数据
            $user = M('User');
            foreach ($productResult['defaultData'] as $key=>&$value){
                $value['nickname'] = $user->field('nickname')->find($value['manager'])['nickname'];
            }
            return $productResult;
        }
    }


    //产品搜索
    public function productSearch(){
        if( IS_POST ){
            $searchValue = I('post.search','',false);
            $pro_rel_ships = M('Productrelationships');
            $map['pn'] = ['like','%'.$searchValue.'%'];
            $searchResult = $pro_rel_ships->field('id,pn,manager')->where($map)->select();  $user = M('User');
            foreach ($searchResult as $key=>&$value){
                $value['nickname'] = $user->field('nickname')->find($value['manager'])['nickname'];
            }
            if( $searchResult ){
                $this->ajaxReturn( ['flag'=>1, 'data'=>$searchResult] );
            }else{
                $this->ajaxReturn( ['flag'=>0, 'msg'=>'没有该型号'] );
            }
        }
    }

    //产品筛选
    public function productFilter(){
        if( IS_POST ){
            $postdata = I('post.','',false);
            $condition = [];  //根据用户选择参数筛选对应产品数据
            if( $postdata['filter']['type'] ){
                $condition['type'] = $postdata['filter']['type'];
            }
            if( $postdata['filter']['wavelength'] ){
                $condition['wavelength'] = $postdata['filter']['wavelength'];
            }
            if( $postdata['filter']['reach'] ){
                $condition['reach'] = $postdata['filter']['reach'];
            }
            if( $postdata['filter']['connector'] ){
                $condition['connector'] = $postdata['filter']['connector'];
            }
            if( $postdata['filter']['casetemp'] ){
                $condition['casetemp'] = $postdata['filter']['casetemp'];
            }
            //print_r($condition);
            $pro_rel_ships = M('Productrelationships');

            $filterResult = $pro_rel_ships->field('id,pn,manager')->where($condition)->select();
            //print_r($filterResult);
            $user = M('User');
            foreach ($filterResult as $key=>&$value){
                $value['nickname'] = $user->field('nickname')->find($value['manager'])['nickname'];
            }
            //print_r($filterResult);
            $categoryResult = $this->getProductData($condition);
            //print_r($filterResult);
            foreach($postdata['filter'] as $key=>&$value){
                if( $value != '' ){
                    unset($categoryResult[$key.'s']);
                }
            }
            //print_r($postdata['filter']);
            if( $filterResult ){
                $this->ajaxReturn( ['flag'=>1, 'data'=>$filterResult, 'category'=>$categoryResult] );
            }else{
                $this->ajaxReturn( ['flag'=>0, 'msg'=>'没有相应型号'] );
            }
        }
    }



    /**
     * 检查是否已经参看消息
     * @param $userid
     */
    protected function checkNotice($userid){

    }

    /**
     * 文件下载类
     */
    public function download(){
        if( IS_POST ){
            //检测文件路径是否包含中文，如果存在中文则转换编码
            if(preg_match("/[\x7f-\xff]/", '.'.I('post.filepath'))){
                $filePath = iconv('UTF-8','GB2312', '.'.I('post.filepath'));
            }else{
                $filePath = '.'.I('post.filepath');
            }
            //如果有提交文件名则用提交的文件名，没有则采用路径提取文件名
            if( I('post.filename') != '' ){
                $fileName = I('post.filename');
            }else{
                $fileName = getBasename(I('post.filepath'));
            }
            if( file_exists( substr($filePath,1) ) ){   //检测文件是否存在
                //如果文件名包含中文则进行urlencode转码
                if (preg_match("/[\x7f-\xff]/", $fileName)) {
                    $fileName = urlencode($fileName);
                }
                //实例化thinkphp下载类
                ob_end_clean();
                $http = new \Org\Net\Http;
                $http->download(substr($filePath,1),$fileName);
            }else{
                $this->error('文件不存在或已被删除');
            }
        }
    }

}
