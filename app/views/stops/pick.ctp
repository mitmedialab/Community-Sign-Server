<?php 
$pageTitle = __("Add a Stop to ".$display['Display']['name'],true);
$this->set('title_for_layout', $pageTitle);
?>

<h2><?=$pageTitle?></h2>

<p>
<?php __('Select a stop to add to the display named ')?> <?=$display['Display']['name']?></b>.
</p>

<form method=POST id="pickStopForm">

<?php echo $form->hidden('displayId',array('value'=>$display['Display']['id']))?>
<?php echo $form->hidden('done',array('value'=>0))?>

<?php echo $form->select('Agency', $agencyList, $selectedAgency,array(
    'onChange'=>"$('#Route').val('');$('#Direction').val('');$('#Stop').val('');$('#pickStopForm').submit();"
));?>

<?php 
if(!empty($routeList)){
    echo $form->select('Route', $routeList, $selectedRoute,array(
	    'onChange'=>"$('#Direction').val('');$('#Stop').val('');$('#pickStopForm').submit();"
	));
}	
?>

<?php 
if(!empty($directionList)){
    echo $form->select('Direction', $directionList, $selectedDirection,array(
        'onChange'=>"$('#Stop').val('');$('#pickStopForm').submit();"
    ));
}   
?>

<?php 
if(!empty($stopList)){
    echo $form->select('Stop', $stopList, $selectedStop,array(
        'onChange'=>"$('#pickStopForm').submit();"
    ));

    echo $form->submit("pick",array( 'onClick'=>"$('#done').val(1);"));
    
}   
?>

</form>

<br />

<h3><?php __('Existing Stops:')?></h3>

<?= $this->element('display_stop_list', array('stops'=>$display['Stop'], 'showRemove'=>true, 'showEdit'=>true));?>
