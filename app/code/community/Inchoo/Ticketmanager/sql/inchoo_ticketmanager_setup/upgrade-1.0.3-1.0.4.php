<?php

$installer = $this;

$installer->startSetup();

$connection = $installer->getConnection();

$connection->addColumn(
    $installer->getTable('inchoo_ticketmanager/reply'),
    'admin_id',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable' => true,
        'comment' => 'Admin ID'
    )
);

$connection->addColumn(
    $installer->getTable('inchoo_ticketmanager/reply'),
    'customer_id',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable' => true,
        'comment' => 'Customer ID'
    )
);

$connection->addConstraint(
    'FK_REPLY_ADMIN',
    $installer->getTable('inchoo_ticketmanager/reply'),
    'admin_id',
    'admin_user',
    'user_id',
    'cascade',
    'cascade'
);

$connection->addConstraint(
    'FK_REPLY_CUSTOMER',
    $installer->getTable('inchoo_ticketmanager/reply'),
    'customer_id',
    'customer_entity',
    'entity_id',
    'cascade',
    'cascade'
);

$installer->endSetup();