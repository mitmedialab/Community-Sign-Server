<?php

App::import('Lib', 'GrandRapidsCalendar');

/**
 * Call this
 * > cake test_wisconsing_cal 
 * @author rahulb
 *
 */
class TestWisconsinCalShell extends Shell {
    
    function main() {
        date_default_timezone_set('UTC');
        $cal = new GrandRapidsCalendar();
        $events = $cal->getUpcomingEvents('America/Chicago');
        
        date_default_timezone_set('America/Chicago');
        foreach($events as $info){
            $ongoinStr = ($info['ongoing']) ? "ongoing" : "one-time";
            $dateStr = date("Y-m-d H:i:s",$info['startdate']);
            $infoStr = " (".$ongoinStr.") from ".$info['src'];
            $this->out($dateStr." : ".$info['summary']);
            $this->out(" ".$infoStr);
            $this->out("  @ ".$info['location']);
            if($info['startdate']<time()){
                $this->out("  Already Started");
            }
        }
        date_default_timezone_set('UTC');
        
    }
}

?>