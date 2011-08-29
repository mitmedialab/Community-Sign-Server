<?php

App::import('Lib', 'PatchCalendar');

/**
 * Call this
 * > cake test_somerville_scout
 * @author rahulb
 *
 */
class TestJppatchShell extends Shell {
    
    function main() {
        date_default_timezone_set('UTC');
        
        $cal = new PatchCalendar();
        $events = $cal->getUpcomingEvents('America/New_York');

        date_default_timezone_set('America/New_York');
        foreach($events as $info){
        	$this->out($info['summary'].date("Y-m-d H:i:s",$info['startdate']));
            $ongoinStr = ($info['ongoing']) ? "ongoing" : "one-time";
            $dateStr = date("Y-m-d H:i:s",$info['startdate']);
            $infoStr = " (".$ongoinStr.") from ".$info['src'];
			$endDateStr = date("Y-m-d H:i:s",$info['enddate']);
            $this->out($dateStr." : ".$info['summary']);
            $this->out(" ".$infoStr);
            $this->out("  @ ".$info['location']);
			$this->out($endDateStr);
			 
        }
        date_default_timezone_set('America/New_York');
        
    }
}

?>