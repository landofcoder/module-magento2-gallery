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

class EasingType implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $options = [];
        $options[] = [
            'label' => 'linear',
            'value' => 'linear'
        ];
        $options[] = [
            'label' => 'swing',
            'value' => 'swing'
        ];
          $options[] = [
            'label' => 'jswing',
            'value' => 'jswing'
        ];
        $options[] = [
            'label' => 'easeInQuad',
            'value' => 'easeInQuad'
        ];
          $options[] = [
            'label' => 'easeInCubic',
            'value' => 'easeInCubic'
        ];
        $options[] = [
            'label' => 'easeInQuart',
            'value' => 'easeInQuart'
        ];
          $options[] = [
            'label' => 'easeInQuint',
            'value' => 'easeInQuint'
        ];
        $options[] = [
            'label' => 'easeInSine',
            'value' => 'easeInSine'
        ];
          $options[] = [
            'label' => 'easeInExpo',
            'value' => 'easeInExpo'
        ];
        $options[] = [
            'label' => 'easeInCirc',
            'value' => 'easeInCirc'
        ];
          $options[] = [
            'label' => 'easeInElastic',
            'value' => 'easeInElastic'
        ];
        $options[] = [
            'label' => 'easeInBack',
            'value' => 'easeInBack'
        ];
          $options[] = [
            'label' => 'easeInBounce',
            'value' => 'easeInBounce'
        ];
        $options[] = [
            'label' => 'easeOutQuad',
            'value' => 'easeOutQuad'
        ];
          $options[] = [
            'label' => 'easeOutCubic',
            'value' => 'easeOutCubic'
        ];
        $options[] = [
            'label' => 'easeOutQuart',
            'value' => 'easeOutQuart'
        ];
          $options[] = [
            'label' => 'easeOutQuint',
            'value' => 'easeOutQuint'
        ];
        $options[] = [
            'label' => 'easeOutSine',
            'value' => 'easeOutSine'
        ];
          $options[] = [
            'label' => 'easeOutExpo',
            'value' => 'easeOutExpo'
        ];
        $options[] = [
            'label' => 'easeOutCirc',
            'value' => 'easeOutCirc'
        ];
          $options[] = [
            'label' => 'easeOutElastic',
            'value' => 'easeOutElastic'
        ];
        $options[] = [
            'label' => 'easeOutBack',
            'value' => 'easeOutBack'
        ];
          $options[] = [
            'label' => 'easeOutBounce',
            'value' => 'easeOutBounce'
        ];
        $options[] = [
            'label' => 'easeInOutQuad',
            'value' => 'easeInOutQuad'
        ];
          $options[] = [
            'label' => 'easeInOutCubic',
            'value' => 'easeInOutCubic'
        ];
        $options[] = [
            'label' => 'easeInOutQuart',
            'value' => 'easeInOutQuart'
        ];
          $options[] = [
            'label' => 'easeInOutQuint',
            'value' => 'easeInOutQuint'
        ];
        $options[] = [
            'label' => 'easeInOutSine',
            'value' => 'easeInOutSine'
        ];
         $options[] = [
            'label' => 'easeInOutExpo',
            'value' => 'easeInOutExpo'
        ];
          $options[] = [
            'label' => 'easeInOutCirc',
            'value' => 'easeInOutCirc'
        ];
        $options[] = [
            'label' => 'easeInOutElastic',
            'value' => 'easeInOutElastic'
        ];
          $options[] = [
            'label' => 'easeInOutBack',
            'value' => 'easeInOutBack'
        ];
        $options[] = [
            'label' => 'easeInOutBounce',
            'value' => 'easeInOutBounce'
        ];
          
        return $options;
    }
}