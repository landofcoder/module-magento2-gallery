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

class SliderAlignment implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'topLeft', 'label' => __('topLeft')],
            ['value' => 'topCenter', 'label' => __('topCenter')],
            ['value' => 'topRight', 'label' => __('topRight')],
            ['value' => 'centerLeft', 'label' => __('centerLeft')],
            ['value' => 'center', 'label' => __('center')],
            ['value' => 'centerRight', 'label' => __('centerRight')], 
            ['value' => 'bottomLeft', 'label' => __('bottomLeft')],
            ['value' => 'bottomCenter', 'label' => __('bottomCenter')],
            ['value' => 'bottomRight', 'label' => __('bottomRight')] 
            ];
    }
}
