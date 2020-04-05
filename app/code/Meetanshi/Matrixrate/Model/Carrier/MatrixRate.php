<?php

namespace Meetanshi\Matrixrate\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Psr\Log\LoggerInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Meetanshi\Matrixrate\Model\ResourceModel\Method\CollectionFactory;
use Meetanshi\Matrixrate\Model\Rate;

/**
 * Class MatrixRate
 * @package Meetanshi\Matrixrate\Model\Carrier
 */
class MatrixRate extends AbstractCarrier implements CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'matrixrate';
    /**
     * @var ErrorFactory
     */
    protected $rateErrorFactory;
    /**
     * @var ResultFactory
     */
    protected $rateResultFactory;
    /**
     * @var MethodFactory
     */
    protected $rateMethodFactory;
    /**
     * @var CollectionFactory
     */
    protected $matrixrateTableFactory;
    /**
     * @var Rate
     */
    protected $matrixrate;

    /**
     * MatrixRate constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param CollectionFactory $matrixrateTableFactory
     * @param Rate $matrixrate
     * @param array $data
     */
    public function __construct(ScopeConfigInterface $scopeConfig, ErrorFactory $rateErrorFactory, LoggerInterface $logger, ResultFactory $rateResultFactory, MethodFactory $rateMethodFactory, CollectionFactory $matrixrateTableFactory, Rate $matrixrate, array $data = [])
    {
        $this->rateErrorFactory = $rateErrorFactory;
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->matrixrateTableFactory = $matrixrateTableFactory;
        $this->matrixrate = $matrixrate;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * @param RateRequest $request
     * @return bool|\Magento\Framework\DataObject|\Magento\Quote\Model\Quote\Address\RateResult\Error|Result|null
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigData('active')) {
            return false;
        }

        $result = $this->rateResultFactory->create();
        $collection = $this->matrixrateTableFactory->create()
            ->addFieldToFilter('is_active', 1)
            ->addStoreFilter($request->getStoreId())
            ->addCustomerGroupFilter($this->getCustomerGroupId($request))
            ->setOrder('pos');

        $rates = $this->matrixrate->calculate($request, $collection);

        $count = 0;
        foreach ($collection as $col) {
            $method = $this->rateMethodFactory->create();
            $method->setCarrier($this->_code);
            $method->setCarrierTitle($this->getConfigData('title'));

            if (isset($rates[$col->getId()]['cost'])) {
                $method->setMethod($this->_code . $col->getId());
                $methodTitle = __($col->getName());
                $methodTitle = str_replace('{day}', $rates[$col->getId()]['time'], $methodTitle);
                $method->setMethodTitle($methodTitle);
                $method->setCost($rates[$col->getId()]['cost']);
                $method->setPrice($rates[$col->getId()]['cost']);
                $result->append($method);
                $count++;
            }
        }

        if (($count == 0) && ($this->getConfigData('showmethod') == 1)) {
            $error = $this->rateErrorFactory->create();
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage($this->getConfigData('specificerrmsg'));
            $result->append($error);
            return $error;
        }
        return $result;
    }

    /**
     * @param $request
     * @return int
     */
    public function getCustomerGroupId($request)
    {
        $allItems = $request->getAllItems();

        if (!$allItems) {
            return 0;
        }

        foreach ($allItems as $item) {
            return $item->getProduct()->getCustomerGroupId();
        }
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        $collection = $this->matrixrateTableFactory->create()
            ->addFieldToFilter('is_active', 1)
            ->setOrder('pos');
        $arr = [];
        foreach ($collection as $method) {
            $methodCode = 'matrixrate' . $method->getMethodId();
            $arr[$methodCode] = $method->getName();
        }
        return $arr;
    }
}
