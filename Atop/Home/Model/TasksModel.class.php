<?php
/**
 * Created by PhpStorm.
 * User: Fulwin
 * Date: 2017/11/2
 * Time: 17:15
 */
namespace Home\Model;
use Think\Model\RelationModel;

class TasksModel extends RelationModel {

    protected $_link = [
        'user' => [
            'mapping_type' => self::BELONGS_TO,
            'foreign_key' => 'createuser'
        ]
    ];

}