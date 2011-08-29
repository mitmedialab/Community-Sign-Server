<?php
/* DisplayStatus Test cases generated on: 2011-01-05 15:01:49 : 1294259689*/
App::import('Model', 'DisplayStatus');

class DisplayStatusTestCase extends CakeTestCase {
	var $fixtures = array('app.display_status', 'app.display', 'app.stop', 'app.display_stop');

	function startTest() {
		$this->DisplayStatus =& ClassRegistry::init('DisplayStatus');
	}

	function endTest() {
		unset($this->DisplayStatus);
		ClassRegistry::flush();
	}

}
?>