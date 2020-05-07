<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Paypal\Controller\Express\AbstractExpress;

use Magento\Framework\Controller\ResultFactory;

class Review extends \Magento\Paypal\Controller\Express\AbstractExpress
{
    /**
     * Review order after returning from PayPal
     *
     * @return void|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        try {
            $this->_initCheckout();
            $this->_checkout->prepareOrderReview($this->_initToken());
            $this->_view->loadLayout();
            $reviewBlock = $this->_view->getLayout()->getBlock('paypal.express.review');
            $reviewBlock->setQuote($this->_getQuote());
            $reviewBlock->getChildBlock('details')->setQuote($this->_getQuote());
            if ($reviewBlock->getChildBlock('shipping_method')) {
                $reviewBlock->getChildBlock('shipping_method')->setQuote($this->_getQuote());
            }
            $this->_view->renderLayout();
            return;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                $e->getMessage()
            );
        } catch (\Exception $e) {
        	// 2020-05-07 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
			// «We can't initialize Express Checkout» / «Wir konnten die Express-Kaufabwicklung nicht initialisieren»:
			// https://github.com/vegandthecity/magento/issues/35
        	df_log_e($e, $this);
            $this->messageManager->addExceptionMessage(
                $e,
                __('We can\'t initialize Express Checkout review.')
            );
        }

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('checkout/cart');
    }
}
