<?php

class Inchoo_Ticketmanager_IndexController extends Mage_Core_Controller_Front_Action
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
            return;
        }
	}

	public function indexAction()
	{
		$this->loadLayout();

		$listBlock = $this->getLayout()->getBlock('ticket.list');

		if($listBlock){
			$currentPage = abs(intval($this->getRequest()->getParam('p')));
			if($currentPage < 1){
				$currentPage = 1;
			}
			$listBlock->setCurrentPage($currentPage);
		}

		$this->renderLayout();
	}

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $model = Mage::getModel('inchoo_ticketmanager/ticket');

        $itemId = $this->getRequest()->getParam('id');
        if($itemId){
            $session = Mage::getSingleton('core/session');
            //check if editing is enabled
            if(!Mage::helper('inchoo_ticketmanager')->getEditTicketEnabled()){
                $this->_redirect('noRoute');
                return;
            }
            $model->load($itemId);
            if(!$model->getId()){
                $session->addError(Mage::helper('inchoo_ticketmanager')->__('Ticket item does not exist.'));
                return $this->_redirect('/ticket');
            }
            if($model->getData('customer_id') !=  Mage::getSingleton('customer/session')->getCustomer()->getId()){
                $this->_redirect('noRoute');
                return;
            }
            $this->_title($model->getSubject());
        }

        $data = Mage::getSingleton('customer/session')->getFormData(true);
        if(!empty($data)){
            $model->addData($data);
        }
        Mage::register('ticket_item', $model);
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        $redirectPath   = 'ticket/index/view';
        $redirectParams = array();

        // check if data sent
        $data = $this->getRequest()->getParams();
        $isNew = true;
        if ($data) {
            $session = Mage::getSingleton('core/session');
            $data = $this->_filterPostData($data);
            // init model and set data
            $model = Mage::getModel('inchoo_ticketmanager/ticket');

            // if ticket item exists, try to load it
            $ticketId = $this->getRequest()->getParam('ticket_id');
            if ($ticketId) {
                //check if editing of existing tickets is enabled
                if(!Mage::helper('inchoo_ticketmanager')->getEditTicketEnabled()){
                    $this->_redirect('noRoute');
                    return;
                }
                $model->load($ticketId);
                if($model->getId()){
                    if($model->getData('customer_id') !=  Mage::getSingleton('customer/session')->getCustomer()->getId()){
                        $session->addError(Mage::helper('inchoo_ticketmanager')->__('You can edit only your Tickets'));
                        $this->_redirect('noRoute');
                        return;
                    }
                }
                else{
                    $session->addError(Mage::helper('inchoo_ticketmanager')->__('Ticket Item does not exist'));
                    $this->_redirect('noRoute');
                    return;
                }
                $isNew = false;
            }

            if($isNew){
                $model->setData(
                    array(
                        'website_id' => Mage::app()->getWebsite()->getId(),
                        'customer_id' => Mage::getSingleton('customer/session')->getCustomer()->getId(),
                        'status' => Inchoo_Ticketmanager_Model_Ticket::STATUS_OPEN
                    ));
            }
            $model->addData($data);

            //backend validation
            if((!$model->hasSubject() || !$model->hasMessage()) || ($model->getSubject() == '' || $model->getMessage() == ''))
            {
                $session->setFormData($data);
                $session->addError('Please, fill all required fields.');
                return $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }

            try {
                $hasError = false;

                $model->save();

                $session->addSuccess(
                    Mage::helper('inchoo_ticketmanager')->__('The Ticket item has been saved.')
                );
                $redirectParams = array('id' => $model->getId());
            } catch (Mage_Core_Exception $e) {
                $hasError = true;
                $session->addError($e->getMessage());
            } catch (Exception $e) {
                $hasError = true;
                $session->addException($e,
                    Mage::helper('inchoo_ticketmanager')->__('An error occurred while saving the ticket item.')
                );
            }

            if ($hasError) {
                $session->setFormData($data);
                $redirectPath   = 'ticket/index/edit';
                $redirectParams = array('id' => $this->getRequest()->getParam('ticket_id'));
            }
        }

        $this->_redirect($redirectPath, $redirectParams);
    }

    protected function _filterPostData($data)
    {
        $data = $this->_filterDates($data, array('time_created'));
        unset($data['return_url']);
        return $data;
    }

	public function viewAction()
	{
		$ticketId = $this->getRequest()->getParam('id');
		if(!$ticketId){
			return $this->_forward('noRoute');
		}

		$model = Mage::getModel('inchoo_ticketmanager/ticket');
		$model->load($ticketId);

		if(!$model->getId()){
			return $this->_forward('noRoute');
		}

        if($model->getData('customer_id') != Mage::getSingleton('customer/session')->getCustomer()->getId()){
            return $this->_forward('noRoute');
        }

		Mage::register('ticket_item', $model);

		Mage::dispatchEvent('before_ticket_item_display', array('ticket_item' => $model));

		$this->loadLayout();
		$itemBlock = $this->getLayout()->getBlock('ticket.item');
		if($itemBlock){
			$listBlock = $this->getLayout()->getBlock('ticket.list');
			if($listBlock){
				$page = (int)$listBlock->getCurrentPage() ? (int)$listBlock->getCurrentPage() : 1;
			}
			else{
				$page = 1;
			}
			$itemBlock->setPage($page);
		}
		$this->renderLayout();
	}

    public function closeAction(){

        $ticket_id = $this->getRequest()->getParam('id');
        if(!$ticket_id){
            $this->_redirect('noRoute');
            return;
        }

        /**
         * @var Inchoo_Ticketmanager_Model_Ticket
         */
        $model = Mage::getModel('inchoo_ticketmanager/ticket')->load($ticket_id);
        if(!$model->getId()){
            $this->_redirect('noRoute');
            return;
        }
        //customers may close only their own tickets
        if($model->getData('customer_id') != Mage::getSingleton('customer/session')->getCustomer()->getId()){
            $this->_redirect('noRoute');
            return;
        }

        $model->setData('status', Inchoo_Ticketmanager_Model_Ticket::STATUS_CLOSED);
        $session = Mage::getSingleton('core/session');
        try{
            $model->save();
            $session->addSuccess(
                Mage::helper('inchoo_ticketmanager')->__('The Ticket item has been closed.')
            );
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        } catch (Exception $e) {
            $session->addException($e,
                Mage::helper('inchoo_ticketmanager')->__('An error occurred while closing the ticket item.')
            );
        }

        $this->_redirect('ticket/index/view', array('id' => $ticket_id));
    }
}