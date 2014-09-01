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
        $collection = Mage::getModel('inchoo_ticketmanager/ticket')->getResourceCollection();

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

        $this->addColumn('message', array(
            'header'    => Mage::helper('inchoo_ticketmanager')->__('Message'),
            'index'     => 'message',
        ));

        $this->addColumn('created_at', array(
            'header'   => Mage::helper('inchoo_ticketmanager')->__('Created'),
            'sortable' => true,
            'width'    => '170px',
            'index'    => 'created_at',
            'type'     => 'datetime',
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