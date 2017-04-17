<?php
namespace Home\Model;
use Think\Model\RelationModel;
/**
 * 登录模型
 * @author Fulwin
 *
 */
class UserModel extends RelationModel {
    
    //自动完成
    protected $_auto = array(
        //创建时间(时间戳)
        array('createtime','time','3','function'),
        //密码加密
        array('password','sha1','3','function'),
    );
    
    //自动验证
    protected $_validate = array(
        //oldpassword密码不能为空
        array('oldpassword','require','-8',self::EXISTS_VALIDATE),
        //newpassword密码不能为空
        array('newpassword','require','-9',self::EXISTS_VALIDATE),
        //newpassword密码不能小于6位或大于30位
        array('newpassword','6,30','-10',self::EXISTS_VALIDATE,'length'),
        //newpassword密码格式不正确
        array('newpassword','/^\w+$/i','-11',self::EXISTS_VALIDATE),
        //repassword密码确认不一致
        array('repassword','newpassword','-12',self::EXISTS_VALIDATE,'confirm'),
        //repassword密码确认不能为空
        array('repassword','require','-13',self::EXISTS_VALIDATE),
    );
    
    protected $_link = array(
        'position'=>array(
            'mapping_type'=>self::BELONGS_TO,
            'foreign_key'=>'position',
            'mapping_fields'=>'id,name',
            'as_fields'=>'id:position_id,name:position_name',
        ),
        'department'=>array(
            'mapping_type'=>self::BELONGS_TO,
            'foreign_key'=>'department',
            'mapping_fields'=>'id,name',
            'as_fields'=>'id:department_id,name:department_name',
        ),
        'userlevel'=>array(
            'mapping_type'=>self::BELONGS_TO,
            'foreign_key'=>'level',
            'mapping_fields'=>'id,levelname',
            'as_fields'=>'id:level_id,levelname:level_name',
        ),
    );
    
    
}