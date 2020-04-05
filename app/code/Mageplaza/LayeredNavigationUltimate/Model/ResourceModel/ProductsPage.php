<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_LayeredNavigationUltimate
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\LayeredNavigationUltimate\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class ProductsPage
 * ProductsList resource
 * @package Mageplaza\LayeredNavigationUltimate\Model\ResourceModel
 */
class ProductsPage extends AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('layered_product_pages', 'page_id');
    }

    /**
     * @param AbstractModel $object
     *
     * @return $this
     */
    protected function _beforeSave(AbstractModel $object)
    {
        parent::_beforeSave($object);

        if (is_array($object->getStoreIds())) {
            $object->setStoreIds(implode(',', $object->getStoreIds()));
        }

        if (is_array($object->getPosition())) {
            $object->setPosition(implode(',', $object->getPosition()));
        }

        return $this;
    }
}
