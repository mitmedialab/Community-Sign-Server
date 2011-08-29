<?php 
$pageTitle = __("Group: ",true).$group['Group']['name'];
$this->set('title_for_layout', $pageTitle);
//pr($display);
?>

<h2><?=$pageTitle?></h2>

<?php
	foreach ($displays as $d){
		echo $this->element('display_status', array('display'=>$d));
		echo '<hr>';
	}	
?>
