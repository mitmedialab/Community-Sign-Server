<?php
/* DisplayFeature Test cases generated on: 2011-01-28 14:01:35 : 1296242615*/
App::import('Model', 'DisplayFeature');

class DisplayFeatureTestCase extends CakeTestCase {
	var $fixtures = array('app.display_feature', 'app.display', 'app.stop', 'app.display_stop', 'app.feature');

	function startTest() {
		$this->DisplayFeature =& ClassRegistry::init('DisplayFeature');
	}

	function endTest() {
		unset($this->DisplayFeature);
		ClassRegistry::flush();
	}

}
?>