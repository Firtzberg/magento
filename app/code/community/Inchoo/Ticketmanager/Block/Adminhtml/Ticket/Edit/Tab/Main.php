<?php
/**
 * Ticket List admin edit form main tab
 *
 * @author Magento
 */
class Inchoo_Ticketmanager_Block_Adminhtml_Ticket_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare form elements for tab
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

        $form->setHtmlIdPrefix('ticket_main_');

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('inchoo_ticketmanager')->__('Ticket Item Info')
        ));

        if ($model->getId()) {
            $fieldset->addField('ticket_id', 'hidden', array(
                'name' => 'ticket_id',
            ));
        }

        $fieldset->addField('subject', 'text', array(
            'name'     => 'subject',
            'label'    => Mage::helper('inchoo_ticketmanager')->__('Ticket Subject'),
            'title'    => Mage::helper('inchoo_ticketmanager')->__('Ticket Subject'),
            'required' => true,
            'disabled' => $isElementDisabled
        ));

        $fieldset->addField('created_at', 'date', array(
            'name'     => 'created_at',
            'format'   => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
            'image'    => $this->getSkinUrl('images/grid-cal.gif'),
            'label'    => Mage::helper('inchoo_ticketmanager')->__('Created'),
            'title'    => Mage::helper('inchoo_ticketmanager')->__('Created'),
            'required' => true
        ));

        Mage::dispatchEvent('adminhtml_ticket_edit_tab_main_prepare_form', array('form' => $form));

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('inchoo_ticketmanager')->__('Ticket Info');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('inchoo_ticketmanager')->__('Ticket Info');
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
