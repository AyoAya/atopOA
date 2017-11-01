<?php
/**
 * Created by PhpStorm.
 * User: Fulwin
 * Date: 2017/1/10
 * Time: 11:02
 */
namespace Home\Controller;
use Think\Model;
use Think\Page;

class FileController extends AuthController {

    # 初始化文件管理首页
    public function index(){
        $model = new Model();
        $map['upgrade'] = 'N';
        if( !I('get.state') ){
            $map['state'] = ['neq', 'Recyle'];
        }
        $condition = 'a.createuser = b.id AND a.upgrade="N" AND a.state <> "Recyle"';
        if( I('get.search') && trim(I('get.search')) != '' ){   // 如果存在搜索数据
            $condition .= ' AND a.filenumber LIKE "%'.I('get.search').'%"';
            $map['filenumber'] = ['like', '%'.I('get.search').'%'];
        }
        if( I('get.filenumber') ){
            $condition .= ' AND a.filenumber LIKE "%'.I('get.filenumber').'%"';
            $map['filenumber'] = ['like', '%'.I('get.filenumber').'%'];
        }
        if( I('get.version') ){
            $condition .= ' AND a.version LIKE "%'.strtoupper(I('get.version')).'%"';
            $map['version'] = ['like', '%'.strtoupper(I('get.version')).'%'];
        }
        if( I('get.state') && I('get.state') != 'null' ){
            $condition .= ' AND a.state = "'.I('get.state').'"';
            $map['state'] = I('get.state');
        }
        if( I('get.description') ){
            $condition .= ' AND a.description LIKE "%'.I('get.description').'%"';
            $map['description'] = ['like', '%'.I('get.description').'%'];
        }
        if( I('get.createuser') && I('get.createuser') != 'null' ){
            $condition .= ' AND a.createuser = '.I('get.createuser');
            $map['createuser'] = I('get.createuser');
        }
        if( I('get.start_time') && !I('get.end_time') ){
            $condition .= ' AND a.createtime > '.strtotime(I('get.start_time'));
            $map['createtime'] = ['gt', strtotime(I('get.start_time'))];
        }elseif( !I('get.start_time') && I('get.end_time') ){
            $condition .= ' AND a.createtime < '.strtotime(I('get.end_time'));
            $map['createtime'] = ['lt', strtotime(I('get.end_time'))];
        }elseif( I('get.start_time') && I('get.end_time') ){
            $condition .= ' AND a.createtime > '.strtotime(I('get.start_time')).' AND a.createtime < '.strtotime(I('get.end_time'));
            $map['createtime'] = [ ['gt', strtotime(I('get.start_time'))], ['lt', strtotime(I('get.end_time'))] ];
        }
        $count = $model->table(C('DB_PREFIX').'file_number')->where($map)->count();
        //数据分页
        $page = new Page($count,C('LIMIT_SIZE'));
        $page->setConfig('prev','<span aria-hidden="true">上一页</span>');
        $page->setConfig('next','<span aria-hidden="true">下一页</span>');
        $page->setConfig('first','<span aria-hidden="true">首页</span>');
        $page->setConfig('last','<span aria-hidden="true">尾页</span>');
        if(C('PAGE_STATUS_INFO')){
            $page->setConfig ( 'theme', '<li><a href="javascript:void(0);">当前%NOW_PAGE%/%TOTAL_PAGE%</a></li>  %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%' );
        }
        $result = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'user b')
                          ->field('a.id,a.filenumber,a.version,a.state,a.createtime,a.createuser,a.description,b.nickname')
                          ->where($condition)
                          ->order('createtime DESC')
                          ->limit($page->firstRow.','.$page->listRows)
                          ->select();
        foreach( $result as $key=>&$value ){
            $value['className'] = $this->fetchClassStyle($value['state']);
            $value['stateName'] = $this->fetchStateName($value['state']);
        }
        // 获取分组创建人数据作用筛选
        $userGroup = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'user b')->field('b.id,b.nickname name')->where('a.createuser=b.id')->group('a.createuser')->select();
        $this->assign('userGroup', $userGroup);
        $pageShow = $page->show();
        $this->assign('pageShow',$pageShow);
        $this->assign('result', $result);
        $this->display();
    }

    # 文件详情页
    public function detail(){
        if( IS_POST ){
            $model = new Model();
            $postData = I('post.','',false);
            $fileData = $model->table(C('DB_PREFIX').'file_number')->find($postData['id']);
            if( $fileData['state'] == 'Archiving' ){    // 如果当前状态为归档，则说明该操作为升版
                $model->startTrans();
                try{
                    $changeOldUpgrade['id'] = $postData['id'];
                    $changeOldUpgrade['upgrade'] = 'Y';
                    //$changeOldUpgrade['state'] = 'Unavailable'; // 将状态改为不可用
                    $changeRow = $model->table(C('DB_PREFIX').'file_number')->save($changeOldUpgrade);  // 将以往版本的升版状态改为Y表示该文件已经升版过了
                    if( $changeRow === false ) throw new \Exception('升版失败');
                    $OldVersion = (float)substr($fileData['version'], 1);  // 获取之前版本准备对比
                    $NewVersion = (float)substr($postData['version'], 1);   // 最新版本
                    if( $NewVersion < $OldVersion ) throw new \Exception('升版的版本号应当比之前高');
                    $upgradeData['type'] = $fileData['type'];
                    $upgradeData['filenumber'] = $fileData['filenumber'];
                    $upgradeData['attachment'] = $postData['attachment'];
                    $upgradeData['description'] = trim($postData['description']);
                    $upgradeData['state'] = 'WaitingReview';
                    $upgradeData['version'] = strtoupper($postData['version']);    // 为保证版本的一致性，一律将版本转换为大写
                    $upgradeData['createuser'] = session('user')['id'];
                    $upgradeData['createtime'] = time();
                    $addId = $model->table(C('DB_PREFIX').'file_number')->add($upgradeData);
                    if( !$addId ) throw new \Exception('升版失败');
                } catch (\Exception $exception) {
                    $model->rollback();
                    $this->ajaxReturn(['flag'=>0, 'msg'=>$exception->getMessage()]);
                }
                $this->ajaxReturn(['flag'=>1, 'msg'=>'升版成功']);
            }else{
                if( isset($postData['attachment']) ){   // 如果上传了新文件则删除以前的附件
                    if( $fileData['attachment'] != '' && $fileData['state'] != 'Archiving' ){    // 如果附件不为空并且状态不为已归档，则删除以前的附件
                        $fileInfo = json_decode($fileData['attachment'], true);
                        $this->removeFile($fileInfo['path']);
                    }
                }
                $postData['version'] = strtoupper($postData['version']);    // 为保证版本的一致性，一律将版本转换为大写
                $postData['state'] = 'WaitingReview';
                $postData['frequency'] = ((int)$fileData['frequency'] + 1); // 每保存一次修改次数+1
                $postData['lastedit_time'] = time();
                $row = $model->table(C('DB_PREFIX').'file_number')->save($postData);
                $row !== false ? $this->ajaxReturn(['flag'=>1, 'msg'=>'保存成功']) : $this->ajaxReturn(['flag'=>0, 'msg'=>'保存失败']);
            }
        }else{
            if( I('get.filenumber') ){
                $model = new Model();
                if( I('get.id') ){
                    $result = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'user b')
                        ->field('a.id,a.filenumber,a.state,a.version,a.attachment,a.description,a.upgrade,a.createtime,b.nickname,b.id user_id')
                        ->where('a.createuser = b.id AND a.id = '.I('get.id'))
                        ->select()[0];
                }else{
                    $result = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'user b')
                        ->field('a.id,a.filenumber,a.state,a.version,a.attachment,a.description,a.upgrade,a.createtime,b.nickname,b.id user_id')
                        ->where('a.createuser = b.id AND a.filenumber = "'.I('get.filenumber').'"')
                        ->order('a.version DESC,id DESC')
                        ->find();
                }
                if( $result['state'] == 'Recyle' ) $this->error('文件号已回收');  // 已回收的文件号页面不允许访问
                if( $result['attachment'] != '' ) $result['file'] = json_decode($result['attachment'], true); // 如果存在附件则将json转数组
                $result['className'] = $this->fetchClassStyle($result['state']);
                $result['stateName'] = $this->fetchStateName($result['state']);
                $this->assign('result', $result);
                $beforeVersions = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'user b')
                                        ->field('a.id,a.filenumber,a.state,a.version,a.attachment,a.description,a.createtime,b.nickname')
                                        ->where('a.createuser = b.id AND filenumber = "'.$result['filenumber'].'" AND version <> "'.$result['version'].'"')
                                        ->order('version DESC')
                                        ->select();
                // 获取当前文件关联的ecn
                $ecnAssocData = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'ecn b,'.C('DB_PREFIX').'ecn_review_item c')
                                  ->field('b.id,b.ecn_number,b.state')
                                  ->where('a.id=c.assoc AND b.id=c.ecn_id AND a.id='.$result['id'])
                                  ->order('c.ecn_id DESC')
                                  ->find();
                // 如果不存在关联的ecn则说明该文件并没有被添加到ecn评审
                if( $ecnAssocData ){
                    $this->assign('ecnAssoc', $ecnAssocData);
                }
                foreach( $beforeVersions as $key=>&$value ){
                    if( !empty($value['attachment']) ){
                        $value['attachment'] = json_decode($value['attachment'], true);
                    }
                }
                $this->assign('beforeVersions', $beforeVersions);
                $this->display();
            }
        }
    }

    # 删除文件
    private function removeFile($path){
        if (preg_match('/[\x7f-\xff]/', $path)) {
            $filepath = getcwd().substr(iconv('UTF-8', 'GB2312', $path),1);
            if( file_exists($filepath) ){
                unlink($filepath);
            }
        }else{
            $filepath = getcwd().substr($path, 1);
            if( file_exists($filepath) ){
                unlink($filepath);
            }
        }
    }

    # 获取指定文件类型的描述信息
    public function getFileTypeDescription(){
        if( IS_POST ){
            $typeId = I('post.fileTypeId');
            $result = M('FileRule')->field('desc')->find($typeId);
            $result ? $this->ajaxReturn(['flag'=>1, 'msg'=>$result['desc']]) : $this->ajaxReturn(['flag'=>0, 'msg'=>'描述信息获取失败']);
        }
    }

    # 编号申请页
    public function apply(){
        if( IS_POST ){
            $model = new Model();
            $result = $model->table(C('DB_PREFIX').'file_rule')->find(I('post.id'));
            if( I('post.apply') == 'system' ){  // 如果是系统生成
                $quantity = $model->table(C('DB_PREFIX').'file_number')->where('createtime > '.( time() - 3600 ).' AND type = "'.$result['name'].'"')->count();
                if( (int)$quantity >= 10 ) $this->ajaxReturn(['flag'=>0, 'msg'=>'同一个文件类型1小时内只能申请10次']);
                // 检查是否存在已回收的记录
                $recyleData = $model->table(C('DB_PREFIX').'file_number')->where('type = "'.$result['name'].'" AND state = "Recyle"')->order('id ASC')->limit(1)->select();
                if( $recyleData ){  // 如果存在已回收的记录则优先使用
                    $reuseRow = $model->table(C('DB_PREFIX').'file_number')->save([
                        'id'=>$recyleData[0]['id'],
                        'state'=>'WaitingEdit',
                        'createtime'=>time(),
                        'createuser'=>session('user')['id']
                    ]);
                    $reuseRow !== false ? $this->ajaxReturn(['flag'=>1, 'msg'=>'申请成功']) : $this->ajaxReturn(['flag'=>0, 'msg'=>'申请失败']);
                }else{
                    $model->startTrans();
                    $current = $result['current'] + 1;
                    $row = $model->table(C('DB_PREFIX').'file_rule')->save(['id'=>$result['id'], 'current'=>$current]); // 当前规则current + 1
                    $increment = ( ( $result['length'] - strlen($result['name']) ) - strlen($current) );    // 计算出需要填充的长度
                    if( $increment <= 0 ) $this->ajaxReturn(['flag'=>0, 'msg'=>'无法申请文件号，请检查文件类型的长度是否合法']);
                    $filenumber = '';
                    $filenumber = $result['name'].str_pad($filenumber, $increment, '0', STR_PAD_RIGHT).$current;    // 生成文件编号
                    $filenumberData['type'] = $result['name'];
                    $filenumberData['filenumber'] = $filenumber;
                    $filenumberData['createtime'] = time();
                    $filenumberData['createuser'] = session('user')['id'];
                    $id = $model->table(C('DB_PREFIX').'file_number')->add($filenumberData);    // 将申请的文件编号保存
                    $row && $id ? $this->ajaxReturn(['flag'=>1, 'msg'=>'申请成功']) : $this->ajaxReturn(['flag'=>0, 'msg'=>'申请失败']);
                }
            }else{  // 如果是手动录入
                $tempRes = $model->table(C('DB_PREFIX').'file_number')->where('filenumber = "'.I('post.filenumber').'"')->select();
                $filenumberData['type'] = 'ISO';
                $filenumberData['filenumber'] = I('post.filenumber');
                $filenumberData['createtime'] = time();
                $filenumberData['createuser'] = session('user')['id'];
                if( $tempRes ){
                    if( $tempRes[0]['state'] != 'Recyle' ){
                        $this->ajaxReturn(['flag'=>0, 'msg'=>'编号已存在']);
                    }else{
                        $filenumberData['id'] = $tempRes[0]['id'];
                        $filenumberData['state'] = 'WaitingEdit';
                        $id = $model->table(C('DB_PREFIX').'file_number')->save($filenumberData);    // 将申请的文件编号保存
                    }
                }else{
                    $id = $model->table(C('DB_PREFIX').'file_number')->add($filenumberData);    // 将申请的文件编号保存
                }
                $id ? $this->ajaxReturn(['flag'=>1, 'msg'=>'申请成功']) : $this->ajaxReturn(['flag'=>0, 'msg'=>'申请失败']);
            }
        }else{
            $rules = $this->getAllFileRules();
            $this->assign('rules', $rules);
            $model = new Model();
            $map['createuser'] = session('user')['id'];
            $map['upgrade'] = 'N';
            $map['state'] = ['neq', 'Recyle'];
            $count = $model->table(C('DB_PREFIX').'file_number')->where($map)->count();
            //数据分页
            $page = new Page($count,C('LIMIT_SIZE'));
            $page->setConfig('prev','<span aria-hidden="true">上一页</span>');
            $page->setConfig('next','<span aria-hidden="true">下一页</span>');
            $page->setConfig('first','<span aria-hidden="true">首页</span>');
            $page->setConfig('last','<span aria-hidden="true">尾页</span>');
            if(C('PAGE_STATUS_INFO')){
                $page->setConfig ( 'theme', '<li><a href="javascript:void(0);">当前%NOW_PAGE%/%TOTAL_PAGE%</a></li>  %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%' );
            }
            $result = $model->table(C('DB_PREFIX').'file_number')->where($map)->order('createtime DESC')->limit($page->firstRow.','.$page->listRows)->select();    // 获取所有属于当前登录用户的文件号
            foreach( $result as $key=>&$value ){
                $value['className'] = $this->fetchClassStyle($value['state']);
                $value['stateName'] = $this->fetchStateName($value['state']);
            }
            $pageShow = $page->show();
            $this->assign('pageShow',$pageShow);
            $this->assign('result', $result);
            $this->display();
        }
    }

    # 删除文件号
    public function delete(){
        if( I('get.id') && is_numeric(I('get.id')) ){
            $model = new Model();
            $row = $model->table(C('DB_PREFIX').'file_number')->delete(I('get.id'));
            $this->redirect('File/index');
        }
    }

    # 根据状态获取class样式
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
            case 'Archiving':        // 已归档
                $className = 'tag tag-success';
                break;
            case 'Unavailable':        // 不可用
                $className = 'tag tag-danger';
                break;
            case 'BeRejected':      // 驳回
                $className = 'tag tag-danger';
                break;
            case 'Recyle':      // 已回收
                $className = 'tag tag-pay';
                break;
            default:                // 如果都不是则直接返回错误的class样式
                $className = 'tag tag-danger';
        }
        return $className;
    }

    # 根据状态获取状态名称
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
            case 'Archiving':        // 已归档
                $stateName = '生　效';
                break;
            case 'Unavailable':        // 不可用
                $stateName = '不可用';
                break;
            case 'BeRejected':      // 驳回
                $stateName = '驳回';
                break;
            case 'Recyle':      // 已回收
                $stateName = '已回收';
                break;
            default:                // 如果都不是则直接返回错误的class样式
                $stateName = 'UNKNOW';
        }
        return $stateName;
    }

    # 获取所有文件规则
    private function getAllFileRules(){
        $model = new Model();
        $result = $model->table(C('DB_PREFIX').'file_rule')->field('id,name')->order('name ASC')->select();
        return $result;
    }

    # 文件规则
    public function fileRules(){
        $model = new Model();
        $count = $model->table(C('DB_PREFIX').'file_rule')->count();
        //数据分页
        $page = new Page($count,C('LIMIT_SIZE'));
        $page->setConfig('prev','<span aria-hidden="true">上一页</span>');
        $page->setConfig('next','<span aria-hidden="true">下一页</span>');
        $page->setConfig('first','<span aria-hidden="true">首页</span>');
        $page->setConfig('last','<span aria-hidden="true">尾页</span>');
        if(C('PAGE_STATUS_INFO')){
            $page->setConfig ( 'theme', '<li><a href="javascript:void(0);">当前%NOW_PAGE%/%TOTAL_PAGE%</a></li>  %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%' );
        }
        $result = $model->table(C('DB_PREFIX').'file_rule a,'.C('DB_PREFIX').'user b')
                        ->field('a.id,a.name,a.length,a.current,a.desc,a.createtime,b.nickname')
                        ->where('a.createuser=b.id')->order('createtime DESC')
                        ->limit($page->firstRow.','.$page->listRows)
                        ->order('a.name ASC')
                        ->select();
        $pageShow = $page->show();
        $this->assign('pageShow',$pageShow);
        $this->assign('result', $result);
        $this->display();

    }

    # 添加文件规则
    public function addRule(){
        if( IS_POST ){
            $postData = I('post.');
            $postData['createtime'] = time();
            $postData['createuser'] = session('user')['id'];
            $model = new Model();
            $id = $model->table(C('DB_PREFIX').'file_rule')->add($postData);
            $id ? $this->ajaxReturn(['flag'=>1, 'msg'=>'创建成功']) : $this->ajaxReturn(['flag'=>0, 'msg'=>'创建失败']);
        }else{
            if( !strstr(session('user')['post'], '1788') ) $this->assign('PERMISSION_DENIED',true);
            $this->display();
        }
    }

    # 编辑文件规则
    public function editRule(){
        if( IS_POST ){
            $postData = I('post.');
            $model = new Model();
            $row = $model->table(C('DB_PREFIX').'file_rule')->save($postData);
            $row !== false ? $this->ajaxReturn(['flag'=>1, 'msg'=>'修改成功']) : $this->ajaxReturn(['flag'=>0, 'msg'=>'修改失败']);
        }else{
            if( !strstr(session('user')['post'], '1788') ) $this->assign('PERMISSION_DENIED',true);
            if( I('get.id') && is_numeric(I('get.id')) ){
                $model = new Model();
                $result = $model->table(C('DB_PREFIX').'file_rule')->find(I('get.id'));
                $this->assign('result', $result);
                $this->display();
            }
        }
    }

    # 回收文件号
    public function recyleFileNumber(){
        if( IS_POST ){
            $id = I('post.fileid');

            $row = M('FileNumber')->save([
                'id'=>$id,
                'state'=>'Recyle',
                'version'=>null,
                'attachment'=>null,
                'description'=>null,
                'frequency'=>0,
                'upgrade'=>'N',
                'lastedit_time'=>null,
            ]);
            $row !== false ? $this->ajaxReturn(['flag'=>1, 'msg'=>'回收成功']) : $this->ajaxReturn(['flag'=>0, 'msg'=>'回收失败']);
        }
    }

    # 删除文件规则
    public function delRule(){
        if( !strstr(session('user')['post'], '1788') ) {
            $this->assign('PERMISSION_DENIED',true);
            $this->display();
            exit;
        };
        if( I('get.id') && is_numeric(I('get.id')) ){
            $model = new Model();
            $row = $model->table(C('DB_PREFIX').'file_rule')->delete(I('get.id'));
            $this->redirect('File/fileRules');  // 删除成功后刷新页面
        }
    }

    # 上传文件
    public function upload(){
        $result = upload( I('post.PATH') , time().uniqid()); // 防止上传文件同名覆盖，生成时间戳加uniqid为二级目录
        $this->ajaxReturn( $result );
    }


}