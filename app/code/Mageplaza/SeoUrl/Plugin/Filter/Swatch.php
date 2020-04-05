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
 * @package     Mageplaza_SeoUrl
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\SeoUrl\Plugin\Filter;

use Mageplaza\SeoUrl\Helper\Data as UrlHelper;

/**
 * Class Swatch
 * @package Mageplaza\SeoUrl\Plugin\Filter
 */
class Swatch
{
    /**
     * @var UrlHelper
     */
    protected $_moduleHelper;

    /**
     * Swatch constructor.
     *
     * @param UrlHelper $moduleHelper
     */
    public function __construct(UrlHelper $moduleHelper)
    {
        $this->_moduleHelper = $moduleHelper;
    }

    /**
     * @param \Magento\Swatches\Block\LayeredNavigation\RenderLayered $subject
     * @param $result
     *
     * @return bool|mixed|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Serializer_Exception
     */
    public function afterBuildUrl(\Magento\Swatches\Block\LayeredNavigation\RenderLayered $subject, $result)
    {
        return $this->_moduleHelper->encodeFriendlyUrl($result);
    }
}
