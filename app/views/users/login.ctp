<?php 
$pageTitle = __("Login",true);
$this->set('title_for_layout', $pageTitle);
?>

<h2><?=$pageTitle?></h2>

<?php print $session->flash(); ?>

<div class="login"> 
    <?php echo $form->create('User', array('action' => 'login'));?> 
        <?php echo $form->input('username');?> 
        <?php echo $form->input('password');?> 
        <?php echo $form->submit('Login');?> 
    <?php echo $form->end(); ?> 
</div> 

<script type="text/javascript">
$(document).ready(function(){
    $("#UserUsername").focus();
});
</script>