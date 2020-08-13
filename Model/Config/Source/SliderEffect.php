<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_Gallery
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\Gallery\Model\Config\Source;

class SliderEffect implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'random', 'label' => __('random')],
            ['value' => 'simpleFade', 'label' => __('simpleFade')],
            ['value' => 'curtainTopLeft', 'label' => __('curtainTopLeft')],
            ['value' => 'curtainTopRight', 'label' => __('curtainTopRight')],
            ['value' => 'curtainBottomLeft', 'label' => __('curtainBottomLeft')],
            ['value' => 'curtainBottomRight', 'label' => __('curtainBottomRight')],
            ['value' => 'curtainSliceLeft', 'label' => __('curtainSliceLeft')],
            ['value' => 'curtainSliceRight', 'label' => __('curtainSliceRight')],
            ['value' => 'blindCurtainTopLeft', 'label' => __('blindCurtainTopLeft')],
            ['value' => 'blindCurtainTopRight', 'label' => __('blindCurtainTopRight')],
            ['value' => 'blindCurtainBottomLeft', 'label' => __('blindCurtainBottomLeft')],
            ['value' => 'blindCurtainBottomRight', 'label' => __('blindCurtainBottomRight')],
            ['value' => 'blindCurtainSliceBottom', 'label' => __('blindCurtainSliceBottom')],
            ['value' => 'stampede', 'label' => __('stampede')],
            ['value' => 'blindCurtainSliceTop', 'label' => __('blindCurtainSliceTop')],
            ['value' => 'mosaic', 'label' => __('mosaic')],
            ['value' => 'mosaicReverse', 'label' => __('mosaicReverse')],
            ['value' => 'mosaicRandom', 'label' => __('mosaicRandom')],
            ['value' => 'mosaicSpiral', 'label' => __('mosaicSpiral')],
            ['value' => 'mosaicSpiralReverse', 'label' => __('mosaicSpiralReverse')],
            ['value' => 'simpltopLeftBottomRighteFade', 'label' => __('topLeftBottomRight')],
            ['value' => 'bottomRightTopLeft', 'label' => __('bottomRightTopLeft')],
            ['value' => 'bottomLeftTopRight', 'label' => __('bottomLeftTopRight')],
            ['value' => 'bottomLeftTopRight', 'label' => __('bottomLeftTopRight')],
            ['value' => 'scrollRight', 'label' => __('scrollRight')],
            ['value' => 'scrollHorz', 'label' => __('scrollHorz')],
            ['value' => 'scrollBottom', 'label' => __('scrollBottom')],
            ['value' => 'scrollTop', 'label' => __('scrollTop')]
            ];
    }
}
