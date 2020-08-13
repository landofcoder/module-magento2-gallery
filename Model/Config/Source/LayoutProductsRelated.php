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

class LayoutProductsRelated implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $options = [];
        $options[] = [
            'label' => 'Carousel',
            'value' => 'owl'
        ];
        $options[] = [
            'label' => 'List',
            'value' => 'list'
        ];
        return $options;
    }
}