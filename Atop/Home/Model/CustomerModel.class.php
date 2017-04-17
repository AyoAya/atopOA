<?php
/**
 * Created by PhpStorm.
 * User: Fulwin
 * Date: 2017/4/12
 * Time: 9:49
 */
namespace Home\Model;
use Think\Model\RelationModel;

class CustomerModel extends RelationModel {

    protected $tableName = 'oacustomercomplaint';

    protected $_link = array(

        'oacustomeroperation' => array(
            'mapping_type' => self::HAS_MANY,
            'foreign_key' => 'main_assoc',
        ),
        'oacustomercomplaintlog' => array(
            'mapping_type' => self::HAS_MANY,
            'foreign_key' => 'cc_id',
        ),

    );






}