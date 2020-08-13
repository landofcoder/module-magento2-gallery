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
namespace Lof\Gallery\Block\Category;

class CategoryList extends \Magento\Framework\View\Element\Template
{
 protected $_coreRegistry = null;
 
    protected $_galleryHelper;
 
    protected $_banner;
    protected $_category;
    /**
     * @param \Magento\Framework\View\Element\Template\Context
 
     * @param array
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\Gallery\Helper\Data $galleryHelper,
        \Lof\Gallery\Model\Banner $banner,
        \Lof\Gallery\Model\Category $category,
        \Magento\Cms\Model\Block $blockModel,
        array $data = []
        ) {
        $this->_galleryHelper = $galleryHelper;
        $this->_coreRegistry = $registry;
        $this->_banner = $banner;
        $this->_category = $category;
        $this->_blockModel = $blockModel;
        parent::__construct($context, $data);
    }

    public function drawItems($collection, $cat, $level = 0)
    {
        foreach ($collection as $_cat) {
            if($_cat->getParentId() == $cat->getId()) {
                $children[] = $this->drawItems($collection, $_cat, ($level + 1));
                $cat->setChildren($children);
            }
        }

        $cat->setLevel($level);
        return $cat;

    }//end drawItems()
    
    public function _toHtml(){
        $itemPerPage = $this->getConfig('limit', 10);
        $store      = $this->_storeManager->getStore();
        $collection = $this->_category
        ->getCollection()
        ->addFieldToFilter("is_active", 1)
        ->addStoreFilter($store)
        ->setOrder("cat_position", "ASC");

        //$collection->getSelect()
        //->limit($itemPerPage);

        $cats       = [];
        foreach ($collection as $_cat) {
            if(!$_cat->getParentId()) {
                $cats[] = $this->drawItems($collection, $_cat);
            }
        }

        $this->setCollection($cats);
        return parent::_toHtml();
    }

    public function drawItem($collection, $html = '')
    {
        foreach ($collection as $_cat) {
            $children = $_cat->getChildren();
            $class    = '';
            if($children) { $class = "class='level".$_cat->getLevel()."' parent";
            }

            $linkClass = '';
            $category  = $this->_coreRegistry->registry('gallery_category');
            if ($category) {
                if ($category->getCategoryId() == $_cat->getCategoryId()) {
                    $linkClass = 'class="active"';
                }
            }

            $html .= '<li '.$class.' >';
            $html .= '<a href="'.$this->_galleryHelper->getCategoryUrl($_cat->getIdentifier()).'" '.$linkClass.'>';
            $html .= $_cat->getName();
            $html .= '</a>';
            if($children) {
                $html .= '<span class="opener"><i class="fa fa-plus-square-o"></i></span>';
                $html .= '<ul class="level'.$_cat->getLevel().' children">';
                $html .= $this->drawItem($children);
                $html .= '</ul>';
            }

            $html .= '</li>';
        }//end foreach

        return $html;

    }//end drawItem()


    public function getCategoryHtml()
    {
        $collection = $this->getCollection();
        $html       = $this->drawItem($collection, '');
        return $html;

    }//end getCategoryHtml()

    /**
     * @param AbstractCollection $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->_postCollection = $collection;
        return $this;
    }

    public function getCollection()
    {
        return $this->_postCollection;
    }

    public function getConfig($key, $default = '')
    {
        if($this->hasData($key)){
            return $this->getData($key);
        }
        $result = $this->_galleryHelper->getConfig($key);
        if($result!=NULL) return $result;
        return $default;
    }
    public function getBanner(){
        $banner = $this->_coreRegistry->registry('lofgallery_category');
        return $banner;
    }
}