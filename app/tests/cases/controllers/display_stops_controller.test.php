<?php
/* DisplayStops Test cases generated on: 2011-01-26 16:01:28 : 1296075628*/
App::import('Controller', 'DisplayStops');

class TestDisplayStopsController extends DisplayStopsController {
	var $autoRender = false;

	function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}
}

class DisplayStopsControllerTestCase extends CakeTestCase {
	var $fixtures = array('app.display_stop', 'app.user');

	function startTest() {
		$this->DisplayStops =& new TestDisplayStopsController();
		$this->DisplayStops->constructClasses();
	}

	function endTest() {
		unset($this->DisplayStops);
		ClassRegistry::flush();
	}

}
?>