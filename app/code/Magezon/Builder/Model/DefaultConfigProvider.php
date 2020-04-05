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

namespace Magezon\Builder\Model;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\Store;

class DefaultConfigProvider implements ConfigProviderInterface
{
	/**
	 * @var string
	 */
	protected $_builderArea = 'all';

	/**
	 * @var \Magezon\Builder\Model\CacheManager
	 */
	protected $builderCacheManager;

	/**
	 * @var \Magezon\Builder\Data\Groups
	 */
	protected $builderGroups;

    /**
     * @var \Magezon\Builder\Data\Elements
     */
    private $builderElements;

	/**
	 * @var \Magezon\Builder\Helper\Data
	 */
	protected $builderHelper;

	/**
	 * @param \Magezon\Builder\Model\CacheManager $builderCacheManager 
	 * @param \Magezon\Builder\Data\Groups        $builderGroups       
	 * @param \Magezon\Builder\Data\Elements      $builderElements     
	 * @param \Magezon\Builder\Helper\Data        $builderHelper       
	 */
	public function __construct(
        \Magezon\Builder\Model\CacheManager $builderCacheManager,
        \Magezon\Builder\Data\Groups $builderGroups,
		\Magezon\Builder\Data\Elements $builderElements,
        \Magezon\Builder\Helper\Data $builderHelper
	) {
		$this->builderCacheManager = $builderCacheManager;
		$this->builderElements     = $builderElements;
		$this->builderGroups       = $builderGroups;
		$this->builderHelper       = $builderHelper;
	}

	/**
	 * @return string
	 */
	public function getBaseUrl()
	{
		$backendHelper     = ObjectManager::getInstance()->get(\Magento\Backend\Helper\Data::class);
		$frontNameResolver = ObjectManager::getInstance()->get(\Magento\Backend\App\Area\FrontNameResolver::class);
		$adminHompePageUrl = $backendHelper->getHomePageUrl();
		$frontName         = $frontNameResolver->getFrontName();
		$urls              = explode('/' . $frontName . '/', $adminHompePageUrl);
		$baseUrl           = $urls[0] . '/' . $frontName . '/';
		return $baseUrl;
	}

    /**
     * Return configuration array
     *
     * @return array|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getConfig()
    {
		$config                            = [];
		$config['baseUrl']                 = $this->getBaseUrl();
		$config['viewFileUrl']             = $this->builderHelper->getViewFileUrl();
		$config['mediaUrl']                = $this->builderHelper->getMediaUrl();
		$config['directives_url']          = $this->builderHelper->getUrl('cms/wysiwyg/directive');
		$config['resizableSizes']['class'] = '\Magezon\Builder\Model\Source\ResizableSizes';
		$config['animationIn']['class']    = '\Magezon\Builder\Model\Source\AnimateIn';
		$config['animationOut']['class']   = '\Magezon\Builder\Model\Source\AnimateOut';
		$config['groups']                  = $this->builderGroups->getGroups();
		$config['loadStylesUrl']           = 'mgzbuilder/ajax/loadStyles';
		$config['loadElementUrl']          = 'mgzbuilder/ajax/loadElement';
		$config['elements']                = $this->getElementList();
		$config['mainElement']             = 'row';
		$config['googleApi']               = $this->builderHelper->getGoogleMapApi();
		$config['shortcode']['class']      = '\Magezon\Builder\Data\Modal\Shortcode';
		$config['fileManagerUrl']          = $this->builderHelper->getUrl('mgzcore/wysiwyg_images/index', [
            'target_element_id' => 'UID'
        ]);
        $config['area'] = $this->getBuilderArea();

		$scopeConfig = ObjectManager::getInstance()->get(\Magento\Framework\App\Config\ScopeConfigInterface::class);

		$resourceUrlWhitelist = [];
		if ($scopeConfig->getValue(Store::XML_PATH_SECURE_BASE_STATIC_URL)) $resourceUrlWhitelist[] = $scopeConfig->getValue(Store::XML_PATH_SECURE_BASE_STATIC_URL) . '**';
		if ($scopeConfig->getValue(Store::XML_PATH_UNSECURE_BASE_STATIC_URL)) $resourceUrlWhitelist[] = $scopeConfig->getValue(Store::XML_PATH_UNSECURE_BASE_STATIC_URL) . '**';
		if ($scopeConfig->getValue(Store::XML_PATH_SECURE_BASE_MEDIA_URL)) $resourceUrlWhitelist[] = $scopeConfig->getValue(Store::XML_PATH_SECURE_BASE_MEDIA_URL) . '**';
		if ($scopeConfig->getValue(Store::XML_PATH_UNSECURE_BASE_MEDIA_URL)) $resourceUrlWhitelist[] = $scopeConfig->getValue(Store::XML_PATH_UNSECURE_BASE_MEDIA_URL) . '**';
		$config['resourceUrlWhitelist'] = $resourceUrlWhitelist;
		return $config;
    }

	/**
	 * @param  array $elements 
	 * @return array           
	 */
	public function getElementList()
	{
		$key = 'MAGEZON_BUILDER_CONFIG' . $this->getBuilderArea();
		if ($cacheData = $this->builderCacheManager->getFromCache($key)) {
			$list = $cacheData;
        } else {
			$builderArea = $this->getBuilderArea();
			$list        = [];
			$elements    = $this->builderElements->getElements();
			if ($elements) {
				foreach ($elements as $element) {
					$config = $element->getConfig();

					if (isset($config['area'])) {
						if (is_string($config['area'])) {
							$area             = $config['area'];
							$config['area']   = [];
							$config['area'][] = $area;
						}
					} else {
						$config['area'] = [];
					}

					if (in_array($builderArea, $config['area']) || empty($config['area'])) {
						unset($config['form']);
						unset($config['fields']);

						//$config = $element->getData();

						// Instant Load, Comment for Lazy Load
						//$config['tabs'] = $element->getFormFields();

						//$config['fields']          = $element->getAllFields();
						$config['defaultValues']   = $element->getFormDefaultValues();
						$list[$element->getType()] = $config;
					}
				}
			}

			foreach ($list as &$_element) {
				if (isset($_element['children']) && $_element['children']) {
					foreach ($list as $k => &$v) {
						if ($v['type'] == $_element['children']) {
							$v['parent'] = $_element['type'];
							break;
						}
					}
				}
			}
    		$cacheData = $this->builderCacheManager->saveToCache($key, $list);
		}
        return $list;
	}

    /**
     * @param string $builderArea
     * @return $this
     */
    protected function setBuilderArea($builderArea)
    {
        $this->_builderArea = $builderArea;
        return $this;
    }

    /**
     * Id field name getter
     *
     * @return string
     */
    public function getBuilderArea()
    {
        return $this->_builderArea;
    }
}