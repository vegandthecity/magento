<?php

namespace Meetanshi\Matrixrate\Controller\Adminhtml\Rate;

use Magento\Framework\Controller\ResultFactory;
use Meetanshi\Matrixrate\Controller\Adminhtml\Rate;

/**
 * Class Delete
 * @package Meetanshi\Matrixrate\Controller\Adminhtml\Rate
 */
class Delete extends Rate
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $id = $this->getRequest()->getParam('id');
        if (!$id) {
            $this->messageManager->addErrorMessage(__('Unable to find a rate to delete'));
            $this->_redirect('matrixrate/method/index');
            return;
        }

        try {
            $rate = $this->rateFactory->create()->load($id);
            $methodId = $rate->getMethodId();

            $rate->delete();

            $this->messageManager->addSuccessMessage(__('Rate has been deleted'));
            $this->_redirect('matrixrate/method/edit', ['id' => $methodId, 'tab' => 'rates']);
            return;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->_redirect('matrixrate/method/index');
            return;
        }
    }
}
