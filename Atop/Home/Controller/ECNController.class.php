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
        $model = new Model();
        $count = $model->table(C('DB_PREFIX').'ecn')->where('disable = "N"')->count();
        //数据分页
        $page = new Page($count,C('LIMIT_SIZE'));
        $page->setConfig('prev','<span aria-hidden="true">上一页</span>');
        $page->setConfig('next','<span aria-hidden="true">下一页</span>');
        $page->setConfig('first','<span aria-hidden="true">首页</span>');
        $page->setConfig('last','<span aria-hidden="true">尾页</span>');
        if(C('PAGE_STATUS_INFO')){
            $page->setConfig ( 'theme', '<li><a href="javascript:void(0);">当前%NOW_PAGE%/%TOTAL_PAGE%</a></li>  %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%' );
        }
        $result = $model->table(C('DB_PREFIX').'ecn a,'.C('DB_PREFIX').'user b')
                        ->field('a.id,a.ecn_number,a.state,a.disable,a.createtime,a.lastedit_time,b.nickname')
                        ->where('a.createuser = b.id AND a.disable = "N"')
                        ->order('a.createtime DESC')
                        ->limit($page->firstRow.','.$page->listRows)
                        ->select();
        foreach( $result as $key=>&$value ){
            $value['className'] = $this->fetchClassStyle($value['state']);
            $value['stateName'] = $this->fetchStateName($value['state']);
        }
        $pageShow = $page->show();
        $this->assign('pageShow',$pageShow);
        $this->assign('result',$result);
        $this->display();
    }



    # 创建ECN
    public function add(){
        if( IS_POST ){
            $model = M('', '', 'MYSQL_CRSAPI');
            $model->startTrans();
            try{
                $postData = I('post.','',false);
                // 拼装ecn表数据
                $ecnData['change_description'] = $postData['change_description'];
                $ecnData['change_reason'] = $postData['change_reason'];
                $ecnData['quote_rule'] = $postData['quote_rule'];
                $ecnData['ecn_type'] = $postData['ecn_type'];
                $ecnData['createtime'] = time();
                $ecnData['createuser'] = session('user')['id'];
                $ecn_id = $model->table(C('DB_PREFIX').'ecn')->add($ecnData);
                if( !$ecn_id ) throw new \Exception('创建失败');
                // 生成ECN编号
                $ecnNumber = 'ECN';
                $ecnNumLen = (12 - (int)strlen($ecn_id));     // 默认ecn长度为12位数
                $ecnNumber = str_pad($ecnNumber, $ecnNumLen, '0', STR_PAD_RIGHT).$ecn_id;
                $saveEcnNum = $model->table(C('DB_PREFIX').'ecn')->save(['id'=>$ecn_id, 'ecn_number'=>$ecnNumber]);   // 保存生成的ecn号()
                if( $saveEcnNum === false ) throw new \Exception('创建失败');
                // 拼装ecn_review表的评审组数据
                foreach( $postData['review_group'] as $key=>&$value ){
                    foreach( $value as $k=>$v ){
                        $ecnReviewData['review_user'] = $v;
                        $ecnReviewData['ecn_id'] = $ecn_id;
                        $ecnReviewData['along'] = ($key + 1);
                        $ecnReviewId = $model->table(C('DB_PREFIX').'ecn_review')->add($ecnReviewData);
                        if( !$ecnReviewId ) throw new \Exception('创建失败');
                    }
                }
                // 拼装ecn_review表的dcc数据
                foreach( $postData['dcc_review'] as $key=>&$value ){
                    $dccReviewData['review_user'] = $value;
                    $dccReviewData['ecn_id'] = $ecn_id;
                    $dccReviewData['along'] = 0;
                    $dccReviewData['is_dcc'] = 'Y';
                    $dccReviewId = $model->table(C('DB_PREFIX').'ecn_review')->add($dccReviewData);
                    if( !$dccReviewId ) throw new \Exception('创建失败');
                }
                // 拼装ecn_review_item表数据
                foreach( $postData['reviewSelected'] as $key=>&$value ){
                    if( $postData['ecn_type'] === 'file' ){  // 如果类型是file
                        // 拼装评审文件的状态数据
                        $changeFileStateData['id'] = $value['id'];
                        $changeFileStateData['state'] = 'InReview';
                        // 修改评审文件的状态为评审中
                        $changeFileStateId = $model->table(C('DB_PREFIX').'file_number')->save($changeFileStateData);
                        if( $changeFileStateId === false ) throw new \Exception('创建失败');
                    }else{
                        // 非文件ecn类型的处理方式...
                    }
                    $ecnReviewItemData['assoc'] = $value['id'];
                    $ecnReviewItemData['ecn_id'] = $ecn_id;
                    // 写入评审项数据
                    $ecnReviewItemId = $model->table(C('DB_PREFIX').'ecn_review_item')->add($ecnReviewItemData);
                    if( !$ecnReviewItemId ) throw new \Exception('创建失败');
                }
            }catch (\Exception $exception){
                $model->rollback();
                $this->ajaxReturn(['flag'=>0, 'msg'=>$exception->getMessage()]);
            }
            $model->commit();
            $this->ajaxReturn(['flag'=>1, 'msg'=>'创建成功']);
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
     * 根据状态获取class样式
     * @param $state
     * @return string
     */
    private function fetchClassStyle($state){
        switch( $state ){
            case 'WaitingEdit':     // 待编辑
                $className = 'tag tag-night';
                break;
            case 'WaitingReview':   // 待评审
                $className = 'tag tag-warning';
                break;
            case 'InReview':        // 评审中
                $className = 'tag tag-info';
                break;
            case 'BeRejected':      // 驳回
                $className = 'tag tag-danger';
                break;
            case 'NotReview':      // 待评审
                $className = 'tag tag-warning';
                break;
            case 'WAIT':      // 待评审
                $className = 'tag tag-warning';
                break;
            default:                // 如果都不是则直接返回错误的class样式
                $className = 'tag tag-danger';
        }
        return $className;
    }

    /**
     * 根据状态获取状态名称
     * @param $state
     * @return string
     */
    private function fetchStateName($state){
        switch( $state ){
            case 'WaitingEdit':     // 待编辑
                $stateName = '待编辑';
                break;
            case 'WaitingReview':   // 待评审
                $stateName = '待评审';
                break;
            case 'InReview':        // 评审中
                $stateName = '评审中';
                break;
            case 'BeRejected':      // 驳回
                $stateName = '驳回';
                break;
            case 'NotReview':      // 待评审
                $stateName = '待评审';
                break;
            case 'WAIT':      // 待评审
                $stateName = '待评审';
                break;
            default:                // 如果都不是则直接返回错误的class样式
                $stateName = 'UNKNOW';
        }
        return $stateName;
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

    // 获取指定ecn类型的数据
    public function getDataOfCurrentEcnType(){
        if( IS_POST ){
            $model = D('Ecn');
            if( I('post.currentType') == 'file' ){
                $result = $model->table(C('DB_PREFIX').'file_number')->where('createuser='.session('user')['id'].' AND state <> "InReview"')->order('createtime DESC')->select();
                $result = $model->jsonToArray($result);
                $this->ajaxReturn($result);
            }else{
                // 如果不是file类型的ecn数据获取...
            }
        }
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
        if( IS_POST ){
            $model = M('', '', 'MYSQL_CRSAPI');
            $model->startTrans();
            try{
                $postData = I('post.','',false);
                // 拼装ecn表数据
                $ecnData['id'] = $postData['id'];
                $ecnData['change_description'] = $postData['change_description'];
                $ecnData['change_reason'] = $postData['change_reason'];
                $ecnData['quote_rule'] = $postData['quote_rule'];
                $ecnData['ecn_type'] = $postData['ecn_type'];
                $ecnData['lastedit_time'] = time();
                $ecnData['createuser'] = session('user')['id'];
                $ecn_row = $model->table(C('DB_PREFIX').'ecn')->save($ecnData);
                if( $ecn_id === false ) throw new \Exception('保存失败');
                $ecnReviewDel_row = $model->table(C('DB_PREFIX').'ecn_review')->where('ecn_id='.$postData['id'])->delete();
                if( !$ecnReviewDel_row ) throw new \Exception('保存失败');
                // 拼装ecn_review表的评审组数据
                foreach( $postData['review_group'] as $key=>&$value ){
                    foreach( $value as $k=>$v ){
                        $ecnReviewData['review_user'] = $v;
                        $ecnReviewData['ecn_id'] = $postData['id'];
                        $ecnReviewData['along'] = ($key + 1);
                        $ecnReviewId = $model->table(C('DB_PREFIX').'ecn_review')->add($ecnReviewData);
                        if( !$ecnReviewId ) throw new \Exception('保存失败');
                    }
                }
                // 拼装ecn_review表的dcc数据
                foreach( $postData['dcc_review'] as $key=>&$value ){
                    $dccReviewData['review_user'] = $value;
                    $dccReviewData['ecn_id'] = $postData['id'];
                    $dccReviewData['along'] = 0;
                    $dccReviewData['is_dcc'] = 'Y';
                    $dccReviewId = $model->table(C('DB_PREFIX').'ecn_review')->add($dccReviewData);
                    if( !$dccReviewId ) throw new \Exception('保存失败');
                }
                $ecnReviewItemDel_row = $model->table(C('DB_PREFIX').'ecn_review_item')->where('ecn_id='.$postData['id'])->delete();
                if( !$ecnReviewItemDel_row ) throw new \Exception('保存失败');
                // 拼装ecn_review_item表数据
                foreach( $postData['reviewSelected'] as $key=>&$value ){
                    $ecnReviewItemData['assoc'] = $value['id'];
                    $ecnReviewItemData['ecn_id'] = $postData['id'];
                    // 写入评审项数据
                    $ecnReviewItemId = $model->table(C('DB_PREFIX').'ecn_review_item')->add($ecnReviewItemData);
                    if( !$ecnReviewItemId ) throw new \Exception('保存失败');
                }
            }catch (\Exception $exception){
                $model->rollback();
                $this->ajaxReturn(['flag'=>0, 'msg'=>$exception->getMessage()]);
            }
            $model->commit();
            $this->ajaxReturn(['flag'=>1, 'msg'=>'保存成功']);
        }else{
            if( I('get.id') && is_numeric(I('get.id')) ){
                $EcnModel = D('Ecn');
                $result = $EcnModel->relation(true)->find(I('get.id'));
                // 将评审人数据提取出来重新排序
                $sortEcnReview = $result['EcnReview'];
                $sort = [];
                foreach ($result['EcnReview'] as $key=>&$value) {
                    $sort[] = $value['along'];
                }
                array_multisort($sort, SORT_ASC, $sortEcnReview);
                $result['EcnReview'] = $sortEcnReview;  // 将排序后的数组重新替换原来的数组
                // 解决dcc排序样式问题，将dcc数组提取出来保存之后删除掉再重新添加进去
                $firstDccReview = $result['EcnReview'][0];
                unset($result['EcnReview'][0]);
                array_push($result['EcnReview'], $firstDccReview);
                sort($result['EcnReview']); // 重新添加之后再排一次序
                $result['className'] = $this->fetchClassStyle($result['state']);
                $result['stateName'] = $this->fetchStateName($result['state']);
                if( $result['ecn_type'] == 'file' ){    // 如果评审类型是file
                    $tableName = 'file_number';
                    foreach( $result['EcnReviewItem'] as $key=>&$value ){
                        $itemData = D('Ecn')->table(C('DB_PREFIX').$tableName)->find($value['assoc']);
                        $itemData['attachment'] = json_decode($itemData['attachment'], true);
                        $value['item'] = $itemData;
                    }
                }else{
                    // 非文件ecn类型的处理方式...
                }
                // 计算合并行、获取审批人姓名、获取样式名和状态名
                if( count($result['EcnReview']) > 1 ){
                    $checkeds = [];
                    foreach( $result['EcnReview'] as $key=>&$value ){
                        if( $value['is_dcc'] != 'Y' ){
                            $checkeds[$value['along']-1][] = $value['review_user'];
                        }else{
                            $checkeds['dcc'][] = $value['review_user'];
                        }
                        $value['user'] = D('Ecn')->table(C('DB_PREFIX').'user')->find($value['review_user']);
                        $value['className'] = $this->fetchClassStyle($value['review_state']);
                        $value['stateName'] = $this->fetchStateName($value['review_state']);
                        $num = 1;
                        if( $key == 0 ){
                            foreach( $result['EcnReview'] as $k=>&$v ){
                                if( $v['along'] == $value['along'] ){
                                    $value['colspan'] = $num++;
                                }
                            }
                        }else{
                            if( $value['along'] != $result['EcnReview'][$key-1]['along'] ){
                                foreach( $result['EcnReview'] as $k=>&$v ){
                                    if( $v['along'] == $value['along'] ){
                                        $value['colspan'] = $num++;
                                    }
                                }
                            }
                        }
                    }
                }
                $result['Checkeds'] = $checkeds;
                $dccUsers = $this->getDccPostAllUsers();
                $this->assign('dccUsers', $dccUsers);
                $this->assign('result', $result);
                $this->assign('rules', $this->getAllEcnRules());
                $this->assign('reviewItems', $this->getEcnReviewItems(I('get.id')));
                $this->display();
            }
        }
    }

    # 发起评审
    public function InitiateReview(){
        if( IS_POST ){
            $model = D('Ecn');
            if( I('post.ecn_type') == 'file' ){
                try {
                    $ecnAllData = $this->getEcnData(I('post.id'));
                    if( !$ecnAllData ) throw new \Exception('发起失败');
                    $changeEcnStateRow = $model->save(['id'=>I('post.id'), 'state'=>'InReview']);   // 修改ecn状态
                    if( !$changeEcnStateRow ) throw new \Exception('发起失败');
                    foreach( $ecnAllData['EcnReviewItem'] as $key=>&$value ){   // 修改文件状态
                        $ecnItemRow = $model->table(C('DB_PREFIX').'file_number')->save(['id'=>$value['assoc'], 'state'=>'InReview']);
                        if( !$ecnItemRow ) throw new \Exception('发起失败');
                    }
                    $pushUsersData = $this->getAlongOfPushUsers(1, $ecnAllData['EcnReview']);   // 获取收件人数据
                    $ccUsersData = $this->getAllCcUsers($ecnAllData['quote_rule']);     // 获取抄送人数据
                    $sendResult = $this->pushEmail('InitiateReview', I('post.ecn_type'), $pushUsersData, $ecnAllData, $ccUsersData);
                    $sendResult ? $this->ajaxReturn(['flag'=>1, 'msg'=>'发起成功']) : $this->ajaxReturn(['flag'=>1, 'msg'=>'发起成功，部分邮件未发送']);
                } catch (\Exception $e) {
                    $this->ajaxReturn(['flag'=>0, 'msg'=>$e->getMessage()]);
                }
            }else{
                // 非文件ecn类型的处理方式...
            }
            $this->ajaxReturn(['flag'=>1, 'msg'=>'发起成功']);
        }
    }

    private function pushEmail($type, $ecnType, $address, $data, $cc = []){
        $httphost = $_SERVER['HTTP_HOST'];
        $dear = count($address) == 1 ? $data['recipient_name'] : 'All';
        if( $ecnType == 'file' ){
            if( $type == 'InitiateReview' ){
                $subject = '['.session('user')['nickname'].'] 发起了编号为【'.$data['ecn_number'].'】的文件评审，请关注';
                $body = '<p class="sm-dear">Dear '.$dear.',</p>';
                $body .= '<p>['.session('user')['nickname'].'] 发起了编号为【'.$data['ecn_number'].'】的文件评审，请关注</p>';
                $body .= '<p>详情请查看链接：<a href="http://'.$httphost.'/ECN/detail/id/'.$data['id'].'" target="_blank">http://'.$httphost.'/ECN/detail/id/'.$data['id'].'</a></p>';
            }else{
                // code...
            }
        }else{
            // 非文件ecn类型的处理方式...
        }
        $sendResult = send_email([['email'=>'vinty_email@163.com','name'=>'蒋明']], '', $subject, $body, [['email'=>'2737583968@qq.com','name'=>'蒋明']]);
        return is_integer($sendResult) ? true : false;
    }

    /**
     * 获取抄送人员数据[姓名/邮箱]
     * @param  [type] $ecnid 规则id
     * @return array
     */
    private function getAllCcUsers($ecnid){
        $model = new Model();
        $ruleData = $model->table(C('DB_PREFIX').'ecn_rule')->find($ecnid);
        $ccIds = explode(',', $ruleData['cc']);
        $TmpCcUsersData = [];
        foreach( $ccIds as $value ){
            $userData = $model->table(C('DB_PREFIX').'user')->field('nickname name,email')->where('post REGEXP "^'.$value.'," OR post REGEXP ",'.$value.'$" OR post REGEXP ",'.$value.'," OR post REGEXP "^'.$value.'$"')->select();
            array_push($TmpCcUsersData, $userData);
        }
        $ccUsersData = [];
        foreach( $TmpCcUsersData as $key=>&$value ){
            foreach( $value as $k=>&$v ){
                array_push($ccUsersData, $v);
            }
        }
        return $ccUsersData;
    }


    /**
     * 获取制定顺序的推送人姓名和邮箱数据
     * @param  [type] $along 顺序[along]
     * @param  [type] $data  数据
     * @return array
     */
    private function getAlongOfPushUsers($along, $data){
        $pushUsersData = [];
        foreach( $data as $key=>&$value ){
            if( $value['along']  == $along ){
                array_push($pushUsersData, [
                    'name' => $value['user']['nickname'],
                    'email' => $value['user']['email']
                ]);
            }
        }
        return $pushUsersData;
    }

    /**
     * 获取Ecn数据
     * @param  string $id 如果指定id则获取数据该id的所有数据，如果不指定则获取所有数据
     * @return mixed
     */
    private function getEcnData($id = ''){
        $model = D('Ecn');
        if( $id ){
            $ecnAllData = $model->relation(true)->find(I('post.id'));
            foreach( $ecnAllData['EcnReview'] as $key=>&$value ){
                $value['user'] = $model->table(C('DB_PREFIX').'user')->find($value['review_user']);
            }
        }else{
            $ecnAllData = $model->relation(true)->select();
            foreach( $ecnAllData as $key=>&$value ){
                foreach( $value['EcnReview'] as $k=>&$v ){
                    $v['user'] = $model->table(C('DB_PREFIX').'user')->find($v['review_user']);
                }
            }
        }
        return $ecnAllData;
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

    // 获取指定项目中包含的文件
    public function getEcnReviewItems($id){
        $model = new Model();
        $result['InReviews'] = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'ecn b,'.C('DB_PREFIX').'ecn_review_item c')
                        ->field('a.id,a.type,a.filenumber,a.state,a.version,a.attachment,a.description')
                        ->where('b.id=c.ecn_id AND c.assoc=a.id AND b.id='.$id)
                        ->order('a.createtime DESC')
                        ->select();
        $InReviewIds = [];
        foreach( $result['InReviews'] as $key=>&$value ){
            array_push($InReviewIds, $value['id']);
            if( $value['attachment'] ){
                $value['attachment'] = json_decode($value['attachment']);
            }
            $value['description'] = strip_tags(trim($value['description']));
        }
        $filenumbers = $model->table(C('DB_PREFIX').'file_number')->where('createuser='.session('user')['id'].' AND state <> "InReview"')->order('createtime DESC')->select();
        foreach( $filenumbers as $key=>&$value ){
            if( $value['attachment'] ){
                $value['attachment'] = json_decode($value['attachment']);
            }
            $value['description'] = strip_tags(trim($value['description']));
        }
        $result['All'] = $filenumbers;
        $result['InReviewIds'] = $InReviewIds;
        return $result;
    }

    // 删除ecn的评审项
    public function removeOriginEcnItem(){
        if( IS_POST ){
            $postData = I('post.');
            $model = new Model();
            if( $postData['ecnType'] == 'file' ){
                $numRow = $model->table(C('DB_PREFIX').'file_number')->save(['id'=>$postData['assoc'], 'state'=>'WaitingReview']);
                $itemRow = $model->table(C('DB_PREFIX').'ecn_review_item')->where('ecn_id = '.$postData['ecn_id'].' AND assoc = '.$postData['assoc'])->delete();
                $itemRow !== false && $numRow !== false ? $this->ajaxReturn(['flag'=>1, 'msg'=>'成功']) : $this->ajaxReturn(['flag'=>0, 'msg'=>'错误']);
            }else{
                // 非文件ecn类型的处理方式...
            }
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

    /**
     * 获取所有职位
     * @return mixed
     */
    private function getAllPositions(){
        $model = new Model();
        $result = $model->table(C('DB_PREFIX').'position')->order('name ASC')->select();
        return $result;
    }




}
