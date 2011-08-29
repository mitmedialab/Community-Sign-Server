<?php 
$pageTitle = __("Create Display",true);
$this->set('title_for_layout', $pageTitle);
?>

<h2><?=$pageTitle?></h2>

<?php print $session->flash(); ?>

<?php
    echo $form->create('Display', array('action' => 'add'));
    echo $form->input('name');
    echo $form->input('serial_num');
    echo $form->input('secret');
    echo $form->end('Save');
?>
