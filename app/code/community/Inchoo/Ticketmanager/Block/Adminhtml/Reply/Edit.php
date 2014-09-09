<?php
/**
 * Ticket List admin edit form container
 *
 * @author Magento
 */
class Inchoo_Ticketmanager_Block_Adminhtml_Reply_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Initialize edit form container
     *
     */
    public function __construct()
    {
        $this->_objectId   = 'id';
        $this->_blockGroup = 'inchoo_ticketmanager';
        $this->_controller = 'adminhtml_reply';

        parent::__construct();

        //in case a new reply is made
        $ticket_id = $this->getRequest()->getParam('ticket_id');
        if(!$ticket_id){
            //if reply is edited
            $model = Mage::getModel('inchoo_ticketmanager/reply')->load($this->getRequest()->getParam($this->_objectId));
            if($model->getId()){
                //reply really exists
                $ticket_id = $model->getData('ticket_id');
            }
        }
        if($ticket_id){
            $this->_addButton('to_ticket',array(
                'label' => Mage::helper('adminhtml')->__('Back to Ticket'),
                'onclick' => "setLocation('".$this->getUrl('*/ticket/edit', array('id' => $ticket_id))."')",
            ),0,100);
        }

        if (Mage::helper('inchoo_ticketmanager/admin')->isActionAllowed('save')) {
            $this->_updateButton('save', 'label', Mage::helper('inchoo_ticketmanager')->__('Save Reply Item'));
            $this->_addButton('saveandcontinue', array(
                'label'   => Mage::helper('adminhtml')->__('Save and Continue Edit'),
                'onclick' => 'saveAndContinueEdit()',
                'class'   => 'save',
            ), -100);
        } else {
            $this->_removeButton('save');
        }

        if (Mage::helper('inchoo_ticketmanager/admin')->isActionAllowed('delete')) {
            $this->_updateButton('delete', 'label', Mage::helper('inchoo_ticketmanager')->__('Delete Reply Item'));
        } else {
            $this->_removeButton('delete');
        }

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('page_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'page_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'page_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        $model = Mage::helper('inchoo_ticketmanager')->getReplyItemInstance();
        if ($model->getId()) {
            return Mage::helper('inchoo_ticketmanager')->__("Edit Reply Item '%s'",
                $this->escapeHtml($model->getSubject()));
        } else {
            return Mage::helper('inchoo_ticketmanager')->__('New Reply Item');
        }
    }
}