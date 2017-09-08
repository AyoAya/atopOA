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
        $count = $model->table(C('DB_PREFIX').'file_number')->count();
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
                          ->field('a.id,a.filenumber,a.version,a.state,a.createtime,b.nickname')
                          ->where('a.createuser = b.id')
                          ->order('lastedit_time DESC,createtime DESC')
                          ->limit($page->firstRow.','.$page->listRows)
                          ->select();
        foreach( $result as $key=>&$value ){
            $value['className'] = $this->fetchClassStyle($value['state']);
            $value['stateName'] = $this->fetchStateName($value['state']);
        }
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
            if( $fileData['attachment'] != '' ){    // 如果附件不为空，则删除以前的附件
                $fileInfo = json_decode($fileData['attachment'], true);
                $this->removeFile($fileInfo['path']);
            }
            $postData['version'] = strtoupper($postData['version']);    // 为保证版本的一致性，一律将版本转换为大写
            $postData['state'] = 'WaitingReview';
            $postData['frequency'] = ((int)$fileData['frequency'] + 1); // 每保存一次修改次数+1
            $postData['lastedit_time'] = time();
            $row = $model->table(C('DB_PREFIX').'file_number')->save($postData);
            $row !== false ? $this->ajaxReturn(['flag'=>1, 'msg'=>'保存成功']) : $this->ajaxReturn(['flag'=>0, 'msg'=>'保存失败']);
        }else{
            if( I('get.id') && is_numeric(I('get.id')) ){
                $model = new Model();
                $result = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'user b')
                                ->field('a.id,a.filenumber,a.state,a.version,a.attachment,a.description,a.createtime,b.nickname')
                                ->where('a.createuser = b.id AND a.id = '.I('get.id'))
                                ->select()[0];
                if( $result['attachment'] != '' ) $result['file'] = json_decode($result['attachment'], true); // 如果存在附件则将json转数组
                $result['className'] = $this->fetchClassStyle($result['state']);
                $result['stateName'] = $this->fetchStateName($result['state']);
                $this->assign('result', $result);
                $beforeVersions = $model->table(C('DB_PREFIX').'file_number a,'.C('DB_PREFIX').'user b')
                                        ->field('a.id,a.filenumber,a.state,a.version,a.attachment,a.description,a.createtime,b.nickname')
                                        ->where('a.createuser = b.id AND filenumber = "'.$result['filenumber'].'" AND version <> "'.$result['version'].'"')
                                        ->order('version DESC')
                                        ->select();
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

    # 编号申请页
    public function apply(){
        if( IS_POST ){
            $model = new Model();
            $result = $model->table(C('DB_PREFIX').'file_rule')->find(I('post.id'));
            $quantity = $model->table(C('DB_PREFIX').'file_number')->where('createtime > '.( time() - 3600 ).' AND type = "'.$result['name'].'"')->count();
            if( (int)$quantity >= 10 ) $this->ajaxReturn(['flag'=>0, 'msg'=>'同一个文件类型1小时内只能申请10次']);
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
        }else{
            $rules = $this->getAllFileRules();
            $this->assign('rules', $rules);
            $model = new Model();
            $count = $model->table(C('DB_PREFIX').'file_number')->count();
            //数据分页
            $page = new Page($count,C('LIMIT_SIZE'));
            $page->setConfig('prev','<span aria-hidden="true">上一页</span>');
            $page->setConfig('next','<span aria-hidden="true">下一页</span>');
            $page->setConfig('first','<span aria-hidden="true">首页</span>');
            $page->setConfig('last','<span aria-hidden="true">尾页</span>');
            if(C('PAGE_STATUS_INFO')){
                $page->setConfig ( 'theme', '<li><a href="javascript:void(0);">当前%NOW_PAGE%/%TOTAL_PAGE%</a></li>  %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%' );
            }
            $result = $model->table(C('DB_PREFIX').'file_number')->where( ['createuser'=>session('user')['id']] )->order('createtime DESC')->limit($page->firstRow.','.$page->listRows)->select();    // 获取所有属于当前登录用户的文件号
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
            case 'BeRejected':      // 驳回
                $className = 'tag tag-danger';
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
            case 'BeRejected':      // 驳回
                $stateName = '驳回';
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
                        ->where('a.createuser=b.id')->order('createtime DESC')->limit($page->firstRow.','.$page->listRows)->select();
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
            if( I('get.id') && is_numeric(I('get.id')) ){
                $model = new Model();
                $result = $model->table(C('DB_PREFIX').'file_rule')->find(I('get.id'));
                $this->assign('result', $result);
                $this->display();
            }
        }
    }

    # 删除文件规则
    public function delRule(){
        if( I('get.id') && is_numeric(I('get.id')) ){
            $model = new Model();
            $row = $model->table(C('DB_PREFIX').'file_rule')->delete(I('get.id'));
            $this->redirect('File/fileRules');  // 删除成功后刷新页面
        }
    }

    # 上传文件
    public function upload(){
        $result = upload( I('post.PATH') );
        $this->ajaxReturn( $result );
    }


}