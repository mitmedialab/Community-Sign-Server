<?php
/**
 * Handles login/logout.  Users are added by hand right now in the DB (password is MD5).
 * @author rahulb
 *
 */
class UsersController extends AppController {

    var $name = 'Users';
    var $authenticates = false;
    
    var $uses = array("User");
    
    var $postLoginController = "displays";
    
    /**
     * Redirect to the right homepage 
     */
    public function index() {
        $this->redirect('login');
    }
    
    /**
     * Allow the user to signup themselves, if the config var says that is ok
     */
    public function signup() {
        
        // make sure self-signup is allowed
        $allowSelfSignup = Configure::read('LostInBoston.AllowSelfSignup');
        if($allowSelfSignup==false) {
            $this->redirect('login');
            return;
        }

        // if it is a post, create and log them in
        if (!empty($this->data)) {
            $this->User->set($this->data);

            if ($this->User->validates()) {
                if ($this->User->save($this->data)) {
                    $user = $this->User->FindById($this->User->id);
                    //log them in
                    $this->createSession($user);
                    //send them to the homepage
                    $this->redirect(array('controller'=>$this->postLoginController));
                } else {
                    //$this->set(AppController::VIEW_ERRORS_VAR, $this->User->validationErrors);
                }
            } else {
                //$this->set(AppController::VIEW_ERRORS_VAR, $this->User->validationErrors);
            }
        }           
        
    }

    /**
     * Let the user login with a form
     */
    public function login() {
    	
        // if they're logged in already, redirect them to the dashboard
        if ( $this->isLoggedIn() ) {
            $this->redirect(array('controller'=>$this->postLoginController));
        }
        
        // handle log in attempts
        if (!empty($this->data)) {
            $this->User->set($this->data);
            if ($this->User->validates()) {
                if ($this->User->verifyLogin($this->data)) {
                    $user = $this->User->FindByUsername($this->data['User']['username']);
                    $this->createSession($user);
                    // logged in success, forward to dashboard
                    $this->redirect(array('controller'=>$this->postLoginController));
                } else {
                    $this->Session->setflash("That username or password is wrong");
                	//$this->set(PermissionedAppController::VIEW_ERRORS_VAR, $this->User->validationErrors);
                }
            } else {
            	$this->Session->setflash("That username or password is wrong");
                //$this->set(PermissionedAppController::VIEW_ERRORS_VAR, $this->User->validationErrors);
            }
        }
    }

    /**
     * Delete the session if they want to log out
     */
    public function logout() {
        $this->deleteSession();
        $this->redirect('/');
    }

    /**
     * Delete the session, logging the user out.
     */
    private function deleteSession() {
        $this->Session->delete(AppController::SESSION_USER_VAR);
    }

    /**
     * Create the session, logging the user in and saving their info to the session.
     * @param unknown_type $userData
     */
    private function createSession($userData) {
        $this->deleteSession();
        $this->Session->write(AppController::SESSION_USER_VAR, $userData);
    }
    
}
?>