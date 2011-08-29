<?php 
$pageTitle = __("Edit Display: ",true).$display['Display']['name'];
$this->set('title_for_layout', $pageTitle);
//pr($display);
?>

<h2><?=$pageTitle?></h2>

<?php print $session->flash(); ?>

<?php
    echo $form->create('Display', array('action' => 'edit'));
    echo $form->input('name');
    echo $form->input('client_password');
    echo $form->input('Feature');
    echo $form->inputs(array('legend'=>__('Error Alerts',true),'send_error_alerts','error_alert_emails'));
    echo $form->inputs(array('legend'=>__('Id for Server',true),'serial_num','secret'));
    echo $form->inputs(array('legend'=>__('Location',true),
        'timezone'=>array('type'=>'select',
                               'options'=>array(0=>'EST',1=>'CDT',2=>'MDT',3=>'MST',4=>'PDT'),
                               'selected'=>$display['Display']['timezone']),
	'group_id',
        'city'));
 
    echo $form->inputs(array('legend'=>__('Content',true),
        'hardware_type'=>array('type'=>'select',
                                'options'=>array('1'=>__('one-line led display',true),'2'=>__('two-line led display',true)),
                               'selected'=>$display['Display']['hardware_type']),
        'text',
	'override_text'=>array('type'=>'textarea','rows'=>2),
	'append_text'=>array('type'=>'textarea','rows'=>2)));

    echo $form->input('id', array('type' => 'hidden')); 
    echo $form->end('Save');
?>
