<?php
/**
 * Created by PhpStorm.
 * User: Fulwin
 * Date: 2017/1/24
 * Time: 10:36
 */
namespace Home\Controller;
use Think\Page;
# 兼容表
class CompatibilityController extends AuthController {

    //加载视图
    public function index(){
        $compatibleMatrix = D('Compatibility');
        $condition = array();
        if( I('get.state') && !empty(I('get.state')) ){
            $condition['state'] = I('get.state');
        }
        if( I('get.search') && !empty(I('get.search')) ){
            $condition['pn_name|vendor_name|model|original'] = array('like','%'.I('get.search').'%');
        }
        if( I('get.Full') && !empty(I('get.Full')) && I('get.Full')=='on' ){
            $condition['state'] = 1;
        }
        if( I('get.General') && !empty(I('get.General')) && I('get.General')=='on' ){
            $condition['state'] = 2;
        }
        if( I('get.Cannot') && !empty(I('get.Cannot')) && I('get.Cannot')=='on' ){
            $condition['state'] = 3;
        }
        if( I('get.pn_name') && !empty(I('get.pn_name')) ){
            $condition['pn_name'] = I('get.pn_name');
        }
        if( I('get.vendor_name') && !empty(I('get.vendor_name')) ){
            $condition['vendor_name'] = I('get.vendor_name');
        }
        if( I('get.model') && !empty(I('get.model')) ){
            $condition['model'] = I('get.model');
        }
        if( I('get.original') && !empty(I('get.original')) ){
            $condition['original'] = I('get.original');
        }
        if(!empty($condition)){
            $this->assign('condition',$condition);
        }
        # print_r($condition);
        if(!empty($condition)){
            $count = $compatibleMatrix->where($condition)->count();
        }else{
            $count = $compatibleMatrix->count();
        }
        //数据分页
        $page = new Page($count,C('LIMIT_SIZE'));
        $page->setConfig('prev','<span aria-hidden="true">上一页</span>');
        $page->setConfig('next','<span aria-hidden="true">下一页</span>');
        $page->setConfig('first','<span aria-hidden="true">首页</span>');
        $page->setConfig('last','<span aria-hidden="true">尾页</span>');
        if(C('PAGE_STATUS_INFO')){
            $page->setConfig ( 'theme', '<li><a href="javascript:void(0);">当前%NOW_PAGE%/%TOTAL_PAGE%</a></li>  %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%' );
        }
        if(!empty($condition)){
            $result = $compatibleMatrix->relation(true)->where($condition)->order('createtime DESC')->limit($page->firstRow.','.$page->listRows)->select();
        }else{
            $result = $compatibleMatrix->relation(true)->order('createtime DESC')->limit($page->firstRow.','.$page->listRows)->select();
        }
        foreach($result as $key=>&$value){
            if($value['state']==1){
                $value['state_text'] = '完全兼容';
            }elseif($value['state']==2){
                $value['state_text'] = '能够使用';
            }else{
                $value['state_text'] = '不能兼容';
            }
        }
        if(I('get.p')){
            $this->assign('pagenumber',I('get.p')-1);
        }
        $this->assign('limitsize',C('LIMIT_SIZE'));
        $pageShow = $page->show();
        $this->assign('pageShow',$pageShow);
        # print_r($result);
        $Productrelationships = M('Productrelationships');
        $filterArr = array();
        $filterArr['filterType'] = $Productrelationships->distinct(true)->field('type')->order('type')->select();
        $filterArr['filterWavelength'] = $Productrelationships->distinct(true)->field('wavelength')->order('wavelength')->select();
        $filterArr['filterConnector'] = $Productrelationships->distinct(true)->field('connector')->select();
        $filterArr['filterCasetemp'] = $Productrelationships->distinct(true)->field('casetemp')->select();
        $filterArr['filterReach'] = $Productrelationships->distinct(true)->field('reach')->select();
        $filterData = $Productrelationships->field('p.pn,p.id pid,u.nickname,p.wavelength,p.reach,p.connector,p.casetemp,p.type,u.id uid,p.manager')->table('__USER__ u,__PRODUCTRELATIONSHIPS__ p')->where('p.manager=u.id')->select();
        $vendorBrand = M('VendorBrand');
        $verdorResult = $vendorBrand->order('brand')->select();
        $this->assign('vendor',$verdorResult);
        $this->assign('filter',$filterArr);
        $this->assign('filterdata',$filterData);
        $this->assign('compatiblematrix',$result);
        $this->display();
    }

    //加载添加页面视图
    public function add(){
        //获取产品筛选数据
        $vendorBrand = M('VendorBrand');
        $verdorResult = $vendorBrand->order('id ASC')->select();
        $this->assign('vendor',$verdorResult);
        $this->assign('productFilter', $this->getProductData());
        $this->display();
    }

    //添加兼容记录
    public function addCompatibleMatrix(){
        if(IS_POST){
            $postData = I('post.');
            $postData['createtime'] = time();
            $postData['person'] = session('user')['nickname'];
            $id = M('Compatibility')->add($postData);
            if( $id ){
                $this->ajaxReturn( ['flag'=>1,'msg'=>'添加成功','id'=>$id] );
            }else{
                $this->ajaxReturn( ['flag'=>0,'msg'=>'添加失败'] );
            }
        }
    }

    //详情页
    public function details(){
        if( I('get.id') && I('get.id')!='' ){
            $id = I('get.id');
            $compatibility = D('Compatibility');
            $result = $compatibility->relation(true)->find($id);
            $pn = $result['pn'];
            $comResult = $compatibility->relation(true)->where("pn='{$pn}'")->order('vendor_name,model ASC')->select();
            foreach($comResult as $key=>&$value){
                if($value['state']==1){
                    $value['state_text'] = '完全兼容';
                }elseif($value['state']==2){
                    $value['state_text'] = '能够使用';
                }else{
                    $value['state_text'] = '不能兼容';
                }
                $value['createtime'] = date('Y-m-d',$value['createtime']);
            }
            $this->assign('comResult',$comResult);
            # print_r($comResult);
            //获取产品经理姓名
            $managerid = $result['Productrelationships']['manager'];
            $result['Productrelationships']['managername'] = M('User')->field('nickname')->find($managerid)['nickname'];
            $this->assign('compatibility',$result);
            $this->display();
        }
    }


}