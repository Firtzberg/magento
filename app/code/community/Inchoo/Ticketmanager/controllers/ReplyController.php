<?php

class Inchoo_Ticketmanager_ReplyController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();
        if(!Mage::helper('inchoo_ticketmanager')->isEnabled()){
            $this->setFlag('', 'no-dispatch', true);
            $this->_redirect('noRoute');
            return;
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
                return;
            }
            if(Mage::getModel('inchoo_ticketmanager/ticket')
                ->getCollection()
                ->addFieldToFilter('ticket_id', $ticketId)
                ->count() < 1){
                $this->_redirect('noRoute');
                return;
            }

            $model->addData(array('ticket_id' => $data['ticket_id'],
                'content' => $data['content'],
                'isAdmin' => 0,
                'customer_id' => Mage::getSingleton('customer/session')->getCustomer()->getId()
            ));

            $session = Mage::getSingleton('core/session');

            //backend validation
            if(!$model->hasContent() || $model->getContent() == ''){
                $session->setFormData($data);
                $session->addError('Please, fill all required fields.');
                return $this->_redirect('*/index/view', array('id' => $this->getRequest()->getParam('ticket_id')));
            }

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