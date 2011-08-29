<?php 
$pageTitle = $display['Display']['name'].__(" Status History",true);
$this->set('title_for_layout', $pageTitle);
?>

<h2><?=$pageTitle?></h2>

<table>
    <tr>
    <th><?php __('Status');?></th>
    <th><?php __('Time');?></th>
    <th><?php __('Age');?></th>
    </tr>
    
<?php 
foreach($statusList as $status) {
?>
    <tr>
        <td><?=$lostInBoston->statusText($status['DisplayStatus']['status'])?></td>
        <td><?=$status['DisplayStatus']['changed']?></td>
        <td><?=$time->relativeTime($status['DisplayStatus']['changed'])?></td>
    </tr>
<?php 
}
?>

</table>

<p>
<?php
echo $paginator->counter(array(
    'format' => 'Page %page% of %pages% (%count% Items)'
)); 
?>
<br/>
<?php echo $paginator->first(); ?>
&nbsp;
<?php echo $paginator->numbers(); ?>
&nbsp;
<?php echo $paginator->last(); ?>
</p>

