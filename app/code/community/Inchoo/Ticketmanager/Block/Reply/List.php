<?php

class Inchoo_Ticketmanager_Block_Reply_List extends Mage_Core_Block_Template
{
    protected function _construct(){
        $this->setTemplate('inchoo/ticketmanager/reply/list.phtml');
    }

    function getTicket(){
        if(!$this->getData('ticket')){
            $this->setData('ticket', Mage::registry('ticket_item'));
        }
        return $this->getData('ticket');
    }

    /**
     * @var Inchoo_Ticketmanager_Model_Resource_Ticket_Collection
     */
    private $_collection = null;

    protected function _prepareCollection(){
        $this->_collection = Mage::getModel('inchoo_ticketmanager/reply')
            ->getCollection()
            ->addFieldToFilter('ticket_id', $this->getTicket()->getId());
        return $this->_collection;
    }

    public function  getCollection(){
        if($this->_collection == null){
            $this->_prepareCollection();
        }
        return $this->_collection;
    }
}