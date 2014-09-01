<?php

class Inchoo_Ticketmanager_Block_List extends Mage_Core_Block_Template
{

    /**
     * Ticket Collection
     *
     * @var Inchoo_Ticketmanager_Model_Ticket_Collection
     */
    protected $_ticketCollection = null;

    /**
     * Retrieve loaded category collection
     *
     * @return Inchoo_Ticketmanager_Model_Ticket_Collection
     */
    protected function _getTicketCollection()
    {
        if (is_null($this->_ticketCollection)) {
            $this->_ticketCollection = Mage::getResourceModel('inchoo_ticketmanager/ticket_collection');
            $this->_ticketCollection->prepareForList($this->getCurrentPage());
        }

        return $this->_ticketCollection;
    }

    /**
     * Retrieve loaded collection
     *
     * @return Inchoo_Ticketmanager_Model_Ticket_Collection
     */
    public function getCollection()
    {
        return $this->_getTicketCollection();
    }

    public function getCurrentPage()
    {
    	return $this->getData('current_page') ? $this->getData('current_page') : 1;
    }

    public function getPager()
    {
    	$pager = $this->getChild('ticket_list_pager');
    	if($pager){
    		$ticketsPerPage = Mage::helper('inchoo_ticketmanager')->getTicketsPerPage();

    		$pager->setAvailableLimit(array($ticketsPerPage => $ticketsPerPage));
    		$pager->setTotalNum($this->getCollection());
    		$pager->setShowPerPage(true);

    		return $pager->toHtml();
    	}

    	return null;
    }

    /**
     * Return URL to item's view page
     *
     * @param Inchoo_TicketManager_Block_Ticket $ticketItem
     * @return string
     */
    public function getItemUrl($ticketItem)
    {
        return $this->getUrl('*/*/view', array('id' => $ticketItem->getId()));
    }
}
