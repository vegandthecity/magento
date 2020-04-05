<?php

namespace Meetanshi\Matrixrate\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

/**
 * Class Method
 * @package Meetanshi\Matrixrate\Block\Adminhtml
 */
class Method extends Container
{
    /**
     *
     */
    public function _construct()
    {
        $this->_controller = 'adminhtml_method';
        $this->_blockGroup = 'Meetanshi_Matrixrate';
        $this->_headerText = __('Methods');
        $this->_addButtonLabel = __('Add Method');
        parent::_construct();
    }
}
