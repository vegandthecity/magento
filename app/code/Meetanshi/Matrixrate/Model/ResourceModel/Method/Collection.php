<?php

namespace Meetanshi\Matrixrate\Model\ResourceModel\Method;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Meetanshi\Matrixrate\Model\ResourceModel\Method
 */
class Collection extends AbstractCollection
{
    /**
     *
     */
    public function _construct()
    {
        $this->_init('Meetanshi\Matrixrate\Model\Method', 'Meetanshi\Matrixrate\Model\ResourceModel\Method');
    }

    /**
     * @param $storeId
     * @return $this
     */
    public function addStoreFilter($storeId)
    {
        $storeId = (int)$storeId;
        $this->getSelect()->where('stores="" OR stores LIKE "%,' . $storeId . ',%"');

        return $this;
    }

    /**
     * @param $groupId
     * @return $this
     */
    public function addCustomerGroupFilter($groupId)
    {
        $groupId = (int)$groupId;
        $this->getSelect()->where('cust_groups="" OR cust_groups LIKE "%,' . $groupId . ',%"');

        return $this;
    }

    /**
     * @return array
     */
    public function toOptionHash()
    {
        return $this->_toOptionHash('method_id', 'comment');
    }
}
