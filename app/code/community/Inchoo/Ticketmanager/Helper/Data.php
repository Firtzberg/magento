<?php

class Inchoo_Ticketmanager_Helper_Data extends Mage_Core_Helper_Data
{
	const XML_PATH_ENABLED = 'ticket/view/enabled';

	const XML_PATH_ITEMS_PER_PAGE = 'ticket/view/items_per_page';

	protected $_ticketItemInstance;

	public function isEnabled($store = null){
		return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED, $store);
	}

	public function getTicketsPerPage($store = null)
	{
		return abs((int)Mage::getStoreConfig(self::XML_PATH_ITEMS_PER_PAGE, $store));
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
}