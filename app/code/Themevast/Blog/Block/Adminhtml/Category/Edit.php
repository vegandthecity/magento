<?php

namespace Themevast\Blog\Block\Adminhtml\Category;
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
   
    protected $_coreRegistry = null;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Themevast_Blog';
        $this->_controller = 'adminhtml_category';

        parent::_construct();

        if ($this->_isAllowedAction('Themevast_Blog::category')) {
            $this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                        ],
                    ]
                ],
                -100
            );
        } else {
            $this->buttonList->remove('save');
        }

        if (!$this->_isAllowedAction('Themevast_Blog::category')) {
            $this->buttonList->remove('delete');
        }
    }


    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '{{tab_id}}']);
    }

    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('category_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'category_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'category_content');
                }
            };
        ";
        return parent::_prepareLayout();
    }
}
