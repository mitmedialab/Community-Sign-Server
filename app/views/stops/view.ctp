<?php 
$pageTitle = __("Stop: ",true).$stop['Stop']['name'];
$this->set('title_for_layout', $pageTitle);
?>

<h2><?=$pageTitle?></h2>

<?php print $session->flash(); ?>

<dl>
        <dt class="altrow"><?php __('Agency')?></dt>
        <dd class="altrow"><?= $stop['Stop']['id']?></dd>
        
        <dt class=""><?php __('Route')?></dt>
        <dd class=""><?= $stop['Stop']['route']?></dd>

        <dt class="altrow"><?php __('Route Name')?></dt>
        <dd class="altrow"><?= $stop['Stop']['route_name']?></dd>

        <dt class=""><?php __('Direction')?></dt>
        <dd class=""><?= $stop['Stop']['direction']?></dd>

        <dt class="altrow"><?php __('Direction Name')?></dt>
        <dd class="altrow"><?= $stop['Stop']['direction_name']?></dd>

        <dt class=""><?php __('Stop Key')?></dt>
        <dd class=""><?= $stop['Stop']['stop_key']?></dd>

        <dt class="altrow"><?php __('Name')?></dt>
        <dd class="altrow"><?= $stop['Stop']['name']?></dd>

</dl>