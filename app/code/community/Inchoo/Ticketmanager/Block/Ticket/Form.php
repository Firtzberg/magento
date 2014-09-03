<?php

class Inchoo_Ticketmanager_Block_Ticket_Form extends Mage_Core_Block_Template
{
    protected function _construct(){
        $this->setTemplate('inchoo/ticketmanager/ticket/form.phtml');
    }

    public function  getSaveUrl(){
        return Mage::getUrl('*/*/save');
    }

    /**
     * @return Inchoo_Ticketmanager_Model_Ticket
     */
    public function  getTicket(){
        return Mage::registry('ticket_item');
    }
}