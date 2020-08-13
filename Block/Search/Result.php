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
namespace Lof\Gallery\Block\Search;

class Result extends \Magento\Framework\View\Element\Template
{

	/**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
	protected $_coreRegistry = null;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $helper;
     protected $_bannerFactory;

    protected $_collection;
    protected $_request;
    protected $_bannerBlock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resource;

    /**
     * @param \Magento\Framework\View\Element\Template\Context
     * @param \Magento\Framework\Registry
 
     * @param array
     */
    public function __construct(
    	\Magento\Framework\View\Element\Template\Context $context,
    	\Magento\Framework\Registry $registry,
    	\Lof\Gallery\Model\Banner $bannerFactory,
    	\Lof\Gallery\Helper\Data $helper,
        \Magento\Framework\App\ResourceConnection $resource,
    	array $data = []
    	) {
    	$this->helper = $helper;
    	$this->_coreRegistry = $registry;
    	$this->_bannerFactory = $bannerFactory;
        $this->_request = $context->getRequest();
        parent::__construct($context, $data);
        $this->_resource = $resource;

    }

    public function getConfig($key, $default = '')
    {
        if($this->hasData($key)){
            return $this->getData($key);
        }
        $result = $this->helper->getConfig($key);
        $c = explode("/", $key);
        if($this->hasData($c[1])){
            return $this->getData($c[1]);
        }
        if($result == ""){
            $this->setData($c[1], $default);
            return $default;
        }
        $this->setData($c[1], $result);
        return $result;
    }

    public function _toHtml(){
        if(!$this->getConfig('general_settings/enable') || !$this->_request->getParam('s')){
            return;
        }
        return parent::_toHtml();
    }

 
    public function setCollection($collection)
    {
    	$this->_collection = $collection;
    	return $this->_collection;
    }

    public function getCollection(){
    	return $this->_collection;
    }

    /**
     * Prepare global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {	
        $searchKey = $this->_request->getParam('s');
        $page_title = 'Search resut for: "' . $searchKey . '"';
        $this->pageConfig->addBodyClass('blog-searchresult');
        if($page_title){
          $this->pageConfig->getTitle()->set($page_title);   
      }
      return parent::_prepareLayout();
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

    public function getBannerBlock()
    {
        $collection = $this->getCollection();
        $block = $this->_bannerBlock;
        $data = $this->getData();
        $block->setData($this->getData())->setCollection($collection);
        $html = $block->toHtml();
        if ($html) {
            return $html;
        }
    }

    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $show_toolbartop = $this->helper->getConfig("latest_page/show_toolbartop");
        $show_toolbarbottom = $this->helper->getConfig("latest_page/show_toolbartop");
        //$layout_type = $this->helper->getConfig("latest_page/layout_type");
        $this->setData('show_toolbartop', $show_toolbartop);
        $this->setData('show_toolbarbottom', $show_toolbarbottom);
        //$this->setData('layout_type', $layout_type);

        $bannerBlock = $this->getLayout()->getBlock('lofgallery.banners.list');
        $this->_bannerBlock = $bannerBlock;
        $data = $bannerBlock->getData();
        $this->addData($data);
        $limit = (int)$this->getConfig('latest_page/limit', 90);
        $itemsperpage = (int)$this->getConfig('latest_page/max_items',9);
        $store = $this->_storeManager->getStore();
        $searchKey = $this->_request->getParam('s');
        //$orderby = $this->getConfig('latest_page/orderby');
        $collection = $this->_bannerFactory->getCollection()
        ->addFieldToFilter('main_table.is_active',1)
        ->setPageSize($itemsperpage)
//        ->addStoreFilter($store)
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
        ->where('main_table.title LIKE "%' . addslashes($searchKey) . '%"')
        ->limit($limit)
        ->group('main_table.banner_id');
        $this->setCollection($collection);   
        //->order("creation_time" . $orderby);
        $toolbar = $this->getToolbarBlock();

        // set collection to toolbar and apply sort
        if($toolbar){
            $toolbar->setData('_current_limit',$itemsperpage)->setCollection($collection);
            $this->setChild('toolbar', $toolbar);
        }
        return parent::_beforeToHtml();
    }
}