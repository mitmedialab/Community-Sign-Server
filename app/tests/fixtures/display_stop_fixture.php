<?php
/* DisplayStop Fixture generated on: 2010-12-10 12:12:33 : 1292002113 */
class DisplayStopFixture extends CakeTestFixture {
	var $name = 'DisplayStop';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'display_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'stop_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array()
	);

	var $records = array(
		array(
			'id' => 1,
			'display_id' => 1,
			'stop_id' => 1,
			'created' => '2010-12-10 12:28:33',
			'modified' => '2010-12-10 12:28:33'
		),
	);
}
?>