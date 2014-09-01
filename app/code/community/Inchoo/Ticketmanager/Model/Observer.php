<?php
/**
 * News module observer
 *
 * @author Magento
 */
class Inchoo_Ticketmanager_Model_Observer
{
    /**
     * Event before show news item on frontend
     * If specified new post was added recently (term is defined in config) we'll see message about this on front-end.
     *
     * @param Varien_Event_Observer $observer
     */
    public function beforeNewsDisplayed(Varien_Event_Observer $observer)
    {
        $ticketItem = $observer->getEvent()->getTicketItem();
        $currentDate = Mage::app()->getLocale()->date();
        $ticketCreatedAt = Mage::app()->getLocale()->date(strtotime($ticketItem->getCreatedAt()));
        $daysDifference = $currentDate->sub($ticketCreatedAt)->getTimestamp() / (60 * 60 * 24);
        if ($daysDifference < 3) {
            Mage::getSingleton('core/session')->addSuccess(Mage::helper('inchoo_ticketmanager')->__('Recently added'));
        }
    }
}