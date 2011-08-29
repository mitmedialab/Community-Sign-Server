<?php

require_once("abstract_calendar.php");

/**
 * A CakePHP class to get upcoming events from the Grand Rapids Calendar
 * @author rahulb
 */
class GrandRapidsCalendar extends AbstractCalendar{

    protected function getCalendarUrl(){
        return "https://www.google.com/calendar/feeds/bkoeshall%40cfswc.org/public/full";
    }
    
    protected function getCacheKeyPrefix(){
        return "grandrapids";
    }

    protected function getStringReplacements(){
        return array(
            
        );
    }
    
}