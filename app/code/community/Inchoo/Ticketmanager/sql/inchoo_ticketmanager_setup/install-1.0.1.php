<?php
$installer = $this;
$installer->startSetup();

$table = $installer
->getConnection()
->newTable($installer->getTable('inchoo_ticketmanager/ticket'));

$table->addColumn('ticket_id',
	Varien_DB_Ddl_Table::TYPE_INTEGER, null,
	array(
		'unsigned' => true,
		'identity' => true,
		'nullable' => false,
		'primary' => true
		),
	'Entity id');

$table->addColumn('subject',
	Varien_Db_Ddl_Table::TYPE_TEXT, 255,
	array(
		'nullable' => false
		),
	'Subject');

$table->addColumn('message',
	Varien_Db_Ddl_Table::TYPE_TEXT, '2M',
	array(
		'nullable' => false
		),
	'Message');

$table->addColumn('created_at',
	Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null,
	array(
		'nullable' => true,
		'default' => 'date()'
		),
	'Creation Time');

$table->setComment('Ticket item');

$installer->getConnection()->createTable($table);

$installer->endSetup();