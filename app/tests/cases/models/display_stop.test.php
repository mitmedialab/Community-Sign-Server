<?php
/* DisplayStop Test cases generated on: 2010-12-10 12:12:34 : 1292002114*/
App::import('Model', 'DisplayStop');

class DisplayStopTestCase extends CakeTestCase {
	var $fixtures = array('app.display_stop', 'app.display', 'app.stop');

	function startTest() {
		$this->DisplayStop =& ClassRegistry::init('DisplayStop');
	}

	function endTest() {
		unset($this->DisplayStop);
		ClassRegistry::flush();
	}

}
?>