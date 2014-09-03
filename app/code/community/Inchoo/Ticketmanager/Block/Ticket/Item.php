<?php

class Inchoo_Ticketmanager_Block_Ticket_Item extends Mage_Core_Block_Template
{
    /**
     * Ticket Collection
     *
     * @var Inchoo_Ticketmanager_Model_Ticket
     */
    protected $_item = null;

    protected function _construct(){
        $this->setTemplate('inchoo/ticketmanager/ticket/item.phtml');
    }

    protected function _getBackQueryParams($additionalParams = array()){
    	return array_merge(array('p' => $this->getPage()), $additionalParams);
    }

    public function getBackUrl()
    {
    	return $this->getUrl('*/', array('_query' => $this->_getBackQueryParams()));
    }

    public function getEditUrl(){
        return $this->getUrl('ticket/index/edit', array('id' => $this->helper('inchoo_ticketmanager')->getTicketItemInstance()->getId()));
    }
}
