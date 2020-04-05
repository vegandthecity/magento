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

namespace Magezon\Builder\Data\Element;

class Row extends AbstractElement
{
    /**
     * @return Magezon\Builder\Data\Form\Element\Fieldset
     */
    public function prepareGeneralTab()
    {
    	$general = parent::prepareGeneralTab();

	    	$general->addChildren(
	            'row_type',
	            'select',
	            [
					'sortOrder'       => 10,
					'key'             => 'row_type',
					'defaultValue'    => 'full_width_row',
					'templateOptions' => [
						'label'   => __('Appearance'),
						'options' => $this->getAppearanceTypes()
	                ]
	            ]
	        );

	        $container1 = $general->addContainerGroup(
	            'container1',
	            [
	                'sortOrder' => 20
	            ]
	        );

		    	$container1->addChildren(
		            'full_height',
		            'toggle',
		            [
						'sortOrder'       => 10,
						'key'             => 'full_height',
						'templateOptions' => [
							'label' => __('Full Height Row')
		                ]
		            ]
		        );

		    	$container1->addChildren(
		            'equal_height',
		            'toggle',
		            [
						'sortOrder'       => 20,
						'key'             => 'equal_height',
						'templateOptions' => [
							'label' => __('Column Equal Height')
		                ]
		            ]
		        );

		    	$container1->addChildren(
		            'content_position',
		            'select',
		            [
						'sortOrder'       => 30,
						'key'             => 'content_position',
						'defaultValue'    => 'top',
						'templateOptions' => [
							'label'       => __('Content Position'),
							'options'     => $this->getContentPosition()
		                ],
	                	'hideExpression' => '!model.equal_height'
		            ]
		        );

        $general->addChildren(
            'gap',
            'number',
            [
				'sortOrder'       => 30,
				'key'             => 'gap',
				'templateOptions' => [
					'label' => __('Columns Gap'),
					'note'  => __('Enter gap between columns in pixels.'),
					'min'   => 0
                ]
            ]
        );

        $general->addChildren(
            'max_width',
            'number',
            [
				'sortOrder'       => 40,
				'key'             => 'max_width',
				'templateOptions' => [
					'label' => __('Max Width(px)')
                ]
            ]
        );

    	return $general;
    }

    /**
     * @return array
     */
    public function getContentPosition()
    {
        return [
            [
				'label' => __('Top'),
				'value' => 'top'
            ],
            [
                'label' => __('Middle'),
                'value' => 'middle'
            ],
            [
                'label' => __('Bottom'),
                'value' => 'bottom'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getAppearanceTypes()
    {
        return [
            [
				'label' => __('Contained'),
				'value' => 'contained'
            ],
            [
                'label' => __('Full Width Row'),
                'value' => 'full_width_row'
            ],
            [
                'label' => __('Full Width Row and Content'),
                'value' => 'full_width_row_content'
            ],
            [
                'label' => __('Full Width Row and Content (no paddings)'),
                'value' => 'full_width_row_content_no_paddings'
            ]
        ];
    }
}