<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/1
 * Time: 23:58
 */
namespace Home\Controller;

use Think\Model;
use Think\Page;

class ECNController extends AuthController {

    # 初始化ECN首页
    public function index(){
        $this->display();
    }

    # 创建ECN
    public function add(){
        if( IS_POST ){
            print_r(I('post.','',false));
        }else{
            $reviews = $this->getWaitingReviewItem();
            $ecnRules = $this->getAllEcnRules();
            $dccUsers = $this->getDccPostAllUsers();
            $this->assign('dccUsers', $dccUsers);
            $this->assign('ecnRules', $ecnRules);
            $this->assign('reviews', json_encode($reviews));
            $this->display();
        }
    }

    /**
     * 获取所有dcc专员
     * @return mixed
     */
    private function getDccPostAllUsers(){
        $model = new Model();
        $result = $model->table(C('DB_PREFIX').'user')->field('id,nickname')->where('post REGEXP "^1788," OR post REGEXP ",1788$" OR post REGEXP ",1788," OR post REGEXP "^1788$" AND state = 1')->select();
        return $result;
    }

    /**
     * 获取所有等待评审的项
     * @return mixed
     */
    private function getWaitingReviewItem(){
        $model = new Model();
        $result['file'] = $model->table(C('DB_PREFIX').'file_number')->where(['state'=>'WaitingReview', 'createuser'=>session('user')['id']])->select();   // 获取所有状态为待评审的文件
        foreach( $result['file'] as $key=>&$value ){
            $value['attachment'] = json_decode($value['attachment'], true);
            $value['description'] = strip_tags($value['description']);
        }
        return $result;
    }

    /**
     * 获取所有ECN规则
     * @return mixed
     */
    private function getAllEcnRules(){
        $model = new Model();
        $result = $model->table(C('DB_PREFIX').'ecn_rule')->field('id,name')->select();
        return $result;
    }

    # 监听ecn规则改变
    public function listenEcnRuleChange(){
        if( IS_POST ){
            $ruleid = I('post.ruleid');
            $model = new Model();
            $result = $model->table(C('DB_PREFIX').'ecn_rule')->find($ruleid);
            $result['ccs'] = $this->getCcsAndRecipients('String', $result['cc']);
            $result['recipients'] = $this->getCcsAndRecipients('Array', $result['recipient']);
            $result['ccs'] = $this->getEcnRuleConfigurationUsers('cc', $result['ccs']);
            $result['recipients'] = $this->getEcnRuleConfigurationUsers('recipients', $result['recipients']);
            //print_r($result);
            $this->ajaxReturn($result);
        }
    }

    /**
     * 获取规则设定的人员
     * @param string $type
     * @param $data
     * @return mixed
     */
    private function getEcnRuleConfigurationUsers($type = 'cc', $data){
        $model = new Model();
        if( $type === 'cc' ){
            foreach( $data as $key=>&$value ){
                $value['cc'] = $model->table(C('DB_PREFIX').'user')
                                     ->field('id,nickname')
                                     ->where('post REGEXP "^'.$value['id'].'," OR post REGEXP ",'.$value['id'].'$" OR post REGEXP ",'.$value['id'].'," OR post REGEXP "^'.$value['id'].'$" AND state = 1')
                                     ->select();
            }
        }else{
            foreach( $data as $key=>&$value ){
                foreach( $value as $k=>&$v ){
                    $result = $model->table(C('DB_PREFIX').'user')
                                            ->field('id,nickname')
                                            ->where('post REGEXP "^'.$v['id'].'," OR post REGEXP ",'.$v['id'].'$" OR post REGEXP ",'.$v['id'].'," OR post REGEXP "^'.$v['id'].'$" AND state = 1')
                                            ->select();
                    if( $result ){
                        $v['recipient_2D'] = $result;
                        foreach( $result as $kk=>&$vv ){
                            $v['recipient_1D'][] = $vv['id'];
                        }
                    }
                }
            }
        }
        return $data;
    }

    # ECN详情
    public function detail(){
        $this->display();
    }

    # ECN规则列表
    public function rules(){
        $model = new Model();
        $count = $model->table(C('DB_PREFIX').'ecn_rule')->count();
        //数据分页
        $page = new Page($count,C('LIMIT_SIZE'));
        $page->setConfig('prev','<span aria-hidden="true">上一页</span>');
        $page->setConfig('next','<span aria-hidden="true">下一页</span>');
        $page->setConfig('first','<span aria-hidden="true">首页</span>');
        $page->setConfig('last','<span aria-hidden="true">尾页</span>');
        if(C('PAGE_STATUS_INFO')){
            $page->setConfig ( 'theme', '<li><a href="javascript:void(0);">当前%NOW_PAGE%/%TOTAL_PAGE%</a></li>  %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%' );
        }
        $result = $model->table(C('DB_PREFIX').'ecn_rule a,'.C('DB_PREFIX').'user b')
            ->field('a.id,a.name,a.description,a.createtime,b.nickname')
            ->where('a.createuser = b.id')
            ->order('createtime DESC')
            ->limit($page->firstRow.','.$page->listRows)
            ->select();
        $pageShow = $page->show();
        $this->assign('pageShow',$pageShow);
        $this->assign('result',$result);
        $this->display();
    }

    # 规则详情
    public function ruleDetail(){
        if( I('get.id') && is_numeric(I('get.id')) ){
            $model = new Model();
            $result = $model->table(C('DB_PREFIX').'ecn_rule a,'.C('DB_PREFIX').'user b')
                            ->field('a.id,a.name,a.description,a.cc,a.recipient,a.createtime,b.nickname')
                            ->where('a.createuser = b.id AND a.id = '.I('get.id'))
                            ->select()[0];
            $result['ccs'] = $this->getCcsAndRecipients('String', $result['cc']);
            $result['recipients'] = $this->getCcsAndRecipients('Array', $result['recipient']);
            $this->assign('result', $result);
            $this->display();
        }
    }

    # 编辑ECN规则
    public function editRule(){
        if( IS_POST ){
            $postData = I('post.','',false);
            foreach($postData as $key=>&$value){
                if( strstr($key, 'positionGroup') ) $recipient[] = array_keys($value);
            }
            $ecnRuleData['id'] = $postData['id'];
            $ecnRuleData['name'] = $postData['name'];
            $ecnRuleData['description'] = $postData['description'];
            $ecnRuleData['cc'] = implode(',', array_keys($postData['cc']));
            $ecnRuleData['recipient'] = $recipient ? json_encode($recipient) : null;    // 避免插入null
            $ecnRuleData['createtime'] = time();
            $ecnRuleData['createuser'] = session('user')['id'];
            $model = new Model();
            $id = $model->table(C('DB_PREFIX').'ecn_rule')->save($ecnRuleData);
            $id ? $this->ajaxReturn(['flag'=>1, 'msg'=>'保存成功']) : $this->ajaxReturn(['flag'=>0, 'msg'=>'保存失败']);
        }else{
            if( I('get.id') && is_numeric(I('get.id')) ){
                $model = new Model();
                $result = $model->table(C('DB_PREFIX').'ecn_rule')->find(I('get.id'));
                $result['ccs'] = $this->getCcsAndRecipients('String', $result['cc']);
                $result['recipients'] = $this->getCcsAndRecipients('Array', $result['recipient']);
                $positions = $this->getAllPositions();
                //$this->markSelectedPost($this->getAllPositions(), $result['ccs']);
                $result['cc'] = explode(',', $result['cc']);
                $result['recipient'] = json_decode($result['recipient'], true);
                $this->assign('result', json_encode($result));
                $this->assign('positions', json_encode($positions));
                $this->display();
            }
        }
    }

    /**
     * 根据收件人/抄送人的数据类型获取对应的人员信息
     * @param string $type  数据类型
     * @param $data  数据
     * @return array|mixed
     */
    private function getCcsAndRecipients($type = 'String', $data){
        $model = new Model();
        if( $type == 'String' ){
            $map['id'] = ['in', $data];
            $result = $model->table(C('DB_PREFIX').'position')->field('id,name')->where($map)->select();
        }else{
            $condition = json_decode($data, true);
            foreach( $condition as $key=>&$value ){
                $map['id'] = ['in', $value];
                $result[] = $model->table(C('DB_PREFIX').'position')->field('id,name')->where($map)->select();
            }
        }
        return $result;
    }

    # 添加ECN规则
    public function addRule(){
        if( IS_POST ){
            $postData = I('post.','',false);
            foreach($postData as $key=>&$value){
                if( strstr($key, 'positionGroup') ) $recipient[] = array_keys($value);
            }
            $ecnRuleData['name'] = $postData['name'];
            $ecnRuleData['description'] = $postData['description'];
            $ecnRuleData['cc'] = implode(',', array_keys($postData['cc']));
            $ecnRuleData['recipient'] = $recipient ? json_encode($recipient) : null;    // 避免插入null
            $ecnRuleData['createtime'] = time();
            $ecnRuleData['createuser'] = session('user')['id'];
            $model = new Model();
            $id = $model->table(C('DB_PREFIX').'ecn_rule')->add($ecnRuleData);
            $id ? $this->ajaxReturn(['flag'=>1, 'msg'=>'创建成功']) : $this->ajaxReturn(['flag'=>0, 'msg'=>'创建失败']);
        }else{
            $this->assign('positions', json_encode($this->getAllPositions()));
            $this->display();
        }
    }

    # 删除规则
    public function deleteRule(){
        if( I('get.id') && is_numeric(I('get.id')) ){
            $model = new Model();
            $result = $model->table(C('DB_PREFIX').'ecn_rule')->delete(I('get.id'));
            $this->redirect('ECN/rules');
        }
    }

    # 获取所有职位
    private function getAllPositions(){
        $model = new Model();
        $result = $model->table(C('DB_PREFIX').'position')->order('name ASC')->select();
        return $result;
    }




}