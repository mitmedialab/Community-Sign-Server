<?php 
$pageTitle = __("Login",true);
$this->set('title_for_layout', $pageTitle);
?>

<h2><?=$pageTitle?></h2>

<?php print $session->flash(); ?>

<div class="login"> 
    <?php echo $form->create('User', array('action' => 'signup'));?> 
        <?php echo $form->input('email');?> 
        <?php echo $form->input('username');?> 
        <?php echo $form->input('password');?> 
        <?php echo $form->submit('Signup');?> 
    <?php echo $form->end(); ?> 
</div> 
