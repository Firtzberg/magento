<?php

$installer = $this;

$configValuesMap = array(
    'inchoo_ticketmanager_email_settings/new_ticket_email_settings/email_template' => 'inchoo_ticketmanager_ticket_posted_email',
    'inchoo_ticketmanager_email_settings/customer_reply_email_settings/email_template' => 'inchoo_ticketmanager_customer_reply_email',
    'inchoo_ticketmanager_email_settings/admin_reply_email_settings/email_template' => 'inchoo_ticketmanager_admin_reply_email'
);

foreach ($configValuesMap as $configPath=>$configValue) {
    $installer->setConfigData($configPath, $configValue);
}