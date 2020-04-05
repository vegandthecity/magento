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
 * @package   Magezon_PageBuilder
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\PageBuilder\Block\Style;

class ContentSlider extends \Magezon\Builder\Block\Style
{
	public function getHtmlId()
	{
		return '.mgz-element.' . $this->getElement()->getHtmlId() . ' .owl-carousel';
	}

	/**
	 * @return string
	 */
	public function getAdditionalStyleHtml()
	{
		$styleHtml = $this->getOwlCarouselStyles();

		$styleHtml .= $this->getLineStyles();

		return $styleHtml;
	}
}