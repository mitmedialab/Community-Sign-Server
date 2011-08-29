<?php
/* Display Fixture generated on: 2010-12-10 12:12:58 : 1292002138 */
class DisplayFixture extends CakeTestFixture {
	var $name = 'Display';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'text' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 1000, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'secret' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'serial_num' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'last_ip' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 100, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'last_request' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'last_text_update' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array()
	);

	var $records = array(
		array(
			'id' => 1,
			'name' => 'Lorem ipsum dolor sit amet',
			'text' => 'Lorem ipsum dolor sit amet',
			'secret' => 'Lorem ipsum dolor sit amet',
			'serial_num' => 'Lorem ipsum dolor sit amet',
			'last_ip' => 'Lorem ipsum dolor sit amet',
			'last_request' => '2010-12-10 12:28:58',
			'last_text_update' => '2010-12-10 12:28:58',
			'created' => '2010-12-10 12:28:58',
			'modified' => '2010-12-10 12:28:58'
		),
	);
}
?>