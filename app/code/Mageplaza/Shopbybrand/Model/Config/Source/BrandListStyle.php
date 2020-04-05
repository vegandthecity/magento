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

namespace Mageplaza\Shopbybrand\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class BrandListStyle
 *
 * @package Mageplaza\Shopbybrand\Model\Config\Source
 */
class BrandListStyle implements OptionSourceInterface
{
    /**
     * Display listing
     */
    const DISPLAY_LISTING = '0';
    /**
     * Display alphabet listing
     */
    const DISPLAY_ALPHABET_LISTING = '1';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('List View'),
                'value' => self::DISPLAY_LISTING
            ],
            [
                'label' => __('Alphabet View'),
                'value' => self::DISPLAY_ALPHABET_LISTING
            ],
        ];
    }
}
