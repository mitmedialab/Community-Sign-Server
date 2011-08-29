<?php
class DisplayStop extends AppModel {
	var $name = 'DisplayStop';
	var $validate = array(
		'display_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'stop_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Display' => array(
			'className' => 'Display',
			'foreignKey' => 'display_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Stop' => array(
			'className' => 'Stop',
			'foreignKey' => 'stop_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
?>