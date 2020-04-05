<?php

namespace Meetanshi\Matrixrate\Controller\Adminhtml\Method;

use Magento\Framework\Controller\ResultFactory;
use Meetanshi\Matrixrate\Controller\Adminhtml\Method;

/**
 * Class Delete
 * @package Meetanshi\Matrixrate\Controller\Adminhtml\Method
 */
class Delete extends Method
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $id = (int)$this->getRequest()->getParam('id');
        $model = $this->methodFactory->create()->load($id);

        if ($id && !$model->getId()) {
            $this->messageManager->addErrorMessage($this->__('Record does not exist'));
            $this->_redirect('*/*/');
            return;
        }

        try {
            $model->delete();
            $this->messageManager->addSuccessMessage(__('Shipping method has been successfully deleted'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $this->_redirect('*/*/');
        return;
    }
}
