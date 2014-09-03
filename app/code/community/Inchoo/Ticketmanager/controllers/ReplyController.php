<?php

class Inchoo_Ticketmanager_ReplyController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();
        if(!Mage::helper('inchoo_ticketmanager')->isEnabled()){
            $this->setFlag('', 'no-dispatch', true);
            $this->_redirect('noRoute');
        }
        if (!Mage::helper('customer')->isLoggedIn()) {
            $this->setFlag('', 'no-dispatch', true);
            $this->_redirect('customer/account');
        }
    }

    public function saveAction(){
        // check if data sent
        $data = $this->getRequest()->getParams();
        if ($data) {
            $data = $this->_filterPostData($data);
            // init model and set data
            $model = Mage::getModel('inchoo_ticketmanager/reply');

            $ticketId = $this->getRequest()->getParam('ticket_id');
            if (!$ticketId) {
                $this->_redirect('noRoute');
            }

            $model->addData(array('ticket_id' => $data['ticket_id'],
            'content' => $data['content']));

            $session = Mage::getSingleton('core/session');
            try {
                $model->save();

                $session->addSuccess(
                    Mage::helper('inchoo_ticketmanager')->__('The Reply has been posted.')
                );
            } catch (Mage_Core_Exception $e) {
                $session->addError($e->getMessage());
            } catch (Exception $e) {
                $session->addException($e,
                    Mage::helper('inchoo_ticketmanager')->__('An error occurred while posting the reply.')
                );
            }
        }

        $this->_redirect('ticket/index/view', array('id' => $this->getRequest()->getParam('ticket_id')));
    }

    protected function _filterPostData($data)
    {
        $data = $this->_filterDates($data, array('time_created'));
        unset($data['return_url']);
        return $data;
    }

}