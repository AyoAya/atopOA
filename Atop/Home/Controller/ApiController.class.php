<?php
/**
 * Created by PhpStorm.
 * User: Fulwin
 * Date: 2017/6/16
 * Time: 10:19
 */
namespace Home\Controller;
use Think\Controller;
use Think\Model;

class ApiController extends Controller {


    public function getUsers($query_id = '') {

        $model = new Model();

        if( $query_id == '' ){

            $pagesize = 15;

            $page = I('get.page') > 0 ? I('get.page') : 1;


            $total = $model->table(C('DB_PREFIX').'user')->where('id<>1')->count();

            $result = $model->table(C('DB_PREFIX').'user a,'.C('DB_PREFIX').'department b,'.C('DB_PREFIX').'position c,'.C('DB_PREFIX').'userlevel d,'.C('DB_PREFIX').'user e')
                ->field('a.id,a.account,a.nickname,a.state,b.name department_name,c.name position_name,d.levelname,a.report,e.nickname report_name')
                ->where('a.id<>1 AND a.department=b.id AND a.position=c.id AND a.level=d.id AND a.report=e.id')
                ->order('id ASC')
                ->limit((($pagesize*($page-1))).','.$pagesize)
                ->select();

            foreach($result as $key=>&$value){
                switch( $value['state'] ){
                    case 1:
                        $value['state_name'] = '正常';
                        $value['tag'] = 'success';
                        break;
                    case 2:
                        $value['state_name'] = '禁用';
                        $value['tag'] = 'danger';
                        break;
                    case 3:
                        $value['state_name'] = '离职';
                        $value['tag'] = 'warning';
                        break;
                    default:
                        $value['state_name'] = 'UNKNOW';
                }
            }

            $data['usersdata'] = $result;

            $data['total'] = (int)$total;

            $data['pagesize'] = $pagesize;

            $this->ajaxReturn($data);

        }else{

            $result = $model->table(C('DB_PREFIX').'user a,'.C('DB_PREFIX').'department b,'.C('DB_PREFIX').'position c,'.C('DB_PREFIX').'userlevel d,'.C('DB_PREFIX').'user e')
                ->field('a.id,a.account,a.nickname,a.password,a.email,a.state,b.name department_name,c.name position_name,d.levelname,a.report,e.nickname report_name')
                ->where('a.id<>1 AND a.id='.$query_id.' AND a.department=b.id AND a.position=c.id AND a.level=d.id AND a.report=e.id')
                ->select();

            foreach($result as $key=>&$value){
                switch( $value['state'] ){
                    case 1:
                        $value['state_name'] = '正常';
                        $value['tag'] = 'success';
                        break;
                    case 2:
                        $value['state_name'] = '禁用';
                        $value['tag'] = 'danger';
                        break;
                    case 3:
                        $value['state_name'] = '离职';
                        $value['tag'] = 'warning';
                        break;
                    default:
                        $value['state_name'] = 'UNKNOW';
                }
            }

            $this->ajaxReturn( $result );

        }

    }

    /**
     * 获取添加用户时需要配备的属性
     * @return mixed
     */
    public function getUserAttributes(){

        $userAttributes['departments'] = $this->getAllDepartment();

        $userAttributes['userlevels'] = $this->getAllUserLevel();

        $userAttributes['positions'] = $this->getAllPosition();

        $this->ajaxReturn( $userAttributes );

    }

    /**
     * 获取所有部门数据
     * @return mixed
     */
    private function getAllDepartment(){

        return M('Department')->select();

    }

    /**
     * 获取所有用户级别数据
     * @return mixed
     */
    private function getAllUserLevel(){

        return M('Userlevel')->select();

    }

    /**
     * 获取所有职位数据
     * @return mixed
     */
    private function getAllPosition(){

        return M('Position')->select();

    }


}