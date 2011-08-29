<?php
/* Feature Test cases generated on: 2011-01-28 14:01:25 : 1296242605*/
App::import('Model', 'Feature');

class FeatureTestCase extends CakeTestCase {
	var $fixtures = array('app.feature', 'app.display', 'app.stop', 'app.display_stop', 'app.display_feature');

	function startTest() {
		$this->Feature =& ClassRegistry::init('Feature');
	}

	function endTest() {
		unset($this->Feature);
		ClassRegistry::flush();
	}

}
?>