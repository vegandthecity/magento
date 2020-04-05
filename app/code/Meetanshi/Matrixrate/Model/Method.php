<?php

namespace Meetanshi\Matrixrate\Model;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Meetanshi\Matrixrate\Model\MethodFactory;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Method
 * @package Meetanshi\Matrixrate\Model
 */
class Method extends AbstractModel
{
    /**
     * @var \Meetanshi\Matrixrate\Model\MethodFactory
     */
    protected $methodFactory;

    /**
     * Method constructor.
     * @param Context $context
     * @param Registry $registry
     * @param \Meetanshi\Matrixrate\Model\MethodFactory $methodFactory
     */
    public function __construct(Context $context, Registry $registry, MethodFactory $methodFactory)
    {
        $this->methodFactory = $methodFactory;
        parent::__construct($context, $registry);
    }

    /**
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('Meetanshi\Matrixrate\Model\ResourceModel\Method');
    }

    /**
     * @param $ids
     * @param $status
     * @return $this
     */
    public function massChangeStatus($ids, $status)
    {
        foreach ($ids as $id) {
            $model = $this->methodFactory->create()->load($id);
            $model->setIsActive($status);
            $model->save();
        }
        return $this;
    }

    /**
     * @param $html
     * @return string|string[]
     */
    public function addComment($html)
    {
        preg_match_all('@<label for="s_method_matrixrate_matrixrate(.+?)">.+?label>@si', $html, $matches);
        if (!empty($matches[0])) {
            $hashMethods = $this->methodFactory->getCollection()->toOptionHash();
            foreach ($matches[0] as $key => $value) {
                $methodId = $matches[1][$key];
                $to[] = $matches[0][$key] . '<div>' . $hashMethods[$methodId] . '</div>';
            }

            $newHtml = str_replace($matches[0], $to, $html);
            return $newHtml;
        }
        return $html;
    }
}
