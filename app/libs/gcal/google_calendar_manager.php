<?php
App::import('Lib','DateUtils');

/**
 * Fetch upcoming events from a google calendar.  This does some hacks
 * around timezone issues.
 * @author rahulb
 */
class GoogleCalendarManager{
    
    public function GoogleCalendarManager(){
    }
    
    /**
     * Get a list of all the upcoming events (between $from and $until).  
     * These event times returned are GMT unix timestamps.  You may want to screen
     * out events that have already started afterwards.
     * @param $feedUrl              the gcal url to parse 
     * @param $from                 find events that start after this unix timestamp
     * @param $until                find events that end before this unix timestamp
     * @param $stringReplacements   replace any strings in the summary and description (TODO: make these regexs)
     * @param $tz                   the name of the timezone this calendar is in
     * @param $src                  the 'category' of the calendar
     */
    public function getEvents($feedUrl, $from, $until=null, $stringReplacements=array(),$tz=null,$src=null){
        if($until==null){
            $until = $from + 100*365*24*60*60;    // from now till 100 years from now (ie. forever)
        }
        $tzOffset = DateUtils::getTimezoneOffset($tz);
        $events = array();

        // fetch the events in the timespan from Google
        // http://code.google.com/apis/calendar/data/2.0/reference.html#Parameters
        $params = array(
            "orderby"=>"starttime",
            "singleevents"=>"true",
            "start-min"=>DateUtils::TimestampToDateRfc3339($from+$tzOffset),
            "start-max"=>DateUtils::TimestampToDateRfc3339($until+$tzOffset),
            "sortorder"=>"ascending"
        );
        $url = $feedUrl."?".http_build_query($params);
        $xml = simplexml_load_file($url);
        // parse the xml into a nicer assoc array
        // http://www.ibm.com/developerworks/opensource/library/os-php-xpath/#N102E6
        foreach($xml->entry as $item){
            
            $gd = $item->children('http://schemas.google.com/g/2005');  // google calendar event info space
            
            // grab the basic info
            $summary = $item->title;
            foreach($stringReplacements as $search=>$replace){
                $summary = str_replace($search,$replace,$summary);
            }
            $description = $item->content;
            foreach($stringReplacements as $search=>$replace){
                $description = str_replace($search,$replace,$description);
            }
            $location = $gd->where->attributes()->valueString; 
            
            // figure out the times
            $recurring = false;
            if ( $gd->when ) {
                $startTime = $gd->when->attributes()->startTime;
                $endTime = $gd->when->attributes()->endTime;
            } elseif ( $gd->recurrence ) {
                $startTime = $gd->recurrence->when->attributes()->startTime;
                $endTime = $gd->recurrence->when->attributes()->endTime;
                $recurring = true; 
            }
            $startTime = strtotime($startTime);
            $endTime = strtotime($endTime);
            $ongoing = ($startTime<time()) && ($endTime>time());            

            // save the upcoming event info in an easier format
            $eventInfo = array(
              'summary'=>stripslashes(trim($summary)),
              'description'=>stripslashes($description),
              'location'=>stripslashes(trim($location)),
              'startdate'=>$startTime,
              'enddate'=>$endTime,
              'fullday'=> (($endTime-$startTime)==24*60*60),
              'ongoing'=>$ongoing,
              'timezone'=>$tz,
              'recurring'=>$recurring,
              'src'=>$src,
            );

            $events[] = $eventInfo;
        }

        usort($events, "datedEventCompare");    // safety - make sure they are chronological
        return $events;
    }   

   
    
}

/**
 * Helper to sort arrays by a subitem called "startdate"
 * @param unknown_type $a
 * @param unknown_type $b
 */
function datedEventCompare($a, $b){
    $aDate = $a['startdate'];
    $bDate = $b['startdate']; 
    if($aDate==$bDate) return 0;
    return ($aDate<$bDate) ? -1 : 1;
}

?>