<?php

namespace Meetanshi\Matrixrate\Block\Adminhtml\Method\Edit\Tab;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store as SystemStore;
use Magento\Framework\View\Asset\Repository;

/**
 * Class Import
 * @package Meetanshi\Matrixrate\Block\Adminhtml\Method\Edit\Tab
 */
class Import extends Form
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
     * @var Repository
     */
    protected $assetRepo;

    /**
     * Import constructor.
     * @param SystemStore $systemStore
     * @param FormFactory $formFactory
     * @param Registry $registry
     * @param Context $context
     * @param Repository $assetRepo
     */
    public function __construct(SystemStore $systemStore, FormFactory $formFactory, Registry $registry, Context $context, Repository $assetRepo)
    {
        $this->systemStore = $systemStore;
        $this->formFactory = $formFactory;
        $this->registry = $registry;
        $this->context = $context;
        $this->assetRepo = $assetRepo;
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

        $fldSet = $form->addFieldset('matrixrate_import', ['legend' => __('Import Rates')]);
        $fldSet->addField('import_clear', 'select', [
            'label' => __('Delete Existing Rates'),
            'name' => 'import_clear',
            'values' => [
                [
                    'value' => 0,
                    'label' => __('No')
                ],
                [
                    'value' => 1,
                    'label' => __('Yes')
                ]]
        ]);

        $csvFile = $this->assetRepo->getUrl('Meetanshi_Matrixrate::csv/rates.csv');
        $csvLink = "<a href=" . $csvFile . " target='_blank'>Download Sample File</a>";

        $fldSet->addField('import_file', 'file', [
            'label' => __('CSV File'),
            'name' => 'import_file',
            'note' => $csvLink
        ]);

        return parent::_prepareForm();
    }
}
