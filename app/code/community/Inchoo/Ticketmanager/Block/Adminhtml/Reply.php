<?php
/**
 * Ticket List admin grid container
 *
 * @author Magento
 */
class Inchoo_Ticketmanager_Block_Adminhtml_Reply extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Block constructor
     */
    public function __construct()
    {
        $this->_blockGroup = 'inchoo_ticketmanager';
        $this->_controller = 'adminhtml_reply';
        $this->_headerText = Mage::helper('inchoo_ticketmanager')->__('Manage Replies');

        parent::__construct();

        /*if (Mage::helper('inchoo_ticketmanager/admin')->isReplyActionAllowed('save')) {
            $this->_updateButton('add', 'label', Mage::helper('inchoo_ticketmanager')->__('Add New Reply'));
        } else {*/
            $this->_removeButton('add');
        //}

    }
}