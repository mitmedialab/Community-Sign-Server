<?php
/* DisplayStatus Fixture generated on: 2011-01-05 15:01:49 : 1294259689 */
class DisplayStatusFixture extends CakeTestFixture {
	var $name = 'DisplayStatus';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'display_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'status' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'changed' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array()
	);

	var $records = array(
		array(
			'id' => 1,
			'display_id' => 1,
			'status' => 1,
			'changed' => '2011-01-05 15:34:49',
			'created' => '2011-01-05 15:34:49',
			'modified' => '2011-01-05 15:34:49'
		),
	);
}
?>