<?php

namespace Meetanshi\Matrixrate\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Ziptype
 * @package Meetanshi\Matrixrate\Model\Config\Source
 */
class Ziptype implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 0,
                'label' => __('Numeric'),
            ],
            [
                'value' => 1,
                'label' => __('String'),
            ],
        ];
    }
}
