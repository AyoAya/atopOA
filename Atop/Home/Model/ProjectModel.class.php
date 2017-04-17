<?php
/**
 * Created by PhpStorm.
 * User: Fulwin
 * Date: 2017/2/20
 * Time: 17:18
 */
namespace Home\model;

use Think\Model\RelationModel;

class ProjectModel extends RelationModel {


    protected $_link = array(

        /*'ProjectPlan' => array(
            'mapping_type' => self::HAS_MANY,
            'foreign_key' => 'plan_pj_id',
        ),*/

    );

}