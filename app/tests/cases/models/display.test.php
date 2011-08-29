<?php
/* Display Test cases generated on: 2010-12-10 12:12:58 : 1292002138*/
App::import('Model', 'Display');

class DisplayTestCase extends CakeTestCase {
	var $fixtures = array('app.display', 'app.display_stop', 'app.stop');

	function startTest() {
		$this->Display =& ClassRegistry::init('Display');
	}

	function endTest() {
		unset($this->Display);
		ClassRegistry::flush();
	}

}
?>