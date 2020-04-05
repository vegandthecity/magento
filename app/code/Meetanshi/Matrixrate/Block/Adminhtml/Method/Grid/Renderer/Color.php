<?php

namespace Meetanshi\Matrixrate\Block\Adminhtml\Method\Grid\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\Input;
use Magento\Framework\DataObject;

/**
 * Class Color
 * @package Meetanshi\Matrixrate\Block\Adminhtml\Method\Grid\Renderer
 */
class Color extends Input
{
    /**
     * @param DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
        $status = $row->getData($this->getColumn()->getIndex());
        if ($status == 1) {
            $colour = "10a900";
            $value = "Active";
        } else {
            $colour = "ff031b";
            $value = "Inactive";
        }
        return '<div style="text-align:center; color:#FFF;font-weight:bold;background:#' . $colour . ';border-radius:8px;width:100%">' . $value . '</div>';
    }
}
