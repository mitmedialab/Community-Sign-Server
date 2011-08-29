<?php
App::import('Component','MsgGenerator'); 
App::import('Component','Email'); 

/**
 * Tasks 
 * @author rahulb
 *
 */
class DisplayManagementTask extends Shell {
    
    var $uses = array('Display','DisplayStatus','User');
  
    /**
     */
    function updateAll() {

        // init components
        $this->MsgGenerator=& new MsgGeneratorComponent();
        $this->MsgGenerator->startup(&$this);
        $this->Email=& new EmailComponent();
        $this->Email->startup(&$this);
        
        $sendAlertsEmailList = $this->User->emailsToSendErrorAlerts();

        // update all the displays
        Cache::clear(false,'predictions');         // flush the cache first
        $this->Display->contain();
        $displayList = $this->Display->Find('all');

        // check for any dead signs
        $this->log("Checking for dead signs: ",LOG_DEBUG);
        foreach($displayList as $display){
            $lastRequest = strtotime($display['Display']['last_request']);
            $now = time();
            $ageSecs = $now - $lastRequest;
            if( ($ageSecs > Configure::read('LostInBoston.SignDeadThresholdSecs')) && 
                ($display['Display']['status']!=Display::STATUS_UNHEARD_FROM) ) {
                $this->log("Display '".$display['Display']['name']."' hasn't been heard from in a while",LOG_DEBUG);
                // mark the display as offline
                $this->Display->id = $display['Display']['id'];
                $this->Display->saveField('status', Display::STATUS_UNHEARD_FROM);
                // update the status history, if the status has changed
                $this->DisplayStatus->saveIfChanged($display['Display']['id'],Display::STATUS_UNHEARD_FROM);
                if($display['Display']['send_error_alerts']){
                    // notify people
                    $this->Email->from    = Configure::read('EmailFromAddress');
                    $this->Email->to      = implode(',',$sendAlertsEmailList);
                    $this->Email->subject = '['.Configure::read('ServerName').'] '.
                                            $display['Display']['name'].' unheard from in a while';
                    $body = "The server marked the ".$display['Display']['name']." as 'unheard from' because ".
                            "we haven't heard from it in a while.  Don't panic... this might just mean that ".
                            "it is rebooting.";
                    $worked = $this->Email->send($body);
                }
            }
        }
        
        // update the text with new predictions
        $this->log("Updating All Displays: ",LOG_DEBUG);
        foreach($displayList as $display){
            $this->MsgGenerator->updateDisplayText($display['Display']['id']);
        }
                
    }
        
}

?>