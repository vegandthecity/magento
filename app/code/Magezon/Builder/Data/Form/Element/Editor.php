<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_Builder
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\Builder\Data\Form\Element;

use \Magento\Framework\App\ObjectManager;

class Editor extends AbstractElement
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @param Factory                           $factoryElement    
     * @param CollectionFactory                 $factoryCollection 
     * @param \Magezon\Builder\Helper\Data      $builderHelper     
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig     
     * @param array                             $data              
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        \Magezon\Builder\Helper\Data $builderHelper,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        $data = []
    ) {
        parent::__construct($factoryElement, $factoryCollection, $builderHelper, $data);
        $this->_wysiwygConfig = $wysiwygConfig;
    }

    public function _construct()
    {
        $this->setType('editor');
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $wysiwyg = array_replace_recursive($this->_wysiwygConfig->getConfig([
            'add_widgets' => false,
            'add_variables' => false
        ])->getData(), [
            'height' => '260px'
        ]);

        unset($wysiwyg['plugins'][1]);
        unset($wysiwyg['plugins'][2]);

        if (isset($wysiwyg['tinymce4'])) {
            $wysiwyg['tinymce4']['toolbar'] .= ' code forecolor backcolor image fontsizeselect lineheightselect removeformat';
            $wysiwyg['tinymce4']['plugins'] .= ' textcolor colorpicker image lineheight';
            $wysiwyg['tinymce4']['toolbar'] = 'fullscreen ' . $wysiwyg['tinymce4']['toolbar'];
            $wysiwyg['tinymce4']['plugins'] = 'fullscreen ' . $wysiwyg['tinymce4']['plugins'];
            $wysiwyg['tinymce4']['fontsize_formats'] = '8px 9px 10px 11px 12px 14px 15px 16px 18px 24px 30px 36px 48px 60px 72px 90px';
        }

        $config = array_replace_recursive([
            'templateOptions' => [
                'element'            => 'Magezon_Builder/js/form/element/editor',
                'templateUrl'        => 'Magezon_Builder/js/templates/form/element/editor.html',
                'wrapperTemplateUrl' => 'Magezon_Builder/js/templates/form/field.html',
                'wysiwyg'            => $wysiwyg
            ]
        ], (array) $this->getData('config'));

        return [
            'config' => $config
        ];
    }
}