<?php
App::import('Xml'); 

/**
 * A CakePHP class to get predictions from the MBTA Rail Feeds:
 * http://www.eot.state.ma.us/default.asp?pgid=content/developer&sid=about#transit
 * @author rahulb
 *
 */
class MbtaRailTrain {

	const NO_PREDICTION_FOUND = -99999;
	const QUERY_BASE_URL = "http://developer.mbta.com/Data/";
	const INFO_TYPE_PREDICTED = "Predicted";   // vs Arrived in result data
    const CACHE_KEY_PREFIX = 'mbta';
	
	public function MbtaRailTrain(){
	}
	
    private function getCacheKey($route){
        return MbtaRailTrain::CACHE_KEY_PREFIX."_".$route;
    }

    private function getQueryUrl($route) {
    	return MbtaRailTrain::QUERY_BASE_URL.$route.".xml";
    }
	
	public function getPrediction($route,$stop) {

	    $cacheKey = $this->getCacheKey($route);
		$cached = Cache::read($cacheKey, 'predictions');
		if($cached==false){
	        $url = $this->getQueryUrl($route);
	        $xmlStr = file_get_contents($url);
	        $parsed_xml = new Xml($xmlStr);
	        $data = Set::reverse($parsed_xml);
	        Cache::write($cacheKey, $data, 'predictions');
		} else {
			$data = $cached;
		}
		
        $mins = MbtaRailTrain::NO_PREDICTION_FOUND;

        // read this for more info: http://developer.mbta.com/RT_Archive/DataExplained.txt
        foreach($data['Root'][$route] as $item){
        	if($mins!=MbtaRailTrain::NO_PREDICTION_FOUND){
        		continue; // only grab the first result
        	}
        	if($item['PlatformKey']==$stop && $item['InformationType']==MbtaRailTrain::INFO_TYPE_PREDICTED){
        		//pr($item);
        		$parts = explode(":",$item['TimeRemaining']);
        		$mins = intval($parts[1])+round(intval($parts[2])/60);
        	}
        }

        return $mins;
	}
            
}