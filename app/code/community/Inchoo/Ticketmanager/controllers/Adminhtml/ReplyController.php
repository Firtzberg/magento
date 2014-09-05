<?php

class Inchoo_Ticketmanager_Adminhtml_ReplyController extends Mage_adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('ticket/manage')
            ->_addBreadcrumb(
                Mage::helper('inchoo_ticketmanager')->__('Tickets'),
                Mage::helper('inchoo_ticketmanager')->__('Tickets')
            )
            ->_addBreadcrumb(
                Mage::helper('inchoo_ticketmanager')->__('Manage Tickets'),
                Mage::helper('inchoo_ticketmanager')->__('Manage Tickets')
            );
        return $this;
    }

    public function indexAction()
    {
        $this->_initAction();
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_title($this->__('Tickets'))
            ->_title($this->__('Reply'));

        //id represents the ticket
        $itemId = $this->getRequest()->getParam('ticket_id');
        if(!$itemId){
            $this->_getSession()->addError('No ticket was referenced');
            return $this->_redirectToTicketGrid();
        }

        $model = Mage::getModel('inchoo_ticketmanager/ticket');
        $model->load($itemId);
        if(!$model->getId()){
            $this->_getSession()->addError('Ticket item does not exist.');
            return $this->_redirectToTicketGrid();
        }

        $this->_initAction();

        $model = Mage::getModel('inchoo_ticketmanager/reply');
        $model->setData('ticket_id', $itemId);
        $model->setData('isAdmin', 1);
        $model->setData('created_at', date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time())));
        $model->setData('admin_id', Mage::getSingleton('admin/session')->getUser()->getId());
        Mage::register('reply_item', $model);

        $this->renderLayout();
    }

    public function editAction()
    {
        //id represents the reply
        $this->_title($this->__('Tickets'))
            ->_title($this->__('Edit Reply'));

        $model = Mage::getModel('inchoo_ticketmanager/reply');

        $itemId = $this->getRequest()->getParam('id');
        if(!$itemId){
            return $this->_redirectToTicketGrid();
        }

        $model->load($itemId);
        if(!$model->getId()){
            $this->_getSession()->addError(Mage::helper('inchoo_ticketmanager')->__('Reply item does not exist.'));
            return $this->_redirectToTicketGrid();
        }

        $this->_initAction()->_addBreadcrumb('Edit Reply','Edit Reply');

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if(!empty($data)){
            $model->addData($data);
        }
        Mage::register('reply_item', $model);

        $this->renderLayout();
    }

    private function _redirectToTicketGrid(){
        return $this->_redirect('*/ticket/index');
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        $redirectPath   = '*/*';
        $redirectParams = array();

        // check if data sent
        $data = $this->getRequest()->getPost();
        if ($data) {
            $data = $this->_filterPostData($data);
            //check if according ticket exists
            // init model and set data
            $model = Mage::getModel('inchoo_ticketmanager/ticket');

            // if ticket item exists, try to load it
            $ticketId = $this->getRequest()->getParam('ticket_id');
            if(!$ticketId){
                $this->_getSession()->addError('No ticket was referenced');
                return $this->_redirectToTicketGrid();
            }
            $model->load($ticketId);
            if(!$model->getId()){
                $this->_getSession()->addError('The according Ticket could not be found.');
                return $this->_redirectToTicketGrid();
            }
            //according ticket exists

            $model = Mage::getModel('inchoo_ticketmanager/reply');
            $replyId = $this->getRequest()->getParam('reply_id');
            if($replyId){                //an existing reply has been edited
                $model->load($replyId);
            }
            else{
                $model->setData('isAdmin', true);
                $model->setData('admin_id', Mage::getSingleton('admin/session')->getUser()->getId());
            }
            $model->addData($data);


            try {
                $hasError = false;
                $model->save();

                $this->_getSession()->addSuccess(
                    Mage::helper('inchoo_ticketmanager')->__('The Reply item has been saved.')
                );

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $redirectPath   = '*/*/edit';
                    $redirectParams = array('id' => $model->getId());
                }
            } catch (Mage_Core_Exception $e) {
                $hasError = true;
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $hasError = true;
                $this->_getSession()->addException($e,
                    Mage::helper('inchoo_ticketmanager')->__('An error occurred while saving the reply item.')
                );
            }

            if ($hasError) {
                $this->_getSession()->setFormData($data);
                $redirectPath   = '*/*/edit';
                $redirectParams = array('id' => $this->getRequest()->getParam('id'));
            }
        }

        $this->_redirect($redirectPath, $redirectParams);
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        // check if we know what should be deleted
        $itemId = $this->getRequest()->getParam('id');
        $ticketId = null;
        if ($itemId) {
            try {
                // init model and delete
                /** @var $model Inchoo_Ticketmanager_Model_Reply */
                $model = Mage::getModel('inchoo_ticketmanager/reply');
                $model->load($itemId);
                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('inchoo_ticketmanager')->__('Unable to find the reply item.'));
                }
                $ticketId = $model->getData('ticket_id');
                $model->delete();

                // display success message
                $this->_getSession()->addSuccess(
                    Mage::helper('inchoo_ticketmanager')->__('The reply item has been deleted.')
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('inchoo_ticketmanager')->__('An error occurred while deleting the reply item.')
                );
            }
        }

        // go to grid
        if($ticketId == null)
            return $this->_redirectToTicketGrid();
        $this->_redirect('*/ticket/edit', array('id' => $ticketId));
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'new':
            case 'save':
                return Mage::getSingleton('admin/session')->isAllowed('ticket/manage/save');
                break;
            case 'delete':
                return Mage::getSingleton('admin/session')->isAllowed('ticket/manage/delete');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('ticket/manage');
                break;
        }
    }

    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array
     * @return array
     */
    protected function _filterPostData($data)
    {
        $data = $this->_filterDates($data, array('time_created'));
        return $data;
    }

    /**
     * Grid ajax action
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function flushAction()
    {
        $this->_forward('index');
    }
}