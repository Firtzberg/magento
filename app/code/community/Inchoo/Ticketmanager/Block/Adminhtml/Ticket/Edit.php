<?php
/**
 * Ticket List admin edit form container
 *
 * @author Magento
 */
class Inchoo_Ticketmanager_Block_Adminhtml_Ticket_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Initialize edit form container
     *
     */
    public function __construct()
    {
        $this->_objectId   = 'id';
        $this->_blockGroup = 'inchoo_ticketmanager';
        $this->_controller = 'adminhtml_ticket';

        parent::__construct();

        if (Mage::helper('inchoo_ticketmanager/admin')->isActionAllowed('save')) {
            $this->_updateButton('save', 'label', Mage::helper('inchoo_ticketmanager')->__('Save Ticket Item'));
            $this->_addButton('saveandcontinue', array(
                'label'   => Mage::helper('adminhtml')->__('Save and Continue Edit'),
                'onclick' => 'saveAndContinueEdit()',
                'class'   => 'save',
            ), -100);
        } else {
            $this->_removeButton('save');
        }

        $this->_updateButton('delete', 'label', Mage::helper('inchoo_ticketmanager')->__('Delete Ticket Item'));

        $this->_addButton('reply', array(
            'label' => Mage::helper('adminhtml')->__('Reply to Ticket'),
            'onclick' => "setLocation('".$this->getUrl('*/reply/new', array('ticket_id' => $this->getRequest()->getParam($this->_objectId)))."')",
        ), 0, 100);

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
        $model = Mage::helper('inchoo_ticketmanager')->getTicketItemInstance();
        if ($model->getId()) {
            return Mage::helper('inchoo_ticketmanager')->__("Edit Ticket Item '%s'",
                $this->escapeHtml($model->getSubject()));
        } else {
            return Mage::helper('inchoo_ticketmanager')->__('New Ticket Item');
        }
    }
}