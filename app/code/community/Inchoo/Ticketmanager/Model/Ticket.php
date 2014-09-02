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

    private $_replies_collection = null;

    protected function _prepareRepliesCollection(){
        $this->_replies_collection = Mage::getModel('inchoo_ticketmanager/reply')
            ->getCollection()
            ->addFieldToFilter('ticket_id', $this->getId())
            ->setOrder('created_at', 'ASC');
        return $this->_replies_collection;
    }

    public function getReplies(){
        if($this->_replies_collection == null){
            $this->_prepareRepliesCollection();
        }
        return $this->_replies_collection;
    }

    public function getRepliesCount(){
        return $this->getReplies()->count();
    }
}