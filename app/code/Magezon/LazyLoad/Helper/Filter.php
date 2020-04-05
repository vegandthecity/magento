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

namespace Magezon\LazyLoad\Helper;

class Filter extends \Magento\Framework\App\Helper\AbstractHelper
{
	/**
	 * @var \Magezon\LazyLoad\Helper\Data
	 */
	protected $dataHelper;

	/**
	 * @param \Magento\Framework\App\Helper\Context $context    
	 * @param \Magezon\LazyLoad\Helper\Data         $dataHelper 
	 */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magezon\LazyLoad\Helper\Data $dataHelper
    ) {
        parent::__construct($context);
        $this->dataHelper = $dataHelper;
    }

    /**
     * Convert content to lazyload html
     * 
     * @param  string $content
     * @return string
     */
	public function filter($content)
	{
		if (!$this->dataHelper->isEnable()) {
			return $content;
		}

		if ($this->dataHelper->getConfig('general/lazy_load_images')) {
			$content = $this->filterImages($content);
		}

		if ($this->dataHelper->getConfig('general/lazy_load_iframes')) {
			$content = $this->filterIframes($content);
		}

		return $content;
	}

	/**
	 * Filter images with placeholders in the content
	 * 
	 * @param  string $content
	 * @return string
	 */
	public function filterImages($content)
	{
		$matches = $search = $replace = [];
		preg_match_all( '/<img[\s\r\n]+.*?>/is', $content, $matches );
		$placeHolderUrl = $this->dataHelper->getPlaceHolderUrl();

		$lazyClasses = $this->dataHelper->getLazyClasses();

		if ($placeHolderUrl != $this->dataHelper->getDefaultPlaceHolderUrl()) {
			$lazyClasses = str_replace('lazy-blur', '', $lazyClasses);
		}

		foreach ($matches[0] as $imgHTML) {
			if ( ! preg_match( "/src=['\"]data:image/is", $imgHTML ) && strpos($imgHTML, 'data-src')===false && ! $this->isSkipElement($imgHTML) ) {

				// replace the src and add the data-src attribute
				$replaceHTML = preg_replace( '/<img(.*?)src=/is', '<img$1src="' . $placeHolderUrl . '" data-src=', $imgHTML );

				// add the lazy class to the img element
				if ( preg_match( '/class=["\']/i', $replaceHTML ) ) {
					$replaceHTML = preg_replace( '/class=(["\'])(.*?)["\']/is', 'class=$1' . $lazyClasses . ' $2$1', $replaceHTML );
				} else {
					$replaceHTML = preg_replace( '/<img/is', '<img class="' . $lazyClasses . '"', $replaceHTML );
				}

				$search[]  = $imgHTML;
				$replace[] = $replaceHTML;
			}
		}

		$content = str_replace( $search, $replace, $content );

		return $content;
	}

	/**
	 * Filter images with placeholders in the content
	 * 
	 * @param  string $content
	 * @return string
	 */
	public function filterIframes($content)
	{
		$matches = $search = $replace = [];
		preg_match_all( '|<iframe\s+.*?</iframe>|si', $content, $matches );
		$placeHolderUrl = $this->dataHelper->getPlaceHolderUrl();
		$lazyClasses = $this->dataHelper->getLazyClasses();

		foreach ($matches[0] as $imgHTML) {
			if ( ! preg_match( "/src=['\"]data:image/is", $imgHTML ) && strpos($imgHTML, 'data-src')===false && ! $this->isSkipElement($imgHTML) ) {

				// replace the src and add the data-src attribute
				$replaceHTML = preg_replace( '/<iframe(.*?)src=/is', '<iframe$1src="' . $placeHolderUrl . '" data-src=', $imgHTML );

				// add the lazy class to the iframe element
				if ( preg_match( '/class=["\']/i', $replaceHTML ) ) {
					$replaceHTML = preg_replace( '/class=(["\'])(.*?)["\']/is', 'class=$1' . $lazyClasses . ' $2$1', $replaceHTML );
				} else {
					$replaceHTML = preg_replace( '/<iframe/is', '<iframe class="' . $lazyClasses . '"', $replaceHTML );
				}

				$search[]  = $imgHTML;
				$replace[] = $replaceHTML;
			}
		}

		$content = str_replace( $search, $replace, $content );

		return $content;
	}

	/**
	 * Check is skip element via specific classes
	 * @param  string  $content
	 * @return boolean
	 */
	protected function isSkipElement($content)
	{
		$skipClassesQuoted = array_map( 'preg_quote', $this->getSkipClasses() );
		$skipClassesORed = implode( '|', $skipClassesQuoted );
		$regex = '/<\s*\w*\s*class\s*=\s*[\'"](|.*\s)' . $skipClassesORed . '(|\s.*)[\'"].*>/isU';
		return preg_match( $regex, $content );
	}

	/**
	 * @return array
	 */
	protected function getSkipClasses()
	{
		$skipClasses = array_map( 'trim', explode( ',', $this->dataHelper->getConfig('general/skip_classes') ) );

		foreach ($skipClasses as $k => $_class) {
			if (!$_class) {
				unset($skipClasses[$k]);
			}
		}

		return $skipClasses;
	}
}