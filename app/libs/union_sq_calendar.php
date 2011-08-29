<?php

require_once("abstract_calendar.php");

/**
 * A CakePHP class to get upcoming events from the Union Sq calendar
 * @author rahulb
 */
class UnionSqCalendar extends AbstractCalendar{

    public function isEnabled(){
        return false;
    }
    
    protected function getCalendarUrl(){
        return "http://www.google.com/calendar/feeds/fn5piuvj8jv6km9mfd8hkq037c%40group.calendar.google.com/public/full";
    }
    
    protected function getCacheKeyPrefix(){
        return "unionsq";
    }

    protected function getStringReplacements(){
        return array(
            
        );
    }
    
}