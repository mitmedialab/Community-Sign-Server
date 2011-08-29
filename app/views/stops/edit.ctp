<?php 
$pageTitle = __("Edit Stop: ",true).$stop['Stop']['name'];
$this->set('title_for_layout', $pageTitle);
?>

<h2><?=$pageTitle?></h2>

<?php print $session->flash(); ?>

<?php
    echo $form->create('Stop', array('action' => 'edit'));
    echo $form->input('agency',array('disabled'=>true));
    echo $form->input('route',array('disabled'=>true));
    echo $form->input('route_name');
    echo $form->input('direction',array('disabled'=>true));
    echo $form->input('direction_name');
    echo $form->input('stop_key',array('disabled'=>true));
    echo $form->input('name');
    echo $form->input('id', array('type' => 'hidden')); 
    echo $form->end('Save');
?>
