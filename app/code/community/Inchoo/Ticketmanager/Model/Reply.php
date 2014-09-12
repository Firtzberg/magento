<?php

class Inchoo_Ticketmanager_Model_Reply extends Mage_Core_Model_Abstract {

    public function _construct(){
        $this->_init('inchoo_ticketmanager/reply');
    }

    protected function _beforeSave()
    {
        parent::_beforeSave();
        if($this->isObjectNew()){
            $this->setData('created_at', Varien_Date::now());
        }
        return $this;
    }

    private $_ticket = null;

    public function  getTicket(){
        if($this->_ticket == null){
            $ticketId = $this->getData('ticket_id');
            if(!$ticketId)
                return null;
            $this->_ticket = Mage::getModel('inchoo_ticketmanager/ticket')->load($ticketId);
        }
        return $this->_ticket;
    }
}