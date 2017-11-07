<?php
/**
 * Created by PhpStorm.
 * User: Fulwin
 * Date: 2017/11/1
 * Time: 11:13
 */
namespace Home\Controller;

use Think\Model;

class TaskController extends AuthController {

    // 任务首页
    public function index(){
        $this->display();
    }

    // 任务编辑页
    public function edit(){
        $this->display();
    }

    // 删除任务
    public function deleteTask(){
        if( IS_POST ){
            $id = I('post.id');
            $row = M('Tasks')->delete($id);
            $row !== false ? $this->ajaxReturn(['flag'=>1, 'msg'=>'删除成功']) : $this->ajaxReturn(['flag'=>0, 'msg'=>'删除失败']);
        }
    }

    // 获取指定任务数据
    public function getSpecifyTaskData(){
        if( IS_POST ){
            require $_SERVER['DOCUMENT_ROOT'].'/RegularTask/task.config.php';
            $taskConfig = json_decode($GLOBALS['taskConfig'], true);
            $id = I('post.id');
            $result = $this->fetchTaskData($id);
            foreach( $taskConfig as $key=>$value ){
                if( $value['value'] == $result['script'] ){
                    $result['is_recipient'] = $value['is_recipient'];
                    $result['is_cc'] = $value['is_cc'];
                }
            }
            $this->ajaxReturn($result);
        }
    }

    // 更改任务状态
    public function changeTaskState(){
        if( IS_POST ){
            $post = I('post.');
            $tasks = D('Tasks');
            $data['id'] = $post['id'];
            $data['state'] = $post['state'] == 'true' ? 'unable' : 'enable';
            $str = $post['state'] == 'true' ? '暂停' : '启用';
            $state = $post['state'] == 'true' ? false : true;
            $row = $tasks->save($data);
            if( $row !== false ) $this->ajaxReturn(['flag'=>1, 'msg'=>$str.'成功', 'state'=>$state]);
        }
    }

    // 格式化任务数据
    public function fetchTaskData($id = ''){
        $tasks = D('Tasks');
        $result = $id ? $tasks->relation(true)->find($id) : $tasks->relation(true)->select();
        if( $id ){
            $result['weeks'] = $result['weeks'] ? explode(',', $result['weeks']) : null;
            $result['days'] = $result['days'] ? explode(',', $result['days']) : null;
            $result['state'] = $result['state'] == 'enable' ? true : false;
            $result['recipients'] = $result['recipients'] ? explode(',', $result['recipients']) : null;
            $result['ccs'] = $result['ccs'] ? explode(',', $result['ccs']) : null;
            return $result;
        }else{
            foreach( $result as $key=>&$value ){
                $value['trigger'] = $this->triggerConversion($value['trigger']);
                $value['recipients'] = $value['recipients'] ? explode(',', $value['recipients']) : null;
                $value['ccs'] = $value['ccs'] ? explode(',', $value['ccs']) : null;
                $value['state'] = $value['state'] == 'enable' ? true : false;
                $value['createtime'] = date('Y-m-d H:i:s', $value['createtime']);
            }
            return $result;
        }
    }

    // 获取首页任务表格数据
    public function indexFetchTaskList(){
        $result = $this->fetchTaskData();
        $this->ajaxReturn($result);
    }

    private function triggerConversion($str){
        switch ($str){
            case 'month':
                $value = '每月';
                break;
            case 'week':
                $value = '每周';
                break;
            case 'day':
                $value = '每天';
                break;
        }
        return $value;
    }

    # 保存任务配置
    public function saveTask(){
        if( IS_POST ){
            $tasks = M('Tasks');
            $data = json_decode(I('post.data', '', false), true);
            if( isset($data['id']) ){
                $data = $this->formatTaskData('insertOrSave', $data);
                $affectRow = $tasks->save($data);
                if( $affectRow !== false ) $this->ajaxReturn(['flag'=>1, 'msg'=>'修改成功']);
            }else{
                $is_exists = $tasks->where(['script'=>$data['script']])->select();
                if( $is_exists ) $this->ajaxReturn(['flag'=>0, 'msg'=>'任务已存在，不能重复创建']);
                $data = $this->formatTaskData('insertOrSave', $data);
                $data['createtime'] = time();
                $data['createuser'] = session('user')['id'];
                $id = $tasks->add($data);
                $id ? $this->ajaxReturn(['flag'=>1, 'msg'=>'创建成功', 'id'=>$id]) : $this->ajaxReturn(['flag'=>0, 'msg'=>'创建失败']);
            }
        }
    }

    private function formatTaskData($type, $data){
        if( $type == 'insertOrSave' ){
            asort($data['days']);
            if( $data['trigger'] == 'month' ){
                $data['days'] = implode(',', $data['days']);
                $data['weeks'] = null;
            }elseif( $data['trigger'] == 'week' ){
                $data['weeks'] = implode(',', $data['weeks']);
                $data['days'] = null;
            }else{
                $data['weeks'] = null;
                $data['days'] = null;
            }
            $data['state']  = $data['state'] ? 'enable' : 'unable';
            $data['recipients'] = $data['recipients'] ? implode(',', $data['recipients']) : null;
            $data['ccs'] = $data['ccs'] ? implode(',', $data['ccs']) : null;
            return $data;
        }else{

        }
    }

    // 新建任务
    public function add(){
        $scriptPath = $_SERVER['DOCUMENT_ROOT'].'/';
        $this->display();
    }

    # 获取执行任务列表
    public function getExecuteTaskList(){
        if( IS_POST ){
            $taskPath = getcwd().'/RegularTask/task.config.php';
            require $taskPath;
            $taskConfig = $GLOBALS['taskConfig'];
            echo $taskConfig;
        }
    }

    # 获取按部门分组的人员列表
    public function getDpmtGroupUsersList(){
        if( IS_POST ){
            $model = new Model();
            $result = $model->table(C('DB_PREFIX').'department')->select();
            foreach( $result as $key=>&$value ){
                $value['users'] = $model->table(C('DB_PREFIX').'user')->where('department = '.$value['id'].' AND state = 1')->select();
            }
            $this->ajaxReturn($result);
        }
    }

    # 星期转换
    private function weekConversion($weekStr){
        $str = 0;
        switch ($weekStr){
            case '星期一':
                $str = 1;
                break;
            case '星期二':
                $str = 2;
                break;
            case '星期三':
                $str = 3;
                break;
            case '星期四':
                $str = 4;
                break;
            case '星期五':
                $str = 5;
                break;
            case '星期六':
                $str = 6;
                break;
            case '星期日':
                $str = 7;
                break;
            default:
                $str = 0;
        }
        return $str;
    }

}