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

class SingleImage extends \Magezon\Builder\Block\Element
{
	/**
	 * @var array
	 */
	protected $_items;

	/**
	 * @var \Magezon\Core\Helper\Data
	 */
	protected $coreHelper;

	/**
	 * @var \Magezon\Builder\Helper\Data
	 */
	protected $builderHelper;

	/**
	 * @var \Magezon\Builder\Helper\Image
	 */
	protected $builderImageHelper;

	/**
	 * @param \Magento\Framework\View\Element\Template\Context $context            
	 * @param \Magezon\Core\Helper\Data                        $coreHelper         
	 * @param \Magezon\Builder\Helper\Data                     $builderHelper      
	 * @param \Magezon\Builder\Helper\Image                    $builderImageHelper 
	 * @param array                                            $data               
	 */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
       	\Magezon\Core\Helper\Data $coreHelper,
       	\Magezon\Builder\Helper\Data $builderHelper,
       	\Magezon\Builder\Helper\Image $builderImageHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
		$this->coreHelper         = $coreHelper;
		$this->builderHelper      = $builderHelper;
		$this->builderImageHelper = $builderImageHelper;
    }

    /**
     * @return array
     */
	public function getWrapperClasses()
	{
		$classes = parent::getWrapperClasses();

		$element = $this->getElement();
		$classes[] = 'mgz-image-hovers';

		return $classes;
	}

	/**
	 * @return string
	 */
	public function getLink()
	{
		$params = [
			'url'      => '',
			'id'       => 0,
			'title'    => '',
			'extra'    => '',
			'nofollow' => 0,
			'blank'    => 0
    	];
		$element = $this->getElement();
		$onclick = $element->getData('onclick');

		switch ($onclick) {
			case 'magnific':
				$link = $this->getSrc();
				if ($popupImage = $element->getData('popup_image')) {
					$link = $this->builderHelper->getImageUrl($popupImage);
				}
				$params['url'] = $link;
				break;

			case 'video_map':
				$params['url'] = $element->getData('video_map');
				break;

			case 'custom_link':
				$params = $this->getLinkParams($element->getData('custom_link'));
				break;
		}

		return $params;
	}

	/**
	 * @return string
	 */
	public function getSrc()
	{
		$src     = '';
		$element = $this->getElement();
		$source  = $element->getData('source');

		switch ($source) {
			case 'media_library':
				$src = $this->builderHelper->getImageUrl($element->getData('image'));
				break;

			case 'external_link':
				$src = $this->coreHelper->filter($element->getData('custom_src'));
				break;
		}

		return $src;
	}

	/**
	 * @return string
	 */
	public function getSrcset()
	{
		$srcset              = '';
		$element             = $this->getElement();
		$src                 = $element->getData('image');
		$image               = $this->builderHelper->getImageUrl($src);
		$tabletImage         = $this->builderHelper->getImageUrl($element->getData('tablet_image'));
		$landscapePhoneImage = $this->builderHelper->getImageUrl($element->getData('landscape_phone_image'));
		$portraitPhoneImage  = $this->builderHelper->getImageUrl($element->getData('portrait_phone_image'));
		$responsiveImages    = $element->getData('responsive_images');
		switch ($responsiveImages) {
			case 'auto':
				// $tabletImage         = $this->builderImageHelper->resize($src, 1024, null, 100, 'magezon/resized');
				// $landscapePhoneImage = $this->builderImageHelper->resize($src, 768, null, 100, 'magezon/resized');
				// $portraitPhoneImage  = $this->builderImageHelper->resize($src, 576, null, 100, 'magezon/resized');
				// $srcset      = $image . ' 1200w';
				// $srcset.= ',' . $tabletImage . ' 1024w';
				// $srcset.= ',' . $landscapePhoneImage . ' 768w';
				// $srcset.= ',' . $portraitPhoneImage . ' 576w';
				break;

			case 'custom':
				if (($tabletImage || $landscapePhoneImage || $portraitPhoneImage)) {
					$srcset = $image . ' 1200w';
					if ($tabletImage) {
						if ($srcset) $srcset .= ',';
						$srcset .= $tabletImage . ' 1024w';
					}
					if ($landscapePhoneImage) {
						if ($srcset) $srcset .= ',';
						$srcset .= $landscapePhoneImage . ' 768w';
					}
					if ($portraitPhoneImage) {
						if ($srcset) $srcset .= ',';
						$srcset .= $portraitPhoneImage . ' 576w';
					}
				}
				break;
		}
		return $srcset;
	}

	/**
	 * @return string
	 */
	public function getImgWrapperClasses()
	{
		$element   = $this->getElement();
		$link      = $this->getLink();
		$classes[] = 'mgz-single-image-wrapper';
		$classes[] = $element->getData('image_style');
		$displayOnHover = $element->getData('display_on_hover');
		if ($displayOnHover) $classes[] = 'item-content-hover';
		return $this->builderHelper->parseClasses($classes);
	}

	/**
	 * @return string
	 */
	public function getLinkClasses()
	{
		$classes = [];
		$element = $this->getElement();
		$onclick = $element->getData('onclick');
		if (($onclick == 'magnific' || $onclick == 'video_map')) $classes[] = 'mgz-magnific';
		return $this->builderHelper->parseClasses($classes);
	}

	public function getLinkTitle()
	{	
		$element = $this->getElement();
		$title = $element->getData('title');
		if (!$title) {
			$src = $this->getSrc();
			if (preg_match('@^.*[\\\\/]([^\\\\/]+)$@s', $src, $matches)) {
				$title = $matches[1];
			} else if (preg_match('@^([^\\\\/]+)$@s', $src, $matches)) {
				$title = $matches[1];
			}
			$title = substr($title, 0, strrpos($title, "."));
			$title = str_replace(['_', '-'], ' ', $title);
		}
		return $title;
	}
}