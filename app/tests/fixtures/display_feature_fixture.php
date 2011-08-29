<?php
/* DisplayFeature Fixture generated on: 2011-01-28 14:01:34 : 1296242614 */
class DisplayFeatureFixture extends CakeTestFixture {
	var $name = 'DisplayFeature';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'display_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'feature_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array()
	);

	var $records = array(
		array(
			'id' => 1,
			'display_id' => 1,
			'feature_id' => 1,
			'created' => '2011-01-28 14:23:34',
			'modified' => '2011-01-28 14:23:34'
		),
	);
}
?>