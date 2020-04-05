<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://magezon.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_LazyLoad
 * @copyright Copyright (C) 2018 Magezon (https://magezon.com)
 */

namespace Magezon\LazyLoad\Block\Product;

use Magento\Catalog\Model\Product\Image\NotLoadInfoImageException;
use Magento\Framework\App\ObjectManager;
use Magento\Catalog\Model\Product;

class ImageBuilder extends \Magento\Catalog\Block\Product\ImageBuilder
{
    /**
     * Create image block
     *
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function create(Product $product = null, string $imageId = null, array $attributes = null)
    {
        $objectManager   = \Magento\Framework\App\ObjectManager::getInstance();
        $coreImageHelper = $objectManager->get('Magezon\Core\Helper\Image');
        $product         = $product ? $product : $this->product;
        $imageId         = $imageId ? $imageId : $this->imageId;
        $attributes      = $attributes ? $attributes : $this->attributes;

        $dataHelper = ObjectManager::getInstance()->get(\Magezon\LazyLoad\Helper\Data::class);
        if (!$dataHelper->isEnable() || !$dataHelper->getConfig('general/lazy_load_images') || $imageId === 'product_base_image') {
            return parent::create($product, $imageId, $attributes);
        }

        /** @var \Magento\Catalog\Helper\Image $helper */
        $helper                 = $this->helperFactory->create()->init($product, $imageId);
        $attributes['data-src'] = $helper->getUrl();
        $this->setAttributes($attributes);
        

        $template = $helper->getFrame()
            ? 'Magezon_LazyLoad::product/image.phtml'
            : 'Magezon_LazyLoad::product/image_with_borders.phtml';

        try {
            $imagesize = $helper->getResizedImageInfo();
        } catch (NotLoadInfoImageException $exception) {
            $imagesize = [$helper->getWidth(), $helper->getHeight()];
        }

        $placeHolderUrl = $dataHelper->getPlaceHolderUrl();

        if ($dataHelper->getConfig('general/preview')) {
            $width  = $helper->getWidth();
            $height = $helper->getHeight() - 1;
            $helper2 = ObjectManager::getInstance()->create(\Magezon\LazyLoad\Helper\Image::class);
            $attrs  = ['width' => $width, 'height' => $height];
            $helper2 = $helper2->init($product, $imageId, $attrs)->setQuality(5);
            $helper2->resize($width, $height);
            $placeHolderUrl = $helper2->getUrl();
        }

        $data = [
            'template'             => $template,
            'image_url'            => $placeHolderUrl,
            'width'                => $helper->getWidth(),
            'height'               => $helper->getHeight(),
            'label'                => $helper->getLabel(),
            'ratio'                => $this->getRatio($helper),
            'custom_attributes'    => $this->getCustomAttributes(),
            'resized_image_width'  => $imagesize[0],
            'resized_image_height' => $imagesize[1],
            'product_id'           => $product->getId()
        ];
        
        $productMetadata = $objectManager->get('Magento\Framework\App\ProductMetadataInterface');

        if ($productMetadata->getVersion() < '2.3.0') {
            return $this->imageFactory->create(['data' => $data]);
        } else {
            $helper = $this->imageFactory->create($product, $imageId, $attributes);
            $helper->setData($data);
            return $helper;
        }
    }
}