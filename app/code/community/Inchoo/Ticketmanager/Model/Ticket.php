<?php

class Inchoo_Ticketmanager_Model_Ticket extends Mage_Core_Model_Abstract
{
	protected function _construct()
	{
		$this->_init('inchoo_ticketmanager/ticket');
	}

	protected function _beforeSave()
	{
		parent::_beforeSave();
		if($this->isObjectNew()){
			$this->setData('created_at', Varien_Date::now());
		}
		return $this;
	}
}