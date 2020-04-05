<?php

namespace Meetanshi\Matrixrate\Controller\Adminhtml\Method;

use Magento\Framework\Controller\ResultFactory;
use Meetanshi\Matrixrate\Controller\Adminhtml\Method;

/**
 * Class MassDelete
 * @package Meetanshi\Matrixrate\Controller\Adminhtml\Method
 */
class MassDelete extends Method
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $ids = $this->getRequest()->getParam('methods');
        if (!is_array($ids)) {
            $this->messageManager->addErrorMessage(__('Please select records'));
            $this->_redirect('*/*/');
            return;
        }

        $deleted = 0;
        foreach ($ids as $id) {
            $this->methodFactory->create()->load($id)->delete();
            $deleted++;
        }

        $this->messageManager->addSuccessMessage(
            __('A total of %1 record(s) have been deleted.', $deleted)
        );

        $this->_redirect('*/*/');
        return;
    }
}
