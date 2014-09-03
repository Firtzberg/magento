<?php

class Inchoo_Ticketmanager_Model_Ticket extends Mage_Core_Model_Abstract
{
    const STATUS_OPEN = 0;
    const STATUS_CLOSED = 1;

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

    /**
     * @return bool
     */
    public function isOpen(){
        return $this->getData('status') == self::STATUS_OPEN;
    }

    /**
     * @return string
     */
    public function getStatus(){
        if($this->isOpen())
            return "Open";
        else return "Closed";
    }

    /**
     * @var Mage_Customer_Model_Customer
     */
    private $_customer = null;
    public function getCustomer(){
        if($this->_customer == null){
            $this->_customer = Mage::getModel('customer/customer')->load($this->getData('customer_id'));
        }
        return $this->_customer;
    }
}