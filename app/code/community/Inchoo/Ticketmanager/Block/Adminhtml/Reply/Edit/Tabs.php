<?php
/**
 * Ticket List admin edit form tabs block
 *
 * @author Magento
 */
class Inchoo_Ticketmanager_Block_Adminhtml_Reply_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
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
        $this->setTitle(Mage::helper('inchoo_ticketmanager')->__('Reply Item Info'));
    }
}
