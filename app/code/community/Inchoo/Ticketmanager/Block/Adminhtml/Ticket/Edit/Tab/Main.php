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
            'label'    => Mage::helper('inchoo_ticketmanager')->__('Created'),
            'title'    => Mage::helper('inchoo_ticketmanager')->__('Created'),
            'readonly' => true,
        ));

        $fieldset->addField('status', 'select', array(
            'name'     => 'status',
            'label'    => Mage::helper('inchoo_ticketmanager')->__('Status'),
            'title'    => Mage::helper('inchoo_ticketmanager')->__('Status'),
            'required' => true,
            'disabled' => $isElementDisabled,
            'values'   => array(
                1  => array('value' => 0, 'label' => Mage::helper('inchoo_ticketmanager')->__('Open')),
                2  => array('value' => 1, 'label' => Mage::helper('inchoo_ticketmanager')->__('Closed'))
            )
        ));

        $model->setData('website_name', Mage::getModel('core/website')->load($model->getData('website_id'))->getData('name'));

        $fieldset->addField('website_name', 'text', array(
            'name'     => 'website_name',
            'label'    => Mage::helper('inchoo_ticketmanager')->__('Website'),
            'title'    => Mage::helper('inchoo_ticketmanager')->__('Website'),
            'disabled' => $isElementDisabled,
            'readonly' => true
        ));

        $fieldset = $form->addFieldset('customer_fieldset', array(
            'legend' => Mage::helper('inchoo_ticketmanager')->__('Customer Info')
        ));

        $customer = Mage::getModel('customer/customer')->load($model->getData('customer_id'));

        $model->setData('customer_email', $customer->getData('email'));
        $attributes = array('firstname', 'middlename', 'lastname');
        $str = '';
        foreach($attributes as $attribute){
            if($customer->getData($attribute) != null){
                $str .= ' ';
                $str .= $customer->getData($attribute);
            }
        }
        $model->setData('customer_name', trim($str));


        $fieldset->addField('customer_name', 'link', array(
            'name'     => 'customer_name',
            'href'     => Mage::helper("adminhtml")->getUrl('adminhtml/customer/edit', array('id' => $model->getData('customer_id'))),
            'label'    => Mage::helper('inchoo_ticketmanager')->__('Customer'),
            'readonly' => true,
            'title'    => 'Show customer'
        ));
        $fieldset->addField('customer_email', 'link', array(
            'name'     => 'customer_email',
            'href'     => 'mailto:'.$model->getData('customer_email'),
            'label'    => Mage::helper('inchoo_ticketmanager')->__('Customer Email'),
            'title'    => Mage::helper('inchoo_ticketmanager')->__('Send Email'),
            'readonly' => true
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
