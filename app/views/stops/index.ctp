<?php 
$pageTitle = __("All Stops",true);
$this->set('title_for_layout', $pageTitle);
?>

<h2><?=$pageTitle?></h2>

<?=$html->link(__("Search",true), '#',
    array('onclick'=>"$('#lib-search').slideToggle();return false;")); ?>
<br />
<div id="lib-search" style="display:none;">
<?php 
echo $form->create('Stop',array('action'=>'search'));
echo $form->input('name');
echo $form->submit('search');
echo $form->end();
?>
</div>

<br />

<?= $this->element('stop_table', array('stopList'=>$stopList));?>

<p>
<?php
echo $paginator->counter(array(
    'format' => 'Page %page% of %pages% (%count% stops)'
)); 
?>
<br/>
<?php echo $paginator->first(); ?>
&nbsp;
<?php echo $paginator->numbers(); ?>
&nbsp;
<?php echo $paginator->last(); ?>
</p>

