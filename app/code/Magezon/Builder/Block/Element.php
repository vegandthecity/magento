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

use \Magento\Framework\App\ObjectManager;

class Element extends \Magento\Framework\View\Element\Template
{
	/**
	 * @var \Magezon\Builder\Data\Elements
	 */
	protected $_elementsManager;

	/**
	 * @var array
	 */
	protected $_blocks = [];

	/**
	 * @var array
	 */
	protected $cacheElements;

    /**
     * Get design area
     *
     * @return string
     */
    public function getArea()
    {
        $area = parent::getArea();
        if ($area == 'graphql' || $area == 'webapi_rest') $area = 'frontend';
        return $area;
    }

    /**
     * Get key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
		$httpContext   = ObjectManager::getInstance()->create(\Magento\Framework\App\Http\Context::class);
		$priceCurrency = ObjectManager::getInstance()->create(\Magento\Framework\Pricing\PriceCurrencyInterface::class);
        return [
			'MAGEZONBUILDERS_ELEMENT',
			$priceCurrency->getCurrencySymbol(),
			$this->_storeManager->getStore()->getId(),
			$this->_design->getDesignTheme()->getId(),
			$httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP),
			$this->getElementType(),
			$this->getElementId(),
			$this->getTemplate()
        ];
    }

    /**
     * @return \Magezon\Builder\Data\Elements
     */
    public function getElementsManager()
    {
    	if ($this->_elementsManager==NULL) {
    		$this->_elementsManager = ObjectManager::getInstance()->get(\Magezon\Builder\Data\Elements::class);
	    }
	    return $this->_elementsManager;
    }

    public function getBuilderElement()
    {
    	return $this->getElementsManager()->getElement($this->getElement()->getType());
    }

    /**
     * @return boolean
     */
	public function getDisableInner()
	{
		return false;
	}

    /**
     * @return boolean
     */
    public function isEnabled()
    {
		$enable         = true;
		$element        = $this->getElement();
		$builderElement = $this->getBuilderElement();
		if ($builderElement) {
	        if (isset($builderElement['requiredFields']) && is_array($builderElement['requiredFields'])) {
	            foreach ($builderElement['requiredFields'] as $field => $status) {
	                if ($status && !$element->getData($field)) {
	                    $enable = false;
	                    break;
	                }
	            }
	        }
	    } else {
	    	$enable = false;
	    }
        return $enable;
    }

    /**
     * @return array
     */
	public function getWrapperClasses()
	{
		$classes        = [];
		$element        = $this->getElement();
		$builderElement = $this->getBuilderElement();
		if ($element->getAnimationIn()) $classes[] = 'mgz-animated ' . $element->getAnimationIn();
		if ($element->getAnimationInfinite()) $classes[] = 'mgz-animated-infinite';
		if ($element->getElClass()) $classes[] = $element->getElClass();
		if ($builderElement->getData('resizable')) {
			$xlSize = $element->hasData('xl_size') ? $element->getData('xl_size') : $element->getData('lg_size');
			$lgSize = $element->getLgSize();
			$mdSize = $element->getMdSize();
			$smSize = $element->getSmSize();
			$xsSize = $element->getXsSize();
			if (!$xlSize && !$lgSize && !$mdSize && !$smSize && !$xsSize) $xsSize = '12';
			if ($xlSize) $classes[] = 'mgz-col-xl-' . $xlSize;
			if ($lgSize) $classes[] = 'mgz-col-lg-' . $lgSize;
			if ($mdSize) $classes[] = 'mgz-col-md-' . $mdSize;
			if ($smSize) $classes[] = 'mgz-col-sm-' . $smSize;
			if ($xsSize) $classes[] = 'mgz-col-xs-' . $xsSize;
			if ($element->getXlOffsetSize()) $classes[] = 'mgz-col-xl-offset-' . $element->getXlOffsetSize();
			if ($element->getLgOffsetSize()) $classes[] = 'mgz-col-lg-offset-' . $element->getLgOffsetSize();
			if ($element->getMdOffsetSize()) $classes[] = 'mgz-col-md-offset-' . $element->getMdOffsetSize();
			if ($element->getSmOffsetSize()) $classes[] = 'mgz-col-sm-offset-' . $element->getSmOffsetSize();
			if ($element->getXsOffsetSize()) $classes[] = 'mgz-col-xs-offset-' . $element->getXsOffsetSize();
		}

		if ($element->getXlHide()) $classes[] = 'mgz-hidden-xl';
		if ($element->getLgHide()) $classes[] = 'mgz-hidden-lg';
		if ($element->getMdHide()) $classes[] = 'mgz-hidden-md';
		if ($element->getSmHide()) $classes[] = 'mgz-hidden-sm';
		if ($element->getXsHide()) $classes[] = 'mgz-hidden-xs';

		if ($element->getData('hidden_default')) {
			$classes[] = 'mgz-hidden';
		}

		if ($element->getData('title_align')) {
			$classes[] = 'mgz-element-title-align-' . $element->getData('title_align');
		}

		$prefixes   = [''];
		$deviceType = $element->getData('device_type');
		if ($deviceType == 'custom') {
			$prefixes = ['', 'lg_', 'md_', 'sm_', 'xs_'];
			foreach ($prefixes as $_k => $_prefix) {
				if ($element->getData($_prefix . 'align')) {
					$classes[] = $_prefix . $element->getData($_prefix . 'align');
				}
				if ($element->getData($_prefix . 'el_float')) {
					$classes[] = $_prefix . 'f-' . $element->getData($_prefix . 'el_float');
				}
			}
		} else {
			if ($element->getData('align') != 'left' && $element->getData('align')) {
				$classes[] = 'mgz-text-' . $element->getData('align');
			}
			if ($element->getData('el_float')) $classes[] = 'f-' . $element->getData('el_float');
		}

		return $classes;
	}

    /**
     * @return array
     */
	public function getInnerClasses()
	{
		$classes = [];

		$element = $this->getElement();

		if ($element->getElInnerClass()) {
			$classes[] = $element->getElInnerClass();
		}

		return $classes;
	}

    /**
     * @return array
     */
    public function getElementAttributes()
    {
    	$attributes = [];

		$element = $this->getElement();

		if ($element->hasData('animation_duration')) {
    		$attributes['data-animation-duration'] = $element->getData('animation_duration');
    	}

		if ($element->hasData('animation_delay')) {
    		$attributes['data-animation-delay'] = $element->getData('animation_delay');
    	}

    	return $attributes;
    }

    /**
     * @param  array $data
     * @return \Magento\Framework\View\Element\Template
     */
    public function getChildElementBlock($element)
    {
		$type = $element->getType();
		$id   = $element->getId();
    	if (!$type || !$id) return;
		$builderElement = $this->getElementsManager()->getElement($type);
		if ($element && $builderElement) {
			if (isset($this->_blocks[$id])) {
				return $this->_blocks[$id];
			} else {
				$this->_blocks[$id] = $element->getElementBlock()->setGlobalData($this->getData('global_data'));
				return $this->_blocks[$id];
			}
		}
    }

    /**
     * @param  \Magezon\Builder\Model\Element $element
     * @return string
     */
    public function getChildElementHtml($element)
    {
    	$elemTemplate = 'element/element.phtml';
    	$builderElement = $this->getElementsManager()->getElement($element->getType());
    	if ($builderElement->getData('elemTemplate')) {
    		$elemTemplate = $builderElement->getData('elemTemplate');
    	}
    	$block = $this->getLayout()->createBlock(\Magezon\Builder\Block\Element::class);
    	$block->setTemplate($elemTemplate);
    	$block->setElement($element);
    	$block->setGlobalData($this->getData('global_data'));
    	return $block->toHtml();
    }

	/**
	 * @return array
	 */
	public function getElements()
	{
		if ($this->cacheElements == NULL) {
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
			$this->cacheElements = $this->processElements($elements);
		}
		return $this->cacheElements;
	}

	/**
	 * @param  array  $elements 
	 * @param  boolean $nested   
	 * @return array            
	 */
	public function processElements($elements, $nested = false)
	{
		$newElements = [];
		foreach ($elements as $data) {
			if (!isset($data['type']) || !$data['type'] || !isset($data['id']) || !$data['id']
				|| (isset($data['disable_element']) && $data['disable_element'])
			) {
				continue;
			}

			$builderElement = $this->getElementsManager()->getElement($data['type']);

			if (!$builderElement) continue;

			$element = $this->getElementsManager()->getElementModel($data);

			// Leak Memory - process all sub childrens
			if (isset($data['elements']) && is_array($data['elements']) && $nested) {
				$element->setElements($this->processElements($data['elements'], $nested));
			}

			$newElements[] = $element;
		}
		return $newElements;
	}

	/**
	 * @param  array $styles 
	 * @return string       
	 */
	public function parseStyles($styles)
	{
		$result = '';
		foreach ($styles as $k => $v) {
			if (!$v) continue;
			$result .= $k . ': ' . $v . ';';
		}
		return $result;
	}

	/**
	 * @param  array $array 
	 * @return string       
	 */
	public function parseJson($array)
	{
		$result = '';
		if (is_array($array)) {
			foreach ($array as $k => $v) {
				if (!$v) continue;
				if ($v === true) $v = 'true';
				if ($v === false) $v = 'false';
				if ($result) $result .= ',';
				$result .= '\'' . $k . '\': ' . $v;
			}
		}
		return $result;
	}

	/**
	 * @param  array $styles 
	 * @return string       
	 */
	public function parseAttributes($attributes)
	{
		if (!is_array($attributes)) return;
		$result = '';
		$count  = count($attributes);
		$index  = 0;
		foreach ($attributes as $k => $v) {
			if (!$v) continue;
			if ($index!=0 && $index!=$count) {
				$result .= ' ';
			}
			$result .= $k . '="' . $v . '"';
			$index++;
		}
		return $result;
	}

	/**
	 * @return string
	 */
	public function getElementsHtml()
	{
		$block = $this->getLayout()->createBlock(\Magezon\Builder\Block\Element::class);
		$block->setElements($this->getElements());
		$block->setTemplate('Magezon_Builder::element/list.phtml');
    	$block->setGlobalData($this->getData('global_data'));
		return $block->toHtml();
	}

	/**
	 * @param  string $name
	 * @return string      
	 */
    public function renderElement($name)
    {
    	try {
    		return $this->getLayout()->renderElement($name);
    	} catch (\Exception $e) {

    	}
    }

    public function toObjectArray($items)
    {
    	$result = [];
    	if (is_array($items)) {
	    	foreach ($items as $item) {
				$result[] = new \Magento\Framework\DataObject($item);
			}
		}
    	return $result;
    }

    public function addGlobalData($key, $data)
    {
    	$globalData = $this->getData('global_data') ? $this->getData('global_data') : [];
    	$globalData[$key] = $data;
    	$this->setData('global_data', $globalData);
    	return $this;
    }

    public function getGlobalData($key)
    {
    	$globalData = $this->getData('global_data') ? $this->getData('global_data') : [];
    	if (isset($globalData[$key])) return $globalData[$key];
    }

    /**
     * @return array
     */
    public function getOwlCarouselOptions()
    {
    	$element = $this->getElement();
    	if ($element->getData('owl_item_xl')) $options['item_xl'] = $element->getData('owl_item_xl');
    	if ($element->getData('owl_item_lg')) $options['item_lg'] = $element->getData('owl_item_lg');
    	if ($element->getData('owl_item_md')) $options['item_md'] = $element->getData('owl_item_md');
    	if ($element->getData('owl_item_sm')) $options['item_sm'] = $element->getData('owl_item_sm');
    	if ($element->getData('owl_item_xs')) $options['item_xs'] = $element->getData('owl_item_xs');
		$lazyLoad                      = $element->getData('owl_lazyload');
		$options['nav']                = $element->getData('owl_nav') ? true : false;
		$options['dots']               = $element->getData('owl_dots') ? true : false;
		$options['autoplayHoverPause'] = $element->getData('owl_autoplay_hover_pause') ? true : false;
		$options['autoplay']           = $element->getData('owl_autoplay') ? true : false;
		$options['autoplayTimeout']    = $element->getData('owl_autoplay_timeout');
		$options['lazyLoad']           = $lazyLoad ? true : false;
		$options['loop']               = $element->getData('owl_loop') ? true : false;
		$options['margin']             = (int) $element->getData('owl_margin');
		$options['autoHeight']         = $element->getData('owl_auto_height') ? true : false;
		$options['rtl']                = $element->getData('owl_rtl') ? true : false;
		$options['center']             = $element->getData('owl_center') ? true : false;
		$options['slideBy']            = $element->getData('owl_slide_by') ? $element->getData('owl_slide_by') : 1;
		$options['animateIn']          = $element->getData('owl_animate_in') ? $element->getData('owl_animate_in') : '';
		$options['animateOut']         = $element->getData('owl_animate_out') ? $element->getData('owl_animate_out') : '';
		$options['stagePadding']       = $element->getData('owl_stage_padding') ? (int)$element->getData('owl_stage_padding') : 0;
		if ($element->getData('owl_dots_speed')) $options['dotsSpeed'] = $element->getData('owl_dots_speed');
		if ($element->getData('owl_autoplay_speed')) $options['autoplaySpeed'] = $element->getData('owl_autoplay_speed');
    	return $options;
    }

    /**
     * @return array
     */
    public function getOwlCarouselClasses()
    {
		$element     = $this->getElement();
		$dotsInsie   = $element->getData('owl_dots_insie');
		$navPosition = $element->getData('owl_nav_position');
		$navSize     = $element->getData('owl_nav_size');
    	if ($dotsInsie) $classes[] = 'mgz-carousel-dot-inside';
    	$classes[] = 'mgz-carousel-nav-position-' . $navPosition;
    	$classes[] = 'mgz-carousel-nav-size-' . $navSize;
    	if ($element->getData('product_equalheight')) $classes[] = 'mgz-carousel-equal-height';
    	return $classes;
    }

    /**
     * Escape a string for the HTML attribute context
     *
     * @param string $string
     * @param boolean $escapeSingleQuote
     * @return string
     */
    public function escapeHtmlAttr($string, $escapeSingleQuote = true)
    {
		if (!$string) return;
        return $this->_escaper->escapeHtmlAttr($string, $escapeSingleQuote);
    }

    /**
     * @param  array $data 
     * @return array       
     */
    public function getLinkParams($data)
    {
    	$coreHelper = ObjectManager::getInstance()->get('\Magezon\Core\Helper\Data');
    	$params = [
			'type'     => 'custom',
			'url'      => '',
			'id'       => 0,
			'title'    => '',
			'extra'    => '',
			'nofollow' => 0,
			'blank'    => 0
    	];
    	if ($data) {
	    	if (is_string($data)) {
				$params['url']  = $data;
				$params['type'] = 'custom';
	    	} else {
				$params = array_merge($params, $data);
	    	}
	    }
    	if ($params['extra']) {
    		$params['url'] = $params['url'] . '?' . $params['extra'];
    	}
    	$params['url'] = $coreHelper->filter(stripslashes($params['url']));
    	return $params;
    }
}