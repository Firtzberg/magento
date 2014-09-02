<?php
$installer = $this;
$installer->startSetup();

$table = $installer
    ->getConnection()
    ->newTable($installer->getTable('inchoo_ticketmanager/reply'));

$table->addColumn('reply_id',
    Varien_DB_Ddl_Table::TYPE_INTEGER, null,
    array(
        'unsigned' => true,
        'identity' => true,
        'nullable' => false,
        'primary' => true
    ),
    'Entity id');

$table->addColumn('ticket_id',
    Varien_DB_Ddl_Table::TYPE_INTEGER, null,
    array(
        'unsigned' => true,
        'nullable' => false
    ),
    'Ticket id');

$table->addColumn('content',
    Varien_Db_Ddl_Table::TYPE_TEXT, '2M',
    array(
        'nullable' => false
    ),
    'Content');

$table->addColumn('created_at',
    Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null,
    array(
        'nullable' => true,
        'default' => 'date()'
    ),
    'Creation Time');

$table->setComment('Reply item');

$installer->getConnection()->createTable($table);

$installer->getConnection()
    ->addConstraint(
    'FK_TICKET_REPLY',
        $installer->getTable('inchoo_ticketmanager/reply'),
        'ticket_id',
        $installer->getTable('inchoo_ticketmanager/ticket'),
        'ticket_id',
        'cascade',
        'cascade'
    );

$installer->endSetup();