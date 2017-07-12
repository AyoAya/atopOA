<?php
/**
 * Created by PhpStorm.
 * User: Fulwin
 * Date: 2017/1/19
 * Time: 11:11
 */
namespace Home\Model;
use Think\Model\RelationModel;

/**
 * 审批模型
 * Class ApprovalModel
 * @package Home\Model
 */
class FileModel extends RelationModel  {

    //关联模型
    protected $_link = array(
        //关联附属表
        'ApprovalAffiliated' => array(
            'mapping_type' => self::HAS_MANY,
            'foreign_key' => 'belong',
            'mapping_name' => 'affiliated',
            'mapping_fields' => 'money,category,customer'
        ),
        //关联状态表
        'ApprovalState' => array(
            'mapping_type' => self::HAS_ONE,
            'foreign_key' => 'subjection',
            'mapping_fields' => 'state,state_text,subjection',
            'as_fields' => 'state,state_text,subjection',
        ),
        //关联日志表
        'ApprovalLog' => array(
            'mapping_type' => self::HAS_MANY,
            'foreign_key' => 'belongs_to',
            'mapping_name' => 'log',
            'mapping_fields' => 'audit_id,audit_name,comment,log_time'
        ),
    );


}