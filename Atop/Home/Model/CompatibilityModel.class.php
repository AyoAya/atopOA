<?php
/**
 * Created by PhpStorm.
 * User: Fulwin
 * Date: 2017/1/24
 * Time: 14:24
 */
namespace Home\Model;
use Think\Model\RelationModel;

class CompatibilityModel extends RelationModel {

    //关联模型
    protected $_link = array(
        //关联设备品牌表
        'VendorBrand' => array(
            'mapping_type' => self::BELONGS_TO,
            'foreign_key' => 'vendor',
        ),
        //关联产品型号表
        'Productrelationships' => array(
            'mapping_type' => self::BELONGS_TO,
            'foreign_key' => 'pn',
        ),

    );

}