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
 * 
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\Gallery\Block\Adminhtml\Category\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('category_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Information'));

        $this->addTab(
            'main_section',
            [
            'label' => __('Category Information'),
            'content' => $this->getLayout()->createBlock('Lof\Gallery\Block\Adminhtml\Category\Edit\Tab\Main')->toHtml()
            ]
            );
        $this->addTab(
            'meta_section',
            [
            'label' => __('SEO'),
            'content' => $this->getLayout()->createBlock('Lof\Gallery\Block\Adminhtml\Category\Edit\Tab\Meta')->toHtml()
            ]
            );
        $this->addTab(
            'banner',
            [
            'label' => __('Category banner'),
            'url' => $this->getUrl('lofgallery/category/banner', ['_current' => true]),
            'class' => 'ajax'
            ]
            );
    }

}
