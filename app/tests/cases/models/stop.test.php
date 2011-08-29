<?php
/* Stop Test cases generated on: 2010-12-10 12:12:16 : 1292002156*/
App::import('Model', 'Stop');

class StopTestCase extends CakeTestCase {
	var $fixtures = array('app.stop', 'app.display', 'app.display_stop');

	function startTest() {
		$this->Stop =& ClassRegistry::init('Stop');
	}

	function endTest() {
		unset($this->Stop);
		ClassRegistry::flush();
	}

}
?>