<?php
App::import('Xml'); 

/**
 * A CakePHP class to get predictions from NextBus 
 * @author rahulb
 *
 */
class NextBus {

	const NO_PREDICTION_FOUND = -99999;
	const QUERY_BASE_URL = "http://webservices.nextbus.com/service/publicXMLFeed";
	const CACHE_KEY_PREFIX = 'nextbus';
	
	public function NextBus(){
	}
	
	private function getCacheKey($agency,$route,$stop){
		return NextBus::CACHE_KEY_PREFIX."_".$agency."_".$route."_".$stop;
	}
	
    private function getQueryUrl($agency,$route,$stop) {
    	return NextBus::QUERY_BASE_URL."?command=predictions&a=".$agency.
                "&r=".$route."&s=".$stop;
    }
	
    /**
     * This manages caching for you, and presumes you have already cleared the cache if you want
     * new data to be fetched.
     * @param unknown_type $agency
     * @param unknown_type $route
     * @param unknown_type $stop
     */
    private function getRawDataAsArray($agency,$route,$stop) {
    	$cacheKey = $this->getCacheKey($agency,$route,$stop);
    	$cached = Cache::read($cacheKey, 'predictions');
    	$xmlAsArray = null;
    	if($cached==false){
	    	$url = $this->getQueryUrl($agency,$route,$stop);
	        $xmlStr = file_get_contents($url);
	        $parsed_xml = new Xml($xmlStr);
	        $xmlAsArray = Set::reverse($parsed_xml);
	        Cache::write($cacheKey, $xmlAsArray, 'predictions');
    	} else {
            $xmlAsArray = $cached;
        }	        
        return $xmlAsArray;
    }
    
	public function getPrediction($agency,$route,$stop) {
        $data = $this->getRawDataAsArray($agency,$route,$stop);

        // make sure the data is there
        if(!array_key_exists("Predictions",$data['Body'])){
            return NextBus::NO_PREDICTION_FOUND;
        }
        if(array_key_exists("dirTitleBecauseNoPredictions",$data['Body']['Predictions'])){
            return NextBus::NO_PREDICTION_FOUND;
        }
        if(!array_key_exists('Prediction',$data['Body']['Predictions']['Direction'])){
            return NextBus::NO_PREDICTION_FOUND;
        }
        if(!array_key_exists(0,$data['Body']['Predictions']['Direction']['Prediction'])){
            return NextBus::NO_PREDICTION_FOUND;
        }
        
        // pull out the predicted arrival in minutes
        $predictionInfo = $data['Body']['Predictions']['Direction']['Prediction'][0];
        $mins = $predictionInfo['minutes'];

        return $mins;
    }
            
}