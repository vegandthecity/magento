<?php

namespace Meetanshi\Matrixrate\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Rate
 * @package Meetanshi\Matrixrate\Model\ResourceModel
 */
class Rate extends AbstractDb
{
    /**
     *
     */
    public function _construct()
    {
        $this->_init('shippingrate', 'rate_id');
    }

    /**
     * @param $methodId
     * @param $data
     * @return string
     */
    public function bulkInsert($methodId, $data)
    {
        $error = '';

        $sql = '';
        if (isset($data)) {
            for ($i = 0, $n = count($data); $i < $n; ++$i) {
                $sql .= ' (NULL,' . $methodId;
                foreach ($data[$i] as $v) {
                    $sql .= ', "' . $v . '"';
                }
                $sql .= '),';
            }

            if ($sql) {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $resource = $objectManager->create('\Magento\Framework\App\ResourceConnection');
                $rateTable = $resource->getTableName('shippingrate');
                $connection = $this->getConnection();
                $sql = 'INSERT INTO `' . $rateTable . '` VALUES ' . substr($sql, 0, -1);
                try {
                    $connection->query($sql);
                } catch (\Exception $e) {
                    $error = $e->getMessage();
                }
            }
        }


        return $error;
    }

    /**
     * @param $methodId
     */
    public function deleteBy($methodId)
    {
        $connection = $this->getConnection();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->create('\Magento\Framework\App\ResourceConnection');
        $rateTable = $resource->getTableName('shippingrate');
        $connection->delete($rateTable, 'method_id=' . (int)$methodId);
    }
}
