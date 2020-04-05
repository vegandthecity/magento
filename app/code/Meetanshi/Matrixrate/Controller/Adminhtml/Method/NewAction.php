<?php

namespace Meetanshi\Matrixrate\Controller\Adminhtml\Method;

use Magento\Framework\Controller\ResultFactory;
use Meetanshi\Matrixrate\Controller\Adminhtml\Method;

/**
 * Class NewAction
 * @package Meetanshi\Matrixrate\Controller\Adminhtml\Method
 */
class NewAction extends Method
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        return $this->resultForwardFactory->create()->forward('edit');
    }
}
