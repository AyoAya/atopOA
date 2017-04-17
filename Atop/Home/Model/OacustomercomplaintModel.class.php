<?php
namespace Home\Model;
use Think\Model\RelationModel;

class OacustomercomplaintModel extends RelationModel {
    
    
    protected $_link = array(
        'Oacustomercomplaintlog' => array(
            'mapping_type'=>self::HAS_MANY,
            'foreign_key'=>'cc_id',
        ),
    );
    
    
    
    
    
    
}