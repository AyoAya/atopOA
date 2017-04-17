<?php
/**
 * Created by PhpStorm.
 * User: Fulwin
 * Date: 2017/2/7
 * Time: 15:50
 */
namespace Home\Model;

use Think\Model\RelationModel;

class AcronymModel extends RelationModel  {

    protected $_link = array(
        'User' => array(
            'mapping_type' => self::BELONGS_TO,
            'foreign_key' => 'add_person',
        ),
    );

}