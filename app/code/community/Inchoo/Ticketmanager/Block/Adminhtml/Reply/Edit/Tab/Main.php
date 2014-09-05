<?php
/**
 * Ticket List admin edit form main tab
 *
 * @author Magento
 */
class Inchoo_Ticketmanager_Block_Adminhtml_Reply_Edit_Tab_Main
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
        /**
         * @var Inchoo_Ticketmanager_Model_Reply
         */
        $model = Mage::helper('inchoo_ticketmanager')->getReplyItemInstance();

        /**
         * Checking if user have permissions to save information
         */
        if (Mage::helper('inchoo_ticketmanager/admin')->isReplyActionAllowed('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('reply_main_');

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('inchoo_ticketmanager')->__('Reply Item Info')
        ));

        if ($model->getId()) {
            $fieldset->addField('reply_id', 'hidden', array(
                'name' => 'reply_id',
            ));
        }

        if ($model->hasData('ticket_id')) {
            $fieldset->addField('ticket_id', 'hidden', array(
                'name' => 'ticket_id',
            ));
        }

        $fieldset->addField('content', 'textarea', array(
            'name'     => 'content',
            'label'    => Mage::helper('inchoo_ticketmanager')->__('Reply Content'),
            'title'    => Mage::helper('inchoo_ticketmanager')->__('Reply Content'),
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

        $fieldset = $form->addFieldset('replier_fieldset', array(
            'legend' => Mage::helper('inchoo_ticketmanager')->__('Replier Info')
        ));

        $fieldset->addField('isAdmin', 'select', array(
            'name'     => 'isAdmin',
            'label'    => Mage::helper('inchoo_ticketmanager')->__('Replier is Admin'),
            'title'    => Mage::helper('inchoo_ticketmanager')->__('Replier is Admin'),
            'required' => false,
            'disabled' => true,
            'values'   => array(
                1  => array('value' => 0, 'label' => Mage::helper('inchoo_ticketmanager')->__('No')),
                2  => array('value' => 1, 'label' => Mage::helper('inchoo_ticketmanager')->__('Yes'))
            ),
            'readonly' => true,
        ));


        $attributes = array('firstname', 'middlename', 'lastname');
        $replier = null;
        $str = '';
        if($model->getData('isAdmin') == 1){
            $replier = Mage::getModel('admin/user')
                ->load($model->getData('admin_id'));
        }else{
            $replier = Mage::getModel('customer/customer')
                ->load($model->getData('customer_id'));
        }
        foreach($attributes as $attribute){
            if($replier->getData($attribute) != null){
                $str .= ' ';
                $str .= $replier->getData($attribute);
            }
        }

        $model->setData('replier_name', trim($str));
        $model->setData('replier_id', $replier->getId());

        if($model->getData('isAdmin')){
            if ($model->getId()) {
                $fieldset->addField('admin_id', 'hidden', array(
                    'name' => 'admin_id',
                ));
            }
            $fieldset->addField('replier_name', 'label', array(
                'name'     => 'replier_name',
                'label'    => Mage::helper('inchoo_ticketmanager')->__('Replier'),
                'readonly' => true,
                'title'    => 'Replier'
            ));
        } else{
            if ($model->getId()) {
                $fieldset->addField('customer_id', 'hidden', array(
                    'name' => 'customer_id',
                ));
            }
            $fieldset->addField('replier_name', 'link', array(
                'name'     => 'replier_name',
                'href'     => Mage::helper("adminhtml")->getUrl('adminhtml/customer/edit',
                        array('id' => $model->getData('replier_id'))),
                'label'    => Mage::helper('inchoo_ticketmanager')->__('Replier'),
                'readonly' => true,
                'title'    => 'Show replier'
            ));
        }

        Mage::dispatchEvent('adminhtml_reply_edit_tab_main_prepare_form', array('form' => $form));

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
        return Mage::helper('inchoo_ticketmanager')->__('Reply Info');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('inchoo_ticketmanager')->__('Reply Info');
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
