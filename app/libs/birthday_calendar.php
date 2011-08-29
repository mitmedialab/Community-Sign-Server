<?php

require_once("abstract_calendar.php");

/**
 * A CakePHP class to get birthdays for Somerville
 */
class BirthdayCalendar extends AbstractCalendar{
 
    public function BirthdayCalender(){

    }
    
    protected function getCalendarUrl(){
    	return "https://www.google.com/calendar/feeds/unionsqbdays%40gmail.com/public/full";
    	//return "https://www.google.com/calendar/feeds/p34cemh31nec15tvp8ahg5qql0%40group.calendar.google.com/public/full";
    }
    
    protected function getCacheKeyPrefix(){
        return "birthday";
    }

    protected function getStringReplacements(){
        return array(
        );
    }
    
	//Only return birthdays that are happening today
    protected function postProcessEvents($events, $tzoffset){
 		$validEvents = array();
		$birthdays = "";
 		foreach($events as $e){
        	if($this->isToday($e)){
            	$birthdays = $birthdays.$e['summary'].', ';
				$startTime = $e['startdate'];
				$endTime = $e['enddate'];
				$startTime = $startTime-$tzoffset;
		        $endTime=$endTime-$tzoffset;
				$ongoing = $e['ongoing'];
				$tz = $e['timezone'];
				$recurring =$e['recurring'];
            }
        }
		if ($birthdays!=""){
			$birthdays = substr_replace($birthdays, '', strrpos($birthdays, ', '), 2);
			$birthdays = "Happy Birthday: ".$birthdays;
			$birthdayEvents = array(
              'summary'=>stripslashes(trim($birthdays)),
              'startdate'=>$startTime,
              'enddate'=>$endTime,
              'fullday'=> (($endTime-$startTime)==24*60*60),
              'location'=>' ',
              'ongoing'=>$ongoing,
              'timezone'=>$tz,
              'recurring'=>$recurring,
        	);
			$validEvents[] = $birthdayEvents;
		}
       	return $validEvents;
	}
	
	//For cache
	protected function postCacheDateIsOk($event){
		//print date("Y-m-d H:i:s", $event['startdate']);
		if ($this->isToday($event)){
			return True;
		}
		return False;
	}
	
	protected function isToday($event){
		$now = strtotime(date("Y-m-d"));    // server's PHP should be set to GMT
		//print date("Y-m-d H:i:s", $now);
		$nowDate = date("Y-m-d", $now);
		$eventDate = date("Y-m-d", $event['startdate']);
		if ($eventDate== $nowDate){
			return True;
		}
		return False;
	}
    
}

