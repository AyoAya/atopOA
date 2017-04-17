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
            echo '<div style="width:170px;height:260px;background:url('.__ROOT__.'/Public/home/img/low_power.png) no-repeat;position:fixed;top:50%;left:50%;margin-left:-73.5px;margin-top:-150px;"><span onclick="history.back();" style="position:absolute;bottom:0;left:50%;width:66px;margin-left:-60px;cursor:pointer;padding:10px 20px;text-align:center;background:#777;color:#fff;border-radius:4px;"> 返回 </span></div>';
            exit;
        }
        
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
            return $productResult;
        }
    }


    //产品搜索
    public function productSearch(){
        if( IS_POST ){
            $searchValue = I('post.search','',false);
            $pro_rel_ships = M('Productrelationships');
            $map['pn'] = ['like','%'.$searchValue.'%'];
            $searchResult = $pro_rel_ships->field('id,pn')->where($map)->select();
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
            $pro_rel_ships = M('Productrelationships');
            $filterResult = $pro_rel_ships->field('id,pn,manager')->where($condition)->select();
            $categoryResult = $this->getProductData($condition);
            foreach($postdata['filter'] as $key=>$value){
                if( $value != '' ){
                    unset($categoryResult[$key.'s']);
                }
            }
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


}