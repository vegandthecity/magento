<?php

namespace Meetanshi\Matrixrate\Controller\Adminhtml\Method;

use Meetanshi\Matrixrate\Controller\Adminhtml\Method;

/**
 * Class MassActivate
 * @package Meetanshi\Matrixrate\Controller\Adminhtml\Method
 */
class MassActivate extends Method
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        return $this->_modifyStatus(1);
    }
}
