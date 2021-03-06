<?php
/**
 * Webkul Odoomagentoconnect Carrier Edit Controller
 * @category  Webkul
 * @package   Webkul_Odoomagentoconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomagentoconnect\Controller\Adminhtml\Carrier;

class ExportAllIds extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Backend\Model\View\Result\Forward
     */
    protected $_resultForwardFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Webkul\Odoomagentoconnect\Helper\Connection $connection,
        \Webkul\Odoomagentoconnect\Model\Carrier $carrierMapping,
        \Magento\Shipping\Model\Config\Source\Allmethods $carrierModel,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ) {
    
        $this->_carrierMapping = $carrierMapping;
        $this->_carrierModel = $carrierModel;
        $this->_connection = $connection;
        $this->_resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Odoomagentoconnect::carrier_save');
    }

    /**
     * Forward to edit
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $exportIds = [];
        $helper = $this->_connection;
        $helper->getSocketConnect();
        $userId = $helper->getSession()->getUserId();
        if ($userId) {
            $collection = $this->_carrierModel->toOptionArray();
            foreach ($collection as $shippingCode => $shippingModel) {
				/**
				 * 2020-04-17 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
				 * 1) «Undefined variable: shippingMethods
				 * in app/code/Webkul/Odoomagentoconnect/Controller/Adminhtml/Carrier/ExportAllIds.php on line 61»:
				 * https://github.com/vegandthecity/magento/issues/22
				 * 2) The `$shippingMethods` variable seems to be a mistype. I have replaced it with `$shippingModel`.
				 */
                if ($shippingModel['label']) {
                    foreach ($shippingModel['value'] as $method) {
                        $shippingMethodCode = $method['value'];
                        $mapping = $this->_carrierMapping->getCollection()
                            ->addFieldToFilter('carrier_code', ['eq'=>$shippingMethodCode]);
                        if ($mapping->getSize() == 0) {
                            array_push($exportIds, $shippingMethodCode);
                        }
                    }
                }
            }
            
            if (count($collection) == 0) {
                $this->messageManager->addSuccess(__("No Attribute Carriers are exist at Magento."));
            } else {
                if (count($exportIds) == 0) {
                    $this->messageManager->addSuccess(
                        __(
                            "All Magento Attribute Carriers are already exported at Odoo."
                        )
                    );
                }
            }
        } else {
            $errorMessage = $helper->getSession()->getErrorMessage();
            $this->messageManager->addError(
                __(
                    "Carrier(s) have not been Exported at Odoo !! Reason : ".$errorMessage
                )
            );
        }
        $this->getResponse()->clearHeaders()->setHeader('content-type', 'application/json', true);
        $this->getResponse()->setBody(json_encode($exportIds));
    }
}
