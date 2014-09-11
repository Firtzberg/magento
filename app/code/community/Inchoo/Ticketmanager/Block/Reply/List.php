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
        $this->_collection->getSelect()
            ->joinLeft(
                array('ad' => 'admin_user'),
                'ad.user_id=main_table.admin_id',
                array('admin_name' => "concat(ad.firstname,' ', ad.lastname)")
            );
        $this->_collection = Mage::helper('inchoo_ticketmanager')->joinCustomerNameToFlatTable($this->_collection);
        $this->_collection->prepareForList($this->getCurrentPage());
        return $this->_collection;
    }

    public function  getCollection(){
        if($this->_collection == null){
            $this->_prepareCollection();
        }
        return $this->_collection;
    }

    function _prepareLayout() {

        $pager = $this->getLayout()->createBlock('page/html_pager', 'reply.pager');
        $repliesPerPage = Mage::helper('inchoo_ticketmanager')->getRepliesPerPage();
        $pager->setAvailableLimit(array($repliesPerPage => $repliesPerPage));

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
}