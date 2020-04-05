<?php

namespace Meetanshi\Matrixrate\Controller\Adminhtml\Method;

use Magento\Framework\Controller\ResultFactory;
use Meetanshi\Matrixrate\Controller\Adminhtml\Method;

/**
 * Class MassInactivate
 * @package Meetanshi\Matrixrate\Controller\Adminhtml\Method
 */
class MassInactivate extends Method
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        return $this->_modifyStatus(0);
    }
}
