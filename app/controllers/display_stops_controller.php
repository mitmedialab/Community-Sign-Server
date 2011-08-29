<?php

/**
 * This lets you edit the descriptive text for a transit stop on each display.  So 
 * instead of saying the default text like: 
 *  "mbta 1 (Harvard Station via Mass. Ave.) Massachusetts Ave @ Vassar St"
 * you can make it say:
 *  "#1 Harvard"
 * @author rahulb
 */
class DisplayStopsController extends AppController {

	var $name = 'DisplayStops';

   public function edit($id = null) {
        $this->DisplayStop->id = $id;
        if (empty($this->data)) {
            $this->data = $this->DisplayStop->read();
            $this->set('displayStop',$this->data);
        } else {
            $this->set('displayStop',$this->DisplayStop->read());
            if ($this->DisplayStop->save($this->data)) {
                $this->Session->setFlash(__('Your display stop text has been updated.',true));
                $this->redirect(array('controller'=>'displays','action' => 'index'));
            }
        }
    }
	
}
?>