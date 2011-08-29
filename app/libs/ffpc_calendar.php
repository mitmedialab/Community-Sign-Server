<?php

require_once("abstract_calendar.php");

/**
 * A CakePHP class to get upcoming events from Friend of Fort Point Channel's google calendar
 * @author rahulb
 */
class FfpcCalendar extends AbstractCalendar{

    protected function getCalendarUrl(){
        return "http://www.google.com/calendar/feeds/friendsoffortpointchannel02210%40gmail.com/public/full";
    }
    
    protected function getCacheKeyPrefix(){
        return "ffpc";
    }

    protected function getStringReplacements(){
        return array(
            "Boston Children's Museum"=>"BCM",
        );
    }
    
    public function isEnabled(){
        return false;
    }
    
}