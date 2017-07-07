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
use Think\Upload;

class ApiController extends Controller
{

    /**
     * 登录后台
     */
    public function login()
    {
        $model = new Model();
        $data = I('get.data', '', false);
        $map['account'] = $data['account'];
        $map['password'] = sha1($data['password']);
        $UserInfoData = $model->table(C('DB_PREFIX') . 'user')->field('id,account,nickname,email,createtime,lasttime,lastip,face,theme,department,position,report,level,state,sex')->where($map)->find();
        if ($UserInfoData) {
            $this->ajaxReturn(['flag' => 1, 'userdata' => $UserInfoData]);
        } else {
            $this->ajaxReturn(['flag' => 0, 'msg' => '账号或密码错误!']);
        }
    }

    /**
     * 获取所有用户
     * @param string $query_id
     */
    public function getUsers($query_id = '')
    {
        $model = new Model();
        if ($query_id == '') {
            $pagesize = 15;
            $page = I('get.page') > 0 ? I('get.page') : 1;
            $total = $model->table(C('DB_PREFIX') . 'user')->where('id<>1')->count();
            $result = $model->table(C('DB_PREFIX') . 'user a,' . C('DB_PREFIX') . 'department b,' . C('DB_PREFIX') . 'position c,' . C('DB_PREFIX') . 'userlevel d')
                ->field('a.id,a.account,a.nickname,a.state,b.name department_name,c.name position_name,d.levelname,a.report')
                ->where('a.id<>1 AND a.department=b.id AND a.position=c.id AND a.level=d.id')
                ->order('id ASC')
                ->limit((($pagesize * ($page - 1))) . ',' . $pagesize)
                ->select();
            foreach ($result as $key => &$value) {
                $value['report_name'] = $model->table(C('DB_PREFIX') . 'user')->field('nickname')->find($value['report'])['nickname'];
                switch ($value['state']) {
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
        } else {
            $result = $model->table(C('DB_PREFIX') . 'user a,' . C('DB_PREFIX') . 'department b,' . C('DB_PREFIX') . 'position c,' . C('DB_PREFIX') . 'userlevel d')
                ->field('a.id,a.account,a.nickname,a.password,a.email,a.sex,a.state,b.id dpmt_id,b.name department_name,c.id pst_id,c.name position_name,d.id usl_id,d.levelname,a.report')
                ->where('a.id<>1 AND a.id=' . $query_id . ' AND a.department=b.id AND a.position=c.id AND a.level=d.id')
                ->select();
            foreach ($result as $key => &$value) {
                foreach ($result as $k => &$v) {
                    if ($v['report'] == $value['id']) {
                        $value['report_name'] = $v['nickname'];
                    } else {
                        $value['report_name'] = null;
                    }
                }
                switch ($value['state']) {
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
            $this->ajaxReturn($result);
        }
    }

    /**
     * 获取添加用户时需要配备的属性
     * @return mixed
     */
    public function getUserAttributes()
    {
        $userAttributes['departments'] = $this->getAllDepartment();
        $userAttributes['userlevels'] = $this->getAllUserLevel();
        $userAttributes['positions'] = $this->getAllPosition();
        $userAttributes['authrule'] = $this->getAllModule();
        $this->ajaxReturn($userAttributes);
    }


    /**
     * 获取产品属性及默认数据
     */
    public function getProductAttributeData()
    {
        $this->ajaxReturn($this->getProductAttribute());
    }

    /**
     * 获取所有部门数据
     * @return mixed
     */
    private function getAllDepartment()
    {
        return M('Department')->select();
    }

    /**
     * 获取所有用户级别数据
     * @return mixed
     */
    private function getAllUserLevel()
    {
        return M('Userlevel')->select();
    }

    /**
     * 获取所有职位数据
     * @return mixed
     */
    private function getAllPosition()
    {
        return M('Position')->select();
    }

    /**
     * 获取所有模块数据
     * @return mixed
     */
    private function getAllModule()
    {
        return M('AuthRule')->where('title<>"系统"')->select();
    }

    /**
     * 获取用户的权限组
     * @param $id 用户id
     * @return array
     */
    private function getUserAuthRule($id)
    {
        $user_account = M('User')->field('account')->find($id);
        $map['title'] = $user_account['account'];
        $result = M('AuthGroup')->where($map)->find();
        $rules = explode(',', $result['rules']);
        return $rules;
    }

    /**
     * 获取产品属性
     * @return mixed
     */
    private function getProductAttribute($condition = [])
    {
        if (is_array($condition) && !empty($condition)) {
            $pro_rel_ships = M('Productrelationships');
            $productResult['types'] = $pro_rel_ships->field('type')->where($condition)->order('type ASC')->group('type')->select();   //类型
            $productResult['wavelengths'] = $pro_rel_ships->field('wavelength,pn')->where($condition)->order('wavelength ASC')->group('wavelength')->select();   //波长
            $productResult['reachs'] = $pro_rel_ships->field('reach')->where($condition)->order('reach ASC')->group('reach')->select();   //距离
            $productResult['connectors'] = $pro_rel_ships->field('connector')->where($condition)->order('connector ASC')->group('connector')->select();   //接口
            $productResult['casetemps'] = $pro_rel_ships->field('casetemp')->where($condition)->order('casetemp ASC')->group('casetemp')->select();   //环境
            $productResult['defaultData'] = $pro_rel_ships->field('pn')->where($condition)->order('pn ASC')->select();   //筛选数据
            //针对环境类型显示不同的名称
            foreach ($productResult['casetemps'] as $key => &$value) {
                if ($value['casetemp'] == 'C') {
                    $value['casetemp_as_name'] = 'C档（0-70°）';
                } else {
                    $value['casetemp_as_name'] = 'I 档（-40°-85°）';
                }
            }
            //针对于DWDM系列产品显示不同的名称
            if (isset($condition['type']) && !empty($condition['type'])) {
                if ($condition['type'] == 'XFP DWDM' || $condition['type'] == 'SFP DWDM') {
                    foreach ($productResult['wavelengths'] as $key => &$value) {
                        $value['wavelength_as_name'] = 'CH' . substr($value['pn'], 4, 2);
                    }
                } elseif ($condition['type'] == 'SFP+ DWDM') {
                    foreach ($productResult['wavelengths'] as $key => &$value) {
                        $value['wavelength_as_name'] = 'CH' . substr($value['pn'], 5, 2);
                    }
                } else {
                    foreach ($productResult['wavelengths'] as $key => &$value) {
                        $value['wavelength_as_name'] = $value['wavelength'];
                    }
                }
            }
            return $productResult;
        } else {
            $pro_rel_ships = M('Productrelationships');
            $productResult['types'] = $pro_rel_ships->field('type')->order('type ASC')->group('type')->select();   //类型
            $productResult['wavelengths'] = $pro_rel_ships->field('wavelength')->where('wavelength not like "%.%"')->order('wavelength ASC')->group('wavelength')->select();   //波长
            $productResult['reachs'] = $pro_rel_ships->field('reach')->order('reach ASC')->group('reach')->select();   //距离
            $productResult['connectors'] = $pro_rel_ships->field('connector')->order('connector ASC')->group('connector')->select();   //接口
            $productResult['casetemps'] = $pro_rel_ships->field('casetemp')->order('casetemp ASC')->group('casetemp')->select();   //环境
            foreach ($productResult['casetemps'] as $key => &$value) {
                if ($value['casetemp'] == 'C') {
                    $value['casetemp_as_name'] = 'C档（0-70°）';
                } else {
                    $value['casetemp_as_name'] = 'I档（-40°-85°）';
                }
            }
            $productResult['defaultData'] = $pro_rel_ships->field('id,pn,manager')->limit('0,30')->select();   //默认加载30条数据
            $user = M('User');
            foreach ($productResult['defaultData'] as $key => &$value) {
                $value['nickname'] = $user->field('nickname')->find($value['manager'])['nickname'];
            }
            return $productResult;
        }
    }

    /**
     * 产品筛选
     */
    public function filterProducts()
    {
        $model = new Model();
        $filterParams = I('get.');
        foreach ($filterParams as $key => &$value) {
            if (!$value) {
                unset($filterParams[$key]);
            }
        }
        if (!empty($filterParams)) {
            $result = $this->getProductAttribute($filterParams);
        } else {
            $result = $this->getProductAttribute();
        }
        $this->ajaxReturn($result);
    }

    /**
     * 产品搜索
     */
    public function productSearch()
    {
        $model = new Model();
        if (trim(I('get.search')) != '') {
            $searchResult = $model->table(C('DB_PREFIX') . 'productrelationships')->field('pn')->where('pn like "%' . I('get.search') . '%"')->order('pn ASC')->select();
            $this->ajaxReturn($searchResult);
        }
    }

    /**
     * 文件上传
     */
    public function upload()
    {
        if (I('post.path') && I('post.path') != '') {
            $savePath = I('post.path');
        }
        $subName = '';
        $upload = new Upload();
        // 设置上传路径
        $upload->savePath = $savePath;
        // 限制上传文件大小为20mb
        $upload->maxSize = 20971520;
        // 开启子目录保存 并以指定参数为子目录
        $upload->autoSub = true;
        if ($subName != '') {
            $upload->subName = $subName;
        }
        // 保持上传文件名不变
        $upload->saveName = uniqid() . '_' . time();
        // 存在同名文件是否是覆盖
        $upload->replace = true;
        // 上传并返回结果
        $fileinfo = $upload->upload();
        if ($fileinfo) {
            $path = './Uploads' . $fileinfo['file']['savepath'] . $fileinfo['file']['savename'];
            $name = $fileinfo['file']['name'];
            $savename = $fileinfo['file']['savename'];
            $ext = strtolower($fileinfo['file']['ext']);
            $mime = $fileinfo['file']['type'];
            $size = $fileinfo['file']['size'];
            $time = time();
            $this->ajaxReturn(['flag' => 1, 'path' => $path, 'name' => $name, 'savename' => $savename, 'ext' => $ext, 'mime' => $mime, 'size' => $size, 'time' => $time]);
        } else {
            $this->ajaxReturn(['flag' => 0, 'msg' => $upload->getError()]);
        }
    }


}