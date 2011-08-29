<?php

App::import('Lib', 'UnionSqCalendar');

/**
 * Call this
 * > cake 
 * @author rahulb
 *
 */
class TestUnionSqShell extends Shell {
    
    function main() {
        date_default_timezone_set('UTC');
        
        $cal = new UnionSqCalendar();
        $events = $cal->getUpcomingEvents('America/New_York');

        foreach($events as $info){
            $ongoinStr = ($info['ongoing']) ? "ongoing" : "one-time";
            $dateStr = date("Y-m-d H:i:s",$info['startdate']);
            $infoStr = " (".$ongoinStr.") from ".$info['src'];
            $this->out($dateStr." : ".$info['summary']);
            $this->out(" ".$infoStr);
            $this->out("  @ ".$info['location']);        
        }
        
    }
    
}

?>