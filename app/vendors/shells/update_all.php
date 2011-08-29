<?php

/**
 * Call this (from cron)
 * > cake update_all
 * @author rahulb
 *
 */
class UpdateAllShell extends Shell {
    
    var $tasks = array('DisplayManagement');
   
    function main() {
         $this->DisplayManagement->updateAll();
    }
}

?>