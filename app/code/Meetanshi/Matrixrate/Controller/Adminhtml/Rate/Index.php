<?php

namespace Meetanshi\Matrixrate\Controller\Adminhtml\Rate;

use Magento\Framework\Controller\ResultFactory;
use Meetanshi\Matrixrate\Controller\Adminhtml\Rate;

/**
 * Class Index
 * @package Meetanshi\Matrixrate\Controller\Adminhtml\Rate
 */
class Index extends Rate
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $html = $resultRedirect->getLayout()->createBlock('Meetanshi\Matrixrate\Block\Adminhtml\Rates')->toHtml();
        $this->getResponse()->setBody($html);
    }
}
