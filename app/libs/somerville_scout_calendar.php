<?php

require_once("abstract_calendar.php");

define("MAPS_HOST", "maps.google.com");
//TODO: move this key into the config file 
define("MAPS_API_KEY", "ABQIAAAAfve8GAScYqZqRMVWLBj2ExRZAU8QRX3o2gMjxRo");

/**
 * A CakePHP class to get upcoming events from the Somerville Scout calendar - this
 * filters the events by a shape I drew around Union Sq. 
 * TODO: move location checking into an abstract base class and have this inherit from that
 * TODO: comment
 * @author rahulb
 */
class SomervilleScoutCalendar extends AbstractCalendar{
    
    // lng, lat
    private $boundaryPolygonPoints = array(
        array(-71.105661392212 , 42.380865195823),
        array(-71.096949577331 , 42.386222390234),
        array(-71.091070175171 , 42.376046500333),
        array(-71.089954376221 , 42.369515309709),
        array(-71.087465286255 , 42.367929966838),
        array(-71.092700958252 , 42.365837252983),
        array(-71.104803085327 , 42.368944590885),
        array(-71.105661392212 , 42.380865195823),
    );
    
    private $lngLat;
    private $geocodeDelay = 0;
    
    public function SomervilleScoutCalendar(){
        $this->geocodeDelay = 0;
        $this->lngLat = $this->getlngLatCache();
    }
    
    protected function getlngLatCache() {
        $cacheKey = $this->getlngLatCacheKeyPrefix();
        $cached = Cache::read($cacheKey, 'calendars');
        if($cached!=false){
            return $cached;
        }
        return array();
    }    
    
    protected function setlngLatCache(){
        $cacheKey = $this->getlngLatCacheKeyPrefix();
        Cache::write($cacheKey, $this->lngLat, 'calendars');
    }
    
    protected function getCalendarUrl(){
        return array(
            "children"=>"https://www.google.com/calendar/feeds/somervillescout.com_cov73q65ovj8l7bg3ugiahhdk0%40group.calendar.google.com/public/full",
            "classes"=>"https://www.google.com/calendar/feeds/somervillescout.com_kj0adk9t7l9f40l2cpifpggdgs%40group.calendar.google.com/public/full",
            "clubs"=>"https://www.google.com/calendar/feeds/somervillescout.com_dn7scoe1230ot1a4203j7rjj30%40group.calendar.google.com/public/full",
            "meetings"=>"https://www.google.com/calendar/feeds/somervillescout.com_vjeq5dhut809rkftb0i56bafmg%40group.calendar.google.com/public/full",
            "sports"=>"https://www.google.com/calendar/feeds/somervillescout.com_43rvt8cakr1o1urjscsms1khvk%40group.calendar.google.com/public/full",
            "events"=>"https://www.google.com/calendar/feeds/somervillescout.com_9ou2gnvd49k3e86avnamp5olt4@group.calendar.google.com/public/full"
            );
    }
    
    protected function getlngLatCacheKeyPrefix(){
        return $this->getCacheKeyPrefix()."_lnglat";
    }
    
    protected function getCacheKeyPrefix(){
        return "somervillescout";
    }

    protected function getStringReplacements(){
        return array(
            "Third Life Studio 33 Union Square (Somerville Ave.)"=> "Third Life Studio",
            "Third Life Studio 33 Union Sq. Somerville"=>"Third Life Studio",
            "Third Life Studio 33 Union Square"=>"Third Life Studio",
            "Somerville Community Growing Center"=>"Growing Center",
        );
    }
    
    protected function postProcessEvents($events,$tzOffset){
        $validEvents = array();
        // determine latlon of each location
        $locations = array();
        foreach($events as $e){
            $isValid = $this->isValidEvent($e);
            //print $e['summary']." @ ".$e['location']." - ".$isValid."\n"; 
            if($isValid){
                $validEvents[] = $e;
            }
        }
        //pr($this->lngLat);
        $this->setlngLatCache();    // write out any updates
        return $validEvents;
    }
    
    protected function isValidEvent($e){
        $locInfo = $this->getLocInfo($e['location']);
        if ($locInfo==null){
            CakeLog::write('debug', "Unable to geocode " . $e['summary'] . " with address " . $e['location']);
            return false;
        }
        return $locInfo['region']!='outside';
    }
    
    protected function getLocInfo($address){
        // check the cache first
        $locParts = explode(",",$address); 
        $name = $locParts[0];
        $pointLocation = new pointLocation();
        if(!array_key_exists($name,$this->lngLat)){
            $lngLat = $this->geocode($address);
            if($lngLat!=null) {
                $region = $pointLocation->pointInPolygon($lngLat,$this->boundaryPolygonPoints);
                $info = array(
                    'lng'=>$lngLat[0],
                    'lat'=>$lngLat[1],
                    'region'=>$region,    
                );
                $this->lngLat[$name] = $info;
            }
        }
        if(array_key_exists($name,$this->lngLat)){
            return $this->lngLat[$name];
        }
        return null;
    }
    
    protected function geocode($address){
        $baseUrl = "http://" . MAPS_HOST . "/maps/geo?output=xml" . "&key=" . MAPS_API_KEY;
        $geocodePending = true;
        $attempts = 3;
        while ($geocodePending && ($attempts<10)) {
            $requestUrl = $baseUrl . "&q=" . urlencode($address);
            $xml = simplexml_load_file($requestUrl);
            $status = $xml->Response->Status->code;
            if (strcmp($status, "200") == 0) {
                $geocodePending = false;
                $coordinates = $xml->Response->Placemark->Point->coordinates;
                $coordinatesSplit = split(",", $coordinates);
                // Format: Longitude, Latitude, Altitude
                $lat = $coordinatesSplit[1];
                $lng = $coordinatesSplit[0];
                return array($lng,$lat);
            } else if (strcmp($status, "620") == 0) {
                // sent geocodes too fast
                $this->geocodeDelay += 100000;
            } else {
                $geocodePending = false;
            }
            usleep($this->geocodeDelay);
            $attempts++;
        }
        return null;
    }
    
}



//  http://www.assemblysys.com/dataServices/php_pointinpolygon.php
class pointLocation {
    var $pointOnVertex = true; // Check if the point sits exactly on one of the vertices

    function pointLocation() {
    }
    
    
        function pointInPolygon($point, $polygon, $pointOnVertex = true) {
        $this->pointOnVertex = $pointOnVertex;
        
        // Transform string coordinates into arrays with x and y values
        $point = $this->lngLatToCoordinates($point);
        $vertices = array(); 
        foreach ($polygon as $vertex) {
            $vertices[] = $this->lngLatToCoordinates($vertex); 
        }
        
        // Check if the point sits exactly on a vertex
        if ($this->pointOnVertex == true and $this->pointOnVertex($point, $vertices) == true) {
            return "vertex";
        }
        
        // Check if the point is inside the polygon or on the boundary
        $intersections = 0; 
        $vertices_count = count($vertices);
    
        for ($i=1; $i < $vertices_count; $i++) {
            $vertex1 = $vertices[$i-1]; 
            $vertex2 = $vertices[$i];
            if ($vertex1['y'] == $vertex2['y'] and $vertex1['y'] == $point['y'] and $point['x'] > min($vertex1['x'], $vertex2['x']) and $point['x'] < max($vertex1['x'], $vertex2['x'])) { // Check if point is on an horizontal polygon boundary
                return "boundary";
            }
            if ($point['y'] > min($vertex1['y'], $vertex2['y']) and $point['y'] <= max($vertex1['y'], $vertex2['y']) and $point['x'] <= max($vertex1['x'], $vertex2['x']) and $vertex1['y'] != $vertex2['y']) { 
                $xinters = ($point['y'] - $vertex1['y']) * ($vertex2['x'] - $vertex1['x']) / ($vertex2['y'] - $vertex1['y']) + $vertex1['x']; 
                if ($xinters == $point['x']) { // Check if point is on the polygon boundary (other than horizontal)
                    return "boundary";
                }
                if ($vertex1['x'] == $vertex2['x'] || $point['x'] <= $xinters) {
                    $intersections++; 
                }
            } 
        } 
        // If the number of edges we passed through is even, then it's in the polygon. 
        if ($intersections % 2 != 0) {
            return "inside";
        } else {
            return "outside";
        }
    }

    
    
    function pointOnVertex($point, $vertices) {
        foreach($vertices as $vertex) {
            if ($point == $vertex) {
                return true;
            }
        }
    
    }

    function lngLatToCoordinates($coordinates) {
        return array("x" => $coordinates[0], "y" => $coordinates[1]);
    }
    
    
}
