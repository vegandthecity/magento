<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_Builder
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\Builder\Block\Element;

class Row extends \Magezon\Builder\Block\Element
{
    /**
     * @return array
     */
	public function getWrapperClasses()
	{
		$classes = parent::getWrapperClasses();

		$element = $this->getElement();
		$classes[] = $element->getData('row_type');

		if ($element->getData('row_type') == 'contained') {
			$classes[] = 'mgz-container';
		}

		if ($element->getData('equal_height')) {
			$classes[] = 'mgz-row-equal-height';
		}

		if ($element->getData('content_position') && $element->getData('equal_height')) {
			$classes[] = 'content-' . $element->getData('content_position');
		}

		if ($element->getData('full_height')) {
			$classes[] = 'mgz-row-full-height';
		}

		return $classes;
	}
}