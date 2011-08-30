<?php
App::Import('Lib','NextBus');
App::Import('Model','Stop');

/**
 * Call this to intialize the database with stops from a nextbus "agency"
 * > cake populate_nextbus_stops
 * @author rahulb
 *
 */
class PopulateNextbusStopsShell extends Shell {
       
    function main() {
        $this->Stop = new Stop(); 
        $nextBus = new NextBus();
        $agency = NextBus::AGENCY_MBTA;
        $existingStops = 0;
        $newStops = 0;
        $this->out("Querying agency ".$agency.": ");
        $routeList = $nextBus->getRouteList($agency);
        foreach($routeList as $info){
            $route = $info['tag'];
            $routeName = $info['title'];
            $this->out("  route ".$routeName);
            $routeConfig = $nextBus->getRouteConfig($agency,$route);
            $routeStopsByTag = array();
            foreach($routeConfig['Stop'] as $stop){
                $routeStopsByTag[$stop['tag']] = $stop;
            }
            foreach($routeConfig['Direction'] as $dir){
                if(!is_array($dir)) continue;
                if(array_key_exists('Stop',$dir)){
                    $dirId = $dir['tag'];
                    $dirName = $dir['title'];
                    foreach($dir['Stop'] as $stopRef){
                        $stop = $routeStopsByTag[$stopRef['tag']];
                        $stopKey = $stop['tag'];
                        $stopName = $stop['title'];
                        $newStop = array(
                            'stop_key'=> $stopKey,
                            'name'=> $stopName,
                            'agency'=> $agency,
                            'route'=> $route,
                            'route_name'=> $routeName,
                            'direction'=> $dirId,
                            'direction_name'=> $dirName,
                        );
                        $existingStop = $this->Stop->findByStopKeyAndRouteAndDirection(
                            $newStop['stop_key'], $newStop['route'], $newStop['direction']);
                        if($existingStop) {
                            $existingStops++;
                        } else {
                            $newStops++;
                            $this->Stop->create();
                            $this->Stop->save($newStop);
                        }
                    }                
                } else {
                    $this->out("    Couldn't find any stops for route ".$route.", direction ".$dir."!");
                }
            }
        }
        $this->out("Found ".$existingStops." existing, ".$newStops." new stops");
    }

}

?>