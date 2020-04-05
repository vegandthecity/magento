<?php

namespace Meetanshi\Matrixrate\Block\Adminhtml\Method;

use Magento\Backend\Block\Widget\Context as Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data as BackendHelper;
use Meetanshi\Matrixrate\Model\ResourceModel\Method\CollectionFactory;
use Meetanshi\Matrixrate\Helper\Data;

/**
 * Class Grid
 * @package Meetanshi\Matrixrate\Block\Adminhtml\Method
 */
class Grid extends Extended
{
    /**
     * @var CollectionFactory
     */
    protected $methodCollection;
    /**
     * @var Data
     */
    protected $helper;

    /**
     * Grid constructor.
     * @param CollectionFactory $methodCollection
     * @param Data $helper
     * @param Context $context
     * @param BackendHelper $backendHelper
     */
    public function __construct(CollectionFactory $methodCollection, Data $helper, Context $context, BackendHelper $backendHelper)
    {
        $this->methodCollection = $methodCollection;
        $this->helper = $helper;

        parent::__construct($context, $backendHelper);
    }

    /**
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('methodGrid');
        $this->setDefaultSort('pos');
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('matrixrate/method/edit', ['id' => $row->getId()]);
    }

    /**
     * @return Extended
     */
    protected function _prepareCollection()
    {
        $collection = $this->methodCollection->create();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return Extended
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $hlp = $this->helper;
        $this->addColumn('method_id', [
            'header' => __('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'method_id',
        ]);

        $this->addColumn('name', [
            'header' => __('Name'),
            'index' => 'name',
        ]);

        $this->addColumn('pos', [
            'header' => __('Priority'),
            'index' => 'pos',
        ]);

        $this->addColumn('is_active', [
            'header' => __('Status'),
            'align' => 'left',
            'width' => '80px',
            'renderer' => '\Meetanshi\Matrixrate\Block\Adminhtml\Method\Grid\Renderer\Color',
            'index' => 'is_active',
            'type' => 'options',
            'options' => $hlp->getStatuses(),
        ]);

        return parent::_prepareColumns();
    }

    /**
     * @return $this|Extended
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('method_id');
        $this->getMassactionBlock()->setFormFieldName('methods');

        $actions = [
            'massActivate' => 'Activate',
            'massInactivate' => 'Inactivate',
            'massDelete' => 'Delete',
        ];

        foreach ($actions as $code => $label) {
            $this->getMassactionBlock()->addItem($code, [
                'label' => __($label),
                'url' => $this->getUrl('*/*/' . $code),
                'confirm' => ($code == 'massDelete' ? __('Are you sure?') : null),
            ]);
        }
        return $this;
    }
}
