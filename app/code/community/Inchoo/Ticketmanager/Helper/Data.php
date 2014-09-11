<?php

class Inchoo_Ticketmanager_Helper_Data extends Mage_Core_Helper_Data
{
	const XML_PATH_ENABLED = 'ticket/view/enabled';

    const XML_PATH_ITEMS_PER_PAGE = 'ticket/view/items_per_page';

    const XML_PATH_REPLIES_PER_PAGE = 'ticket/view/replies_per_page';

    const XML_PATH_EDIT_TICKET_ENABLED = 'ticket/view/edit_ticket_enabled';

    protected $_ticketItemInstance;
    protected $_replyItemInstance;

	public function isEnabled($store = null){
		return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED, $store);
	}

    public function getTicketsPerPage($store = null)
    {
        return abs((int)Mage::getStoreConfig(self::XML_PATH_ITEMS_PER_PAGE, $store));
    }

    public function getRepliesPerPage($store = null)
    {
        return abs((int)Mage::getStoreConfig(self::XML_PATH_REPLIES_PER_PAGE, $store));
    }

    public function getTicketItemInstance()
    {
        if(!$this->_ticketItemInstance){
            $this->_ticketItemInstance = Mage::registry('ticket_item');

            if(!$this->_ticketItemInstance){
                Mage::throwException($this->__('Ticket item instance does not exist in Registry'));
            }
        }

        return $this->_ticketItemInstance;
    }

    public function getReplyItemInstance()
    {
        if(!$this->_replyItemInstance){
            $this->_replyItemInstance = Mage::registry('reply_item');

            if(!$this->_replyItemInstance){
                Mage::throwException($this->__('Reply item instance does not exist in Registry'));
            }
        }

        return $this->_replyItemInstance;
    }

    public function getEditTicketEnabled(){
        return false;
        return Mage::getConfig(self::XML_PATH_EDIT_TICKET_ENABLED);
    }

    /**
     * @param Mage_Core_Model_Resource_Db_Collection_Abstract $collection flat table collection
     * @param string $MainTableForeignKey Foreign key column name in flat table
     * @param array $columns array of strings of joining columns
     * @param string $eavType type of entity, can be found in eav_entity_type
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    public function joinEavAttibutesToFlatTable($collection, $MainTableForeignKey, $eavType, $columns)
    {
        $entityType = Mage::getModel('eav/entity_type')->loadByCode($eavType);
        $entityTable = Mage::getSingleton('core/resource')->getTableName($entityType->getEntityTable());
        $attributes = $entityType->getAttributeCollection()->getItems();
        $found = array();
        foreach($attributes as $attribute){
            foreach($columns as $c)
                if($attribute->getAttributeCode() == $c)
                {
                    $found[$c] = $attribute;
                }
        }
        foreach($found as $column => $attribute){
            $alias = $eavType.'T_'.$column;
            $table = $entityTable.'_'.$attribute->getBackendType();
            $collection->getSelect()
                ->joinLeft(array($alias => $table),
                    'main_table.'.$MainTableForeignKey.'='.$alias.'.entity_id and '.$alias.'.attribute_id='.$attribute->getAttributeId(),
                    array($eavType.'_'.$column => $alias.'.value')
                );
        }
        return $collection;
    }

    /**
     * @param Mage_Core_Model_Resource_Db_Collection_Abstract $collection flat table collection
     * @param string $MainTableForeignKey Name of column used as foreign key
     * @param string $columnName Name of customer name column in result
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    public function joinCustomerNameToFlatTable($collection, $MainTableForeignKey = 'customer_id', $columnName = 'customer_name')
    {
        $entityType = Mage::getModel('eav/entity_type')->loadByCode('customer');
        $entityTable = Mage::getSingleton('core/resource')->getTableName($entityType->getEntityTable());
        $attributes = $entityType->getAttributeCollection()->getItems();
        $found = array();
        $columns = array('prefix', 'firstname', 'middlename', 'lastname', 'suffix');
        foreach($attributes as $attribute){
            foreach($columns as $c)
                if($attribute->getAttributeCode() == $c)
                {
                    $found[$c] = $attribute;
                }
        }
        foreach($found as $c => $attribute){
            $collection->getSelect()
                ->joinLeft(array($c.'T' => $entityTable.'_varchar'),
                    'main_table.'.$MainTableForeignKey.'='.$c.'T.entity_id and '.$c.'T.attribute_id='.$attribute->getAttributeId(),
                null);
        }
        $columnSelect = 'LTRIM(concat(';
        foreach($columns as $c){
            if($found[$c])
                $columnSelect .= 'if('.$c.'T.value is not null and '.$c.'T.value != "",CONCAT('.$c.'T.value, " "),""),';
        }
        $columnSelect = rtrim($columnSelect, ',');
        $columnSelect .= ')) as '.$columnName;
        $collection->getSelect()
            ->columns($columnSelect);
        return $collection;
    }
}