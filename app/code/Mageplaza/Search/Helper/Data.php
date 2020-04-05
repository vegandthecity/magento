<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Search
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Search\Helper;

use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Config;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Customer\Model\ResourceModel\Group\CollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Escaper;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Reports\Model\ResourceModel\Product\CollectionFactory as ProductFactory;
use Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory as BestSellerProduct;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Helper\AbstractData;
use Mageplaza\Search\Model\Config\Source\Search;

/**
 * Class Data
 * @package Mageplaza\Search\Helper
 */
class Data extends AbstractData
{
    const CONFIG_MODULE_PATH = 'mpsearch';
    const MAX_QUERY_RESULT   = 'max_query_results';

    /**
     * @var Visibility
     */
    protected $productVisibility;

    /**
     * @var Config
     */
    protected $catalogConfig;

    /**
     * @var PricingHelper
     */
    protected $_priceHelper;

    /**
     * @var Escaper
     */
    protected $_escaper;

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var CollectionFactory
     */
    protected $_customerGroupFactory;

    /**
     * @var FormatInterface
     */
    protected $localeFormat;

    /**
     * @var CategoryFactory
     */

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var ProductFactory
     */
    protected $_productsFactory;

    /**
     * @var BestSellerProduct
     */
    protected $bestsellersCollection;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param ObjectManagerInterface $objectManager
     * @param CollectionFactory $customerGroupCollectionFactory
     * @param Escaper $escaper
     * @param PricingHelper $priceHelper
     * @param Visibility $catalogProductVisibility
     * @param Config $catalogConfig
     * @param Session $customerSession
     * @param FormatInterface $localeFormat
     * @param CategoryFactory $categoryFactory
     * @param ProductFactory $productsFactory
     * @param BestSellerProduct $bestsellersCollection
     * @param TimezoneInterface $localDate
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        ObjectManagerInterface $objectManager,
        CollectionFactory $customerGroupCollectionFactory,
        Escaper $escaper,
        PricingHelper $priceHelper,
        Visibility $catalogProductVisibility,
        Config $catalogConfig,
        Session $customerSession,
        FormatInterface $localeFormat,
        CategoryFactory $categoryFactory,
        ProductFactory $productsFactory,
        BestSellerProduct $bestsellersCollection,
        TimezoneInterface $localDate
    ) {
        $this->_customerGroupFactory = $customerGroupCollectionFactory;
        $this->_escaper = $escaper;
        $this->_priceHelper = $priceHelper;
        $this->productVisibility = $catalogProductVisibility;
        $this->catalogConfig = $catalogConfig;
        $this->_customerSession = $customerSession;
        $this->localeFormat = $localeFormat;
        $this->categoryFactory = $categoryFactory;
        $this->_productsFactory = $productsFactory;
        $this->bestsellersCollection = $bestsellersCollection;
        $this->_localeDate = $localDate;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return $this->getConfigGeneral('enabled', $storeId) && $this->isModuleOutputEnabled();
    }

    /**
     * @param string $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getConfigGeneral($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(static::CONFIG_MODULE_PATH . '/general' . $code, $storeId);
    }

    /**
     * @return \Mageplaza\Search\Helper\Media
     */
    public function getMediaHelper()
    {
        return $this->objectManager->get(Media::class);
    }

    /**
     * @param null $store
     *
     * @return string
     */
    public function getSearchBy($store = null)
    {
        $searchBy = $this->getConfigGeneral('search_by', $store);

        return self::jsonEncode(explode(',', $searchBy));
    }

    /**
     * @param null $store
     *
     * @return string
     */
    public function getDisplay($store = null)
    {
        $searchBy = $this->getConfigGeneral('display', $store);

        return self::jsonEncode(explode(',', $searchBy));
    }

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function isEnableSuggestion($storeId = null)
    {
        return $this->getConfigGeneral('search_by/enable', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getSortBy($storeId = null)
    {
        return $this->getConfigGeneral('search_by/sort_by', $storeId);
    }

    /**
     * Create json file to contain product data
     * @return array
     */
    public function createJsonFile()
    {
        $errors = [];
        $customerGroups = $this->_customerGroupFactory->create();
        foreach ($this->storeManager->getStores() as $store) {
            foreach ($customerGroups as $group) {
                try {
                    $this->createJsonFileForStore($store, $group->getId());
                } catch (\Exception $e) {
                    $errors[] = __(
                        'Cannot generate data for store %1 and customer group %2, %3',
                        $store->getCode(),
                        $group->getCode(),
                        $e->getMessage()
                    );
                }
            }
        }

        return $errors;
    }

    /**
     * Create json file to contain new product, most viewed, bestsellers data
     * @return array
     */
    public function createAdditionJsonFile()
    {
        $errors = [];
        $customerGroups = $this->_customerGroupFactory->create();
        foreach ($this->storeManager->getStores() as $store) {
            foreach ($customerGroups as $group) {
                try {
                    $this->createAdditionJsonFileForStore($store, $group->getId());
                } catch (\Exception $e) {
                    $errors[] = __(
                        'Cannot generate data for store %1 and customer group %2, %3',
                        $store->getCode(),
                        $group->getCode(),
                        $e->getMessage()
                    );
                }
            }
        }

        return $errors;
    }

    /**
     * @param $store
     * @param $group
     *
     * @return $this
     */
    public function createJsonFileForStore($store, $group)
    {
        $products = $this->createJsonFileProduct($store, $this->getProducts($store, $group), Search::PRODUCT_SEARCH);

        $newProducts = $this->createJsonFileProduct(
            $store,
            $this->getNewProducts($store, $group),
            Search::NEW_PRODUCTS
        );

        $mostViewedProducts = $this->createJsonFileProduct(
            $store,
            $this->getMostViewedProducts($store),
            Search::MOST_VIEWED_PRODUCTS
        );

        $bestsellers = $this->createJsonFileProduct($store, $this->getBestSellers($store), Search::BESTSELLERS);

        $this->getMediaHelper()->createJsFile(
            $this->getJsFilePath($group, $store),
            ';var mp_products_search = ' . $products . ';'
        );
        $this->getMediaHelper()->createJsFile(
            $this->getAdditionJsFilePath($group, $store),
            ';var mp_new_product_search = ' . $newProducts . ';
            var mp_most_viewed_products = ' . $mostViewedProducts . ';
            var mp_bestsellers = ' . $bestsellers . ';'
        );

        return $this;
    }

    /**
     * A cron job will run this function everyday
     *
     * @param $store
     * @param $group
     *
     * @return $this
     */
    public function createAdditionJsonFileForStore($store, $group)
    {
        $newProducts = $this->createJsonFileProduct(
            $store,
            $this->getNewProducts($store, $group),
            Search::NEW_PRODUCTS
        );

        $mostViewedProducts = $this->createJsonFileProduct(
            $store,
            $this->getMostViewedProducts($store),
            Search::MOST_VIEWED_PRODUCTS
        );

        $bestsellers = $this->createJsonFileProduct($store, $this->getBestSellers($store), Search::BESTSELLERS);

        $this->getMediaHelper()->createJsFile(
            $this->getAdditionJsFilePath($group, $store),
            ';var mp_new_product_search = ' . $newProducts . ';
            var mp_most_viewed_products = ' . $mostViewedProducts . ';
            var mp_bestsellers = ' . $bestsellers . ';'
        );

        return $this;
    }

    /**
     * @param $store
     * @param $collection
     * @param $option
     *
     * @return $this|string
     */
    public function createJsonFileProduct($store, $collection, $option)
    {
        if (!$this->isEnabled($store->getId())) {
            return $this;
        }

        $productList = [];
        $maxQueryResults = $this->getConfigGeneral(self::MAX_QUERY_RESULT);
        $resultCount = 0;

        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */

        /** @var \Magento\Catalog\Model\Product $product */
        foreach ($collection as $product) {
            if ($option == Search::BESTSELLERS) {
                $product = $this->objectManager->create('Magento\Catalog\Model\Product')->load($product->getProductId());
            }

            /** The maximum results of [Most Viewed | New Products| Bestsellers] is $maxQueryResults*/
            if ($option == Search::PRODUCT_SEARCH || ($option != Search::PRODUCT_SEARCH && $resultCount < $maxQueryResults)) {
                $productList[] = [
                    'value' => $product->getName(),
                    'c'     => $product->getCategoryIds(),
                    //categoryIds
                    'd'     => $this->getProductDescription($product, $store),
                    //short description
                    'p'     => $this->_priceHelper->currencyByStore($product->getFinalPrice(), $store, false, false),
                    //price
                    'i'     => $this->getMediaHelper()->getProductImage($product),
                    //image
                    'u'     => $this->getProductUrl($product),
                    //product url
                    'o'     => $option
                ];
            }

            $resultCount++;
        }
        $data = self::jsonEncode($productList);

        return $data;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return bool|string
     */
    protected function getProductUrl($product)
    {
        $productUrl = $product->getProductUrl();
        $requestPath = $product->getRequestPath();
        if (!$requestPath) {
            $pos = strpos($productUrl, 'catalog/product/view');
            if ($pos !== false) {
                $productUrl = substr($productUrl, $pos + 20);
            }
        } else {
            $productUrl = $requestPath;
        }

        return $productUrl;
    }

    /**
     * @param $product
     * @param $store
     *
     * @return array|bool|string
     */
    protected function getProductDescription($product, $store)
    {
        $attributeHtml = strip_tags($product->getShortDescription());
        $attributeHtml = trim($this->_escaper->escapeHtml($attributeHtml));

        $limitDesLetter = (int) $this->getConfigGeneral('max_letter_numbers', $store->getId());
        if ($limitDesLetter > 0 && strlen($attributeHtml) > $limitDesLetter) {
            $attributeHtml = mb_substr($attributeHtml, 0, $limitDesLetter, mb_detect_encoding($attributeHtml));
            $attributeHtml .= '...';
        }

        return $attributeHtml;
    }

    /**
     * @param $customerGroupId
     * @param $store
     *
     * @return string
     */
    public function getJsFilePath($customerGroupId, $store)
    {
        return Media::TEMPLATE_MEDIA_PATH . '/' . $store->getCode() . '_' . $customerGroupId . '.js';
    }

    /**
     * @param $customerGroupId
     * @param $store
     *
     * @return string
     */
    public function getAdditionJsFilePath($customerGroupId, $store)
    {
        return Media::TEMPLATE_MEDIA_PATH . '/' . $store->getCode() . '_' . $customerGroupId . '_addition.js';
    }

    /**
     * @return string
     */
    public function getJsFileUrl()
    {
        $customerGroupId = $this->_customerSession->getCustomerGroupId();

        /** @var \Magento\Store\Model\Store $store */
        $store = $this->storeManager->getStore();

        $mediaDirectory = $this->getMediaHelper()->getMediaDirectory();
        $filePath = $this->getJsFilePath($customerGroupId, $store);
        if (!$mediaDirectory->isFile($filePath)) {
            $this->createJsonFileForStore($store, $customerGroupId);
        }

        return $this->getMediaHelper()->getMediaUrl($filePath);
    }

    /**
     * @return string
     */
    public function getAdditionJsFileUrl()
    {
        $customerGroupId = $this->_customerSession->getCustomerGroupId();

        /** @var \Magento\Store\Model\Store $store */
        $store = $this->storeManager->getStore();

        $mediaDirectory = $this->getMediaHelper()->getMediaDirectory();
        $filePath = $this->getAdditionJsFilePath($customerGroupId, $store);
        if (!$mediaDirectory->isFile($filePath)) {
            $this->createJsonFileForStore($store, $customerGroupId);
        }

        return $this->getMediaHelper()->getMediaUrl($filePath);
    }

    /**
     * @return array
     */
    public function getCategoryTree()
    {
        $categoriesOptions = [0 => __('All Categories')];

        $maxLevel = max(0, (int) $this->getConfigGeneral('category/max_depth')) ?: 2;
        $parent = $this->storeManager->getStore()->getRootCategoryId();
        $categories = $this->categoryFactory->create();
        $categoryCollection = $categories->getCategories($parent, 1, false, true);
        if ($categories->getUseFlatResource()) {
            foreach ($categoryCollection as $category) {
                if ($category->getParentId() == $parent) {
                    $this->getCategoryOptions($category, $categoriesOptions, $maxLevel);
                }
            }
        } else {
            foreach ($categoryCollection as $category) {
                $this->getCategoryOptions($category, $categoriesOptions, $maxLevel);
            }
        }

        return $categoriesOptions;
    }

    /**
     * @param $category
     * @param $options
     * @param $level
     * @param string $htmlPrefix
     *
     * @return $this
     */
    protected function getCategoryOptions($category, &$options, $level, $htmlPrefix = '')
    {
        if ($level <= 0) {
            return $this;
        }
        $level--;

        $options[$category->getId()] = $htmlPrefix . $category->getName();

        $htmlPrefix .= '- ';
        if (count($this->getChildCategories($category))) {
            foreach ($this->getChildCategories($category) as $childCategory) {
                $this->getCategoryOptions($childCategory, $options, $level, $htmlPrefix);
            }
        }

        return $this;
    }

    /**
     * @param $category
     *
     * @return mixed
     */
    public function getChildCategories($category)
    {
        return $category->getChildrenCategories();
    }

    /**
     * @return string
     */
    public function getPriceFormat()
    {
        return self::jsonEncode($this->localeFormat->getPriceFormat());
    }

    /**
     * @param $store
     * @param $group
     *
     * @return \Magento\Reports\Model\ResourceModel\Product\Collection
     */
    public function getProducts($store, $group)
    {
        $collection = $this->_productsFactory->create();
        $collection->addAttributeToSelect($this->catalogConfig->getProductAttributes())
            ->setStore($store)
            ->addPriceData($group)
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addStoreFilter()
            ->addUrlRewrite()
            ->setVisibility($this->productVisibility->getVisibleInSearchIds());

        return $collection;
    }

    /**
     * @param $store
     * @param $group
     *
     * @return \Magento\Reports\Model\ResourceModel\Product\Collection
     */
    public function getNewProducts($store, $group)
    {
        $collection = $this->_productsFactory->create();

        $todayStartOfDayDate = $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate = $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');

        $collection->addAttributeToSelect($this->catalogConfig->getProductAttributes())
            ->addAttributeToFilter('news_from_date', ['date' => true, 'to' => $todayStartOfDayDate])
            ->addAttributeToFilter('news_to_date', ['date' => true, 'from' => $todayEndOfDayDate])
            ->setStore($store)
            ->addPriceData($group)
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addStoreFilter()
            ->addUrlRewrite()
            ->setVisibility($this->productVisibility->getVisibleInSearchIds());

        return $collection;
    }

    /**
     * @param $store
     *
     * @return \Magento\Framework\DataObject[]
     */
    public function getMostViewedProducts($store)
    {
        $collection = $this->_productsFactory->create()
            ->addAttributeToSelect('*')
            ->addViewsCount()
            ->setStoreId($store->getId())
            ->addStoreFilter($store->getId());
        $items = $collection->getItems();

        return $items;
    }

    /**
     * @param $store
     *
     * @return $this
     */
    public function getBestSellers($store)
    {
        return $this->bestsellersCollection->create()
            ->setModel('Magento\Catalog\Model\Product')
            ->addStoreFilter($store->getId())
            ->setPeriod('year');
    }
}
