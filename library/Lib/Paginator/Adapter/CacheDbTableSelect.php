<?php

class Lib_Paginator_Adapter_CacheDbTableSelect extends Zend_Paginator_Adapter_DbSelect
{
    /**
     * Returns a Zend_Db_Table_Rowset_Abstract of items for a page.
     *
     * @param  integer $offset Page offset
     * @param  integer $itemCountPerPage Number of items per page
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $this->_select->limit($itemCountPerPage, $offset);
        return $this->_select->getTable()->enableCache()->fetchAll($this->_select);
    }
}