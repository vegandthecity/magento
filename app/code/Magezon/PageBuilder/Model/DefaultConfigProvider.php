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
 * @package   Magezon_PageBuilder
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\PageBuilder\Model;

class DefaultConfigProvider extends \Magezon\Builder\Model\DefaultConfigProvider
{
	/**
	 * @var \Magezon\Builder\Data\ModalProfileFactory
	 */
	protected $profileFactory;

	/**
	 * @param \Magezon\Builder\Model\CacheManager        $builderCacheManager 
	 * @param \Magezon\Builder\Data\Groups               $builderGroups       
	 * @param \Magezon\Builder\Data\Elements             $builderElements     
	 * @param \Magezon\Builder\Helper\Data               $builderHelper       
	 * @param \Magezon\Builder\Data\Modal\ProfileFactory $profileFactory      
	 */
	public function __construct(
        \Magezon\Builder\Model\CacheManager $builderCacheManager,
        \Magezon\Builder\Data\Groups $builderGroups,
		\Magezon\Builder\Data\Elements $builderElements,
        \Magezon\Builder\Helper\Data $builderHelper,
        \Magezon\Builder\Data\Modal\ProfileFactory $profileFactory
	) {
		parent::__construct($builderCacheManager, $builderGroups, $builderElements, $builderHelper);
		$this->profileFactory = $profileFactory;
	}

	/**
	 * @return array
	 */
	public function getConfig()
	{
		$config                 = parent::getConfig();
		$profile                = $this->profileFactory->create();
		$config['builderClass'] = 'Magezon\PageBuilder\Block\Builder';
		$config['timezone']['class'] = '\Magezon\PageBuilder\Model\Source\Timezone';
		$config['profile'] = [
			'class'         => '\Magezon\PageBuilder\Block\Profile',
			'key'           => 'mgz_pagebuilder',
			'home'          => 'https://www.magezon.com/magezon-page-builder.html?utm_campaign=mgzbuilder&utm_source=mgz_user&utm_medium=backend',
			'defaultSettings' => $profile->prepareForm()->getFormDefaultValues(),
			'settings'        => [
				'class' => 'Magezon\Builder\Data\Modal\Profile'
			],
			'templates' => [
				'class' => 'Magezon\PageBuilder\Data\Modal\Templates'
			],
			'switchMode' => true
		];
		return $config;
	}
}