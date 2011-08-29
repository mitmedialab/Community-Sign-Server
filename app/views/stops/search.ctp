<?php 
$pageTitle = __("Stops like '".$searchTerm."'",true);
$this->set('title_for_layout', $pageTitle);
?>

<h2><?=$pageTitle?></h2>

<p>
<?php
echo $paginator->counter(array(
    'format' => '(%count% results)'
)); 
?>
</p>

<?= $this->element('stop_table', array('stopList'=>$stopList));?>

