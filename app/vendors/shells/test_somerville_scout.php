<?php

App::import('Lib', 'SomervilleScoutCalendar');

/**
 * Call this
 * > cake test_somerville_scout
 * @author rahulb
 *
 */
class TestSomervilleScoutShell extends Shell {
    
    function main() {
        date_default_timezone_set('UTC');
        
        $cal = new SomervilleScoutCalendar();
        $events = $cal->getUpcomingEvents('America/New_York', true);

        date_default_timezone_set('America/New_York');
        foreach($events as $info){
            $ongoinStr = ($info['ongoing']) ? "ongoing" : "one-time";
            $dateStr = date("Y-m-d H:i:s",$info['startdate']);
            $infoStr = " (".$ongoinStr.") from ".$info['src'];
            $this->out($dateStr." : ".$info['summary']);
            $this->out(" ".$infoStr);
            $this->out("  @ ".$info['location']); 
        }
        date_default_timezone_set('America/New_York');
        
    }
}

?>