<display version = "1.1">
    <message>
        <info><?php echo $msg ?> </info>
    </message>
    <commandlist>
	 <?php
	if ($shouldRestart){
		echo '<command> restart </command>';
	} 
	?>
    </commandlist>
</display>

