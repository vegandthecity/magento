<?php

namespace Meetanshi\Matrixrate\Block\Adminhtml\Method\Edit\Tab;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store as SystemStore;
use Meetanshi\Matrixrate\Helper\Data;

/**
 * Class General
 * @package Meetanshi\Matrixrate\Block\Adminhtml\Method\Edit\Tab
 */
class General extends Form
{
    /**
     * @var SystemStore
     */
    protected $systemStore;
    /**
     * @var FormFactory
     */
    protected $formFactory;
    /**
     * @var Registry
     */
    protected $registry;
    /**
     * @var Context
     */
    protected $context;
    /**
     * @var Data
     */
    protected $helper;

    /**
     * General constructor.
     * @param SystemStore $systemStore
     * @param FormFactory $formFactory
     * @param Registry $registry
     * @param Context $context
     * @param Data $helper
     */
    public function __construct(SystemStore $systemStore, FormFactory $formFactory, Registry $registry, Context $context, Data $helper)
    {
        $this->systemStore = $systemStore;
        $this->formFactory = $formFactory;
        $this->registry = $registry;
        $this->context = $context;
        $this->helper = $helper;
        parent::__construct($context);
    }

    /**
     * @return Form
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $form = $this->formFactory->create();
        $this->setForm($form);
        $helper = $this->helper;

        $fldInfo = $form->addFieldset('general', ['legend' => __('General')]);
        $fldInfo->addField('name', 'text', [
            'label' => __('Name'),
            'required' => true,
            'name' => 'name',
            'note' => 'Variable {day} will be replaced with the estimated delivery value from the corresponding CSV column',
        ]);

        $fldInfo->addField('is_active', 'select', [
            'label' => __('Status'),
            'name' => 'is_active',
            'options' => $helper->getStatuses(),
        ]);

        $fldInfo->addField('pos', 'text', [
            'label' => __('Priority'),
            'name' => 'pos',
        ]);

        $fldInfo->addField('select_rate', 'select', [
            'label' => __('For products with different shipping types'),
            'name' => 'select_rate',
            'values' => [
                [
                    'value' => '0',
                    'label' => __('Sum up rates')
                ],
                [
                    'value' => '1',
                    'label' => __('Select maximal rate')
                ],
                [
                    'value' => '2',
                    'label' => __('Select minimal rate')
                ]]
        ]);

        $fldStore = $form->addFieldset('apply_in', ['legend' => __('Visible In')]);
        $fldStore->addField('stores', 'multiselect', [
            'label' => __('Stores'),
            'name' => 'stores[]',
            'values' => $this->systemStore->getStoreValuesForForm(),
            'note' => __('Leave empty if there are no restrictions'),
        ]);

        $fldCust = $form->addFieldset('apply_for', ['legend' => __('Applicable For')]);
        $fldCust->addField('cust_groups', 'multiselect', [
            'name' => 'cust_groups[]',
            'label' => __('Customer Groups'),
            'values' => $helper->getAllGroups(),
            'note' => __('Leave empty if there are no restrictions'),
        ]);

        //set form values
        $form->setValues($this->registry->registry('matrixrate_method')->getData());

        return parent::_prepareForm();
    }
}
