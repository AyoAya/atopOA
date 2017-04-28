<?php 
namespace Home\Controller;
use Think\Model;
use Think\Page;

class SampleController extends AuthController {

    //初始化样品管理首页
    public function index(){
        $person = M('Sample');
        //是否有查询
        if(I('get.search')){
            $count = $person->where('order_num LIKE "%'.I('get.search').'%" OR create_person_name LIKE "%'.I('get.search').'%"')->count();
            $this->assign( 'search' , I('get.search') );
            print_r($count);
        }else{
            $count = $person->count();
        }
        //数据分页
        $page = new Page($count,15);
        $page->setConfig('prev','<span aria-hidden="true">上一页</span>');
        $page->setConfig('next','<span aria-hidden="true">下一页</span>');
        $page->setConfig('first','<span aria-hidden="true">首页</span>');
        $page->setConfig('last','<span aria-hidden="true">尾页</span>');
        if(C('PAGE_STATUS_INFO')){
            $page->setConfig ( 'theme', '<li><a href="javascript:void(0);">当前%NOW_PAGE%/%TOTAL_PAGE%</a></li>  %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%' );
        }
        //是否需要显示查询结果
        if(I('get.search')){
            $sampleResult = $person->where('order_num LIKE "%'.I('get.search').'%" OR create_person_name LIKE "%'.I('get.search').'%"')->order('id DESC')->limit($page->firstRow.','.$page->listRows)->select();
        }else{
            $sampleResult = $person->order('id DESC')->limit($page->firstRow.','.$page->listRows)->select();
        }
        $pageShow = $page->show();
        $sample_detail = M('Sample_detail');
        $user = M('user');
        foreach ($sampleResult as $key=>&$value) {
            //与订单详情表对接
            $value['child'] = $sample_detail->where('detail_assoc="' . $value['id'] . '"')->select();
            //订单状态颜色
            //print_r($value['order_state'].'----');
            if ($value['order_state'] >= 1 && $value['order_state'] < 6) {
                $value['color'] = '#428bca';
            } elseif ($value['order_state'] >= 6) {
                $value['color'] = '#5cb85c';
            } else {
                $value['color'] = '#d9534f';
            }
            //统计子数据条数
            foreach ($value['child'] as $name => $id) {
                $value['total'] = $sample_detail->where('detail_assoc="' . $value['id'] . '"')->count();
            }
            //与user表对接
            $value['u'] = $user->where('id="' . $value['create_person_id'] . '"')->select();
            foreach ($value['u'] as $name=>$id) {
            }
        }
        $this->assign('personList',$sampleResult);
        $this->assign('page',$pageShow);
        $this->assign('sampleResult',$sampleResult);
        $this->display();
    }

    //初始化添加页面及添加入库
    public function add(){

        if(IS_POST) {
            if (I('post.order_num')) {
                $sample = M('sample');
                $sample_data['order_num'] = I('post.order_num');
                $sample_data['order_charge'] = I('post.order_charge');
                $sample_data['create_time'] = time();
                $sample_data['create_person_id'] = session('user')['id'];
                $sample_data['create_person_name'] = session('user')['nickname'];
                $sample_data['order_state'] = 1;
                $rel = $sample->where('order_num="'.I('post.order_num').'"')->select(); //订单号是否重复

               // print_r($rel);
               // exit();

                if(!empty($rel)){
                    $this->ajaxReturn(['flag'=>0,'msg'=>'订单已存在！']);
                }else{
                    $sample_model = new Model();
                    $sample_model->startTrans();
                    $sample_id = $sample_model->table('atop_sample')->add($sample_data);
                    $jsonStr = I('post.sample_details','',false);
                    $arr = json_decode($jsonStr,true);
                    //arr(arr(),arr()) 循环详情表数据
                    foreach ($arr['sample_detail'] as $key=>&$value){
                        $value['detail_assoc'] = $sample_id;
                    }
                    $adds = $sample_model->table('atop_sample_detail')->addAll($arr['sample_detail']);

                    if($adds && $sample_id){
                        $sample_model->commit();
                        $this->ajaxReturn(['flag'=>1,'msg'=>'添加订单成功！','id'=>$sample_id]);
                    }else{
                        $sample_model->rollback();
                        $this->ajaxReturn(['flag'=>0,'msg'=>'添加订单失败！']);
                    }
                }
            }
        } else {
            $this->assign('productFilter', $this->getProductData());
            $this->display();
        }

    }


    //上传附件
    public function upload(){
        $info = FileUpload('/SampleAttachment/',1,0,'sample_attachment_');
        if( is_array($info) ){  //检测文件上传是否成功，如果返回的结果为数组表示成功，否则为失败
            $return_data['flag'] = 1;
            $return_data['source'] = $info['Attachment']['name'];
            $return_data['newname'] = $info['Attachment']['savename'];
            $return_data['size'] = $info['Attachment']['size'];
            $return_data['ext'] = $info['Attachment']['ext'];
            $return_data['savepath'] = '/Uploads'.$info['Attachment']['savepath'].$info['Attachment']['savename'];
            $this->ajaxReturn($return_data);
        }else{
            $return_data['flag'] = 0;
            $return_data['msg'] = $info;
            $this->ajaxReturn($return_data);
            exit;
        }
    }

    //删除附件
    public function removeFile(){
        if( IS_POST && I('post.filepath') ){
            if( file_exists( '.'.I('post.filepath') ) ){
                if( unlink('.'.I('post.filepath')) ){
                    $this->ajaxReturn( ['flag'=>1,'msg'=>'删除成功'] );
                }else{
                    $this->ajaxReturn( ['flag'=>0,'msg'=>'删除失败'] );
                }
            }else{
                $this->ajaxReturn( ['flag'=>0,'msg'=>'文件不存在'] );
            }
        }
    }

    //样品单总览
    public function overview(){
        if (I('get.id')) {
            $model = new Model();
            $sample_detail = M('sampleDetail');

            $child_data = $model->field('a.id,a.pn,a.count,a.detail_assoc,a.customer,a.brand,a.model,a.note,a.requirements_date,a.expect_date,a.actual_date,b.type,b.pn,c.nickname,d.create_time,d.create_person_name,d.order_num,d.order_charge')
                ->table(C('DB_PREFIX') . 'sample_detail a,' . C('DB_PREFIX') . 'productrelationships b,' . C('DB_PREFIX') . 'user c,' . C('DB_PREFIX') . 'sample d')
                ->where('a.detail_assoc=' . I('get.id') . ' AND a.product_id=b.id AND b.manager=c.id AND a.detail_assoc=d.id')->select();
            if (!empty($child_data)) {
                $this->assign('childData', $child_data);

                //获取样品附件列表
            }
            //print_r($child_data);
                $attachment = M('SampleAttachment');
                $map['attachment_assoc'] = I('get.id');
                $attachmentResult = $attachment->where($map)->select();
                $this->assign('attachmentResult', $attachmentResult);
                $this->display();
        }
    }



    //订单详情页
    public function detail(){

        if( I('get.id') ){
            $detailResult = M()->table(C('DB_PREFIX').'sample a,'.C('DB_PREFIX').'sample_detail b,'.C('DB_PREFIX').'user c,'.C('DB_PREFIX').'productrelationships d')
                ->field('a.order_num,b.detail_assoc,b.count,b.customer,b.brand,b.model,b.note,b.requirements_date,b.expect_date,b.actual_date,c.nickname,d.type,d.pn')
                ->where('a.id=b.detail_assoc AND b.manager=c.id AND b.product_id=d.id AND b.id='.I('get.id'))->select();



               /* $data['time'] = time();
                $data['state'] = I('post.state');
                $data['desc'] = I('post.desc');

                print_r($data);*/






            //print_r($detailResult);
            //获取样品附件列表
            $attachment = M('SampleAttachment');
            $map['attachment_assoc'] = $detailResult[0]['detail_assoc'];
            $attachmentResult = $attachment->where($map)->select();

            $step = M('SampleStep');
            $stepResult = $step->select();
            $this->assign('stepResult',$stepResult);
            $this->assign('attachmentResult',$attachmentResult);
            $this->assign('detailResult',$detailResult);
            $this->display();
        }
    }

}