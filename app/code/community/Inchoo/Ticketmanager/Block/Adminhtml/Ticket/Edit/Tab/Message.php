<?php
/**
 * Ticket List admin edit form content tab
 *
 * @author Magento
 */
class Inchoo_Ticketmanager_Block_Adminhtml_Ticket_Edit_Tab_Message
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Load WYSIWYG on demand and prepare layout
     *
     * @return Inchoo_Ticketmanager_Block_Adminhtml_Ticket_Edit_Tab_Messsage
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        return $this;
    }

    /**
     * Prepares tab form
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $model = Mage::helper('inchoo_ticketmanager')->getTicketItemInstance();

        /**
         * Checking if user have permissions to save information
         */
        if (Mage::helper('inchoo_ticketmanager/admin')->isActionAllowed('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('ticket_message_');

        $fieldset = $form->addFieldset('message_fieldset', array(
            'legend' => Mage::helper('inchoo_ticketmanager')->__('Message'),
            'class'  => 'fieldset-wide'
        ));

        $fieldset->addField('message', 'textarea', array(
            'label'    => Mage::helper('inchoo_ticketmanager')->__('Message'),
            'title'    => Mage::helper('inchoo_ticketmanager')->__('Message'),
            'name'     => 'message',
            'required' => true,
            'disabled' => $isElementDisabled,
        ));

        // Setting custom renderer for content field to remove label column

        $form->setValues($model->getData());
        $this->setForm($form);

        Mage::dispatchEvent('adminhtml_ticket_edit_tab_content_prepare_form', array('form' => $form));

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('inchoo_ticketmanager')->__('Message');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('inchoo_ticketmanager')->__('Message');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }
}
