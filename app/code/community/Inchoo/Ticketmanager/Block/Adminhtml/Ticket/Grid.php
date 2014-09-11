<?php
/**
 * Ticket List admin grid
 *
 * @author Magento
 */
class Inchoo_Ticketmanager_Block_Adminhtml_Ticket_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Init Grid default properties
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('ticket_list_grid');
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
        $wanted = array('firstname', 'middlename', 'lastname');
        /**
         * @var Inchoo_Ticketmanager_Model_Resource_Ticket_Collection
         */
        $collection = Mage::getModel('inchoo_ticketmanager/ticket')->getResourceCollection();
        $collection->includeWebsiteNames();
        $collection = Mage::helper('inchoo_ticketmanager')->joinCustomerNameToFlatTable($collection);

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
        $this->addColumn('ticket_id', array(
            'header'    => Mage::helper('inchoo_ticketmanager')->__('ID'),
            'width'     => '50px',
            'index'     => 'ticket_id',
        ));

        $this->addColumn('subject', array(
            'header'    => Mage::helper('inchoo_ticketmanager')->__('Ticket Subject'),
            'index'     => 'subject',
        ));

        $this->addColumn('customer_name', array(
            'header'    => Mage::helper('inchoo_ticketmanager')->__('Customer'),
            'index'     => 'customer_name',
            'filter' => false
        ));

        $this->addColumn('website_name', array(
            'header'    => Mage::helper('inchoo_ticketmanager')->__('Website'),
            'index'     => 'website_name',
            'filter_index' => 'ws.name'
        ));

        $this->addColumn('created_at', array(
            'header'   => Mage::helper('inchoo_ticketmanager')->__('Created'),
            'sortable' => true,
            'width'    => '170px',
            'index'    => 'created_at',
            'type'     => 'datetime',
        ));

        $this->addColumn('status', array(
            'header'   => Mage::helper('inchoo_ticketmanager')->__('Status'),
            'sortable' => true,
            'width'    => '170px',
            'index'    => 'status',
            'type'     => 'options',
            'options'  => array(
                1 => Mage::helper('inchoo_ticketmanager')->__('Closed'),
                0 => Mage::helper('inchoo_ticketmanager')->__('Open'),
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
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'ticket',
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
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /**
     * Grid url getter
     *
     * @return string current grid url
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
}