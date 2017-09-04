<?php
/**
 * Created by PhpStorm.
 * User: Fulwin
 * Date: 2017/9/4
 * Time: 14:39
 */
namespace Home\Model;
use Think\Model\RelationModel;

class EcnModel extends RelationModel {

    //定义主表名称
    protected $tableName = 'ecn';

    // 关联模型
    protected $_link = [
        'EcnReview' => [
            'mapping_type' => self::HAS_MANY,
            'foreign_key' => 'ecn_id'
        ],
        'EcnReviewItem' => [
            'mapping_type' => self::HAS_MANY,
            'foreign_key' => 'ecn_id'
        ],
        'User' => [
            'mapping_type' => self::BELONGS_TO,
            'foreign_key' => 'createuser'
        ]
    ];


}


