<?php
/**
 * Created by PhpStorm.
 * User: Hrvoje
 * Date: 9/1/14
 * Time: 8:53 AM
 */

class Inchoo_Ticketmanager_Helper_Admin  extends Mage_Core_Helper_Abstract{

    /**
     * @param string $action
     * @return bool
     */
    public function isActionAllowed($action){
        return Mage::getSingleton('Admin/session')->isAllowed('ticket/manage/'.$action);
    }

} 