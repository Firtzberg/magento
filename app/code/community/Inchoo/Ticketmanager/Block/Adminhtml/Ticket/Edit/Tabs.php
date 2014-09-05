<?php
/**
 * Ticket List admin edit form tabs block
 *
 * @author Magento
 */
class Inchoo_Ticketmanager_Block_Adminhtml_Ticket_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize tabs and define tabs block settings
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('page_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('inchoo_ticketmanager')->__('Ticket Item Info'));
    }

    protected function _prepareLayout(){
        parent::_prepareLayout();
        $this->addTab('ticket_replies', array(
            'label'     => Mage::helper('catalog')->__('Replies'),
            'content'   => $this->getLayout()
                    ->createBlock('inchoo_ticketmanager/adminhtml_reply_grid')->toHtml(),
            'active'    => true,
        ));

        /*$this->addTab('ticket_replies2', array(
            'label'     => Mage::helper('catalog')->__('Replies2'),
            'url'       => $this->getUrl('*//***//*replies', array('_current' => true)),
            'active'    => true,
            'class'     => 'ajax',
        ));*/
    }
}
