<?php
namespace Home\Model;
use Think\Model\RelationModel;

class SampleModel extends RelationModel {
    
    //自动完成订单号和时间的创建
    protected $_auto = array(
        array('order','generateOrderNumber',self::MODEL_INSERT,'function'),
        array('createtime','time',self::MODEL_INSERT,'function'),
    );
    
    //关联模型
    protected $_link = array(
        'SampleS' => array(
            'mapping_type'=>self::HAS_ONE,
            'foreign_key'=>'s_assoc',
            'mapping_fields'=>'id,s_assoc,s_status,s_comment,s_time,wid',
            'as_fields'=>'id:sid,s_assoc,s_status,s_comment,s_time,wid:w_id',
        ),
        'SampleW' => array(
            'mapping_type'=>self::HAS_ONE,
            'foreign_key'=>'w_assoc',
            'mapping_fields'=>'id,w_assoc,w_status,w_comment,w_time,cid',
            'as_fields'=>'id:wid,w_assoc,w_status,w_comment,w_time,cid:c_id',
        ),
        'SampleC' => array(
            'mapping_type'=>self::HAS_ONE,
            'foreign_key'=>'c_assoc',
            'mapping_fields'=>'id,c_assoc,c_status,c_comment,c_time,yid',
            'as_fields'=>'id:cid,c_assoc,c_status,c_comment,c_time,yid:y_id',
        ),
        'SampleY' => array(
            'mapping_type'=>self::HAS_ONE,
            'foreign_key'=>'y_assoc',
            'mapping_fields'=>'id,y_assoc,y_status,y_comment,y_time,fid',
            'as_fields'=>'id:yid,y_assoc,y_status,y_comment,y_time,fid:f_id',
        ),
        'SampleF' => array(
            'mapping_type'=>self::HAS_ONE,
            'foreign_key'=>'f_assoc',
            'mapping_fields'=>'id,f_assoc,f_status,logistics,awb,f_comment,f_time,kid,attachment',
            'as_fields'=>'id:fid,f_assoc,f_status,logistics,awb,f_comment,f_time,kid:k_id,attachment',
        ),
        'SampleK' => array(
            'mapping_type'=>self::HAS_ONE,
            'foreign_key'=>'k_assoc',
            'mapping_fields'=>'id,k_assoc,k_status,k_comment,k_time',
            'as_fields'=>'id:kid,k_assoc,k_status,k_comment,k_time',
        ),
        'User' => array(
            'mapping_type'=>self::BELONGS_TO,
            'foreign_key'=>'uid',
            'mapping_fields'=>'id,account,nickname',
            'as_fields'=>'id:uid,account,nickname',
        ),
    );
    
    
    
    
    
}