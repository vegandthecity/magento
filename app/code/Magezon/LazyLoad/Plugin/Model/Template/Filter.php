<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://magezon.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_LazyLoad
 * @copyright Copyright (C) 2018 Magezon (https://magezon.com)
 */

namespace Magezon\LazyLoad\Plugin\Model\Template;

class Filter
{
	/**
	 * @var \Magezon\LazyLoad\Helper\Data
	 */
	protected $dataHelper;

	/**
	 * @var \Magezon\LazyLoad\Helper\Filter
	 */
	protected $filterHelper;

	/**
	 * @param \Magezon\LazyLoad\Helper\Data   $dataHelper   
	 * @param \Magezon\LazyLoad\Helper\Filter $filterHelper 
	 */
	public function __construct(
		\Magezon\LazyLoad\Helper\Data $dataHelper,
		\Magezon\LazyLoad\Helper\Filter $filterHelper
	) {
		$this->dataHelper   = $dataHelper;
		$this->filterHelper = $filterHelper;
	}

	public function afterFilter(
		\Magento\Cms\Model\Template\Filter $filter,
		$result
	) {
		if (is_string($result) && $this->dataHelper->isEnable() && $this->dataHelper->getConfig('general/lazy_load_cms')) {
			$result = $this->filterHelper->filter($result);
		}
		return $result;
	}
}