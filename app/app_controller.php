<?php
/**
 * Short description for class.
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       cake
 * @subpackage    cake.app
 */
class AppController extends Controller {

	// the version of the app
    const APP_VERSION = "0.3";
    
    // the variable used to save the user model object in the session
    const SESSION_USER_VAR = "User";
    
    // variables exposed to every view
    //const VIEW_ERRORS_VAR = "errors";
    const VIEW_LOGGED_IN_VAR = "isLoggedIn";
    const VIEW_USER_VAR = "user";
    const VIEW_APP_VERSION_VAR = "appVersion";
    
    // use these variables in your controllers to set security levels
    const CONTROLLER_AUTHENTICATES_VAR = "authenticates";
    
    var $uses = array('User');
    var $helpers = array('Html','Javascript','Time', 'Form','Session','LostInBoston');
    var $components = array('RequestHandler','Session');
    var $layout = "lostinboston";
    
    /**
     * Calling this forces the user to login before seeing a page
     */
    protected function checkSession() {
        // If the session info hasn't been set force them to the log in page
        if (!$this->isLoggedIn()) {
            $this->log("failed session check!",LOG_DEBUG);
            if($this->RequestHandler && $this->RequestHandler->isAjax()) {
                // handle session timeout on ajax requests differently to not
                // get embedded login page (ie. on the dashboard)
                $this->layout('ajax');
            } else {
                $this->redirect('/users/login');    
            }
        }
    }

    /**
     * Based on the lack of presence of variables on this controller, do
     * the appropriate security checks.  Also adds in the default view variables. 
     */
    function beforeFilter() {
        $this->set(AppController::VIEW_LOGGED_IN_VAR, false);
    	
        // expose global constants
        $this->set(AppController::VIEW_APP_VERSION_VAR,
                   AppController::APP_VERSION);
        
        // limit certain controllers to logged-in people
        $authVarName = AppController::CONTROLLER_AUTHENTICATES_VAR;
        if(!array_key_exists($authVarName,$this) || $this->$authVarName==true) {
            $this->checkSession();
        }
        
        // set up the base vars
        //$this->set(AppController::VIEW_ERRORS_VAR, null);
        $this->set(AppController::VIEW_LOGGED_IN_VAR, $this->isLoggedIn());
        if($this->isLoggedIn()) {
            $this->set(AppController::VIEW_USER_VAR, $this->getUser());
        } else {
            $this->set(AppController::VIEW_USER_VAR, null);
        }
    }    
    
    /**
     * Get the user id of the logged in user, null if not logged in.
     */
    protected function getUserId() {
        if ( !$this->isLoggedIn() ) return null;
        $user = $this->Session->read(AppController::SESSION_USER_VAR);
        return $user['User']['id'];
    }
    
    /**
     * Return if there is a user logged in.
     */
    protected function isLoggedIn() {
        return $this->Session->check(AppController::SESSION_USER_VAR);
    }
    
    /**
     * Get the currently logged in user.  Null if not logged in.
     */
    protected function getUser() {
        if( !$this->isLoggedIn() ) return null;
        return $this->User->FindById( $this->getUserId() );
    }
    
}
?>