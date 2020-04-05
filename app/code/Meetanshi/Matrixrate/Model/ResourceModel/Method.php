<?php
namespace Meetanshi\Matrixrate\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Method
 * @package Meetanshi\Matrixrate\Model\ResourceModel
 */
class Method extends AbstractDb
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('shippingmethod', 'method_id');
    }
}
