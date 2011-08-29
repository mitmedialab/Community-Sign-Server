<?php
App::import('Xml'); 
App::import('Helper', 'Time');
// TODO: make this automaticly include the right files 
App::import('Lib', 'NextBus');
App::import('Lib', 'MbtaRailTrain');
App::import('Lib', 'NoaaWeather');
App::import('Lib', 'DotwellCalendar');
App::import('Lib', 'FfpcCalendar');
App::import('Lib', 'UnionSqCalendar');
App::import('Lib', 'CivicCalendar');
App::import('Lib', 'SomervilleScoutCalendar');
App::import('Lib', 'GrandRapidsCalendar');
App::import('Lib', 'PatchCalendar');
App::import('Lib', 'BirthdayCalendar');

/**
 * This is the engine that stitches all the various content together.
 */
class MsgGeneratorComponent extends Object {

    // TODO: support international timezones
    var $timezoneLookup = array( 
        0=>'America/New_York',      // EDT 
        1=>'America/Chicago',       // CDT
        2=>'America/Boise',         // MDT
        3=>'America/Phoenix',       // MST
        4=>'America/Los_Angeles',   // PDT
    ); 
    
    // HACK: these need to match the ids in the db feature table
	const FEATURE_BOSTON_WEATHER = 1;
    const FEATURE_DOTWELL_CAL = 2;
    const FEATURE_FFPC_CAL = 3;
    const FEATURE_UNION_SQ_CAL = 4;
    const FEATURE_CIVIC_CAL = 5;
    const FEATURE_SCOUT_CAL = 6;
    const FEATURE_GRAND_RAPIDS_WEATHER = 7;
    const FEATURE_GRAND_RAPIDS_CAL = 8;
	const FEATURE_JP_PATCH_CAL = 9;
	const FEATURE_BIRTHDAY_CAL = 10;
    
    const MAX_EVENTS_TO_SHOW = 5;       // TODO: make this a setting on each installed display
    const EVENT_NAME_MAX_CHARS = 80;    // events with names longer than this will be trimmed
	
    var $controller = null;

    /**
     * Make double-sure we are in UTC 
     * @param $controller
     */
    function startup(&$controller) {
        date_default_timezone_set('UTC');
        $this->controller = $controller;
        $this->timeHelper = new TimeHelper();
    }
    
    /**
     * Translates unix time into display-local time.
     * @param $eventInfo
     * @param $displayTimezone
     */
    private function getEventTimeStr($eventInfo, $displayTimezone) {  
        date_default_timezone_set( $displayTimezone );
        $timestamp = $eventInfo['startdate'];
		//print date("Y-m-d H:i:s", $eventInfo['startdate']);
		//exit();
        $ongoing = $eventInfo['ongoing'];
        $relTime = "";
        if($this->timeHelper->isToday($timestamp)) {
            $relTime = "Today";
        } else if($this->timeHelper->isTomorrow($timestamp)) {
            $relTime = date('D',$timestamp);
        } else {
            $relTime = date('D',$timestamp);
        }
        if($eventInfo['fullday']==false){
            $relTime.=" ".date('g',$timestamp);
            $minsStr = date('i',$timestamp);
            if($minsStr!="00") {
                $relTime.=":".$minsStr;
            }
            $relTime.=date('a',$timestamp);
        }
        date_default_timezone_set('UTC');
        return $relTime;
    }
       
    /**
     * This tries to be clever about adding in the location name of the end of the event name
     * @param unknown_type $event
     */
    private function getEventNameStr($event){
        $eventName = "";
        // Make sure the event name isn't too long
        if(strlen($event['summary'])>MsgGeneratorComponent::EVENT_NAME_MAX_CHARS){
            $eventName = substr($event['summary'],0,MsgGeneratorComponent::EVENT_NAME_MAX_CHARS)."...";
        } else {
            $eventName = $event['summary']; 
        }
        // pull out location name
        $locationParts = split(",",$event['location']);
        $locName = $locationParts[0];
		if ($locName != ' '){
			$eventName = $eventName." (".$locName.")";
		}
        return $eventName;
    }
    
    /**
     * Change the list of events into an array of strings for display 
     * @param unknown_type $events
     * @param unknown_type $displayTimezone
     */
    private function getEventTextList($events, $displayTimezone){
        $eventTexts = array();
        // show the next N one-time events
        if(count($events)>0){
            $oneTimeEvents = array_slice($events,0,MsgGeneratorComponent::MAX_EVENTS_TO_SHOW,true);
			
            foreach($oneTimeEvents as $timestamp=>$info){
                $eventName = $this->getEventNameStr( $info );
                $eventTexts[] = $this->getEventTimeStr($info, $displayTimezone)."\n".$eventName;
            }
        }
        return $eventTexts;
    } 

	/**
	 * Checks if message has an odd number of lines and fixes it (needed for two line displays to work
	 * correctly)
	 */
	private function fixOddLines($message){
		$lines = substr_count($message, "\n");
		if ($lines % 2 == 0){
			$message = $message."\n   ";
		}
		return $message;
	}

	/**
     * This is the heart of this class.
     * Assemble the text that we should show on the sign based the stops it has
     * @param $displayId
     */
    function updateDisplayText($displayId){
    	$nowStr = date("Y:m:d H:i:s");
    	
    	// need to instantiate all the content sources
    	$nextBus = new NextBus();
        $mbtaRail = new MbtaRailTrain();
        $weather = new NoaaWeather();
        $dotwellCal = new DotwellCalendar();
        $ffpcCal = new FfpcCalendar();
        $unionSqCal = new UnionSqCalendar();
        $civicCal = new CivicCalendar();
        $scoutCal = new SomervilleScoutCalendar();
        $grCal = new GrandRapidsCalendar();
        $patchCal= new PatchCalendar();
		$birthCal = new BirthdayCalendar();
		
		// load the display we care about
        $display = $this->controller->Display->FindById($displayId);
    	$this->log("  ".$display['Display']['name'].":",LOG_DEBUG);
        $str = "";
        $displayTimezone = $this->timezoneLookup[ $display['Display']['timezone'] ];
        
        // check if the display overriden by a custom message
        if(strlen($display['Display']['override_text'])>0) {
        	//$str = $display['Display']['override_text'];
			$str = str_replace("\r"," ",$display['Display']['override_text']);
			$str = $this->fixOddLines($str);
        } else {
	        
            // normal display, so stitch the content togther
	        $msgs = array('predictions'=>array(),'texts'=>array());
	        
	        // get the weather
	        $temperature = NoaaWeather::NO_PREDICTION_FOUND;
	        if($this->controller->Display->hasFeature($display,MsgGeneratorComponent::FEATURE_BOSTON_WEATHER)){
	        	$temperature = $weather->getCurrentTempFarenheit(NoaaWeather::BOSTON_FEED_NAME);
	        } else if($this->controller->Display->hasFeature($display,MsgGeneratorComponent::FEATURE_GRAND_RAPIDS_WEATHER)){
	            $temperature = $weather->getCurrentTempFarenheit(NoaaWeather::GRAND_RAPIDS_FEED_NAME);
	        }
	        
	        // get any special text to show
	        $appendText = null;
	        if( strlen($display['Display']['append_text'])>0 ){
	            //$appendText = $display['Display']['append_text'];
				$appendText = str_replace("\r"," ",$display['Display']['append_text']);
				$appendText = $this->fixOddLines($appendText);
	        }
	        
	        // merge all calendars together
	        // TODO: make this smarter so that they are all time ordered in the end
	        $eventText = array();
			if($this->controller->Display->hasFeature($display,MsgGeneratorComponent::FEATURE_BIRTHDAY_CAL)){
                $events = $birthCal->getUpcomingEvents($displayTimezone);
                $eventText = array_merge($eventText,$this->getEventTextList($events, $displayTimezone));
            }
	        if($this->controller->Display->hasFeature($display,MsgGeneratorComponent::FEATURE_DOTWELL_CAL)){
                $events = $dotwellCal->getUpcomingEvents($displayTimezone);
                $eventText = array_merge($eventText,$this->getEventTextList($events, $displayTimezone));
	        }
            if($this->controller->Display->hasFeature($display,MsgGeneratorComponent::FEATURE_FFPC_CAL)){
                $events = $ffpcCal->getUpcomingEvents($displayTimezone);
                $eventText = array_merge($eventText,$this->getEventTextList($events, $displayTimezone));
            }
            if($this->controller->Display->hasFeature($display,MsgGeneratorComponent::FEATURE_UNION_SQ_CAL)){
                $events = $unionSqCal->getUpcomingEvents($displayTimezone);
                $eventText = array_merge($eventText,$this->getEventTextList($events, $displayTimezone));
            }
            if($this->controller->Display->hasFeature($display,MsgGeneratorComponent::FEATURE_CIVIC_CAL)){
                $events = $civicCal->getUpcomingEvents($displayTimezone);
                $eventText = array_merge($eventText,$this->getEventTextList($events, $displayTimezone));
            }
            if($this->controller->Display->hasFeature($display,MsgGeneratorComponent::FEATURE_SCOUT_CAL)){
                $events = $scoutCal->getUpcomingEvents($displayTimezone);
                $eventText = array_merge($eventText,$this->getEventTextList($events, $displayTimezone));
            }
            if($this->controller->Display->hasFeature($display,MsgGeneratorComponent::FEATURE_GRAND_RAPIDS_CAL)){
                $events = $grCal->getUpcomingEvents($displayTimezone);
                $eventText = array_merge($eventText,$this->getEventTextList($events, $displayTimezone));
            }
			if($this->controller->Display->hasFeature($display,MsgGeneratorComponent::FEATURE_JP_PATCH_CAL)){
                $events = $patchCal->getUpcomingEvents($displayTimezone);
                $eventText = array_merge($eventText,$this->getEventTextList($events, $displayTimezone));
            }
			
            // get any transit information
	        foreach($display['Stop'] as $stop){
	
	        	$data = null;
	        	$dataIsOk = false;
	        	
	        	// query the approrpaite data source to get the data we want
	        	switch($stop['agency']){
	        		case Stop::AGENCY_MBTA_BUS:
	        		case Stop::AGENCY_MIT_SHUTTLE:
	                    $data = $nextBus->getPrediction($stop['agency'],$stop['route'],$stop['stop_key']);
	                    if($data!=NextBus::NO_PREDICTION_FOUND) {
	                        $dataIsOk = true;
	                    }
	                    break;
	        		case Stop::AGENCY_MBTA_T:
	        			$data = $mbtaRail->getPrediction($stop['route'],$stop['stop_key']);
	                    if($data!=MbtaRailTrain::NO_PREDICTION_FOUND) {
	                        $dataIsOk = true;
	                    }
	        			break;
	        	}
	        	
	        	if($dataIsOk==false){
	        		continue;
	        	}
	        	
	        	// assemble the text to show
	            $description = "";
	            if($stop['DisplayStop']['name']==null){
		        	switch($stop['agency']) {
		                case Stop::AGENCY_MBTA_BUS:
		                    $description.= "#".$stop['route_name']." (".$stop['direction_name'].") ";
		                	break;
		                case Stop::AGENCY_MIT_SHUTTLE:
		                	$description.= $stop['route_name']." ".$stop['direction_name']." ";
		                    break;
		                case Stop::AGENCY_MBTA_T:
		                    $description.= $stop['route']." Line ".$stop['direction_name']." ";
		                    break;
		        	}
	            } else {
	            	$description = $stop['DisplayStop']['name'];
	            }
	        	$meta = "";
	            if(intval($data)>0) {
	                $meta = $data." ";
	                $meta.= (($data>1) ? "mins " : "min ");
	            } else {
	                $meta = "Arriving "; 
	            }
	
	            // and add it to the list (to be sorted later)
	            $msgs['predictions'][] = intval($data);
	            $msgs['texts'][] = array('meta'=>$meta,'description'=>$description);
	        }
	
	        // stitch it all together (sort by arrival time, soonest first)
	        date_default_timezone_set( $displayTimezone );
	        $currentTime = date('g:ia');
            date_default_timezone_set('UTC');
	        array_multisort($msgs['predictions'], SORT_NUMERIC, $msgs['texts'], SORT_STRING);
	        
	        // prefix info
	        switch($display['Display']['hardware_type']){
                case Display::HW_TYPE_ONE_LINE:
                    $str = $currentTime." ||| ";
                    if($temperature!=NoaaWeather::NO_PREDICTION_FOUND){
                        $str.= $temperature."F ||| ";
                    }
                    break;
                case Display::HW_TYPE_TWO_LINE:
                    $str = $currentTime."\n".$display['Display']['city'];
                    if($temperature!=NoaaWeather::NO_PREDICTION_FOUND){
                        $str.= " ".$temperature."F";
                    }
                    $str.= "\n";
                    break;
	        }
	        
	        // extra custom text
	        if($appendText!=null){
	            $str.=$appendText;
    	        switch($display['Display']['hardware_type']){
                    case Display::HW_TYPE_ONE_LINE:
                        $str.=" ||| ";
                        break;
                    case Display::HW_TYPE_TWO_LINE:
                        $str.= "\n";
                        break;
                }
	        }
	        
	        // transit predictions
	        foreach($msgs['texts'] as $info) {
	        	switch($display['Display']['hardware_type']){
                    case Display::HW_TYPE_ONE_LINE:
                        $str.= $info['description']." ".$info['meta']." ||| ";
                        break;
                    case Display::HW_TYPE_TWO_LINE:
                        $str.= $info['meta']."\n".$info['description']."\n";
                        break;
	               }
	        }
	        
	        //events
	        foreach($eventText as $item){
                switch($display['Display']['hardware_type']){
                    case Display::HW_TYPE_ONE_LINE:
                        $str.= str_replace("\n"," ||| ",$item);
                        break;
                    case Display::HW_TYPE_TWO_LINE:
                        $str.= $item."\n";
                        break;
                }
	        }

        }
       
        // and save/log it
        $this->log("    ".$str,LOG_DEBUG);
        $display['Display']['text'] = $str;
        $display['Display']['last_text_update'] = $nowStr;
        $this->controller->Display->save($display['Display']);    	

    }

}

?>
