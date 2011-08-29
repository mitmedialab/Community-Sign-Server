<?php
App::import('Lib','CivicCalendar'); 
App::import('Lib','DotwellCalendar'); 
App::import('Lib','FfpcCalendar'); 
App::import('Lib','UnionSqCalendar'); 
App::import('Lib','SomervilleScoutCalendar'); 
App::import('Lib','PatchCalendar'); 
App::import('Lib','BirthdayCalendar'); 

/**
 * Tasks 
 * @author rahulb
 *
 */
class CalendarManagementTask extends Shell {
      
    /**
     */
    function updateAll() {

        Cache::clear(false,'calendars');         // flush the cache first
        
        $this->log("Updating Civic Calendar",LOG_DEBUG);
        $cal = new CivicCalendar();
        $cal->getUpcomingEvents();
        
        $this->log("Updating Dotwell Calendar",LOG_DEBUG);
        $cal = new DotwellCalendar();
        $cal->getUpcomingEvents();
        
        $this->log("Updating FFPC Calendar",LOG_DEBUG);
        $cal = new FfpcCalendar();
        $cal->getUpcomingEvents();
        
        $this->log("Updating UnionSq Calendar",LOG_DEBUG);
        $cal = new UnionSqCalendar();
        $cal->getUpcomingEvents();
        
        $this->log("Updating Somerville Scout Calendar",LOG_DEBUG);
        $cal = new SomervilleScoutCalendar();
        $cal->getUpcomingEvents();

        $this->log("Updating JP Patch Calendar",LOG_DEBUG);
        $cal = new PatchCalendar();
        $cal->getUpcomingEvents();

        $this->log("Updating Union Sq Birthday Calendar",LOG_DEBUG);
        $cal = new BirthdayCalendar();
        $cal->getUpcomingEvents();
        
    }
        
}

?>