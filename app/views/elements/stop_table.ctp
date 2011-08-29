<?php 

/**
 * List all the stops associated with a display
 * @param   stopList       array of 'stop' objects
 */

?>

<table>
    <tr>
        <th><?=$paginator->sort(__('Agency',true), 'agency');?></th>
        <th><?=$paginator->sort(__('Route',true), 'route_name');?></th>
        <th><?=$paginator->sort(__('Direction',true), 'direction_name');?></th>
        <th><?=$paginator->sort(__('Stop',true), 'name');?></th>
        <td><?php __('Edit')?></td>
    </tr>
    
<?php
foreach($stopList as $stop) {
?>  <tr>
        <td><?=$stop['Stop']['agency']?></td>
        <td><?=$stop['Stop']['route_name']?></td>
        <td><?=$stop['Stop']['direction_name']?></td>
        <td><?=$stop['Stop']['name']?></td>
        <td><?=$html->link(__("edit",true),array('action'=>'edit',$stop['Stop']['id']))?></td>
    </tr>
<?php 
}
?>
</table>
