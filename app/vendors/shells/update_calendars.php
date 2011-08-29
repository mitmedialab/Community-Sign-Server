<?php

/**
 * Call this (from cron) once a day
 * > cake update_calendars
 * @author rahulb
 *
 */
class UpdateCalendarsShell extends Shell {
    
    var $tasks = array('CalendarManagement');
   
    function main() {
         $this->CalendarManagement->updateAll();
    }
}

?>