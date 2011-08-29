<?php 

/**
 * List all the stops associated with a display
 * @param   stops       array of 'stop' objects
 */

?>

<ul>
<?php
foreach ($stops as $stop) {
    //pr($stop);
?>

<li> 
<?php 
$longName = $stop['agency']." : ".$stop['route_name']." : ".$stop['direction_name']." : ".$stop['name'];
if($stop['DisplayStop']['name']!=null){ 
	echo $stop['DisplayStop']['name']." <small>( ".$longName." )</small>"; 
} else {
	echo $longName;
}
?>

    <small>
<?php 
    echo $html->link(__("edit text",true),array('controller'=>'displayStops','action'=>'edit', $stop['DisplayStop']['id']) );
?> 

<?php 
        echo $html->link(__("remove stop",true),array('controller'=>'displays','action'=>'removeStop',
                $stop['DisplayStop']['id']), array(),
                __("Are you sure you want to remove this stop from this display?",true)); 	
?>

<?php 
//        echo $html->link(__("edit stop",true),array('controller'=>'stops','action'=>'edit', $stop['id']) );
?>

    </small>
</li>

<?php 
}
?>

</ul>
