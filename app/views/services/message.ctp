<?php 
$pageTitle = __("Add More to Display: ",true).$display['Display']['name'];
$this->set('title_for_layout', $pageTitle);
//pr($display);
?>

<h2><?=$pageTitle?></h2>

<?php print $session->flash(); ?>

<?php
	echo $this->element('display_status', array('display'=>$display['Display']));
	echo '<hr>';
	echo $form->create('Display', array('url'=>array('controller'=>'Services', 'method'=>'message')));
	echo $form->inputs(array('legend'=>__('Content',true), 'append_text'=>array('type'=>'textarea','rows'=>2)));
	echo $form->input('password');
	echo $form->hidden('id');
	echo $form->end('Save');
?>	
