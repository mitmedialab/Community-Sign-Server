<?php
class Display extends AppModel {
	
	// keep in line with lib-sign-ctrl on displays
    const STATUS_UNKNOWN = 0;
	const STATUS_BOOTING = 1;
	const STATUS_OK = 2;
	const STATUS_SIGN_COMMS_ERROR = 3;
	const STATUS_SERVER_CONNECT_ERROR = 4;
	const STATUS_BLANKED_DISPLAY = 5;
	const STATUS_UNHEARD_FROM = 6;
	const STATUS_VERSION_MISMATCH = 7;
	
	const HW_TYPE_ONE_LINE = 1;
	const HW_TYPE_TWO_LINE = 2;
	
	var $name = 'Display';
	var $actsAs = array('Containable');
	 
	var $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'secret' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'serial_num' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array('Group');
	
    var $hasAndBelongsToMany = array(
        'Stop' =>
            array(
                'className'              => 'Stop',
                'joinTable'              => 'display_stops',
                'foreignKey'             => 'display_id',
                'associationForeignKey'  => 'stop_id',
                'unique'                 => true,
                'conditions'             => '',
                'fields'                 => '',
                'order'                  => '',
                'limit'                  => '',
                'offset'                 => '',
                'finderQuery'            => '',
                'deleteQuery'            => '',
                'insertQuery'            => ''
            ),
        'Feature' =>
            array(
                'className'              => 'Feature',
                'joinTable'              => 'display_features',
                'foreignKey'             => 'display_id',
                'associationForeignKey'  => 'feature_id',
                'unique'                 => true,
                'conditions'             => '',
                'fields'                 => '',
                'order'                  => '',
                'limit'                  => '',
                'offset'                 => '',
                'finderQuery'            => '',
                'deleteQuery'            => '',
                'insertQuery'            => ''
            )
    );

    function hasFeature($display,$featureId){
    	foreach($display['Feature'] as $feature){
    		if($feature['id']==$featureId){
    			return True;
    		}
    	}
    	return False;
    }
    
}
?>