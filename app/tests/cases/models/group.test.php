<?php
/* Group Test cases generated on: 2011-06-15 14:06:28 : 1308148468*/
App::import('Model', 'Group');

class GroupTestCase extends CakeTestCase {
	var $fixtures = array('app.group', 'app.display', 'app.stop', 'app.display_stop', 'app.feature', 'app.display_feature');

	function startTest() {
		$this->Group =& ClassRegistry::init('Group');
	}

	function endTest() {
		unset($this->Group);
		ClassRegistry::flush();
	}

}
?>