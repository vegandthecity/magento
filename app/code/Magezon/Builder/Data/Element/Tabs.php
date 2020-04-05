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

class Tabs extends AbstractElement
{
    /**
     * Prepare modal components
     */
    public function prepareForm()
    {
    	parent::prepareForm();
    	$this->prepareTab();
    	return $this;
    }

    /**
     * @return Magezon\Builder\Data\Form\Element\Fieldset
     */
    public function prepareGeneralTab()
    {
    	$general = parent::prepareGeneralTab();

	    	$general->addChildren(
	            'title',
	            'text',
	            [
					'sortOrder'       => 10,
					'key'             => 'title',
					'templateOptions' => [
						'label' => __('Widget Title')
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
		            'title_align',
		            'select',
		            [
						'sortOrder'       => 10,
						'key'             => 'title_align',
						'defaultValue'    => 'center',
						'templateOptions' => [
							'label'   => __('Title Alignment'),
							'options' => $this->getAlignOptions()
		                ]
		            ]
		        );

		    	$container1->addChildren(
		            'title_tag',
		            'select',
		            [
						'sortOrder'       => 20,
						'key'             => 'title_tag',
						'defaultValue'    => 'h2',
						'templateOptions' => [
							'label'   => __('Title Tag'),
							'options' => $this->getHeadingType()
		                ]
		            ]
		        );

		    	$container1->addChildren(
		            'show_line',
		            'toggle',
		            [
						'sortOrder'       => 30,
						'key'             => 'show_line',
						'defaultValue'    => true,
						'templateOptions' => [
							'label' => __('Show Line')
		                ]
		            ]
		        );

	    	$general->addChildren(
	            'description',
	            'textarea',
	            [
					'sortOrder'       => 30,
					'key'             => 'description',
					'templateOptions' => [
						'label' => __('Widget Description')
	                ]
	            ]
	        );

	    	$general->addChildren(
	            'gap',
	            'text',
	            [
					'sortOrder'       => 40,
					'key'             => 'gap',
					'templateOptions' => [
						'label' => __('Gap')
	                ]
	            ]
	        );

	    	$general->addChildren(
	            'active_tab',
	            'text',
	            [
					'sortOrder'       => 50,
					'key'             => 'active_tab',
					'defaultValue'    => 1,
					'templateOptions' => [
						'label' => __('Active Tab'),
						'note'  => __('Enter active tab number. Leave empty or enter non-existing number to close all tabs on page load.')
	                ]
	            ]
	        );

	        $container2 = $general->addContainerGroup(
	            'container2',
	            [
					'sortOrder' => 60
	            ]
		    );

		    	$container2->addChildren(
		            'no_fill_content_area',
		            'toggle',
		            [
						'sortOrder'       => 10,
						'key'             => 'no_fill_content_area',
						'defaultValue'    => false,
						'templateOptions' => [
							'label' => __('Do not fill content area?')
		                ]
		            ]
		        );

		    	$container2->addChildren(
		            'hide_empty_tab',
		            'toggle',
		            [
						'sortOrder'       => 20,
						'key'             => 'hide_empty_tab',
						'templateOptions' => [
							'label' => __('Hide Empty Tab')
		                ]
		            ]
		        );

	        $container3 = $general->addContainerGroup(
	            'container3',
	            [
					'sortOrder' => 70
	            ]
		    );

		    	$container3->addChildren(
		            'mobile_accordion',
		            'toggle',
		            [
						'sortOrder'       => 10,
						'key'             => 'mobile_accordion',
						'templateOptions' => [
							'label' => __('Mobile Accordion')
		                ]
		            ]
		        );

		    	$container3->addChildren(
		            'hover_active',
		            'toggle',
		            [
						'sortOrder'       => 20,
						'key'             => 'hover_active',
						'templateOptions' => [
							'label' => __('Active Tab when Hover')
		                ]
		            ]
		        );

    	return $general;
    }

    /**
     * @return Magezon\Builder\Data\Form\Element\Fieldset
     */
    public function prepareTab()
    {
    	$tab = $this->addTab(
            'tab_item',
            [
                'sortOrder'       => 50,
                'templateOptions' => [
                    'label' => __('Tab Item')
                ]
            ]
        );

	        $container1 = $tab->addContainerGroup(
	            'container1',
	            [
					'sortOrder' => 10
	            ]
		    );

		    	$container1->addChildren(
		            'tab_align',
		            'select',
		            [
						'sortOrder'       => 10,
						'key'             => 'tab_align',
						'defaultValue'    => 'left',
						'templateOptions' => [
							'label'   => __('Alignment'),
							'options' => $this->getAlignOptions()
		                ]
		            ]
		        );

		    	$container1->addChildren(
		            'tab_position',
		            'select',
		            [
						'sortOrder'       => 20,
						'key'             => 'tab_position',
						'defaultValue'    => 'top',
						'templateOptions' => [
							'label'   => __('Position'),
							'options' => $this->getPositionOptions()
		                ]
		            ]
		        );

		    	$container1->addChildren(
		            'spacing',
		            'text',
		            [
						'sortOrder'       => 30,
						'key'             => 'spacing',
						'templateOptions' => [
							'label' => __('Spacing')
		                ]
		            ]
		        );

	        $border1 = $tab->addContainerGroup(
	            'border1',
	            [
					'sortOrder' => 20
	            ]
	        );

		    	$border1->addChildren(
		            'tab_border_width',
		            'text',
		            [
						'sortOrder'       => 10,
						'key'             => 'tab_border_width',
						'templateOptions' => [
							'label' => __('Border Width')
		                ]
		            ]
		        );

		    	$border1->addChildren(
		            'tab_border_radius',
		            'text',
		            [
						'sortOrder'       => 20,
						'key'             => 'tab_border_radius',
						'templateOptions' => [
							'label' => __('Border Radius')
		                ]
		            ]
		        );

                $border1->addChildren(
                    'tab_border_style',
                    'select',
                    [
						'key'             => 'tab_border_style',
						'sortOrder'       => 30,
						'templateOptions' => [
							'label'       => __('Border Style'),
							'options'     => $this->getBorderStyle(),
							'placeholder' => __('Theme defaults')
                        ]
                    ]
                );

        	$tab->addChildren(
	            'title_font_size',
                'text',
	            [
					'sortOrder'       => 30,
					'key'             => 'title_font_size',
					'templateOptions' => [
	                    'label' => __('Title Font Size')
	                ]
	            ]
	        );

        	$colors = $tab->addTab(
	            'colors',
	            [
	                'sortOrder'       => 40,
	                'templateOptions' => [
	                    'label' => __('Title Colors')
	                ]
	            ]
	        );

	        	$normal = $colors->addContainerGroup(
		            'normal',
		            [
						'sortOrder'       => 10,
						'templateOptions' => [
							'label' => __('Normal')
		                ]
		            ]
		        );

			        $color1 = $normal->addContainerGroup(
			            'color1',
			            [
							'sortOrder' => 10
			            ]
			        );

				    	$color1->addChildren(
				            'tab_color',
				            'color',
				            [
								'sortOrder'       => 10,
								'key'             => 'tab_color',
								'templateOptions' => [
									'label' => __('Text Color')
				                ]
				            ]
				        );

				    	$color1->addChildren(
				            'tab_background_color',
				            'color',
				            [
								'sortOrder'       => 20,
								'key'             => 'tab_background_color',
								'templateOptions' => [
									'label' => __('Background Color')
				                ]
				            ]
				        );

				    	$color1->addChildren(
				            'tab_border_color',
				            'color',
				            [
								'sortOrder'       => 30,
								'key'             => 'tab_border_color',
								'templateOptions' => [
									'label' => __('Border Color')
				                ]
				            ]
				        );

	        	$hover = $colors->addContainerGroup(
		            'hover',
		            [
						'sortOrder'       => 20,
						'templateOptions' => [
							'label' => __('Hover')
		                ]
		            ]
		        );

			        $color2 = $hover->addContainerGroup(
			            'color2',
			            [
							'sortOrder' => 10
			            ]
			        );

				    	$color2->addChildren(
				            'tab_hover_color',
				            'color',
				            [
								'sortOrder'       => 10,
								'key'             => 'tab_hover_color',
								'templateOptions' => [
									'label' => __('Text Color')
				                ]
				            ]
				        );

				    	$color2->addChildren(
				            'tab_hover_background_color',
				            'color',
				            [
								'sortOrder'       => 20,
								'key'             => 'tab_hover_background_color',
								'templateOptions' => [
									'label' => __('Background Color')
				                ]
				            ]
				        );

				    	$color2->addChildren(
				            'tab_hover_border_color',
				            'color',
				            [
								'sortOrder'       => 30,
								'key'             => 'tab_hover_border_color',
								'templateOptions' => [
									'label' => __('Border Color')
				                ]
				            ]
				        );

	        	$active = $colors->addContainerGroup(
		            'active',
		            [
						'sortOrder'       => 30,
						'templateOptions' => [
							'label' => __('Active')
		                ]
		            ]
		        );

			        $color3 = $active->addContainerGroup(
			            'color3',
			            [
							'sortOrder' => 10
			            ]
			        );

				    	$color3->addChildren(
				            'tab_active_color',
				            'color',
				            [
								'sortOrder'       => 10,
								'key'             => 'tab_active_color',
								'templateOptions' => [
									'label' => __('Text Color')
				                ]
				            ]
				        );

				    	$color3->addChildren(
				            'tab_active_background_color',
				            'color',
				            [
								'sortOrder'       => 20,
								'key'             => 'tab_active_background_color',
								'templateOptions' => [
									'label' => __('Background Color')
				                ]
				            ]
				        );

				    	$color3->addChildren(
				            'tab_active_border_color',
				            'color',
				            [
								'sortOrder'       => 30,
								'key'             => 'tab_active_border_color',
								'templateOptions' => [
									'label' => __('Border Color')
				                ]
				            ]
				        );

        	$contentColors = $tab->addContainerGroup(
	            'content_colors',
	            [
	                'sortOrder'       => 50,
	                'templateOptions' => [
	                    'label' => __('Content Colors')
	                ]
	            ]
	        );

		    	$contentColors->addChildren(
		            'tab_content_background_color',
		            'color',
		            [
						'sortOrder'       => 10,
						'key'             => 'tab_content_background_color',
						'templateOptions' => [
							'label' => __('Background Color')
		                ]
		            ]
		        );

        return $tab;
    }

    /**
     * @return array
     */
    public function getDefaultValues()
    {
        return [
            'margin_bottom' => '15px'
        ];
    }
}