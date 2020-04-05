<?php

namespace Meetanshi\Matrixrate\Controller\Adminhtml\Method;

use Magento\Framework\Controller\ResultFactory;
use Meetanshi\Matrixrate\Controller\Adminhtml\Method;

/**
 * Class Edit
 * @package Meetanshi\Matrixrate\Controller\Adminhtml\Method
 */
class Edit extends Method
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $id = $this->getRequest()->getParam('id');
        $model = $this->methodFactory->create()->load($id);

        if ($id && !$model->getId()) {
            $this->messageManager->addErrorMessage(__('Record does not exist'));
            $this->_redirect('*/*/');
            return;
        }

        $data = $this->backendSession->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        } else {
            $this->prepareForEdit($model);
        }

        $this->registry->register('matrixrate_method', $model);

        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Matrix Rates') : __('New Matrix Rate'),
            $id ? __('Edit Matrix Rates') : __('New Matrix Rate')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Matrix Rates'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getName() : __('New Matrix Rate'));

        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magento_Sales::shipping_table')
            ->addBreadcrumb(__('Matrix Rates'), __('Matrix Rates'))
            ->addBreadcrumb(__('Manage Matrix Rates'), __('Manage Matrix Rates'));
        return $resultPage;
    }
}
