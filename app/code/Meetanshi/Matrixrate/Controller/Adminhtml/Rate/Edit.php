<?php

namespace Meetanshi\Matrixrate\Controller\Adminhtml\Rate;

use Magento\Framework\Controller\ResultFactory;
use Meetanshi\Matrixrate\Controller\Adminhtml\Rate;

/**
 * Class Edit
 * @package Meetanshi\Matrixrate\Controller\Adminhtml\Rate
 */
class Edit extends Rate
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $id = $this->getRequest()->getParam('id');
        $model = $this->rateFactory->create()->load($id);

        $mid = (int)$this->getRequest()->getParam('mid');

        if (!$mid) {
            $mid = $model->getMethodId();
        }

        if (!$mid && !$model->getId()) {
            $this->messageManager->addErrorMessage(__('Record #%1 does not exist', $id));
            $this->_redirect('matrixrate/method/index');
            return;
        }
        $data = $this->backendSession->getFormData(true);

        if (!empty($data)) {
            $model->setData($data);
        }

        if ($mid && !$model->getId()) {
            $model->setMethodId($mid);
            $model->setWeightFrom('0');
            $model->setQtyFrom('0');
            $model->setPriceFrom('0');
            $model->setWeightTo('99999999');
            $model->setQtyTo('99999999');
            $model->setPriceTo('99999999');
        }

        $this->registry->register('matrixrate_rate', $model);
        $this->_view->loadLayout();
        $this->_addContent($this->_view->getLayout()->createBlock('\Meetanshi\Matrixrate\Block\Adminhtml\Rate\Edit'));
        $this->_view->renderLayout();
    }
}
