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

    private $ccs = [];  // 抄送

    # 初始化ECN首页
    public function index(){

        $model = new Model();
        $count = $model->table(C('DB_PREFIX').'ecn')->where('disable = "N" AND state <> "NotPass"')->count();
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
                        ->field('a.id,a.ecn_number,a.state,a.disable,a.change_description,a.createtime,a.lastedit_time,b.nickname')
                        ->where('a.createuser = b.id AND a.disable = "N" AND a.state <> "NotPass"')
                        ->order('a.id DESC')
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
        $model = M('', '', 'MYSQL_CRSAPI');
        if( IS_POST ){
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
                $uniqueReviewUserId = null;
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
            $this->ajaxReturn(['flag'=>1, 'msg'=>'创建成功','ecn'=>$ecnNumber]);
        }else{
            if( I('get.id') && is_numeric(I('get.id')) ){   // 如果当前文件已经存在ECN则直接跳转的对应的ECN详情页面
                $tmpRes = $model->table(C('DB_PREFIX').'ecn a,'.C('DB_PREFIX').'ecn_review_item b')->field('a.id,a.ecn_number')->where('a.id = b.ecn_id AND b.assoc = '.I('get.id'))->group('b.ecn_id')->find();
                if( !empty($tmpRes) ){
                    redirect('/ECN/detail/'.$tmpRes['ecn_number']);
                    exit;
                }
                $quickSelect = $this->getWaitingReviewItem(I('get.id'));
                $this->assign('quickSelect', $quickSelect);
            }
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
            case 'BeRejected':      // 已拒绝
                $className = 'tag tag-danger';
                break;
            case 'NotReview':      // 待评审
                $className = 'tag tag-warning';
                break;
            case 'NotPass':      // 未通过
                $className = 'tag tag-danger';
                break;
            case 'Complete':      // 已完成
                $className = 'tag tag-success';
                break;
            case 'WAIT':      // 待评审
                $className = 'tag tag-warning';
                break;
            case 'PASS':      // 已通过
                $className = 'tag tag-success';
                break;
            case 'REFUSE':      // 已拒绝
                $className = 'tag tag-danger';
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
            case 'BeRejected':      // 已拒绝
                $stateName = '已拒绝';
                break;
            case 'NotReview':      // 待评审
                $stateName = '待评审';
                break;
            case 'NotPass':      // 未通过
                $stateName = '未通过';
                break;
            case 'Complete':      // 已完成
                $stateName = '已发行';
                break;
            case 'WAIT':      // 待评审
                $stateName = '待评审';
                break;
            case 'PASS':      // 已通过
                $stateName = '已通过';
                break;
            case 'REFUSE':      // 已拒绝
                $stateName = '已拒绝';
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
    private function getWaitingReviewItem($id = ''){
        $model = new Model();
        if( $id ){
            $result['file'] = $model->table(C('DB_PREFIX').'file_number')->where(['state'=>'WaitingReview', 'createuser'=>session('user')['id'], 'id'=>$id])->select();   // 获取所有状态为待评审的文件
        }else{
            $result['file'] = $model->table(C('DB_PREFIX').'file_number')->where(['state'=>'WaitingReview', 'createuser'=>session('user')['id']])->select();   // 获取所有状态为待评审的文件
        }
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
                $result = $model->table(C('DB_PREFIX').'file_number')
                                ->where('createuser='.session('user')['id'].' AND state not in ( "InReview","WaitingEdit","Archiving","Recyle" )')
                                ->order('createtime DESC')
                                ->select();
                foreach( $result as $key=>&$value ){    // 如果文件已经生成了ecn则排除掉
                    $tmpRes = $model->table(C('DB_PREFIX').'ecn_review_item a,'.C('DB_PREFIX').'ecn b')->where('a.assoc = '.$value['id'].' AND a.ecn_id = b.id')->select();
                    if( $tmpRes ) unset($result[$key]);
                }
                sort($result);  // unset之后重新排序
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
            $result['ccs'] = $result['cc'] ? $this->getCcsAndRecipients('String', $result['cc']) : null;
            $result['recipients'] = $this->getCcsAndRecipients('Array', $result['recipient']);
            $result['ccs'] = $this->getEcnRuleConfigurationUsers('cc', $result['ccs']);
            $result['recipients'] = $this->getEcnRuleConfigurationUsers('recipients', $result['recipients']);
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
            $EcnModel = D('Ecn');
            $model->startTrans();
            try{
                $postData = I('post.','',false);
                $EcnAllData = $EcnModel->relation(true)->find($postData['id']);
                // 收集ecn表数据
                $ecnData['change_description'] = $postData['change_description'];
                $ecnData['change_reason'] = $postData['change_reason'];
                $ecnData['quote_rule'] = $postData['quote_rule'];
                $ecnData['ecn_type'] = $postData['ecn_type'];
                // 如果保存当前ecn的状态不是拒绝则更新最后修改时间，如果是拒绝则创建新的时间
                if( $EcnAllData['state'] != 'BeRejected' ){
                    $ecnData['lastedit_time'] = time();
                }else{
                    $ecnData['createtime'] = time();
                }
                $ecnData['createuser'] = session('user')['id'];
                if( $EcnAllData['state'] != 'BeRejected' ){
                    // 拼装ecn表数据
                    $ecnData['id'] = $postData['id'];
                    $ecn_row = $model->table(C('DB_PREFIX').'ecn')->save($ecnData);
                    if( $ecn_row === false ) throw new \Exception('保存失败');
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
                }else{  // 当该ecn被拒绝后seq+1记录当前是第seq+1次评审，以往记录可通过加参数seq=X查询以前记录
                    $ecnData['ecn_number'] = $EcnAllData['ecn_number'];
                    $ecnData['seq'] = ($EcnAllData['seq'] + 1);
                    $copyEcnHistoryCoverId = $ecn_row = $model->table(C('DB_PREFIX').'ecn')->add($ecnData); // 拒绝之后增加一条新的记录，ecn_number和前一次保持一致，但seq(评审次数)+1
                    if( !$copyEcnHistoryCoverId ) throw new \Exception('保存失败');
                    $changeEcnStateRow = $model->table(C('DB_PREFIX').'ecn')->save(['id'=>$postData['id'], 'state'=>'NotPass']);    // 将前一次的ecn状态改为未通过
                    if( !$changeEcnStateRow ) throw new \Exception('保存失败');
                    // 拼装ecn_review表的评审组数据
                    foreach( $postData['review_group'] as $key=>&$value ){
                        foreach( $value as $k=>$v ){
                            $ecnReviewData['review_user'] = $v;
                            $ecnReviewData['ecn_id'] = $copyEcnHistoryCoverId;
                            $ecnReviewData['along'] = ($key + 1);
                            $ecnReviewId = $model->table(C('DB_PREFIX').'ecn_review')->add($ecnReviewData);
                            if( !$ecnReviewId ) throw new \Exception('保存失败');
                        }
                    }
                    // 拼装ecn_review表的dcc数据
                    foreach( $postData['dcc_review'] as $key=>&$value ){
                        $dccReviewData['review_user'] = $value;
                        $dccReviewData['ecn_id'] = $copyEcnHistoryCoverId;
                        $dccReviewData['along'] = 0;
                        $dccReviewData['is_dcc'] = 'Y';
                        $dccReviewId = $model->table(C('DB_PREFIX').'ecn_review')->add($dccReviewData);
                        if( !$dccReviewId ) throw new \Exception('保存失败');
                    }
                    // 拼装ecn_review_item表数据
                    foreach( $postData['reviewSelected'] as $key=>&$value ){
                        $ecnReviewItemData['assoc'] = $value['id'];
                        $ecnReviewItemData['ecn_id'] = $copyEcnHistoryCoverId;
                        // 写入评审项数据
                        $ecnReviewItemId = $model->table(C('DB_PREFIX').'ecn_review_item')->add($ecnReviewItemData);
                        if( !$ecnReviewItemId ) throw new \Exception('保存失败');
                    }
                }
            }catch (\Exception $exception){
                $model->rollback();
                $this->ajaxReturn(['flag'=>0, 'msg'=>$exception->getMessage()]);
            }
            $model->commit();
            $this->ajaxReturn(['flag'=>1, 'msg'=>'保存成功', 'ecn_id'=>$copyEcnHistoryCoverId]);
        }else{
            if( I('get.num') ){
                $EcnModel = D('Ecn');
                $history = I('get.history') && is_numeric(I('get.history')) ? true : false;
                if( $history ){ // 如果存在history参数，则说明访问的是历史记录
                    $result = $EcnModel->relation(true)->find(I('get.history'));
                }else{
                    // 永远获取当前ecn号最新的记录
                    $result = $EcnModel->relation(true)->where('ecn_number = "'.I('get.num').'"')->order('seq DESC')->limit(1)->find();
                }
                // 如果存在历史的评审记录则注入模板
                $ecnHistoryReview = $EcnModel->relation('User')->where('ecn_number = "'.I('get.num').'" AND id <> '.$result['id'])->order()->select();
                if( $ecnHistoryReview ){
                    foreach( $ecnHistoryReview as $key=>&$value ){
                        $value['className'] = $this->fetchClassStyle($value['state']);
                        $value['stateName'] = $this->fetchStateName($value['state']);
                    }
                    $this->assign('HistoryReview', $ecnHistoryReview);
                }
                // 将评审人数据提取出来重新排序
                $sortEcnReview = $result['EcnReview'];
                $sort = [];
                foreach ($result['EcnReview'] as $key=>&$value) {
                    $sort[] = $value['along'];
                }
                array_multisort($sort, SORT_ASC, $sortEcnReview);
                $result['EcnReview'] = $sortEcnReview;  // 将排序后的数组重新替换原来的数组
                // 解决dcc排序样式问题，将dcc数组提取出来保存之后删除掉再重新添加进去
                $dccArray = [];
                foreach( $result['EcnReview'] as $key=>&$value){
                    if( $value['is_dcc'] == 'Y' ){
                        array_push($dccArray, $value);
                        unset($result['EcnReview'][$key]);
                    }
                }
                foreach( $dccArray as $key=>&$value ){
                    array_push($result['EcnReview'], $value);
                }
                array_values($result['EcnReview']); // 重新添加之后再排一次序
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
                $MapNotIn = [];
                foreach( $result['Checkeds'] as $key=>&$value ){    // 过滤掉已在评审当中的参与人
                    foreach( $value as $k=>&$v ){
                        array_push($MapNotIn, $v);
                    }
                }
                $MapNotIn = implode(',', $MapNotIn);
                $allUsers = $EcnModel->table(C('DB_PREFIX').'user')->field('id,nickname')->where('id <> 1 AND state = 1 AND id NOT IN ('.$MapNotIn.')')->select();
                $dccUsers = $this->getDccPostAllUsers();
                $this->assign('dccUsers', $dccUsers);
                $this->assign('result', $result);
                $this->assign('rules', $this->getAllEcnRules());
                $this->assign('AllUsers', $allUsers);
                $this->assign('reviewItems', $this->getEcnReviewItems($result['id']));
                // 查看当前登陆用户是否有评审该ecn的权限
                $ecnRV = $EcnModel->table(C('DB_PREFIX').'ecn_review')->where(['ecn_id'=>$result['id'], 'along'=>$result['current_along'], 'review_user'=>session('user')['id']])->find();
                // 当前登陆用户在评审列表中并且没有评审记录才可以评审
                if( $result['current_along'] ){
                    if( in_array(session('user')['id'], $result['Checkeds'][$result['current_along']-1]) && $ecnRV['already_review'] == 'N' && $result['state'] != 'BeRejected' && $result['state'] != 'NotReview' ) $this->assign('reviewAuth', true);
                }else{
                    if( in_array(session('user')['id'], $result['Checkeds']['dcc']) && $ecnRV['already_review'] == 'N' && $result['state'] != 'BeRejected' && $result['state'] != 'NotReview' ) $this->assign('reviewAuth', true);
                }
                //print_r($result);
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
                    if( $changeEcnStateRow === false ) throw new \Exception('发起失败');
                    foreach( $ecnAllData['EcnReviewItem'] as $key=>&$value ){   // 修改文件状态
                        $ecnItemRow = $model->table(C('DB_PREFIX').'file_number')->save(['id'=>$value['assoc'], 'state'=>'InReview']);
                        if( $ecnItemRow === false ) throw new \Exception('发起失败');
                    }
                    $pushUsersData = $this->getAlongOfPushUsers(1, $ecnAllData['EcnReview']);   // 获取收件人数据
                    $ccUsersData = [['name'=>session('user')['nickname'], 'email'=>session('user')['email']]];     // 获取抄送人数据
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

    # 撤回评审
    public function rollback(){
        if( IS_POST ){
            $postData = I('post.');
            try {
                $EcnModel = D('Ecn');
                $ecnAllData = $this->getEcnData($postData['id']);
                if( $ecnAllData['ecn_type']  == 'file' ){   // 如果是文件类型的ecn，撤回之后将文件状态更改为待评审WaitingReview
                    // 将属于当前用户的事项标记为完成
                    $affectedRow = $this->clearMatter($_SERVER['HTTP_HOST'].'/ECN/detail/'.$ecnAllData['ecn_number']);
                    if( !$affectedRow ) throw new \Exception('撤回失败');
                    foreach( $ecnAllData['EcnReviewItem'] as $key=>&$value ){
                        $changeFileStateRow = $EcnModel->table(C('DB_PREFIX').'file_number')->save(['id'=>$value['assoc'], 'state'=>'WaitingReview']);
                        if( $changeFileStateRow === false ) throw new \Exception('撤回失败');
                    }
                }else{
                    // 非文件ecn类型的处理方式...
                }
                // 将ecn表状态更改为NotReview，并且将当前current_along强制为1
                $changeEcnStateRow = $EcnModel->save(['id'=>$postData['id'], 'state'=>'NotReview', 'current_along'=>1]);
                if( $changeEcnStateRow === false ) throw new \Exception('撤回失败');
                // 清除评审记录
                $removeEcnReviewDataRow = $EcnModel->table(C('DB_PREFIX').'ecn_review')->where('ecn_id = '.$postData['id'])->save([
                    'already_review'=>'N',
                    'review_state'=>'WAIT',
                    'remark'=>null,
                    'review_time'=>null
                ]);
                if( $removeEcnReviewDataRow === false ) throw new \Exception('撤回失败');
                $ccUsersData = [['name'=>session('user')['nickname'], 'email'=>session('user')['email']]];     // 获取抄送人数据
                if( $ecnAllData['current_along'] ){ // 判断用户是否是在dcc撤回评审，如果是则通知所有参与评审的人，如果不是则通知当前级及之前级的所有人
                    $this->getCurrentAlongAndPrevAlongData($ecnAllData['current_along'], $ecnAllData['id']);   // 获取收件人数据
                    $sendResult_Rollback = $this->pushEmail('Rollback', $postData['ecn_type'], $this->ccs, $ecnAllData, $ccUsersData);
                }else{
                    $reviewUsers = $EcnModel->table(C('DB_PREFIX').'ecn_review a,'.C('DB_PREFIX').'user b')->field('b.nickname name,b.email')->where('a.review_user=b.id AND a.ecn_id='.$postData['id'])->select();
                    $sendResult_Rollback = $this->pushEmail('Rollback', $postData['ecn_type'], $reviewUsers, $ecnAllData, $ccUsersData);
                }
                $sendResult_Rollback ? $this->ajaxReturn(['flag'=>1, 'msg'=>'撤回成功']) : $this->ajaxReturn(['flag'=>1, 'msg'=>'撤回成功，部分邮件未发送']);
            } catch(\Exception $exception) {
                $this->ajaxReturn(['flag'=>0, 'msg'=>'撤回失败']);
            }
            $this->ajaxReturn(['flag'=>1, 'msg'=>'撤回成功']);
        }
    }

    # 提交评审数据
    public function postReview(){
        if( IS_POST ){
            $postData = I('post.');
            $model = D('Ecn');
            try {
                $ecnAllData = $this->getEcnData($postData['ecn_id']);
                $ccUsersData = $this->getAllCcUsers($ecnAllData['quote_rule']);     // 获取抄送人数据
                if( !$ecnAllData ) throw new \Exception('评审失败');
                if( $ecnAllData['state'] === 'BeRejected' ){
                    throw new \Exception('评审已被拒绝');
                }elseif( $ecnAllData['state'] === 'NotReview' ){
                    throw new \Exception('评审已被撤回');
                }
                $ccUsersData = $this->getAllCcUsers($ecnAllData['quote_rule']);     // 获取抄送人数据
                $model->startTrans();   // 开启事务
                $map['review_user'] = session('user')['id'];
                $map['ecn_id'] = $postData['ecn_id'];
                $map['along'] = $postData['along'];
                $ecnReviewRow = $model->table(C('DB_PREFIX').'ecn_review')->where($map)->save([
                    'already_review' => 'Y',
                    'review_state' => $postData['review_state'],
                    'remark' => $postData['remark'],
                    'review_time' => time()
                ]);
                if( !$ecnReviewRow ) throw new \Exception('评审失败');
                // 如果存在额外的评审人，则在同级插入一条新纪录
                if( isset($postData['extra']) && $postData['extra'] == 'on' ){
                    $ecnReviewAddId = $model->table(C('DB_PREFIX').'ecn_review')->add([
                        'review_user' => $postData['review_user'],
                        'ecn_id' => $postData['ecn_id'],
                        'along' => $postData['along']
                    ]);
                    if( !$ecnReviewAddId ) throw new \Exception('评审失败');
                    $extraUserData = $model->table(C('DB_PREFIX').'user')->field('id,nickname,email')->find($postData['review_user']);
                    $extraUserInfo = ['email'=>$extraUserData['email'], 'name'=>$extraUserData['nickname']];  // 通知额外的评审人
                    array_push($ccUsersData, $extraUserInfo);
                }
                // 如果评审状态为拒绝则修改ecn主表状态为BeRejected
                if( $postData['review_state']  != 'PASS' ){
                    $ecnRow = $model->table(C('DB_PREFIX').'ecn')->save([
                        'id' => $postData['ecn_id'],
                        'state' => 'BeRejected'
                    ]);
                    if( !$ecnRow ) throw new \Exception('评审失败');
                    if( $ecnAllData['ecn_type'] == 'file' ){
                        foreach( $ecnAllData['EcnReviewItem'] as $key=>&$value ){   // 如果评审被拒绝则修改文件状态
                            $changeFileNumberStateRow = $model->table(C('DB_PREFIX').'file_number')->save(['id'=>$value['assoc'], 'state'=>'WaitingReview']);
                            if( $changeFileNumberStateRow === false ) throw new \Exception('评审失败');
                        }
                    }else{
                        // 非文件ecn类型的处理方式...
                    }
                    $pushUsersData = [ ['email'=>$ecnAllData['User']['email'], 'name'=>$ecnAllData['User']['nickname']] ];  // 通知该ecn创建人
                    $this->getCurrentAlongAndPrevAlongData($ecnAllData['current_along'], $postData['ecn_id']);
                    $sendResult = $this->pushEmail('ReviewAction', $postData['ecn_type'], $pushUsersData, $ecnAllData, $this->ccs, ['reviewState'=>$postData['review_state'], 'remark'=>$postData['remark']]);
                    $sendResult ? $this->ajaxReturn(['flag'=>1, 'msg'=>'发起成功']) : $this->ajaxReturn(['flag'=>1, 'msg'=>'发起成功，部分邮件未发送']);
                }else{
                    // 检查当前评审级是否已经走完
                    $compMap['ecn_id'] = $postData['ecn_id'];
                    $compMap['along'] = $postData['along'];
                    $compMap['review_state'] = 'PASS';
                    $currentAlongMap['ecn_id'] = $postData['ecn_id'];
                    $currentAlongMap['along'] = $ecnAllData['current_along'];
                    $currentAlongCount = $model->table(C('DB_PREFIX').'ecn_review')->where($currentAlongMap)->count();
                    $alongCompleteNum = $model->table(C('DB_PREFIX').'ecn_review')->where($compMap)->count();
                    if( $alongCompleteNum == $currentAlongCount ){   // 检查当前级已通过的评审是否等于当前级的总长度，
                        // 如果当前级已经进行完毕，则通知下一级评审人和该ecn创建人
                        // 如果当前级已经处于dcc评审并且审核已全部通过则告知该ecn创建人该ecn评审已经完成
                        if( !$postData['along'] ){   // 如果当前along已经是dcc评审
                            $changeECNStateRow = $model->table(C('DB_PREFIX').'ecn')->save(['id'=>$ecnAllData['id'], 'state'=>'Complete']);
                            if( $changeECNStateRow === false ) throw new \Exception('评审失败');
                            if( $ecnAllData['ecn_type'] == 'file' ){  // 如果当前ecn类型是file并且评审流程已走完，则修改文件状态为已归档
                                foreach( $ecnAllData['EcnReviewItem'] as $key=>&$value ){
                                    $changeFileStateRow = $model->table(C('DB_PREFIX').'file_number')->save(['id'=>$value['assoc'], 'state'=>'Archiving']);
                                    if( $changeFileStateRow === false ) throw new \Exception('评审失败');
                                    // 检查当前文件号是否存在已归档版本
                                    $fileNumberResult = $model->table(C('DB_PREFIX').'file_number')->find($value['assoc']);
                                    $otherVersions = $model->table(C('DB_PREFIX').'file_number')->where('filenumber = "'.$fileNumberResult['filenumber'].'" AND id <> '.$value['assoc'].' AND state = "Archiving"')->select();
                                    if( $otherVersions ){   // 如果存在已归档版本则将已归档版本的状态更改为不可用Unavailable
                                        foreach($otherVersions as $key=>&$value){
                                            $changeOtherVersionStateRow = $model->table(C('DB_PREFIX').'file_number')->save(['id'=>$value['id'], 'state'=>'Unavailable']);
                                            if( $changeOtherVersionStateRow === false ) throw new \Exception('评审失败');
                                        }
                                    }
                                }
                            }else{
                                // 非文件ecn类型的处理方式...
                            }
                            $reviewUsers = $model->table(C('DB_PREFIX').'ecn_review a,'.C('DB_PREFIX').'user b')->field('b.nickname name,b.email')->where('a.review_user=b.id AND a.ecn_id='.$postData['ecn_id'])->select();
                            $pushUsersData = ['email'=>$ecnAllData['User']['email'], 'name'=>$ecnAllData['User']['nickname']];  // 如果当前是dcc评审并且步骤也已经走完，则通知该ecn创建人
                            array_push($reviewUsers, $pushUsersData);
                            array_push($ccUsersData, ['email'=>session('user')['email'], 'name'=>$ecnAllData['User']['nickname']]);  // 将自己添加到抄送人
                            $sendResult_Complete = $this->pushEmail('Complete', $postData['ecn_type'], $reviewUsers, $ecnAllData, $ccUsersData, ['reviewState' => $postData['review_state'], 'remark' => $postData['remark']]);
                        }else{
                            // 判断顺位along是否存在评审人数据，如果存在则给下一级评审人员推送邮件，不存在则给DCC人员发送邮件
                            $nextAlongCount = $model->table(C('DB_PREFIX').'ecn_review')->where([
                                'ecn_id' => $postData['ecn_id'],
                                'along' => ($postData['along'] + 1),
                            ])->count();
                            $alongNumber = $nextAlongCount ? ($postData['along'] + 1) : 0;
                            $ecnRow = $model->table(C('DB_PREFIX').'ecn')->save(['id'=>$postData['ecn_id'], 'current_along'=>$alongNumber]);    // 更新当前ecn顺位
                            if( !$ecnRow ) throw new \Exception('评审失败');
                            $pushUsersData = $this->getAlongOfPushUsers($alongNumber, $ecnAllData['EcnReview']);   // 获取收件人数据
                            $this->getCurrentAlongAndPrevAlongData($postData['along'], $postData['ecn_id']);
                            array_push($this->ccs, ['email'=>$ecnAllData['User']['email'], 'name'=>$ecnAllData['User']['nickname']]);  // 将创建人添加到抄送人
                            $sendResult_Complete = $this->pushEmail('PushDown', $postData['ecn_type'], $pushUsersData, $ecnAllData, $this->ccs, ['reviewState' => $postData['review_state'], 'remark' => $postData['remark']]);
                        }
                        $sendResult_Complete ? $this->ajaxReturn(['flag'=>1, 'msg'=>'发起成功']) : $this->ajaxReturn(['flag'=>1, 'msg'=>'发起成功，部分邮件未发送']);
                    }else{
                        $pushUsersData = [ ['email'=>$ecnAllData['User']['email'], 'name'=>$ecnAllData['User']['nickname']] ];  // 通知该ecn创建人
                        $this->getCurrentAlongAndPrevAlongData($postData['along'], $ecnAllData['id']);
                        array_push($this->ccs, ['email'=>session('user')['email'], 'name'=>$ecnAllData['User']['nickname']]);  // 将自己添加到抄送人
                        if( isset($postData['extra']) && $postData['extra'] == 'on' ) {
                            array_push($this->ccs, $extraUserInfo);
                            $sendResult = $this->pushEmail('ReviewAction', $postData['ecn_type'], $pushUsersData, $ecnAllData, $this->ccs, ['reviewState' => $postData['review_state'], 'remark' => $postData['remark']], $extraUserData);
                        }else{
                            $sendResult = $this->pushEmail('ReviewAction', $postData['ecn_type'], $pushUsersData, $ecnAllData, $this->ccs, ['reviewState' => $postData['review_state'], 'remark' => $postData['remark']]);
                        }
                        $sendResult ? $this->ajaxReturn(['flag'=>1, 'msg'=>'发起成功']) : $this->ajaxReturn(['flag'=>1, 'msg'=>'发起成功，部分邮件未发送']);
                    }
                }
            } catch (Exception $e) {
                $model->rollback();
                $this->ajaxReturn(['flag'=>0, 'msg'=>$e->getMessage()]);
            }
            $model->commit();
            $this->ajaxReturn(['flag'=>1, 'msg'=>'评审成功']);
        }
    }

    /**
     * 拼装邮件数据并发送
     * @param  [string] $type    邮件类型
     * @param  [string] $ecnType    ecn类型
     * @param  [array] $address     收件人
     * @param  [array] $data    邮件内容数据
     * @param  [array]  $cc      抄送人
     * @param  [array]  $reviewData      评审信息
     * @param  [array]  $extra      额外评审人信息
     * @return bool
     */
    private function pushEmail($type, $ecnType, $address, $data, $cc = [], $reviewData, $extra=[]){
        $httphost = $_SERVER['HTTP_HOST'];
        foreach( $data['EcnReviewItem'] as $key=>&$value ){
            if( $data['ecn_type'] == 'file' ){
                $value['item'] = M('FileNumber')->find($value['assoc']);
                $value['item']['attachment'] = json_decode($value['item']['attachment'], true);
            }
        }
        $subject = '[文件管理] '.$data['ecn_number'].' 文件评审';
        $table = '<table width="100%" cellpadding="15" cellspacing="0">';
        $table .= '<thead>';
        $table .= '<tr>';
        $table .= '<th width="20%">文件号</th>';
        $table .= '<th width="10%">版本号</th>';
        $table .= '<th width="20%">附件</th>';
        $table .= '<th width="50%">文件描述</th>';
        $table .= '</tr>';
        $table .= '</thead>';
        $table .= '<tbody>';
        foreach( $data['EcnReviewItem'] as $key=>&$value ){
            $filePath = 'http://'.$httphost.'/'.$value['item']['attachment']['path'];
            $link = 'http://'.$httphost.'/File/detail/'.$value['item']['filenumber'];
            $table .= '<tr>';
            $table .= '<td><a href="'.$link.'" target="_blank">'.$value['item']['filenumber'].'</a></td>';
            $table .= '<td>'.$value['item']['version'].'</td>';
            $table .= '<td><a href="'.$filePath.'" target="_blank">'.$value['item']['attachment']['name'].'</a></td>';
            $table .= '<td>'.$value['item']['description'].'</td>';
            $table .= '</tr>';
        }
        $table .= '</tbody>';
        $table .= '</table>';
        $stateText = $reviewData['reviewState'] === 'PASS' ? '通过' : '拒绝';
        if( count($address) == 1 ){     // 如果数组中只有一个元素
            if (count($address) == count($address, 1)) {    // 判断是否是二维数组
                $dear = 'All';
            } else {
                $dear = $address[0]['name'];
            }
        }else{
            $dear = 'All';
        }
        if( $type == 'InitiateReview' ){    // 发起评审
            if( $ecnType == 'file' ){
                // 插入待办事项
                foreach( $address as $key=>&$value ){
                    $TodolistId = $this->insertNewMatter([
                        'who' => $value['id'],
                        'matter_name' => $subject,
                        'url' => $_SERVER['HTTP_HOST'].'/ECN/detail/'.$data['ecn_number']
                    ]);
                    if( !$TodolistId ) return false;
                }
                $body = '<p class="sm-dear">Dear '.$dear.',</p>';
                $body .= '<p>['.session('user')['nickname'].'] 发起了编号为'.$data['ecn_number'].'的文件评审，请及时处理。</p>';
                $body .= $table;
                $body .= '<p class="ck-lj">详情请查看链接：<a href="http://'.$httphost.'/ECN/detail/'.$data['ecn_number'].'" target="_blank">http://'.$httphost.'/ECN/detail/id/'.$data['id'].'</a></p>';
            }else{
                // 非文件ecn类型的处理方式...
            }
        }elseif( $type == 'PushDown' ){     // 向下推送
            if( $ecnType == 'file' ){
                // 将属于当前用户的事项标记为完成
                $affectedRow = $this->markMatterAsDoneSpecifyUser($_SERVER['HTTP_HOST'].'/ECN/detail/'.$data['ecn_number']);
                if( !$affectedRow ) return false;
                // 插入待办事项
                foreach( $address as $key=>&$value ){
                    $TodolistId = $this->insertNewMatter([
                        'who' => $value['id'],
                        'matter_name' => $subject,
                        'url' => $_SERVER['HTTP_HOST'].'/ECN/detail/'.$data['ecn_number']
                    ]);
                    if( !$TodolistId ) return false;
                }
                $body = '<p class="sm-dear">Dear '.$dear.',</p>';
                $body .= '<p>['.session('user')['nickname'].'] 推送了 ['.$data['User']['nickname'].'] 发起的编号为'.$data['ecn_number'].'的文件评审，请及时处理。</p>';
                $body .= trim($reviewData['remark']) != '' ? '<p class="remark">备注：'.replaceEnterWithBr($reviewData['remark']).'</p>' : '';
                $body .= $table;
                $body .= '<p class="ck-lj">详情请查看链接：<a href="http://'.$httphost.'/ECN/detail/'.$data['ecn_number'].'" target="_blank">http://'.$httphost.'/ECN/detail/id/'.$data['id'].'</a></p>';
            }else{
                // 非文件ecn类型的处理方式...
            }
        }elseif( $type == 'ReviewAction' ){   // 评审
            if( $ecnType == 'file' ){
                if( $reviewData['reviewState'] === 'PASS' ){
                    // 将属于当前用户的事项标记为完成
                    $affectedRow = $this->markMatterAsDoneSpecifyUser($_SERVER['HTTP_HOST'].'/ECN/detail/'.$data['ecn_number']);
                    if( !$affectedRow ) return false;
                }else{
                    // 将属于当前用户的事项标记为完成
                    $affectedRow = $this->markMatterAsDoneSpecifyURL($_SERVER['HTTP_HOST'].'/ECN/detail/'.$data['ecn_number']);
                    if( !$affectedRow ) return false;
                }
                $body = '<p class="sm-dear">Dear '.$dear.',</p>';
                if( empty($extra) ){
                    $body .= '<p>['.session('user')['nickname'].'] '.$stateText.'了您发起的编号为'.$data['ecn_number'].'的文件评审。</p>';
                }else{
                    $body .= '<p>['.session('user')['nickname'].'] '.$stateText.'了您发起的编号为'.$data['ecn_number'].'的文件评审，并将['.$extra['nickname'].']添加为评审人。</p>';
                    // 如果添加了额外评审人，则插入一条属于该评审人的待办事项
                    $TodolistId = $this->insertNewMatter([
                        'who' => $extra['id'],
                        'matter_name' => $subject,
                        'url' => $_SERVER['HTTP_HOST'].'/ECN/detail/'.$data['ecn_number']
                    ]);
                }
                $body .= trim($reviewData['remark']) != '' ? '<p class="remark">备注：'.replaceEnterWithBr($reviewData['remark']).'</p>' : '';
                $body .= $table;
                $body .= '<p class="ck-lj">详情请查看链接：<a href="http://'.$httphost.'/ECN/detail/'.$data['ecn_number'].'" target="_blank">http://'.$httphost.'/ECN/detail/id/'.$data['id'].'</a></p>';
            }else{
                // 非文件ecn类型的处理方式...
            }
        }elseif( $type == 'Complete' ){     // 评审结束
            // 将属于当前用户的事项标记为完成
            $affectedRow = $this->markMatterAsDoneSpecifyUser($_SERVER['HTTP_HOST'].'/ECN/detail/'.$data['ecn_number']);
            if( !$affectedRow ) return false;
            $body = '<p class="sm-dear">Dear '.$dear.',</p>';
            $body .= '<p>['.session('user')['nickname'].'] 通过了 ['.$data['User']['nickname'].'] 发起的编号为'.$data['ecn_number'].'的文件评审，下列文件已归档。</p>';
            $body .= trim($reviewData['remark']) != '' ? '<p class="remark">备注：'.replaceEnterWithBr($reviewData['remark']).'</p>' : '';
            $body .= $table;
            $body .= '<p class="ck-lj">详情请查看链接：<a href="http://'.$httphost.'/ECN/detail/'.$data['ecn_number'].'" target="_blank">http://'.$httphost.'/ECN/detail/id/'.$data['id'].'</a></p>';
        }elseif( $type == 'Rollback' ){     // 撤回
            if( $ecnType == 'file' ){
                $body = '<p class="sm-dear">Dear '.$dear.',</p>';
                $body .= '<p>['.session('user')['nickname'].'] 撤回了编号为'.$data['ecn_number'].'的文件评审。</p>';
                $body .= $table;
                $body .= '<p class="ck-lj">详情请查看链接：<a href="http://'.$httphost.'/ECN/detail/'.$data['ecn_number'].'" target="_blank">http://'.$httphost.'/ECN/detail/id/'.$data['id'].'</a></p>';
            }else{
                // 非文件ecn类型的处理方式...
            }
        }
        $sendResult = send_email($address, '', $subject, $body, $cc);
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
            // 将抄送人添加到数组并过滤掉非正常状态的人员
            $userData = $model->table(C('DB_PREFIX').'user')->field('nickname name,email')->where('post REGEXP "^'.$value.'," OR post REGEXP ",'.$value.'$" OR post REGEXP ",'.$value.'," OR post REGEXP "^'.$value.'$" AND state = 1')->select();
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
                    'id' => $value['user']['id'],
                    'name' => $value['user']['nickname'],
                    'email' => $value['user']['email']
                ]);
            }
        }
        return $pushUsersData;
    }

    /**
     * 获取当前along和上一级along的所有人数据
     * @param  [type] $along 顺序[along]
     * @param  [type] $data  数据
     * @return array
     */
    private function getCurrentAlongAndPrevAlongData($along, $ecnid){
        if( $along ){
            $result = M()->table(C('DB_PREFIX').'ecn_review a,'.C('DB_PREFIX').'user b')->field('b.id,b.nickname name,b.email')->where('a.review_user=b.id AND b.state = 1 AND a.along='.$along.' AND a.ecn_id='.$ecnid)->select();
            if( $result ){
                foreach($result as $key=>&$value){
                    array_push($this->ccs, $value);
                }
            }
            if( $along - 1 ){
                $this->getCurrentAlongAndPrevAlongData($along-1, $ecnid, $this->ccs);
            }
        }
    }

    /**
     * 获取Ecn数据
     * @param  string $id 如果指定id则获取数据该id的所有数据，如果不指定则获取所有数据
     * @return mixed
     */
    private function getEcnData($id = ''){
        $model = D('Ecn');
        if( $id ){
            $ecnAllData = $model->relation(true)->find($id);
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
            ->order('a.name ASC')
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
            if( $result['cc'] ){
                $result['ccs'] = $this->getCcsAndRecipients('String', $result['cc']);
            }else{
                $result['ccs'] = null;
            }
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
            if( !strstr(session('user')['post'], '1788') ) $this->assign('PERMISSION_DENIED',true);
            if( I('get.id') && is_numeric(I('get.id')) ){
                $model = new Model();
                $result = $model->table(C('DB_PREFIX').'ecn_rule')->find(I('get.id'));
                $result['ccs'] = $result['cc'] ? $this->getCcsAndRecipients('String', $result['cc']) : null;
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
            if( !strstr(session('user')['post'], '1788') ) $this->assign('PERMISSION_DENIED',true);
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
        if( !strstr(session('user')['post'], '1788') ) {
            $this->assign('PERMISSION_DENIED',true);
            $this->display();
            exit;
        }
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

    /*public function test(){
        $address = [
            ['name'=>'李平', 'email'=>'253399505@qq.com'],
            ['name'=>'李平', 'email'=>'liping@atoptechnology.com'],
            ['name'=>'李平', 'email'=>'risky-lp@163.com'],
            ['name'=>'蒋明', 'email'=>'jiangming@atoptechnology.com'],
        ];
        echo send_Email($address, '', 'test', 'test', [['name'=>'蒋明','email'=>'vinty_email@163.com']]);
    }*/



}
