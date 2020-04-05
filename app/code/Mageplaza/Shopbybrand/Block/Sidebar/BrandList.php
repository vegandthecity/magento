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
 * @package     Mageplaza_Shopbybrand
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Shopbybrand\Block\Sidebar;

use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\Shopbybrand\Block\Brand;

/**
 * Class BrandList
 *
 * @package Mageplaza\Shopbybrand\Block\Sidebar
 */
class BrandList extends Brand
{
    /**
     * Default feature template
     *
     * @type string
     */
    protected $_template = 'Mageplaza_Shopbybrand::sidebar/list.phtml';

    /**
     * Default title sidebar brand thumbnail
     */
    const TITLE = 'Brand List';
    /**
     * Default title sidebar brand thumbnail
     */
    const LIMIT = '7';

    /**
     * @return mixed|string
     */
    public function getTitle()
    {
        return $this->helper->getModuleConfig('sidebar/brand_thumbnail/title') ?: self::TITLE;
    }

    /**
     * @inheritDoc
     */
    public function getCollection($type = null, $option = null)
    {
        $collection     = parent::getCollection($type, $option);
        $limit          = $this->helper->getModuleConfig('sidebar/brand_thumbnail/limit_brands') ?: self::LIMIT;
        $collectionSize = $collection->getSize();
        if ($collectionSize > $limit) {
            $collection->setPageSize($limit);
        }

        return $collection;
    }

    /**
     * @param null $brand
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getBrandUrl($brand = null)
    {
        return $this->helper->getBrandUrl($brand);
    }

    /**
     * @param $brand
     *
     * @return string
     */
    public function getBrandImageUrl($brand)
    {
        return $this->helper->getBrandImageUrl($brand);
    }
}
