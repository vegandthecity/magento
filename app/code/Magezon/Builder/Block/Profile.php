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

namespace Magezon\Builder\Block;

class Profile extends \Magezon\Builder\Block\Element
{
	/**
	 * @var string
	 */
	protected $_template = 'Magezon_Builder::profile.phtml';

	/**
	 * @var array
	 */
	protected $_flatElements;

	/**
	 * @var \Magezon\Builder\Helper\Data
	 */
	protected $builderHelper;

	/**
	 * @param \Magento\Framework\View\Element\Template\Context $context       
	 * @param \Magezon\Builder\Helper\Data                     $builderHelper 
	 * @param array                                            $data          
	 */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magezon\Builder\Helper\Data $builderHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
		$this->builderHelper = $builderHelper;
    }

    protected function _toHtml()
    {
    	return $this->builderHelper->minify(parent::_toHtml());
    }

	/**
	 * @param array $elements
	 */
	public function processFlatElements($elements)
	{
		foreach ($elements as $element) {
			if ($element->getData('elements') && $element->getElements()) {
				$this->processFlatElements($element->getElements());
			}
			$this->_flatElements[] = $element;
		}
	}

	/**
	 * @return array
	 */
	public function getFlatElements()
	{
		if ($this->_flatElements === null) {
			if ($this->getElement()) {
				$elements = $this->getElement()->getElements();	
			} else {
				$elements = $this->getData('elements');
			}
			if (is_string($elements)) {
				$elements = strtr( $elements, ' ', '+');
				if ( base64_encode(base64_decode($elements, true)) === $elements) {
					$elements = base64_decode($elements);
					$elements = json_decode($elements, TRUE);
				} else {
					$elements = [];
				}
			}
			if (!$elements || !is_array($elements)) $elements = [];
			$newElements = $this->processElements($elements, true);
			$this->processFlatElements($newElements, true);
		}
		return $this->_flatElements;
	}

	/**
	 * @param  string $type 
	 * @return string       
	 */
	public function getStyleHtml($element)
	{
		$builderElement = $this->getElementsManager()->getElement($element->getType());
		if (!$builderElement) return;

		$styleBlock = $builderElement->getStyleBlock();
		if (!$styleBlock) $styleBlock = \Magezon\Builder\Block\Style\Element::class;

		$data = [
			'element_id'   => $element->getId(),
			'element_type' => $element->getType()
		];

		$block = $this->getLayout()->createBlock($styleBlock, '', [
			'data' => $data
		]);

		if ($template = $builderElement->getStyleTemplate()) $block->setTemplate($template);

		$block->setElement($element);
		return $block->toHtml();
	}

	/**
	 * @return string
	 */
	public function getStylesHtml()
	{
		$html = '';
		$flatElements = $this->getFlatElements();
		if ($flatElements) {
			foreach ($flatElements as $element) {
				$html .= $this->getStyleHtml($element);
			}
		}
		return $html;
	}
}