<?php

App::import('Lib', 'BirthdayCalendar');

/**
 * Call this
 * > cake test_somerville_scout
 * @author rahulb
 *
 */
class TestBirthdayShell extends Shell {
    
    function main() {
        date_default_timezone_set('UTC');
        $cal = new BirthdayCalendar();
        $events = $cal->getUpcomingEvents('America/New_York');
        date_default_timezone_set('America/New_York');

        $this->out($events[0]['summary']);
   		print date("Y-m-d H:i:s", $events[0]['startdate']);
        date_default_timezone_set('America/New_York');
        
    }
}

?>