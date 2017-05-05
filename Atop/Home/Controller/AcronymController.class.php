<?php
namespace Home\Controller;
/**
 * 缩略词
 * @author Fulwin
 * 2016-10-12
 */
class AcronymController extends AuthController {
    
    //初始化页面
    public function index(){
        $thumbword = D('Acronym');
        $result = $thumbword->relation(true)->group('acronym')->order('acronym ASC')->select();
        $letter = range('A','Z');
        $arr = array();
        foreach($letter as &$value){
            foreach($result as $k=>&$v){
                if( substr($v['acronym'],0,1)==$value ){
                    $arr[$value][] = $v;
                }
            }
        }
        # print_r($arr);
        $this->assign('letter',$letter);
        $this->assign('acronym',$arr);
        $this->display();
    }

    //添加页面载入
    public function add(){
        if(IS_POST){
            $postData = I('post.');
            $postData['add_time'] = date('Y-m-d H:i:s',time());
            $postData['add_person'] = session('user')['id'];
            $id = M('Acronym')->add($postData);
            if( $id ){
                $this->ajaxReturn( ['flag'=>1,'msg'=>'添加成功','id'=>$id] );
            }else{
                $this->ajaxReturn( ['flag'=>0,'msg'=>'添加失败'] );
            }
        }else{
            $this->display();
        }
    }

    //缩略词详情页
    public function details(){
        if( I('get.id') ){
            $acronym = D('Acronym');
            $result = $acronym->relation(true)->find(I('get.id'));
            $duplication = $acronym->where("acronym='".$result['acronym']."'")->select();
            if( count($duplication) > 1 ){
                foreach($duplication as $key=>&$value){
                    if( $value['id'] == I('get.id') ){
                        unset($duplication[$key]);
                    }
                }
                $this->assign('duplication',$duplication);
            }
            # print_r($duplication);
            $this->assign('acronym',$result);
            $this->display();
        }
    }
    
} 