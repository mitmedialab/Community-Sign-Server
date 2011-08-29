<?php
class User extends AppModel {
	var $name = 'User';
	var $validate = array(
		'username' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'password' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'email' => array(
			'email' => array(
				'rule' => array('email'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

    function beforeSave() {
        if(isset($this->data[$this->name]['password'])){
            $this->data[$this->name]['password'] = md5($this->data[$this->name]['password']);
        }
        return true; 
    }
    
    function verifyLogin($data) {
        $user = $this->findByUsernameAndPassword(
                    $data[$this->name]['username'],
                    md5($data[$this->name]['password']) 
        );

        if (!empty($user)) {
            return $user;
        } else {
            $this->invalidate('', 'Invalid login, please try again');
        }
        
        return false;
    }
    
    function emailsToSendErrorAlerts(){
        return $this->find('list', array('conditions'=>array('send_error_alerts'=>1),
          'fields'=>array('email')
        ) );
    }
    
}
?>