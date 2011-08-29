<?php 
$pageTitle = __("Edit Stop Text for this Display: ",true);
$this->set('title_for_layout', $pageTitle);
?>

<h2><?=$pageTitle?></h2>

<?php print $session->flash(); ?>

<label><?php __('Display:')?></label>
<p><?=$displayStop['Display']['name']?></p>

<br/>

<label><?php __('Stop:')?></label>
<p><?=$displayStop['Stop']['agency']?> <?=$displayStop['Stop']['route_name']?> (<?=$displayStop['Stop']['direction_name']?>) <?=$displayStop['Stop']['name']?></p>

<br/>

<?php
    echo $form->create('DisplayStop', array('action' => 'edit'));
    echo $form->input('name');
    echo $form->input('id', array('type' => 'hidden')); 
    echo $form->end('Save');
?>
