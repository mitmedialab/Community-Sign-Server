<?php

require_once("abstract_calendar.php");

/**
 * A CakePHP class to get upcoming events from the Civic Media calendar
 * @author rahulb
 */
class CivicCalendar extends AbstractCalendar{
    
    public function isEnabled(){
        return false;
    }
    
    protected function getCalendarUrl(){
        return "http://civic.mit.edu/calendar-ical";
    }
    
    protected function getCacheKeyPrefix(){
        return "civic";
    }

    protected function getStringReplacements(){
        return array(
            
        );
    }
    
}