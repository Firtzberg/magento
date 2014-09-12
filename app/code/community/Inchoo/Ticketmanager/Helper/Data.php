<?php

class Inchoo_Ticketmanager_Helper_Data extends Mage_Core_Helper_Data
{
	const XML_PATH_ENABLED = 'inchoo_ticketmanager_ticket/view/enabled';

    const XML_PATH_ITEMS_PER_PAGE = 'inchoo_ticketmanager_ticket/view/items_per_page';

    const XML_PATH_REPLIES_PER_PAGE = 'inchoo_ticketmanager_ticket/view/replies_per_page';

    const XML_PATH_EDIT_TICKET_ENABLED = 'inchoo_ticketmanager_ticket/view/edit_ticket_enabled';

    const XML_PATH_ALL_TICKETS_VISIBLE = 'inchoo_ticketmanager_ticket/view/all_tickets_visible';

    const XML_PATH_CUSTOMER_CAN_REPLY_ALL = 'inchoo_ticketmanager_ticket/view/can_reply_all';

    const XML_PATH_NEW_TICKET_EMAIL_TEMPLATE = 'inchoo_ticketmanager_email_settings/new_ticket_email_settings/email_template';
    const XML_PATH_CUSTOMER_REPLY_EMAIL_TEMPLATE = 'inchoo_ticketmanager_email_settings/customer_reply_email_settings/email_template';
    const XML_PATH_ADMIN_REPLY_EMAIL_TEMPLATE = 'inchoo_ticketmanager_email_settings/admin_reply_email_settings/email_template';

    const XML_PATH_ADMIN_EMAIL = 'inchoo_ticketmanager_email_settings/general/admin_email';

    const XML_PATH_GENERAL_EMAIL_ENABLED = 'inchoo_ticketmanager_email_settings/general/enabled';
    const XML_PATH_NEW_TICKET_EMAIL_ENABLED = 'inchoo_ticketmanager_email_settings/new_ticket_email_settings/enabled';
    const XML_PATH_CUSTOMER_REPLY_EMAIL_ENABLED = 'inchoo_ticketmanager_email_settings/customer_reply_email_settings/enabled';
    const XML_PATH_CUSTOMER_FOREIGN_REPLY_EMAIL_ENABLED = 'inchoo_ticketmanager_email_settings/customer_reply_email_settings/replier_enabled';
    const XML_PATH_ADMIN_REPLY_EMAIL_ENABLED = 'inchoo_ticketmanager_email_settings/admin_reply_email_settings/enabled';

    protected $_ticketItemInstance;
    protected $_replyItemInstance;

    public function isEnabled($store = null){
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED, $store);
    }

    public function getTicketsPerPage($store = null)
    {
        return abs((int)Mage::getStoreConfig(self::XML_PATH_ITEMS_PER_PAGE, $store));
    }

    public function getRepliesPerPage($store = null)
    {
        return abs((int)Mage::getStoreConfig(self::XML_PATH_REPLIES_PER_PAGE, $store));
    }

    public function getEditTicketEnabled(){
        return Mage::getStoreConfig(self::XML_PATH_EDIT_TICKET_ENABLED);
    }

    public function getAllTicketsVisible(){
        return Mage::getStoreConfig(self::XML_PATH_ALL_TICKETS_VISIBLE);
    }

    public function getCustomerCanReplyAll(){
        return Mage::getStoreConfig(self::XML_PATH_CUSTOMER_CAN_REPLY_ALL)&&Mage::getStoreConfig(self::XML_PATH_ALL_TICKETS_VISIBLE);
    }

    public function getGeneralEmailEnabled(){
        return Mage::getStoreConfig(self::XML_PATH_GENERAL_EMAIL_ENABLED);
    }

    public function getNewTicketEmailAdminEnabled(){
        return $this->getGeneralEmailEnabled()&&Mage::getStoreConfig(self::XML_PATH_NEW_TICKET_EMAIL_ENABLED);
    }

    public function getReplyEmailCustomerEnabled(){
        return $this->getGeneralEmailEnabled()&&Mage::getStoreConfig(self::XML_PATH_CUSTOMER_REPLY_EMAIL_ENABLED);
    }

    public function getReplyEmailAdminEnabled(){
        return $this->getGeneralEmailEnabled()&&Mage::getStoreConfig(self::XML_PATH_ADMIN_REPLY_EMAIL_ENABLED);
    }

    public function getReplyEmailReplierEnabled(){
        return $this->getGeneralEmailEnabled()&&$this->getAllTicketsVisible()&&Mage::getStoreConfig(self::XML_PATH_CUSTOMER_FOREIGN_REPLY_EMAIL_ENABLED);
    }

    public function getNewTicketEmailAdminTemplate(){
        return Mage::getStoreConfig(self::XML_PATH_NEW_TICKET_EMAIL_TEMPLATE);
    }

    public function getReplyEmailCustomerTemplate(){
        return Mage::getStoreConfig(self::XML_PATH_CUSTOMER_REPLY_EMAIL_TEMPLATE);
    }

    public function getReplyEmailAdminTemplate(){
        return Mage::getStoreConfig(self::XML_PATH_ADMIN_REPLY_EMAIL_TEMPLATE);
    }

    public function getAdminEmail(){
        return Mage::getStoreConfig(self::XML_PATH_ADMIN_EMAIL);
    }

    public function sendNewTicketEmailToAdmin($ticket){
        if(Mage::helper('inchoo_ticketmanager')->getNewTicketEmailAdminEnabled()){
            $templateId = $this->getNewTicketEmailAdminTemplate();
            /**
             * @var $customer Mage_Customer_Model_Customer
             */
            $customer = $ticket->getCustomer();
            $sender = array('name' => $customer->getName(),
                'email' => $customer->getEmail());
            $receiver = $this->getAdminEmail();
            $vars = array('customer' => $customer,
                'ticket' => $ticket,
                'ticket_url' => Mage::getUrl('adminhtml/ticket/edit', array('id' => $ticket->getId()))
            );
            Mage::getModel('core/email_template')
                ->sendTransactional($templateId, $sender, $receiver, null, $vars, Mage::app()->getStore()->getId());
        }
    }

    public function sendNewReplyEmailsOnReply($reply){
        $adminReplied = $reply->getData('isAdmin');
        $ticket = $reply->getTicket();
        if($adminReplied){
            $user = Mage::getModel('admin/user')->load($reply->getData('admin_id'));
            $sender = array('name' => $user->getData('username'),
                'email' => $user->getData('email'));
        } else{
            $customer = Mage::getModel('customer/customer')->load($reply->getData('customer_id'));
            $sender = array('name' => $customer->getName(),
                'email' => $customer->getEmail());
        }
        $vars = array('replier_name' => $sender['name'],
            'reply' => $reply,
            'ticket' => $ticket
        );
        $storeId = Mage::app()->getStore()->getId();
        //email admin if needed
        if(!$adminReplied && $this->getReplyEmailAdminEnabled()){
            //admin must receive email
            $vars['ticket_url'] = Mage::getUrl('adminhtml/ticket/edit', array('id' => $ticket->getId()));
            $templateId = $this->getReplyEmailAdminTemplate();
            $receiver = $this->getAdminEmail();
            Mage::getModel('core/email_template')
                ->sendTransactional($templateId, $sender, $receiver, null, $vars, $storeId);
        }
        //admin emailed if needed

        if(!$this->getReplyEmailCustomerEnabled())
            return;

        $vars['ticket_url'] = Mage::getUrl('inchoo_ticketmanager/index/view', array('id' => $ticket->getId()));
        $templateId = $this->getReplyEmailCustomerTemplate();
        //email ticket owner if needed
        if($adminReplied || $reply->getData('customer_id') != $ticket->getData('customer_id')){
            //email ticket owner
            $vars['customer'] = Mage::getModel('customer/customer')->load($ticket->getData('customer_id'));
            $receiver = $vars['customer']->getEmail();
            Mage::getModel('core/email_template')
                ->sendTransactional($templateId, $sender, $receiver, null, $vars, $storeId);
        }
        //ticket owner emailed if needed

        if($this->getReplyEmailReplierEnabled()){
            //find other repliers, only customers
            $collection = Mage::getModel('inchoo_ticketmanager/reply')->getCollection()
                ->addFieldToFilter('ticket_id', $ticket->getId())
                ->addFieldToFilter('isAdmin', 0);
            //remove ticket owners replies
            $collection = $collection->addFieldToFilter('customer_id', array('neq' => $ticket->getData('customer_id')));
            //remove replier if ticket owner is not same as replier
            if(!$adminReplied && $reply->getData('customer_id') != $ticket->getData('customer_id'))
                $collection = $collection->addFieldToFilter('customer_id', array('neq' => $reply->getData('customer_id')));
            //group by customer_id and select customer_ids
            $collection->getSelect()->reset(Zend_Db_Select::COLUMNS)
                ->columns('customer_id')
                ->group('customer_id');
            foreach($collection as $row){
                //foreach id in collection get customer and send email
                $customer_id = $row->getData('customer_id');
                $vars['customer'] = Mage::getModel('customer/customer')->load($customer_id);
                $receiver = $vars['customer']->getEmail();
                Mage::getModel('core/email_template')
                    ->sendTransactional($templateId, $sender, $receiver, null, $vars, $storeId);
            }
        }
    }

    public function getTicketItemInstance()
    {
        if(!$this->_ticketItemInstance){
            $this->_ticketItemInstance = Mage::registry('ticket_item');

            if(!$this->_ticketItemInstance){
                Mage::throwException($this->__('Ticket item instance does not exist in Registry'));
            }
        }

        return $this->_ticketItemInstance;
    }

    public function getReplyItemInstance()
    {
        if(!$this->_replyItemInstance){
            $this->_replyItemInstance = Mage::registry('reply_item');

            if(!$this->_replyItemInstance){
                Mage::throwException($this->__('Reply item instance does not exist in Registry'));
            }
        }

        return $this->_replyItemInstance;
    }

    /**
     * @param Mage_Core_Model_Resource_Db_Collection_Abstract $collection flat table collection
     * @param string $MainTableForeignKey Foreign key column name in flat table
     * @param array $columns array of strings of joining columns
     * @param string $eavType type of entity, can be found in eav_entity_type
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    public function joinEavAttibutesToFlatTable($collection, $MainTableForeignKey, $eavType, $columns)
    {
        $entityType = Mage::getModel('eav/entity_type')->loadByCode($eavType);
        $entityTable = Mage::getSingleton('core/resource')->getTableName($entityType->getEntityTable());
        $attributes = $entityType->getAttributeCollection()->getItems();
        $found = array();
        foreach($attributes as $attribute){
            foreach($columns as $c)
                if($attribute->getAttributeCode() == $c)
                {
                    $found[$c] = $attribute;
                }
        }
        foreach($found as $column => $attribute){
            $alias = $eavType.'T_'.$column;
            $table = $entityTable.'_'.$attribute->getBackendType();
            $collection->getSelect()
                ->joinLeft(array($alias => $table),
                    'main_table.'.$MainTableForeignKey.'='.$alias.'.entity_id and '.$alias.'.attribute_id='.$attribute->getAttributeId(),
                    array($eavType.'_'.$column => $alias.'.value')
                );
        }
        return $collection;
    }

    /**
     * @param Mage_Core_Model_Resource_Db_Collection_Abstract $collection flat table collection
     * @param string $MainTableForeignKey Name of column used as foreign key
     * @param string $columnName Name of customer name column in result
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    public function joinCustomerNameToFlatTable($collection, $MainTableForeignKey = 'customer_id', $columnName = 'customer_name')
    {
        $entityType = Mage::getModel('eav/entity_type')->loadByCode('customer');
        $entityTable = Mage::getSingleton('core/resource')->getTableName($entityType->getEntityTable());
        $attributes = $entityType->getAttributeCollection()->getItems();
        $found = array();
        $columns = array('prefix', 'firstname', 'middlename', 'lastname', 'suffix');
        foreach($attributes as $attribute){
            foreach($columns as $c)
                if($attribute->getAttributeCode() == $c)
                {
                    $found[$c] = $attribute;
                }
        }
        foreach($found as $c => $attribute){
            $collection->getSelect()
                ->joinLeft(array($c.'T' => $entityTable.'_varchar'),
                    'main_table.'.$MainTableForeignKey.'='.$c.'T.entity_id and '.$c.'T.attribute_id='.$attribute->getAttributeId(),
                null);
        }
        $columnSelect = 'LTRIM(concat(';
        foreach($columns as $c){
            if($found[$c])
                $columnSelect .= 'if('.$c.'T.value is not null and '.$c.'T.value != "",CONCAT('.$c.'T.value, " "),""),';
        }
        $columnSelect = rtrim($columnSelect, ',');
        $columnSelect .= ')) as '.$columnName;
        $collection->getSelect()
            ->columns($columnSelect);
        return $collection;
    }
}