<?php
/**
 * This is the meat of where you setup and manage displays.  Displays are individual
 * installations of community signs.  Each can be assigned different transit stops
 * and/or different "features".
 * 
 * @author rahulb
 */
class DisplaysController extends AppController {

	var $name = 'Displays';
    var $uses = array('Display','DisplayStop','DisplayStatus','Feature','DisplayFeature','Group');
    var $components = array('MsgGenerator');
	
    var $paginate = array(
           'DisplayStatus'=>array(
                'limit' => 50,
                'order' => array('DisplayStatus.changed'=>'DESC'),
                )
        );
        
    public function index() {
		$displayList = $this->Display->Find('all',array('order'=>array('Display.name'=>'asc')));
		foreach($displayList as &$display){
			$display['DisplayStatusReboot'] = $this->DisplayStatus->lastBoot($display['Display']['id']);
		}
		$this->set('displayList',$displayList);
	}

	public function delete($id) {
		$this->Display->delete($id,false);
		$this->DisplayStop->deleteAll(array('display_id'=>$id));
		$this->DisplayStatus->deleteAll(array('display_id'=>$id));
        
		$this->Session->setFlash(__('Your display has been deleted.',true));
		$this->redirect(array('action' => 'index'));
	}
	
    public function add() {
        if (!empty($this->data)) {
        	$this->data['Display']['status'] = Display::STATUS_UNKNOWN;
        	$this->set('display',$this->Display->read());
            if ($this->Display->save($this->data)) {
            	// update the status history, if the status has changed
                $this->DisplayStatus->saveIfChanged($this->Display->id,$this->data['Display']['status']);
            	// redirect user
            	$this->Session->setFlash(__('Your display has been created.',true));
                $this->redirect(array('action' => 'index'));
            }
        }
    }	
	
	public function edit($id = null) {
	    $this->Display->id = $id;
	    if (empty($this->data)) {
	        $this->data = $this->Display->read();
	        $this->set('display',$this->data);
            $featureList = $this->Feature->find('list');
            $this->set('features',$featureList);
			$groupList = $this->Group->find('list');
			$this->set('groups',$groupList);
	    } else {
	    	$this->set('display',$this->Display->read());
	        if ($this->Display->save($this->data)) {
	            $this->Session->setFlash(__('Your display has been updated.',true));
	            $this->redirect(array('action' => 'index'));
	        }
	    }
	}
	
	public function updateText($displayId){
		$this->MsgGenerator->updateDisplayText($displayId);
        $this->redirect(array('action' => 'index'));
	}
	
	public function removeStop($displayStopId){
		$this->DisplayStop->delete($displayStopId);
		$this->redirect(array("action"=>'index'));
	}
	
	public function statusHistory($id){
		
		$display = $this->Display->FindById($id);
		$this->set('display',$display);
		
		$statusList = $this->paginate('DisplayStatus', array( 'DisplayStatus.display_id' => $id));
/*		$statusList = $this->DisplayStatus->Find('all', array('conditions'=>array('display_id'=>$id),
          'order'=>array('changed'=>'DESC')
        ) );*/
        $this->set('statusList',$statusList);
        
	}
	
	#For signs running xml
	public function restart($displayId){
		$display = $this->Display->findById($displayId);
		$display['Display']['restart'] = 1;
		$this->Display->save($display);
		$this->redirect(array("action"=>'index'));
	}
}
?>