<?php
/**
 * Ticket List admin grid
 *
 * @author Magento
 */
class Inchoo_Ticketmanager_Block_Adminhtml_Reply_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Init Grid default properties
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('reply_list_grid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Prepare collection for Grid
     *
     * @return Inchoo_Ticketmanager_Block_Adminhtml_Ticket_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('inchoo_ticketmanager/reply')->getResourceCollection();
        if($this->getRequest()->getParam('id')){
            $collection->addFieldToFilter('ticket_id', $this->getRequest()->getParam('id'));
        }

        $attributes = array('firstname', 'middlename', 'lastname');
        $replier = null;
        foreach($collection as $item){
            $str = '';
            if($item->getData('isAdmin') == 1){
                $replier = Mage::getModel('admin/user')
                    ->load($item->getData('admin_id'));
            }else{
                $replier = Mage::getModel('customer/customer')
                    ->load($item->getData('customer_id'));
            }
            foreach($attributes as $attribute){
                if($replier->getData($attribute) != null){
                    $str .= ' ';
                    $str .= $replier->getData($attribute);
                }
            }
            $item->setData('replier_name', trim($str));
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare Grid columns
     *
     * @return Mage_Adminhtml_Block_Catalog_Search_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('reply_id', array(
            'header'    => Mage::helper('inchoo_ticketmanager')->__('ID'),
            'width'     => '50px',
            'index'     => 'reply_id',
        ));

        $this->addColumn('content', array(
            'header'    => Mage::helper('inchoo_ticketmanager')->__('Content'),
            'index'     => 'content',
        ));

        $this->addColumn('replier_name', array(
            'header'    => Mage::helper('inchoo_ticketmanager')->__('Replier'),
            'index'     => 'replier_name',
            'width'    => '170px',
            'filter'    => false,
            'sortable'  => false
        ));

        $this->addColumn('created_at', array(
            'header'   => Mage::helper('inchoo_ticketmanager')->__('Created'),
            'sortable' => true,
            'width'    => '170px',
            'index'    => 'created_at',
            'type'     => 'datetime',
        ));

        $this->addColumn('isAdmin', array(
            'header'   => Mage::helper('inchoo_ticketmanager')->__('Replier is Admin'),
            'sortable' => true,
            //'filter'   => true,
            'width'    => '170px',
            'index'    => 'isAdmin',
            'type'     => 'options',
            'options'  => array(
                1 => Mage::helper('inchoo_ticketmanager')->__('Yes'),
                0 => Mage::helper('inchoo_ticketmanager')->__('No'),
            )
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('inchoo_ticketmanager')->__('Action'),
                'width'     => '100px',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(array(
                    'caption' => Mage::helper('inchoo_ticketmanager')->__('Edit'),
                    'url'     => array('base' => '*/*/edit'),
                    'field'   => 'id'
                )),
                'sortable'  => false,
                'index'     => 'reply',
            ));

        return parent::_prepareColumns();
    }

    /**
     * Return row URL for js event handlers
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/reply/edit', array('id' => $row->getId()));
    }

    /**
     * Grid url getter
     *
     * @return string current grid url
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/reply/grid', array('_current' => true));
    }
}