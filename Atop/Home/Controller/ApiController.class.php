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

            $result = $model->table(C('DB_PREFIX').'user a,'.C('DB_PREFIX').'department b,'.C('DB_PREFIX').'position c,'.C('DB_PREFIX').'userlevel d')
                ->field('a.id,a.account,a.nickname,a.state,b.name department_name,c.name position_name,d.levelname,a.report')
                ->where('a.id<>1 AND a.department=b.id AND a.position=c.id AND a.level=d.id')
                ->order('id ASC')
                ->limit((($pagesize*($page-1))).','.$pagesize)
                ->select();

            foreach($result as $key=>&$value){

                $value['report_name'] = $model->table(C('DB_PREFIX').'user')->field('nickname')->find( $value['report'] )['nickname'];

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

            $result = $model->table(C('DB_PREFIX').'user a,'.C('DB_PREFIX').'department b,'.C('DB_PREFIX').'position c,'.C('DB_PREFIX').'userlevel d')
                ->field('a.id,a.account,a.nickname,a.password,a.email,a.sex,a.state,b.id dpmt_id,b.name department_name,c.id pst_id,c.name position_name,d.id usl_id,d.levelname,a.report')
                ->where('a.id<>1 AND a.id='.$query_id.' AND a.department=b.id AND a.position=c.id AND a.level=d.id')
                ->select();

            foreach($result as $key=>&$value){

                foreach( $result as $k=>&$v ){

                    if( $v['report'] == $value['id'] ){

                        $value['report_name'] = $v['nickname'];

                    }else{

                        $value['report_name'] = null;

                    }

                }

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

            $result['rules'] = $this->getUserAuthRule($query_id);

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

        $userAttributes['authrule'] = $this->getAllModule();

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

    /**
     * 获取所有模块数据
     * @return mixed
     */
    private function getAllModule(){

        return M('AuthRule')->where('title<>"系统"')->select();

    }

    /**
     * 获取用户的权限组
     * @param $id 用户id
     * @return array
     */
    private function getUserAuthRule( $id ){

        $user_account = M('User')->field('account')->find($id);

        $map['title'] = $user_account['account'];

        $result = M('AuthGroup')->where($map)->find();

        $rules = explode(',', $result['rules']);

        return $rules;

    }


}