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

    // 将附件的json格式转换为数组
    public function jsonToArray($data, $field = 'attachment'){
        foreach( $data as $key=>&$value ){
            if( $value[$field] ){
                $value[$field] = json_decode($value[$field], true);
            }
        }
        return $data;
    }


}


