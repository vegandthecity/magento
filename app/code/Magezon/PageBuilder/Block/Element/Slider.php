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

namespace Magezon\PageBuilder\Block\Element;

class Slider extends \Magezon\Builder\Block\Element
{
	public function getIframeSrc($slide)
	{
		$src = '';
		if ($slide['background_type'] == 'youtube' && $slide['youtube_id']) {
			$params['mute']                         = $slide['video_mute'] ? 1 : 0;
			$params['modestbranding']               = $slide['video_modest_branding'] ? 1 : 0;
			$params['rel']                          = $slide['video_related_videos'] ? 1 : 0;
			if ($slide['loop']) $params['playlist'] = $slide['youtube_id'];
			$src = 'https://www.youtube.com/embed/' . $slide['youtube_id'] . '?' . http_build_query($params);
		}
		if ($slide['background_type'] == 'vimeo' && $slide['vimeo_id']) {
			$src = 'https://player.vimeo.com/video/' . $slide['vimeo_id'];
		}
		return $src;
	}
}