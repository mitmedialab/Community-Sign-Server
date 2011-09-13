<?php

require_once("abstract_calendar.php");

/**
 * A CakePHP class to get upcoming events from JP Patch
 */
class PatchCalendar extends AbstractCalendar{
    
    public function PatchCalendar(){
       
    }
    
    protected function getCalendarUrl(){
        return "http://api.patch.com/publication/jamaicaplain/events:future_chronological?level=mobile_list";
    }
    
    protected function getCacheKeyPrefix(){
        return "patch";
    }

    protected function getStringReplacements(){
        return array( 
        	"Jamaica Plain" => "JP",
        	"Public Library" => "Library",
        	"Boston City Hall" => "City Hall",
        	"Branch Library" => "Library",
        	"Bella Luna Restaurant and Milky Way Lounge" => "Bella Luna / Milk Way",
        	"First Church In JP Unitarian Universalist" => "First Church",
        	"MSPCA-Angell Animal Medical Center" => "MSPCA-Angell",
        	"St John's Episcopal Church" =>"St John's",
        );
    }
    
	/**
	 * Override abstract_calendar to parse JSON
	 */
	public function getUpcomingEvents($tz=null) {
		$events = array();
        
	    $cacheKey = $this->getEventCacheKeyPrefix();   // check cache of final list of events (updated daily)
	    $cached = Cache::read($cacheKey, 'calendars');
	    $tzOffset = DateUtils::getTimezoneOffset($tz);
	    if($cached==false){
    	    $urls = $this->getCalUrlList();
            $now = strtotime(date("Y-m-d"));    // server's PHP should be set to GMT
            $until = $now + 7*24*60*60; // one week's worth of events
            $eventsByCal = array();
			$events = array();
			$allTags = array();
            // fetch each calendar full of events
            foreach($urls as $calendarName=>$url){
            	#Get string from website
                $allInfo = file_get_contents($url);
                #Parse JSON
                //var_dump(json_decode($allInfo));
                $parsedInfo=json_decode($allInfo);
				
				foreach($parsedInfo as $index=>$info){
					if (property_exists($info,"address")){
						$tags = array();
						foreach($info->categories as $category){
							$tags[] = $category->name;
						}
						$summary = $info->name;
						$description = $info->description;
						$src = $info->categories;
						$timeValues = $this->getTimes($info, $tzOffset);
						$startTimeFinal = $timeValues[0];
            			$endTimeFinal = $timeValues[1];
						
            			$ongoing = ($startTimeFinal<time()) && ($endTimeFinal>time()); //ongoing, one-time
            			
            			if ($info->address != null){
            				$location = $info->address->street;
            			}
						else{
							$location = null;
						}
						if (property_exists($info,"venue")){
							if($info->venue!=null){
								$venueName = $info->venue->name;
								if($location!=null) {
									$location = $venueName . " " . $location;
								} else {
									$location = $venueName;
								}
							}
						}
					
						if($location!=null){
							 foreach($this->getStringReplacements() as $search=>$replace){
             				   $location = str_replace($search,$replace,$location);
            				}
						}
					
						$recurring = false;
						$pos = strpos($info->ical, 'RRULE');
						if ($pos !== false) {
   							$recurring = true;				
						}
					
						//Put all of above info into an array					
						$eventInfo = array(
                		'summary'=>stripslashes(trim($summary)),
                		'description'=>stripslashes($description),
                		'location'=>stripslashes($location),
                		'startdate'=>$startTimeFinal,
                		'enddate'=>$endTimeFinal,
                		'fullday'=> (($endTimeFinal-$startTimeFinal)==24*60*60),
                		'ongoing'=>$ongoing,
                		'timezone'=>$tz,
                		'recurring'=>$recurring,
                		'src'=>$tags,        // array of Path category names        	
                		);
						
						//print date("H:i:s",$eventInfo['startdate']);
						$allTags = array_unique(array_merge($allTags,$tags));
						//print $info->name.date("Y-m-d H:i:s",$eventInfo['startdate']).' ';
						array_push($events, $eventInfo);
						
                	}
					$eventsByCal[$calendarName] = $events;
				}	
            }
			
            //merge all the calendars
            if (sizeof($eventsByCal) >1){
           		$onetime = array();
            	foreach($eventsByCal as $key=>$srcEvents){
                	$onetime = array_merge($onetime,$srcEvents);
            	}
            	usort($onetime, "datedEventCompare");
            	$events = $onetime;
			}
			 
            Cache::write($cacheKey, $events, 'calendars');
	    } else {
    	    // if we pulled from a cached list of all the events, we need
            // to screen out any that may have started already 
            $removed = 0;
    	    foreach($cached as $event){
                $startTimeFinal = $event['startdate'];
                if($startTimeFinal < time()) {
                    $removed++;
                    continue;
                }
                $events[] = $event;
            }
	    }

	    return $events;
	}
	
	/**
	 * Determine time/date stuff
	 */
    protected function getTimes($info, $tzoffset){
		$startTime = $info->next_occurrence;
		$minutes = (int)($info->duration/60); //need to convert duration to right format
		$hours = (int)($minutes/ 60); 
    	$minutes -= $hours * 60; 
		$firstColon= strpos($startTime, ':');
		$startTimeHour=intval(substr($startTime, $firstColon-2, $firstColon-1));
		$startTimeMin=intval(substr($startTime, $firstColon+1, $firstColon+2)); 
		$hours = $hours + $startTimeHour;
		$minutes = $minutes + $startTimeMin;
		$endTime = sprintf("%d:%02.0f", $hours, $minutes); 
		$startDateIndex = strpos($startTime, ' '); //need to get an end date too, assume it's same as start
		$endDate = substr($startTime, 0,$startDateIndex);
		$endTime = $endDate.$endTime;
		$startTime = strtotime($startTime); //convert to time object
        $endTime = strtotime($endTime);
		//$startTime = $startTime-$tzoffset;
		//$endTime=$endTime-$tzoffset;
		
        return array($startTime, $endTime);
    }
}
