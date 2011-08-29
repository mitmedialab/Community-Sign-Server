<?php

/**
 * This manages the wizard UI for selecting a transit stop to add to a display.
 * @author rahulb
 *
 */
class StopsController extends AppController {

	var $name = 'Stops';

	var $uses = array('Stop','Display','DisplayStop');
	
    var $components = array('MsgGenerator');
		
    var $scaffold;
    
    var $paginate = array(
           'Stop'=>array(
                'limit' => 100,
                'order' => array('Stop.agency','Stop.route_name','Stop.direction_name','Stop.name'),
                )
        );

    public function view($id) {
    	$stop = $this->Stop->FindById($id);
    	$this->set("stop",$stop);    	
    }
        
    public function edit($id) {
        $this->Stop->id = $id;
        if (empty($this->data)) {
            $this->data = $this->Stop->read();
            $this->set('stop',$this->data);
        } else {
            $this->set('stop',$this->Display->read());
            if ($this->Stop->save($this->data)) {
                $this->Session->setFlash(__('This stop has been updated.',true));
                $this->redirect(array('action' => 'view',$id));
            }
        }
    }
            
    public function search() {
    	$name = $this->data['Stop']['name'];
        $this->paginate['Stop']['limit'] = 1000;
    	$this->paginate['Stop']['conditions'][] = array(
                'OR' => array(
                    'Stop.name LIKE' => "%$name%",
                )
            );
        $stopList = $this->paginate('Stop');
        $this->set('stopList',$stopList);
        $this->set('searchTerm',$name);
    }
        
    public function index() {
    	$stopList = $this->paginate('Stop');
        $this->set('stopList',$stopList);
    }	
	
    public function pick($displayId){
    	    	
    	$display = $this->Display->FindById($displayId);
    	$this->set('display',$display);
    	
    	// build the agency list
    	$rawAgencyList = $this->Stop->Find('all',array(
    	   'fields'=>array('DISTINCT Stop.agency'),
    	));
    	$agencyList = array();
    	foreach($rawAgencyList as $item){
    		$agencyList[ $item['Stop']['agency'] ] = $item['Stop']['agency'];
    	}
        $this->set('agencyList',$agencyList);
        $selectedAgency = "";
        if(isset($this->data['Agency'])) {
            $selectedAgency = $this->data['Agency'];
        }
        $this->set('selectedAgency',$selectedAgency);
        
        // now build the route list
        $routeList = array();
        if(!empty($selectedAgency)){
	        $rawRouteList = $this->Stop->Find('all',array(
	           'fields'=>array('DISTINCT Stop.route_name'),
	            'conditions'=>array('Stop.agency'=>$selectedAgency)
	        ));
	        $routeList = array();
	        foreach($rawRouteList as $item){
	            $routeList[ $item['Stop']['route_name'] ] = $item['Stop']['route_name'];
	        }
	        $selectedRoute = "";
	        if(isset($this->data['Route'])) {
	            $selectedRoute = $this->data['Route'];
	        }
	        $this->set('selectedRoute',$selectedRoute);
        }
        $this->set('routeList',$routeList);
                       
        // now build the direction list
        $directionList = array();
        if(!empty($selectedRoute)){
            $rawDirectionList = $this->Stop->Find('all',array(
               'fields'=>array('DISTINCT Stop.direction_name'),
                'conditions'=>array('Stop.agency'=>$selectedAgency, 'Stop.route_name'=>$selectedRoute)
            ));
            $directionList = array();
            foreach($rawDirectionList as $item){
                $directionList[ $item['Stop']['direction_name'] ] = $item['Stop']['direction_name'];
            }
            $selectedDirection = "";
            if(isset($this->data['Direction'])) {
                $selectedDirection = $this->data['Direction'];
            }
            $this->set('selectedDirection',$selectedDirection);
        }
        $this->set('directionList',$directionList);        
        
        // now build the stop list
        $stopList = array();
        if(!empty($selectedDirection)){
            $stopList = $this->Stop->Find('list',array(
                'conditions'=>array('Stop.agency'=>$selectedAgency, 'Stop.route_name'=>$selectedRoute,
                                    'direction_name'=>$selectedDirection)
            ));
            $selectedStop = "";
            if(isset($this->data['Stop'])) {
                $selectedStop = $this->data['Stop'];
            }
            $this->set('selectedStop',$selectedStop);
        }
        $this->set('stopList',$stopList);                

        // is it time to save it?
        if($this->data['done']==1){
        	// save the new stop/display association
        	$newStopData = array(
        	   'display_id'=>$displayId,
        	   'stop_id'=>$selectedStop
        	);
            $this->DisplayStop->save($newStopData);
        	$this->Session->setFlash("Added a new stop to ".$display['Display']['name'].", and fetched the latest predictions");
        	// and update, for good measure
        	$this->MsgGenerator->updateDisplayText($displayId);
        	// redirect to homepage
        	$this->redirect(array('controller'=>'displays'));
        }
        
    }
    
}
?>