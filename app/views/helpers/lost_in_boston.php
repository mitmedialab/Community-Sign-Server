<?php

class LostInBostonHelper extends AppHelper {

	function statusText($status) {
		$cssClass = "lib-status-unknown"; // placeholder
        switch($status) {
        	case Display::STATUS_BOOTING: 
            case Display::STATUS_OK: 
                $cssClass = "lib-status-ok";
            	break;
            case Display::STATUS_UNKNOWN: 
            case Display::STATUS_SIGN_COMMS_ERROR: 
            case Display::STATUS_SERVER_CONNECT_ERROR: 
            case Display::STATUS_BLANKED_DISPLAY: 
            case Display::STATUS_UNHEARD_FROM: 
			case Display::STATUS_VERSION_MISMATCH:
            	$cssClass = "lib-status-bad";
                break;
        }
        $msg = "<div class=\"".$cssClass."\">".$this->rawStatusText($status)."</span>";
        return $msg;
    }
    
    function rawStatusText($status){
        $msg = __("Uknown Status!",true);
        switch($status) {
            case Display::STATUS_UNKNOWN: 
                $msg = __("Unknown",true);
                break;
            case Display::STATUS_BOOTING: 
                $msg =  __("Booting",true);
                break;
            case Display::STATUS_OK: 
                $msg =  __("OK",true);
                break;
            case Display::STATUS_SIGN_COMMS_ERROR: 
                $msg =  __("Sign Comms Error",true);
                break;
            case Display::STATUS_SERVER_CONNECT_ERROR: 
                $msg =  __("Server Connect Error",true);
                break;
            case Display::STATUS_BLANKED_DISPLAY: 
                $msg =  __("Blanked Display / No Server",true);
                break;
            case Display::STATUS_UNHEARD_FROM: 
                $msg =  __("unheard from in a while!",true);
                break;
			case Display::STATUS_VERSION_MISMATCH:
				$msg = __("Version Mismatch",true);
				break;
        }
        return $msg;    	
    }

}

?>