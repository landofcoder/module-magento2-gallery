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

class BannerGrid extends \Magento\Framework\View\Element\Template
{
 protected $_coreRegistry = null;
 
    protected $_galleryHelper;
 
    protected $_banner;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resource;

    /**
     * @param \Magento\Framework\View\Element\Template\Context
 
     * @param array
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\Gallery\Helper\Data $galleryHelper,
        \Lof\Gallery\Model\Banner $banner,
        \Magento\Cms\Model\Block $blockModel,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
        ) {
        $this->_galleryHelper = $galleryHelper;
        $this->_coreRegistry = $registry;
        $this->_banner = $banner;
        $this->_blockModel = $blockModel;
        parent::__construct($context, $data);
        $this->_resource = $resource;


    }
        /**
     * Retrieve Toolbar block
     *
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function getToolbarBlock()
    {
        $block = $this->getLayout()->getBlock('lofgallery_toolbar');
        if ($block) {
            return $block;
        }
    }
    protected function _prepareLayout()
    {   
        $identify = $this->getConfig('general_settings/url_prefix');
        $page_title = $this->getConfig('latest_page/page_title');
        $meta_description = $this->getConfig('latest_page/meta_description');
        $meta_keywords = $this->getConfig('latest_page/meta_keywords');

        $this->_addBreadcrumbs();
            $this->pageConfig->addBodyClass('gallery-cat-' . $identify);
            if($page_title){
                $this->pageConfig->getTitle()->set($page_title);   
            }
        if($meta_keywords){
            $this->pageConfig->setKeywords($meta_keywords);   
        }
        if($meta_description){
            $this->pageConfig->setDescription($meta_description);   
        }
        return parent::_prepareLayout();
    }
 
    public function setCollection($collection)
    {
        $this->_collection = $collection;
        return $this;
    }

    public function getCollection()
    {
        return $this->_collection;
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
        $banner = $this->_coreRegistry->registry('lofgallery_banner');
        return $banner;
    }
        /**
     * Prepare breadcrumbs
     *
     * @param \Magento\Cms\Model\Page $brand
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function _addBreadcrumbs()
    {
        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $brandRoute = $this->getConfig('general_settings/route');
        $show_breadcrumbs = $this->getConfig('latest_page/show_breadcrumbs');

        if($show_breadcrumbs && $breadcrumbsBlock){
            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $baseUrl
                ]
             );

            $breadcrumbsBlock->addCrumb(
                'lofgallery',
                [
                    'label' => 'Gallery',
                    'title' => 'Gallery',
                    'link' => ''
                ]
             );
        }
    }
    public function _beforeToHtml(){
        $limit = $this->getConfig('latest_page/limit', 90);
        $itemPerpage = $this->getConfig('latest_page/max_items',9);
        $collection = $this->_banner
        ->getCollection()
        ->addFieldToFilter("main_table.is_active", 1)
        ->setPagesize($itemPerpage)
        ->setCurPage(1);

        $collection->getSelect()
        ->joinLeft(
            [
            'lg' => $this->_resource->getTableName('lof_gallery')],
            'lg.banner_id = main_table.banner_id',
            [
            'id' => 'category_id',
            'position' => 'position'
            ]
            )
        ->joinLeft(
            [
            'lgbc' => $this->_resource->getTableName('lof_gallery_banner_category')],
            'lgbc.category_id = lg.category_id',
            [
            'category_idenfify' => 'identifier'         
            ]    
            )
        ->limit($limit)
        ->group('main_table.banner_id');
        $this->setCollection($collection);
        $toolbar = $this->getToolbarBlock();

        // set collection to toolbar and apply sort
        if($toolbar){
            $toolbar->setData('_current_limit',$itemPerpage)->setCollection($collection);
            $this->setChild('toolbar', $toolbar);
        }
         return parent::_beforeToHtml();
    }


}