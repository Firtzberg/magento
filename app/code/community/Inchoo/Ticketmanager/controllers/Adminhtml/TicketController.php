<?php

class Inchoo_Ticketmanager_Adminhtml_TicketController extends Mage_adminhtml_Controller_Action
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
		$this->_title($this->__('Tickets'))
		->_title($this->__('Manage Tickets'));
		$this->_initAction();
		$this->renderLayout();
	}

	public function newAction()
	{
		$this->_forward('edit');
	}

	public function editAction()
	{
		$this->_title($this->__('Tickets'))
		->_title($this->__('Manage Tickets'));

		$model = Mage::getModel('inchoo_ticketmanager/ticket');

		$itemId = $this->getRequest()->getParam('id');
		if($itemId){
			$model->load($itemId);
			if(!$model->getId()){
				$this->_getSession()->addError(Mage::helper('inchoo_ticketmanager')->__('Ticket item does not exist.'));
				return $this->_redirect('*/*/');
			}
			$this->_title($model->getSubject());
			$breadCrumb = Mage::helper('inchoo_ticketmanager')->__('Edit Item');
		} else{
			$this->_title(Mage::helper('inchoo_ticketmanager')->__('New Item'));
			$breadCrumb = Mage::helper('inchoo_ticketmanager')->__('New Item');
		}

		$this->_initAction()->_addBreadcrumb($breadCrumb, $breadCrumb);

		$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
		if(!empty($data)){
			$model->addData($data);
		}
		Mage::register('ticket_item', $model);

		$this->renderLayout();
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
            // init model and set data
            $model = Mage::getModel('inchoo_ticketmanager/ticket');

            // if ticket item exists, try to load it
            $ticketId = $this->getRequest()->getParam('ticket_id');
            if ($ticketId) {
                $model->load($ticketId);
            }

            $model->addData($data);

            try {
                $hasError = false;
                $model->save();

                $this->_getSession()->addSuccess(
                    Mage::helper('inchoo_ticketmanager')->__('The Ticket item has been saved.')
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
                    Mage::helper('inchoo_ticketmanager')->__('An error occurred while saving the ticket item.')
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
        if ($itemId) {
            try {
                // init model and delete
                /** @var $model Inchoo_Ticketmanager_Model_Item */
                $model = Mage::getModel('inchoo_ticketmanager/ticket');
                $model->load($itemId);
                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('inchoo_ticketmanager')->__('Unable to find a ticket item.'));
                }
                $model->delete();

                // display success message
                $this->_getSession()->addSuccess(
                    Mage::helper('inchoo_ticketmanager')->__('The ticket item has been deleted.')
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('inchoo_ticketmanager')->__('An error occurred while deleting the ticket item.')
                );
            }
        }

        // go to grid
        $this->_redirect('*/*/');
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