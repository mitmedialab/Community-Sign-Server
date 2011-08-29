<?php
class DisplayStatus extends AppModel {
	var $name = 'DisplayStatus';
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
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Display' => array(
			'className' => 'Display',
			'foreignKey' => 'display_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	public function lastBoot($displayId){
        return  $this->find('first', array(
          'conditions'=>array('DisplayStatus.display_id'=>$displayId,'DisplayStatus.status'=>Display::STATUS_BOOTING),
          'order'=>array('changed'=>'DESC')
        ) );
    }
	
	public function latest($displayId){
		return $this->find('first', array('conditions'=>array('display_id'=>$displayId),
		  'order'=>array('changed'=>'DESC')
		) );
	}
	
	public function saveIfChanged($displayId,$status){
		$existing = $this->latest($displayId);
        $hasChanged = false;
		if($existing==null) {
			$hasChanged = true;
		} else {
			if($existing['DisplayStatus']['status']!=$status) {
				$hasChanged = true;
			}
		} 
		if($hasChanged) {
			$data = array('display_id'=>$displayId,'status'=>$status,'changed'=>date("Y:m:d H:i:s") );
			$this->save($data);
		}
		return $hasChanged;
	}
	
}
?>