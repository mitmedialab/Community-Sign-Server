<?php
require_once('date_utils.php');
require_once("gcal/google_calendar_manager.php");

/**
 * A base class for any calendar data sources.  This offers up the useful
 * getUpcomingEvents method.
 * @author rahulb
 *
 */
abstract class AbstractCalendar {

    abstract protected function getCalendarUrl();
    abstract protected function getCacheKeyPrefix();

    /**
     * Override this and return false if you don't want the calendar to update,
     * and don't want it to show in  the list of options.
     */
    public function isEnabled(){
        return true;
    }
    
    protected function getEventCacheKeyPrefix(){
        return $this->getCacheKeyPrefix()."_events";
    }
    
    /**
     * Override this to return an array of strings, keyed by
     * searchStr=>replaceStr.  This is useful to replace long, recurring strings
     * in events names with shorter versions of them (ie. replace "Boston Children's
     * Museum" with "BMC")
     */
    protected function getStringReplacements(){
        return array();
    }
        
    /**
     * Return an array of urls to fetch events from, keyed by the category name 
     * of the calendar.  This allows one conceptual "calendar" to have multiple
     * feeds (ie. like the Somerville Scout). 
     */
    protected function getCalUrlList(){
        if(!is_array($this->getCalendarUrl())){
            return array('default'=>$this->getCalendarUrl());
        }
        return $this->getCalendarUrl();
    }

    /**
     * Return a list of upcoming events - an array of arrays contained keyed
     * info about the event.  These will be sorted by time.  This manages
     * a cache internally, and filters out events that have already started. 
     * Remember, the times are in GMT unix timestamps.
     * @param $tz
     * @param $skipCache
     */
    public function getUpcomingEvents($tz=null,$skipCache=false) {
        $events = array();
        
        if($this->isEnabled()==false){  // bail if the calendar is not enabled
            return $events;
        }

        $tzOffset = 0;
        if($tz!=null) {
            $tzOffset = DateUtils::getTimezoneOffset($tz);
        }
        
	    $cacheKey = $this->getEventCacheKeyPrefix();   // check cache of final list of events (updated daily)
	    $cached = Cache::read($cacheKey, 'calendars');
	   	
	    if ($cached==false || $skipCache){
    	    $urls = $this->getCalUrlList();
            $now = strtotime(date("Y-m-d"));    // server's PHP should be set to GMT
            $until = $now + 7*24*60*60; // one week's worth of events
            $eventsByCal = array();
            // fetch each calendar full of events
            foreach($urls as $key=>$url){
                $calMgr = new GoogleCalendarManager();
                //$data = $this->getRawData($key,);
                $events = $calMgr->getEvents($url, $now, $until, $this->getStringReplacements(),$tz,$key);
                $eventsByCal[$key] = $events;
            }
            // merge all the calendars
            $onetime = array();
            foreach($eventsByCal as $key=>$srcEvents){
                $onetime = array_merge($onetime,$srcEvents);
            }
            usort($onetime, "datedEventCompare");
            $events = $onetime;
            $events = $this->postProcessEvents($events, $tzOffset);
            //print count($events)." events!";
			//print $events[0]['summary'].date("Y-m-d H:i:s",$events[0]['startdate']);
            Cache::write($cacheKey, $events, 'calendars');
	    } else {
    	    // if we pulled from a cached list of all the events, we need
            // to screen out any that may have started already 
            $removed = 0;
    	    foreach($cached as $event){
                if (!$this->postCacheDateIsOk($event)){
                    $removed++;
                    continue;                	
                }
                $events[] = $event;
            }
            //print "from cache ".count($events)." removed ".$removed;
		}
	    return $events;
	}
	
	/**
	 * This is called after it is pulled out of the cache, to make sure
	 * the date is still valid.  Return true if it is ok, return false if
	 * you don't want to show the event anymore.
	 */
	protected function postCacheDateIsOk($event){
		$startTime = $event['startdate'];
		return ($startTime > time());
	}
	
	/**
	 * Override this to process all the events before there are returned 
	 * from getUpcomingEvents.  This is useful if you need to do something
	 * like geocode them and screen them by location too.
	 * @param unknown_type $events
	 */
	protected function postProcessEvents($events, $tzOffset){
	    return $events;
	}
            
}

?>