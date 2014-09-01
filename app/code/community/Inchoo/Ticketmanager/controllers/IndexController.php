<?php

class Inchoo_Ticketmanager_IndexController extends Mage_Core_Controller_Front_Action
{
	public function preDispatch()
	{
		parent::preDispatch();
		if(!Mage::helper('inchoo_ticketmanager')->isEnabled()){
			$this->setFlag('', 'no-dispatch', true);
			$this->_redirect('noRoute');
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
}