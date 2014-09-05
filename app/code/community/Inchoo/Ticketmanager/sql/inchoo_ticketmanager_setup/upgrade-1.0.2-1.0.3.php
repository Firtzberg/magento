<?php

$installer = $this;

$installer->startSetup();

$connection = $installer->getConnection();

$connection->addColumn(
    $installer->getTable('inchoo_ticketmanager/reply'),
    'isAdmin',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
        'nullable' => false,
        'comment' => 'Determins if replier is admin'
    )
);

$connection->addColumn(
    $installer->getTable('inchoo_ticketmanager/ticket'),
    'customer_id',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'unsigned' => true,
        'nullable' => false,
        'comment' => 'ID of customer who posted the ticket'
    )
);

$connection->addColumn(
    $installer->getTable('inchoo_ticketmanager/ticket'),
    'website_id',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'unsigned' => true,
        'nullable' => false,
        'comment' => "ID of website"
    )
);

$connection->addColumn(
    $installer->getTable('inchoo_ticketmanager/ticket'),
    'status',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
        'nullable' => false,
        'comment' => 'Status of the ticket. 0 = open, 1 = closed',
        'default' => false
    )
);

$connection->addConstraint(
    'FK_TICKET_WEBSITE',
    $installer->getTable('inchoo_ticketmanager/ticket'),
    'website_id',
    $installer->getTable('core/website'),
    'website_id',
    'cascade',
    'cascade'
);

$connection->addConstraint(
    'FK_TICKET_CUSTOMER',
    $installer->getTable('inchoo_ticketmanager/ticket'),
    'customer_id',
    'customer_entity',
    'entity_id',
    'cascade',
    'cascade'
);

$installer->endSetup();