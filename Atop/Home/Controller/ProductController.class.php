<?php
/**
 * Created by PhpStorm.
 * User: Fulwin
 * Date: 2017/3/30
 * Time: 9:46
 */
namespace Home\Controller;
use Think\Model;
use Think\Page;

class ProductController extends AuthController {

    # 初始化页面
    public function index(){

        $proModel = M('Productrelationships');

        if( I('get.search') ){
            $count = $proModel->table('atop_productrelationships a,atop_user b')->where('a.manager=b.id AND a.pn LIKE "%'.I('get.search').'%"')->count();
            $this->assign( 'search' , I('get.search') );
        }else{
            $count = $proModel->table('atop_productrelationships a,atop_user b')->where('a.manager=b.id')->count();
        }
        //echo $count;

        //数据分页
        $page = new Page($count,15);
        $page->setConfig('prev','<span aria-hidden="true">上一页</span>');
        $page->setConfig('next','<span aria-hidden="true">下一页</span>');
        $page->setConfig('first','<span aria-hidden="true">首页</span>');
        $page->setConfig('last','<span aria-hidden="true">尾页</span>');
        if(C('PAGE_STATUS_INFO')){
            $page->setConfig ( 'theme', '<li><a href="javascript:void(0);">当前%NOW_PAGE%/%TOTAL_PAGE%</a></li>  %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%' );
        }

        $userPosition = M()->table('atop_position a,atop_user b')->field('a.name')->where('a.id=b.position AND b.id='.session('user')['id'])->select(); //获取当前登录用户的职位信息

        if( I('get.search') ){
            $proResult = $proModel->table('atop_productrelationships a,atop_user b')
                ->field('a.id,a.type,a.stage,a.pn,a.rate,a.wavelength,a.component,a.txpwr,a.rxsens,a.connector,a.casetemp,a.reach,a.multirate,b.nickname')
                ->where('a.manager=b.id AND a.pn LIKE "%'.I('get.search').'%"')->order('type,pn ASC')->limit($page->firstRow.','.$page->listRows)->select();
        }else{
            $proResult = $proModel->table('atop_productrelationships a,atop_user b')
                ->field('a.id,a.type,a.stage,a.pn,a.rate,a.wavelength,a.component,a.txpwr,a.rxsens,a.connector,a.casetemp,a.reach,a.multirate,b.nickname')
                ->where('a.manager=b.id')->order('type,pn ASC')->limit($page->firstRow.','.$page->listRows)->select();
        }
        $pageShow = $page->show();
        if(I('get.p')){
            $this->assign('pagenumber',I('get.p')-1);
        }
        $this->assign('limitsize',C('LIMIT_SIZE'));
        $this->assign('pageShow',$pageShow);

        //获取筛选数据
        $filter['type'] = $proModel->field('type')->distinct(true)->order('type ASC')->select();
        $filter['wavelength'] = $proModel->field('wavelength')->distinct(true)->order('wavelength ASC')->select();
        $filter['connector'] = $proModel->field('connector')->distinct(true)->order('connector ASC')->select();
        $filter['casetemp'] = $proModel->field('casetemp')->distinct(true)->order('casetemp ASC')->select();
        $filter['reach'] = $proModel->field('reach')->distinct(true)->order('reach ASC')->select();

        $this->assign('filter',$filter);

        $this->assign('position',$userPosition[0]['name']);
        $this->assign('products',$proResult);
        $this->display();

    }


    # 产品添加页面
    public function add(){
        if( IS_POST ){
            $productData = I('post.');
            $productData['manager'] = session('user')['id'];
            $proModel = M('Productrelationships');
            $id = $proModel->add($productData);
            if( $id ){
                $this->ajaxReturn( ['flag'=>$id,'msg'=>'添加成功'] );
            }else{
                $this->ajaxReturn( ['flag'=>0,'msg'=>'添加失败'] );
            }
        }else{
            //检查访问当前页面的用户是否是产品经理
            $userPosition = M()->table('atop_position a,atop_user b')->field('a.name')->where('a.id=b.position AND b.id='.session('user')['id'])->select(); //获取当前登录用户的职位信息
            if( $userPosition[0]['name'] != '产品经理' ){
                $this->error('您没有权限');exit;
            }
            $types = M('Productrelationships')->field('type')->distinct(true)->order('type ASC')->select();
            # print_r($types);
            $this->assign('types',$types);
            $this->display();
        }
    }


    # 产品修改页面
    public function edit(){
        if( IS_POST ){
            $model = new Model();
            $row = $model->table(C('DB_PREFIX').'productrelationships')->save(I('post.', '', false));
            if( $row !== false ){
                $this->ajaxReturn( ['flag'=>1, 'msg'=>'修改成功'] );
            }else{
                $this->ajaxReturn( ['flag'=>0, 'msg'=>'修改失败'] );
            }
        }else{
            if( I('get.id') && is_numeric(I('get.id')) ){
                $model = new Model();
                $result = $model->table(C('DB_PREFIX').'productrelationships')->find( I('get.id') );
                //获取筛选数据
                $filter['types'] = $model->table(C('DB_PREFIX').'productrelationships')->field('type')->distinct(true)->order('type ASC')->select();
                $filter['connectors'] = $model->table(C('DB_PREFIX').'productrelationships')->field('connector')->distinct(true)->order('connector ASC')->select();
                $filter['casetemps'] = $model->table(C('DB_PREFIX').'productrelationships')->field('casetemp')->distinct(true)->order('casetemp ASC')->select();
                $this->assign('filter',$filter);
                $this->assign('product',$result);
                $this->display();
            }else{
                $this->error('参数错误');
            }
        }
    }


    # 产品删除
    public function delete(){
        if( IS_POST && I('post.id') && is_numeric(I('post.id')) ){
            $model = new Model();
            $row = $model->table(C('DB_PREFIX').'productrelationships')->delete(I('post.id'));
            if( $row ){
                $this->ajaxReturn( ['flag'=>1, 'msg'=>'删除成功'] );
            }else{
                $this->ajaxReturn( ['flag'=>0, 'msg'=>'删除失败'] );
            }
        }
    }



}