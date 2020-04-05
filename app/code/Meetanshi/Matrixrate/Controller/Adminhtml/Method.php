<?php

namespace Meetanshi\Matrixrate\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Meetanshi\Matrixrate\Model\MethodFactory;
use Meetanshi\Matrixrate\Model\RateFactory;
use Magento\Backend\Model\Auth\Session;

/**
 * Class Method
 * @package Meetanshi\Matrixrate\Controller\Adminhtml
 */
abstract class Method extends Action
{
    /**
     * @var string
     */
    protected $_title = 'Custom Shipping Methods';
    /**
     * @var string
     */
    protected $_modelName = 'method';
    /**
     * @var MethodFactory
     */
    protected $methodFactory;
    /**
     * @var RateFactory
     */
    protected $rateFactory;
    /**
     * @var Registry
     */
    protected $registry;
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var Context
     */
    protected $context;
    /**
     * @var Session
     */
    protected $backendSession;

    /**
     * Method constructor.
     * @param MethodFactory $methodFactory
     * @param Registry $registry
     * @param Context $context
     * @param ForwardFactory $resultForwardFactory
     * @param RateFactory $rateFactory
     * @param PageFactory $resultPageFactory
     * @param Session $backendSession
     */
    public function __construct(MethodFactory $methodFactory, Registry $registry, Context $context, ForwardFactory $resultForwardFactory, RateFactory $rateFactory, PageFactory $resultPageFactory, Session $backendSession)
    {
        parent::__construct($context);
        $this->methodFactory = $methodFactory;
        $this->registry = $registry;
        $this->context = $context;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->rateFactory = $rateFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->backendSession = $backendSession;
    }

    /**
     * @param $model
     * @return bool
     */
    public function prepareForSave($model)
    {
        $fields = ['stores', 'cust_groups'];
        foreach ($fields as $value) {
            $val = $model->getData($value);
            $model->setData($value, '');
            if (is_array($val)) {
                $model->setData($value, ',' . implode(',', $val) . ',');
            }
        }
        return true;
    }

    /**
     * @param $model
     * @return bool
     */
    public function prepareForEdit($model)
    {
        $fields = ['stores', 'cust_groups'];
        foreach ($fields as $value) {
            $val = $model->getData($value);
            if (!is_array($val)) {
                $model->setData($value, explode(',', $val));
            }
        }
        return true;
    }

    /**
     * @param $status
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function _modifyStatus($status)
    {
        $ids = $this->getRequest()->getParam('methods');
        if ($ids && is_array($ids)) {
            try {
                $this->methodFactory->create()->massChangeStatus($ids, $status);
                $message = __('Total of %1 record(s) have been updated.', sizeof($ids));
                $this->messageManager->addSuccessMessage($message);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        } else {
            $this->messageManager->addErrorMessage(__('Please select method(s).'));
        }

        return $this->_redirect('*/*');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
