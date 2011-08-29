<?php
App::import('Xml'); 

/**
 * A CakePHP class to get current temperature (hardcoded to Boston for now)
 * http://www.weather.gov/xml/current_obs/seek.php?state=ma&Find=Find
 * @author rahulb
 *
 */
class NoaaWeather {

	const NO_PREDICTION_FOUND = -99999;
	const QUERY_BASE_URL = "http://www.weather.gov/xml/current_obs/";
	const BOSTON_FEED_NAME = "KBOS.xml";
	const GRAND_RAPIDS_FEED_NAME = "KISW.xml";
	const CACHE_KEY_PREFIX = 'weather';
	
	public function NoaaWeather(){
	}
	
    private function getCacheKey($feed){
    	$feedWithoutExtension = substr($feed, 0, strrpos($feed, '.')); 
        return NoaaWeather::CACHE_KEY_PREFIX."_".$feedWithoutExtension;
    }

    private function getQueryUrl($feed) {
    	return NoaaWeather::QUERY_BASE_URL.$feed;
    }
	
    /**
     * Return current temp, or NO_PREDICTION_FOUND if it is invalid
     */
	public function getCurrentTempFarenheit($feed=null) {
	    if($feed==null){
    		$feed = NoaaWeather::BOSTON_FEED_NAME;
	    }
		$cacheKey = $this->getCacheKey($feed);
		$cached = Cache::read($cacheKey, 'predictions');
		if($cached==false){
	        $url = $this->getQueryUrl($feed);
	        $xmlStr = file_get_contents($url);
	        $parsed_xml = new Xml($xmlStr);
	        $data = Set::reverse($parsed_xml);
	        Cache::write($cacheKey, $data, 'predictions');
		} else {
			$data = $cached;
		}

		$temp = NoaaWeather::NO_PREDICTION_FOUND;
        
        if(array_key_exists('temp_f',$data['CurrentObservation'])){
        	$temp = round($data['CurrentObservation']['temp_f']);
        }

       if( ($temp > -100) && ($temp < 120) ) {
            return $temp;
        } else {
            return NoaaWeather::NO_PREDICTION_FOUND;
        }
        
	}
            
}