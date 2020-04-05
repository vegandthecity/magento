<?php

namespace Meetanshi\Matrixrate\Controller\Adminhtml\Method;

use Magento\Framework\Controller\ResultFactory;
use Meetanshi\Matrixrate\Controller\Adminhtml\Method;

/**
 * Class Index
 * @package Meetanshi\Matrixrate\Controller\Adminhtml\Method
 */
class Index extends Method
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Magento_Sales::shipping_table');
        $resultPage->addBreadcrumb(__('Matrix Rates'), __('Matrix Rates'));
        $resultPage->addBreadcrumb(__('Manage Matrix Rates'), __('Matrix Rates'));
        $resultPage->getConfig()->getTitle()->prepend(__('Matrix Rates'));
        $this->_addContent($this->_view->getLayout()->createBlock('\Meetanshi\Matrixrate\Block\Adminhtml\Method'));
        $this->_view->renderLayout();
    }
}
