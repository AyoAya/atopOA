<?php
namespace Home\Controller;
/**
 * 文档中心
 * @author Fulwin
 * 2016-10-12
 */
class DCCController extends AuthController {

    //初始化页面
    public function index(){
        $user = M('User');
        $nowuser_department = $user->find(session('user')['id'])['department'];
        if( I('get.department') && !empty(I('get.department')) ){
            $userDepartment = I('get.department');
        }else{
            $userDepartment = $nowuser_department;
        }
        switch($userDepartment){
            case 1:
                $department = 'development';
                $folders_public = read_all_dir('./Uploads/dcc/development/public');
                $folders_private = read_all_dir('./Uploads/dcc/development/private');
                break;
            case 2:
                $department = 'production';
                $folders_public = read_all_dir('./Uploads/dcc/production/public');
                $folders_private = read_all_dir('./Uploads/dcc/production/private');
                break;
            case 3:
                $department = 'quality';
                $folders_public = read_all_dir('./Uploads/dcc/quality/public');
                $folders_private = read_all_dir('./Uploads/dcc/quality/private');
                $folders_iso = read_all_dir('./Uploads/dcc/quality/ISO9001');
                break;
            case 4:
                $department = 'sales';
                $folders_public = read_all_dir('./Uploads/dcc/sales/public');
                $folders_private = read_all_dir('./Uploads/dcc/sales/private');
                break;
            case 5:
                $department = 'plan';
                $folders_public = read_all_dir('./Uploads/dcc/plan/public');
                $folders_private = read_all_dir('./Uploads/dcc/plan/private');
                break;
            case 6:
                $department = 'administration';
                $folders_public = read_all_dir('./Uploads/dcc/administration/public');
                $folders_private = read_all_dir('./Uploads/dcc/administration/private');
                break;
            case 9:
                $department = 'market';
                $folders_public = read_all_dir('./Uploads/dcc/market/public');
                $folders_private = read_all_dir('./Uploads/dcc/market/private');
                break;
        }

        $folders['public'] = $folders_public;

        if( I('get.department') && !empty(I('get.department')) ){
            if( $nowuser_department != I('get.department') && $nowuser_department != 0 ){
                $folders['private'] = '您没有权限访问该部门内部文件';
            }else{
                $folders['private'] = $folders_private;
            }
        }else{
            $folders['private'] = $folders_private;
        }

        $this->assign('userDepartment',$userDepartment);
        $this->assign('folders',$folders);
        $this->display();
    }

    //切换部门时默认访问公开的目录下面的文件
    public function changeDepartment(){
        if(!IS_POST) return;
        if( I('post.departmentid') && !empty(I('post.departmentid')) ){
            $userDepartment = I('post.departmentid');
            switch($userDepartment){
                case 1:
                    $department = 'development';
                    $folders = read_all_dir('./Uploads/dcc/development/public');
                    break;
                case 2:
                    $department = 'production';
                    $folders = read_all_dir('./Uploads/dcc/production/public');
                    break;
                case 3:
                    $department = 'quality';
                    $folders = read_all_dir('./Uploads/dcc/quality/public');
                    break;
                case 4:
                    $department = 'sales';
                    $folders = read_all_dir('./Uploads/dcc/sales/public');
                    break;
                case 5:
                    $department = 'plan';
                    $folders = read_all_dir('./Uploads/dcc/plan/public');
                    break;
                case 9:
                    $department = 'market';
                    $folders = read_all_dir('./Uploads/dcc/market/public');
                    break;
            }
            $this->ajaxReturn($folders);exit;
        }
    }

    //查看指定部门内部目录
    public function privateFolder(){
        if(!IS_POST) return;
        $user = M('User');
        $userDepartment = $user->find(session('user')['id'])['department'];
        //echo $userDepartment.' | '.I('post.department');
        if( I('post.department') && !empty(I('post.department')) ){
            if( I('post.folder')=='private' && $userDepartment != I('post.department') && $userDepartment != 0){
                echo -1;exit;
            }else{
                $departmentID = I('post.department');
                switch($departmentID){
                    case 1:
                        $department = 'development';
                        $folders = read_all_dir('./Uploads/dcc/development/'.I('post.folder'));
                        break;
                    case 2:
                        $department = 'production';
                        $folders = read_all_dir('./Uploads/dcc/production/'.I('post.folder'));
                        break;
                    case 3:
                        $department = 'quality';
                        $folders = read_all_dir('./Uploads/dcc/quality/'.I('post.folder'));
                        break;
                    case 4:
                        $department = 'sales';
                        $folders = read_all_dir('./Uploads/dcc/sales/'.I('post.folder'));
                        break;
                    case 5:
                        $department = 'plan';
                        $folders = read_all_dir('./Uploads/dcc/plan/'.I('post.folder'));
                        break;
                    case 9:
                        $department = 'market';
                        $folders = read_all_dir('./Uploads/dcc/market/'.I('post.folder'));
                        break;
                }
                $this->ajaxReturn($folders);exit;
            }
        }
    }


}
