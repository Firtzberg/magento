<?php

class Inchoo_Ticketmanager_Block_Ticket_List extends Mage_Core_Block_Template
{

    /**
     * Ticket Collection
     *
     * @var Inchoo_Ticketmanager_Model_Ticket_Collection
     */
    protected $_ticketCollection = null;

    protected function _construct(){
        $this->setTemplate('inchoo/ticketmanager/ticket/list.phtml');
    }

    /**
     * Retrieve loaded category collection
     *
     * @return Inchoo_Ticketmanager_Model_Ticket_Collection
     */
    protected function _getTicketCollection()
    {
        if (is_null($this->_ticketCollection)) {
            $this->_ticketCollection = Mage::getResourceModel('inchoo_ticketmanager/ticket_collection')
            ->addFieldToFilter('website_id', Mage::app()->getWebsite()->getId());
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

    function _prepareLayout() {

        $pager = $this->getLayout()->createBlock('page/html_pager', 'ticket.pager');
        $ticketsPerPage = Mage::helper('inchoo_ticketmanager')->getTicketsPerPage();
        $pager->setAvailableLimit(array($ticketsPerPage => $ticketsPerPage));

        $this->setChild('pager', $pager);
        parent::_prepareLayout();
    }

    function _toHtml() {
        $this->getChild('pager')->setCollection($this->getCollection());
        return parent::_toHtml();
    }

    public function getPagerHtml() {
        return $this->getChildHtml('pager');
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
