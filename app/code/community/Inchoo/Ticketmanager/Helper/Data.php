<?php

class Inchoo_Ticketmanager_Helper_Data extends Mage_Core_Helper_Data
{
	const XML_PATH_ENABLED = 'ticket/view/enabled';

    const XML_PATH_ITEMS_PER_PAGE = 'ticket/view/items_per_page';

    const XML_PATH_REPLIES_PER_PAGE = 'ticket/view/replies_per_page';

    const XML_PATH_EDIT_TICKET_ENABLED = 'ticket/view/edit_ticket_enabled';

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

    public function getEditTicketEnabled(){
        return false;
        return Mage::getConfig(self::XML_PATH_EDIT_TICKET_ENABLED);
    }
}