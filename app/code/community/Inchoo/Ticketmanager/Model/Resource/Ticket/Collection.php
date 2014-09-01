<?php

class Inchoo_Ticketmanager_Model_Resource_Ticket_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
	protected function _construct()
	{
		$this->_init('inchoo_ticketmanager/ticket');
	}

	public function prepareForList($page){
		$this->setPageSize(Mage::helper('inchoo_ticketmanager')
			->getTicketsPerPage());
		$this->setCurPage($page)
		->setOrder('created_at', Varien_Data_Collection::SORT_ORDER_DESC);
		return $this;
	}
}