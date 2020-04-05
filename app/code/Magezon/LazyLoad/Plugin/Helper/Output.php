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

namespace Magezon\LazyLoad\Plugin\Helper;

class Output
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

	public function aroundProcess(
		\Magento\Catalog\Helper\Output $subject,
		callable $proceed,
		$method,
		$result,
		$params
	) {
		$result = $proceed($method, $result, $params);
		if (is_string($result) && $this->dataHelper->isEnable()) {
			if (($method === 'productAttribute' && $this->dataHelper->getConfig('general/lazy_load_product_attribute'))
				|| ($method === 'categoryAttribute' && $this->dataHelper->getConfig('general/lazy_load_category_attribute'))
			) {
				$result = $this->filterHelper->filter($result);
			}
		}
		return $result;
	}
}