<?php
/* Stops Test cases generated on: 2010-12-10 12:12:33 : 1292001453*/
App::import('Controller', 'Stops');

class TestStopsController extends StopsController {
	var $autoRender = false;

	function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}
}

class StopsControllerTestCase extends CakeTestCase {
	var $fixtures = array('app.stop', 'app.user');

	function startTest() {
		$this->Stops =& new TestStopsController();
		$this->Stops->constructClasses();
	}

	function endTest() {
		unset($this->Stops);
		ClassRegistry::flush();
	}

}
?>