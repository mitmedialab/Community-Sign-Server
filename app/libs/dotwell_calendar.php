<?php

require_once("abstract_calendar.php");

/**
 * A CakePHP class to get upcoming events from dotwell's google calendar
 * @author rahulb
 */
class DotwellCalendar extends AbstractCalendar {
	
    protected function getCalendarUrl(){
        return "http://www.google.com/calendar/feeds/dothousemultiservicecenter%40gmail.com/public/full";
    }
    
    protected function getCacheKeyPrefix(){
        return "dotwell";
    }
    
    public function isEnabled(){
        return false;
    }    
                
}