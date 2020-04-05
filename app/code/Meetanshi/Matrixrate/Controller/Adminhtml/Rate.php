<?php

namespace Meetanshi\Matrixrate\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Registry;
use Meetanshi\Matrixrate\Model\RateFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\Auth\Session;

/**
 * Class Rate
 * @package Meetanshi\Matrixrate\Controller\Adminhtml
 */
abstract class Rate extends Action
{
    /**
     * @var RateFactory
     */
    protected $rateFactory;
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var Registry
     */
    protected $registry;
    /**
     * @var Context
     */
    protected $context;
    /**
     * @var Session
     */
    protected $backendSession;

    /**
     * Rate constructor.
     * @param RateFactory $rateFactory
     * @param Registry $registry
     * @param Context $context
     * @param ForwardFactory $resultForwardFactory
     * @param PageFactory $resultPageFactory
     * @param Session $backendSession
     */
    public function __construct(RateFactory $rateFactory, Registry $registry, Context $context, ForwardFactory $resultForwardFactory, PageFactory $resultPageFactory, Session $backendSession)
    {
        parent::__construct($context);
        $this->rateFactory = $rateFactory;
        $this->registry = $registry;
        $this->context = $context;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->backendSession = $backendSession;
    }

    /**
     * @return mixed
     */
    protected function initModel()
    {
        $model = $this->rateFactory->create();

        if ($this->getRequest()->getParam('id')) {
            $model->load($this->getRequest()->getParam('id'));
        }

        $this->registry->register('matrixrate_rate', $model);
        return $model;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
