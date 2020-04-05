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

namespace Magezon\PageBuilder\Block\Element;

class Countdown extends \Magezon\Builder\Block\Element
{
	public function getTime()
	{
		$element  = $this->getElement();
		$year     = (int)$element->getData('year');
		$month    = (int)$element->getData('month');
		$day      = (int)$element->getData('day');
		$hours    = (int)$element->getData('hours');
		$minutes  = $element->getData('minutes');
		$str      = $year . '-' . $month . '-' . $day . ' ' . $hours . ':' . $minutes . ':00';
		$timezone = $element->getData('time_zone') ? $element->getData('time_zone') : 'UTC';
		$date     = new \DateTime($str, new \DateTimeZone($timezone));
		return $date->format(\DateTime::ATOM);
	}
}