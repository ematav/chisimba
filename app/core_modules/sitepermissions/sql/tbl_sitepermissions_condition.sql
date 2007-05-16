<?php
// Table Name
$tablename = 'tbl_sitepermissions_condition';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table used to keep a list of conditions and their properties', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
	),
	'typeid' => array(
		'type' => 'text',
		'length' => 32,
	),
	'name' => array(
		'type' => 'text',
		'length' => 50,
		'notnull' => TRUE,
    ),
    'target' => array(
		'type' => 'text',
		'length' => 255,
    ),
);

//create other indexes here...

$name = 'FK_condition';

$indexes = array(
    'fields' => array(
        'typeid' => array(),
    )
);
?>