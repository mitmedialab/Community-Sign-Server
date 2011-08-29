<?php
/**
 * Public method are filed in here for now.  We can use routing later to make prettier
 * URLs.  For instance, this is where the signs hit to get their content for display.
 * 
 * @author rahulb
 *
 */
class ServicesController extends AppController {

	var $name = 'Services';
	//var $scaffold;
	var $uses = array('Display','DisplayStatus','User','Group');
	var $authenticates = false;
	var $components = array('Email', 'MsgGenerator');

	/**
	 * Old method of retriving the info for each sign - this is to be deprecated soon
	 * in favour of the XML method.
	 */
	public function current_text(){
        $this->layout = 'ajax';
        
        // parse args
        $serial = null;
        $secret = null;
        $status = null;
        $version = null;
        if(isset($this->params['url']['serial'])) $serial = $this->params['url']['serial'];
        if(isset($this->params['url']['secret'])) $secret = $this->params['url']['secret'];
        if(isset($this->params['url']['status'])) $status = $this->params['url']['status'];
        if(isset($this->params['url']['version'])) $version = $this->params['url']['version'];
        
        if( empty($serial) && empty($secret) && empty($status) ) {
        	
            // if no args show error msg
        	$this->set( 'msg', __("Server Error: Bad Request") );
        	$this->log("Error: didn't get enough args from the client");
        	
        } else {
        	
        	// grab the display current text and return it
            $this->Display->contain();
        	$display = $this->Display->FindBySerialNumAndSecret($serial,$secret);
            
            if($display) {
            	$this->log("Request from display ".$display['Display']['name'],LOG_DEBUG);
            	
            	$this->set('msg',$display['Display']['text']);
            	
            	//$this->log($display['Display']['text'],LOG_DEBUG);

            	// and update the last request time and ip
                $remoteIpAddr = $this->RequestHandler->getClientIP();
            	$display['Display']['last_ip'] = $remoteIpAddr;
            	$display['Display']['status'] = $status;
                $display['Display']['version'] = $version;
            	$display['Display']['last_request'] = date("Y:m:d H:i:s");
            	$this->Display->save($display);
            	
            	// update the status history, if the status has changed
            	$hasChanged = $this->DisplayStatus->saveIfChanged($display['Display']['id'],$status);
            	
            	if($hasChanged){
            		$badStates = array(Display::STATUS_SIGN_COMMS_ERROR, Display::STATUS_UNKNOWN,
            		                  /*Display::STATUS_SERVER_CONNECT_ERROR,*/ Display::STATUS_BLANKED_DISPLAY);            		
            		// send email alert if bad state and marked to send email alerts
            		if(in_array($status,$badStates) && $display['Display']['send_error_alerts']){
            			App::import('Helper', 'LostInBoston');
                        $this->LostInBoston= new LostInBostonHelper();
            			$statusText = $this->LostInBoston->rawStatusText($status);
            			// notify people
		                $this->Email->from    = Configure::read('EmailFromAddress');
		                $this->Email->to      = implode(',', $this->User->emailsToSendErrorAlerts());
                        $extraEmails = $display['Display']['error_alert_emails'];     // grab extra custom emails
		                if(strlen(trim($extraEmails))>0){
    		                $this->Email->to .= ",".$extraEmails;
		                }
		                $this->Email->subject = '['.Configure::read('ServerName').'] '.
		                                        $display['Display']['name'].' '.$statusText;
		                $body = "The ".$display['Display']['name']." display reported it status as ".$statusText.
		                          ".  Consider yourself warned.";
		                $worked = $this->Email->send($body);
		                $this->log("Sent an email because the display ".$display['Display']['name']." entered a bad state of ".$status,LOG_DEBUG);
            		}
            	}
            	
            } else {
            	// if unknown display send back error msg and log it
            	$this->set('msg',__("Server Error: Unknown Display"));
            	$this->log("got request from unknown display $serial / $secret");
            }
        }
        
    }

    /**
     * New method of retrieving the content to show on a sign
     */
	public function current_xml(){
        $this->layout = 'ajax';
        
        // parse args
        $serial = null;
        $secret = null;
        $status = null;
        $version = null;
        if(isset($this->params['url']['serial'])) $serial = $this->params['url']['serial'];
        if(isset($this->params['url']['secret'])) $secret = $this->params['url']['secret'];
        if(isset($this->params['url']['status'])) $status = $this->params['url']['status'];
        if(isset($this->params['url']['codeVersion'])) $codeVersion = $this->params['url']['codeVersion'];
		if(isset($this->params['url']['protocolVersion'])) $protocolVersion = $this->params['url']['protocolVersion'];
        
        if( empty($serial) && empty($secret) && empty($status) ) {
        	
            // if no args show error msg
        	$this->set( 'msg', __("Server Error: Bad Request") );
        	$this->log("Error: didn't get enough args from the client");
        	
        } else {
        	
        	// grab the display current text and return it
            $this->Display->contain();
        	$display = $this->Display->FindBySerialNumAndSecret($serial,$secret);
            
            if($display) {
				//Check if sign needs to restart
				$shouldRestart = False;
				if ($display['Display']['restart'] == 1){
					$shouldRestart = True;
					$display['Display']['restart'] = 0; //reset restart back to 0
				}
				$this->set('shouldRestart', $shouldRestart);

            	$this->log("Request from display ".$display['Display']['name'],LOG_DEBUG);
            	
            	$fixedText = str_replace('&', 'and', $display['Display']['text']);
            	$this->set('msg',$fixedText);
            	
            	//$this->log($display['Display']['text'],LOG_DEBUG);

            	// and update the last request time and ip
                $remoteIpAddr = $this->RequestHandler->getClientIP();
            	$display['Display']['last_ip'] = $remoteIpAddr;
            	$display['Display']['status'] = $status;
                $display['Display']['version'] = $codeVersion;
            	$display['Display']['last_request'] = date("Y:m:d H:i:s");
            	$this->Display->save($display);
            	
            	// update the status history, if the status has changed
            	$hasChanged = $this->DisplayStatus->saveIfChanged($display['Display']['id'],$status);
            	
            	if($hasChanged){
            		$badStates = array(Display::STATUS_SIGN_COMMS_ERROR, Display::STATUS_UNKNOWN,
            		                  /*Display::STATUS_SERVER_CONNECT_ERROR,*/ Display::STATUS_BLANKED_DISPLAY);            		
            		// send email alert if bad state and marked to send email alerts
            		if(in_array($status,$badStates) && $display['Display']['send_error_alerts']){
            			App::import('Helper', 'LostInBoston');
                        $this->LostInBoston= new LostInBostonHelper();
            			$statusText = $this->LostInBoston->rawStatusText($status);
            			// notify people
		                $this->Email->from    = Configure::read('EmailFromAddress');
		                $this->Email->to      = implode(',', $this->User->emailsToSendErrorAlerts());
		                $this->Email->subject = '['.Configure::read('ServerName').'] '.
		                                        $display['Display']['name'].' '.$statusText;
		                $body = "The ".$display['Display']['name']." display reported it status as ".$statusText.
		                          ".  Consider yourself warned.";
		                $worked = $this->Email->send($body);
		                $this->log("Sent an email because the display ".$display['Display']['name']." entered a bad state of ".$status,LOG_DEBUG);
            		}
            	}
            	
            } else {
            	// if unknown display send back error msg and log it
            	$this->set('msg',__("Server Error: Unknown Display",true));
            	$this->log("got request from unknown display $serial / $secret");
            }
        }
        
    }


	/**
	 * Allow user to change the append text for a display if they know the custom password
	 */
	public function message($id = null) {
		#Given the computer id, edit that computer info.
	    if (empty($this->data)) {
			$this->Display->id = $id;
	        $this->data = $this->Display->read();
	        $this->set('display',$this->data);
	    } else {
			$display = $this->Display->findById($this->data['Display']['id']);
	    	$this->set('display',$this->Display->read());
			#Check password from previous form
			if ($display['Display']['client_password'] == $this->data['Display']['password']){
				if ($this->Display->save($this->data)) {
					$this->Session->setFlash(__('Your display has been updated.',true));
					$this->MsgGenerator->updateDisplayText($this->data['Display']['id']);
					$this->redirect(array('action' => 'message', $this->data['Display']['id']));
				}
			}
			else{
				$this->Session->setFlash(__('Your password was incorrect.',true));
				$this->data['Display']['password'] = null;
				$this->redirect(array('action' => 'message', $this->data['Display']['id']));
			}
	    }
	}

	/**
	 * Get status for a certain group of signs
	 */
	public function group_status($group_url){
		#Use group url to find group of computers with those ids
		$group = $this->Group->findByUrl($group_url);
		
		$this->set('group',$group);
		$this->set('displays',$group['Display']);
    
	}
}
?>