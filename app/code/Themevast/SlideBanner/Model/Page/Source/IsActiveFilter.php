<?php
namespace Magento\Cms\Model\Page\Source;

/**
 * Is active filter source
 */
class IsActiveFilter extends IsActive
{
    public function toOptionArray()
    {
        return array_merge([['label' => '', 'value' => '']], parent::toOptionArray());
    }
}
