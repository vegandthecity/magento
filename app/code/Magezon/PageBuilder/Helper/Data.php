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

namespace Magezon\PageBuilder\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magezon\Builder\Helper\Data
     */
    protected $builderHelper;

    /**
     * @var \Magezon\Core\Helper\Data
     */
    protected $coreHelper;

    /**
     * @var \Magezon\Builder\Model\CacheManager
     */
    protected $cacheManager;

    /**
     * @param \Magento\Framework\App\Helper\Context      $context       
     * @param \Magento\Store\Model\StoreManagerInterface $_storeManager 
     * @param \Magento\Framework\View\LayoutInterface    $layout        
     * @param \Magezon\Builder\Helper\Data               $builderHelper 
     * @param \Magezon\Core\Helper\Data                  $coreHelper    
     * @param \Magezon\Builder\Model\CacheManager        $cacheManager  
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $_storeManager,
        \Magezon\Builder\Helper\Data $builderHelper,
        \Magezon\Core\Helper\Data $coreHelper,
        \Magezon\Builder\Model\CacheManager $cacheManager
    ) {
        parent::__construct($context);
        $this->_storeManager = $_storeManager;
        $this->builderHelper = $builderHelper;
        $this->coreHelper    = $coreHelper;
        $this->cacheManager  = $cacheManager;
    }

    /**
     * @param  string $key
     * @param  null|int $store
     * @return null|string
     */
    public function getConfig($key, $store = null)
    {
        $store     = $this->_storeManager->getStore($store);
        $websiteId = $store->getWebsiteId();
        $result    = $this->scopeConfig->getValue(
            'mgzpagebuilder/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        return $result;
    }

    /**
     * @return boolean
     */
    public function isEnable()
    {
        return $this->getConfig('general/enable');
    }

    /**
     * @param  string $value
     * @return string       
     */
    public function filter($value)
    {
        if ($value && is_string($value)) {
            $key  = 'mgz_pagebuilder';
            $prex = '/\[' . $key . '\](.*?)\[\/' . $key . '\]/si';
            preg_match_all($prex, $value, $matches, PREG_SET_ORDER);
            if ($matches) {
                $search = $replace = [];
                foreach ($matches as $row) {
                    $search[]  = $row[0];
                    $replace[] = $this->builderHelper->prepareProfileBlock('\Magezon\PageBuilder\Block\Profile', $row[1])->toHtml();
                }
                $value = str_replace($search, $replace, $value);
            }
        }
        return $value;
    }

    /**
     * @return array
     */
    public function getTemplates()
    {
        $templates = [];
        try {
            $key = 'MAGEZON_PAGEBUILDER_TEMPLATES';
            $templates = $this->cacheManager->getFromCache($key);
            if ($templates) {
                return $this->coreHelper->unserialize($templates);
            }
            $url = 'https://www.magezon.com/productfile/pagebuilder/templates.json';
            $ch  = curl_init();
            curl_setopt ($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $content = curl_exec($ch);
            curl_close($ch);
            if ($content) {
                $templates = $this->coreHelper->unserialize($content);

                $newTemplates = [];
                foreach ($templates as $template) {
                    try {
                        $newTemplates[] = $template;
                    } catch (\Exception $e) {
                    }
                }
                $templates = $newTemplates;
            }
            $this->cacheManager->saveToCache($key, $templates);
        } catch (\Exception $e) {

        }
        return $templates;
    }
}