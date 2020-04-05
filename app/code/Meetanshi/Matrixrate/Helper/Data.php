<?php

namespace Meetanshi\Matrixrate\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Customer\Model\ResourceModel\Group\CollectionFactory as CustomerGroupCollection;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory as CountryCollection;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory as RegionCollection;
use Magento\Eav\Model\Config;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 * @package Meetanshi\Matrixrate\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var CountryCollection
     */
    protected $countryCollection;
    /**
     * @var RegionCollection
     */
    protected $regionCollection;
    /**
     * @var CustomerGroupCollection
     */
    protected $customergroupCollection;
    /**
     * @var Config
     */
    protected $eavConfig;

    /**
     * Data constructor.
     * @param Context $context
     * @param CustomerGroupCollection $customergroupCollection
     * @param CountryCollection $countryCollection
     * @param RegionCollection $regionCollection
     * @param Config $eavConfig
     */
    public function __construct(Context $context, CustomerGroupCollection $customergroupCollection, CountryCollection $countryCollection, RegionCollection $regionCollection, Config $eavConfig)
    {
        parent::__construct($context);
        $this->customergroupCollection = $customergroupCollection;
        $this->countryCollection = $countryCollection;
        $this->regionCollection = $regionCollection;
        $this->eavConfig = $eavConfig;
    }

    /**
     * @return array
     */
    public function getAllGroups()
    {
        $customerGroups = $this->customergroupCollection->create()->load()->toOptionArray();
        $found = false;
        foreach ($customerGroups as $group) {
            if ($group['value'] == 0) {
                $found = true;
            }
        }
        if (!$found) {
            array_unshift($customerGroups, ['value' => 0, 'label' => __('NOT LOGGED IN')]);
        }
        return $customerGroups;
    }

    /**
     * @return array
     */
    public function getStatuses()
    {
        return [
            '0' => __('Inactive'),
            '1' => __('Active'),
        ];
    }

    /**
     * @return array
     */
    public function getStates()
    {
        $hash = [];
        $countries = $this->getCountries();
        $collection = $this->regionCollection->create()->getData();
        foreach ($collection as $state) {
            $hash[$state['region_id']] = $countries[$state['country_id']] . "/" . $state['default_name'];
        }
        asort($hash);
        $hashAll['0'] = 'All';
        $hash = $hashAll + $hash;
        return $hash;
    }

    /**
     * @return array
     */
    public function getCountries()
    {
        $hash = [];
        $countries = $this->countryCollection->create()->toOptionArray();
        foreach ($countries as $country) {
            if ($country['value']) {
                $hash[$country['value']] = $country['label'];
            }
        }
        asort($hash);
        $hashAll['0'] = 'All';
        $hash = $hashAll + $hash;
        return $hash;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getShippingType()
    {
        $hash = [];
        $attribute = $this->eavConfig->getAttribute('catalog_product', 'shipping_type');
        if ($attribute->usesSource()) {
            $options = $attribute->getSource()->getAllOptions(false);
        }
        foreach ($options as $option) {
            $hash[$option['value']] = $option['label'];
        }
        asort($hash);
        $hashAll['0'] = 'All';
        $hash = $hashAll + $hash;
        return $hash;
    }

    /**
     * @return mixed
     */
    public function allowPromo()
    {
        return $this->scopeConfig->getValue('carriers/matrixrate/allow_free_shipping', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function ignoreVirtual()
    {
        return $this->scopeConfig->getValue('carriers/matrixrate/ignore_virtual', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function dontSplit()
    {
        return $this->scopeConfig->getValue('carriers/matrixrate/dont_split', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getAfterDiscount()
    {
        return $this->scopeConfig->getValue('carriers/matrixrate/after_discount', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getIncludingTax()
    {
        return $this->scopeConfig->getValue('carriers/matrixrate/including_tax', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function allowNumericZip()
    {
        return $this->scopeConfig->getValue('carriers/matrixrate/allow_numeric_zip', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $data
     * @return array
     */
    public function getNumericZip($data)
    {
        $array = ['area' => '', 'district' => ''];

        if (!empty($data)) {
            $zipSpell = str_split($data);
            foreach ($zipSpell as $element) {
                if ($element === ' ') {
                    break;
                }
                if (is_numeric($element)) {
                    $array['district'] = $array['district'] . $element;
                } elseif (empty($array['district'])) {
                    $array['area'] = $array['area'] . $element;
                }
            }
        }

        return $array;
    }
}
