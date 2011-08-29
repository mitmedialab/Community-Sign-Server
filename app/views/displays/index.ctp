<?php 
$pageTitle = __("All Displays",true);
$this->set('title_for_layout', $pageTitle);
?>

<h2><?=$pageTitle?></h2>

<hr/>

<?php 
//pr($displayList);
foreach($displayList as $display) {
    $detailsId = "lib-details-".$display['Display']['id'];    
	echo $this->element('display_status', array('display'=>$display['Display']));
?>
	

    <div style="float:right"><small><?=$html->link(__("delete",true),array('action'=>'delete',$display['Display']['id']),
        array(),
       __("Are you sure you want to delete this display?",true)
    )?></small></div>

    <p>
    <?=$html->link(__("details",true), '#',
        array('onclick'=>"$('#".$detailsId."').slideToggle();return false;")); ?> |
    <?=$html->link(__("update now",true),array('action'=>'updateText',$display['Display']['id']))?> |
     <?=$html->link(__("add stop",true),array('controller'=>'stops','action'=>'pick',$display['Display']['id']))?> | 
    <?=$html->link(__("edit",true),array('action'=>'edit',$display['Display']['id']))?> |
    <?=$html->link(__("history",true),array('action'=>'statusHistory',$display['Display']['id']))?> |
    <? if($display['Display']['version'][0] == '2'){
	    if($display['Display']['restart'] == '1'){
		echo $html->link(__("restart (pending)",true),array('action'=>'restart',$display['Display']['id']));
	    }
	    else{
		echo $html->link(__("restart",true),array('action'=>'restart',$display['Display']['id']));
	    }
       } 
    ?>
    </p>
    
    <div  id="<?=$detailsId?>" style="display:none;">
    <table>
        
        <tr>
            <th><?php __("Details")?></th>
            <th><?=count($display['Stop'])?> <?php __('Stops')?></th>
        </tr>
        <tr>
            <td style="width:300">
    <p>
    <small>
    <?php __("Last Request:");?> <?=$time->relativeTime($display['Display']['last_request'])?>
    <br /> 
    <?php __("Last IP");?> <?=$display['Display']['last_ip']?>
    <br /> 
    <?php __("Last Text Update:");?> <?=$time->relativeTime($display['Display']['last_text_update'])?>
    <br /> 
    <?php __("Last Reboot:");?> 
    <? if($display['DisplayStatusReboot']){
            print $time->relativeTime($display['DisplayStatusReboot']['DisplayStatus']['changed']);
       } else {
       	__("No reboot found");
       }
    ?>
    <br /> 
    <?php __("Serial Num:");?> <?=$display['Display']['serial_num']?> 
    <br /> 
    <?php __("Version:");?> <?=$display['Display']['version']?>
    </small> 
    </p>
            
            </td>
            <td>
                <?= $this->element('display_stop_list', array('stops'=>$display['Stop']));?>
            </td>
        </tr>
    </table>
    </div>
    
    <br />

	<hr/>
<?php 
}
?>

<br />
<br />

<p>
<?=$html->link(__("add a new display",true),array('action'=>'add'))?>
</p>
