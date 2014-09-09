<?php
/**
 * Ticket List admin grid container
 *
 * @author Magento
 */
class Inchoo_Ticketmanager_Block_Adminhtml_Ticket extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Block constructor
     */
    public function __construct()
    {
        $this->_blockGroup = 'inchoo_ticketmanager';
        $this->_controller = 'adminhtml_ticket';
        $this->_headerText = Mage::helper('inchoo_ticketmanager')->__('Manage Tickets');

        parent::__construct();

        $this->_removeButton('add');
    }
}