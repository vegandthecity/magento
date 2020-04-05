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

class ImageCarousel extends \Magezon\Builder\Block\Element
{
	/**
	 * @var \Magezon\Builder\Helper\Image
	 */
	protected $builderImageHelper;

	/**
	 * @var \Magezon\Builder\Helper\Data
	 */
	protected $builderHelper;

	/**
	 * @param \Magento\Framework\View\Element\Template\Context $context            
	 * @param \Magezon\Builder\Helper\Image                    $builderImageHelper 
	 * @param \Magezon\Builder\Helper\Data                     $builderHelper      
	 * @param array                                            $data               
	 */
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magezon\Builder\Helper\Image $builderImageHelper,
		\Magezon\Builder\Helper\Data $builderHelper,
		array $data = []
	) {
		parent::__construct($context, $data);
		$this->builderImageHelper = $builderImageHelper;
		$this->builderHelper      = $builderHelper;
	}

	/**
	 * @param  string $src 
	 * @return string      
	 */
	public function getImage($src)
	{
		$element = $this->getElement();
		$size    = $this->getsize();
		if ($size) {
			$src = $this->builderImageHelper->resize($src, $size['width'], $size['height'], 100, 'magezon/resized', ['keepAspectRatio' => false]);
		} else {
			$src = $this->builderHelper->getImageUrl($src);
		}
		return $src;
	}

	/**
	 * @return array
	 */
	public function getsize()
	{
		$size    = [];
		$element = $this->getElement();
		$size    = array_filter(explode("x", $element->getData('image_size')));
		if ($size) {
			$width  = $size[0];
			$height = isset($size[1]) ? $size[1] : 0;
			$size = [
				'width'  => $width,
				'height' => $height
			];
		}
		return $size;
	}
}