<?php
App::import('Xml'); 

/**
 * A CakePHP class to interface to the NextBus Transit API.  This uses some caching for you
 * in order to limit queries to their server.
 * @author rahulb
 *
 */
class NextBus {

	const NO_PREDICTION_FOUND = -99999;
	const QUERY_BASE_URL = "http://webservices.nextbus.com/service/publicXMLFeed";
	const CACHE_KEY_PREFIX = 'nextbus_';
	
	const AGENCY_MBTA = 'mbta';
	
	const CMD_ROUTE_LIST = "routeList";
	const CMD_ROUTE_CONFIG = "routeConfig";
	const CMD_PREDICTONS = "predictions";
	const CMD_MULTISTOP_PREDICTIONS = "predictionsForMultiStops";
	const CMD_VEHICLE_LOCATIONS = "vehicleLocations";
	
	public function NextBus(){
	}
	
	private function getQueryBaseUrl($command,$agency){
	    return NextBus::QUERY_BASE_URL."?command=".$command."&a=".$agency;
	}
	
    
    /**
     * This manages caching for you.
     */
	private function fetchUrlOrCachedAsArray($url,$cacheKey,$forceRefresh=false){
        $cached = Cache::read($cacheKey,'predictions');
        $xmlAsArray = null;
        if($cached==false || $forceRefresh){
            $xmlStr = file_get_contents($url);
            $parsedXml = new XML($xmlStr);
            $xmlAsArray = Set::reverse($parsedXml);
            Cache::write($cacheKey, $xmlAsArray, 'predictions');
        } else {
            $xmlAsArray  = $cached;
        }   
        return $xmlAsArray;   
	}
	    
	public function getPrediction($agency,$route,$stop) {
	    $cacheKey = NextBus::CACHE_KEY_PREFIX.$agency."_".$route."_".$stop;
	    $url = $this->getQueryBaseUrl(NextBus::CMD_PREDICTONS,$agency).
                "&r=".$route."&s=".$stop;
        $data = $this->fetchUrlOrCachedAsArray($url,$cacheKey);

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

    public function getRouteList($agency){
        $cacheKey = NextBus::CACHE_KEY_PREFIX.$agency."_route_list";
        $url = $this->getQueryBaseUrl(NextBus::CMD_ROUTE_LIST,$agency);
        $data = $this->fetchUrlOrCachedAsArray($url,$cacheKey);
        return $data['Body']['Route'];
    }
    
    public function getRouteConfig($agency,$route){
        $cacheKey = NextBus::CACHE_KEY_PREFIX.$agency."_route_config_".$route;
        $url = $this->getQueryBaseUrl(NextBus::CMD_ROUTE_CONFIG,$agency).
                "&r=".$route;
        $data  = $this->fetchUrlOrCachedAsArray($url,$cacheKey);
        return $data['Body']['Route'];
    }
    
}