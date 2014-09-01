<?php

class Inchoo_Ticketmanager_Block_Ticket extends Mage_Core_Block_Template
{

    /**
     * Ticket Collection
     *
     * @var Inchoo_Ticketmanager_Model_Ticket_Collection
     */
    protected $_item = null;

    protected function _getBackQueryParams($additionalParams = array()){
    	return array_merge(array('p' => $this->getPage()), $additionalParams);
    }

    public function getBackUrl()
    {
    	return $this->getUrl('*/', array('_query' => $this->_getBackQueryParams()));
    }
}
