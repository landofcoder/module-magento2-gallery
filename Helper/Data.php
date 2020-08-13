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
namespace Lof\Gallery\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Banner Collection
     */
    protected $_bannerCollection;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $_storeManager;

    /**
     * Brand config node per website
     *
     * @var array
     */
    protected $_config = [];

    /**
     * Template filter factory
     *
     * @var \Magento\Catalog\Model\Template\Filter\Factory
     */
    protected $_templateFilterFactory;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

	/**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager 
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Lof\Gallery\Model\Category $bannerCollection,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider
        ) {
        parent::__construct($context);
        $this->_filterProvider = $filterProvider;
        $this->_storeManager = $storeManager;
        $this->_bannerCollection = $bannerCollection;
    }

    public function getGroupList(){
        $result = array();
        $collection = $this->_bannerCollection->getCollection()
        ->addFieldToFilter('name', array('neq' => ''));
        foreach ($collection as $category) {
            $result[$category->getId()] = $category->getName();
        }
        return $result;
    }
    
    public function filter($str)
    {
        $html = $this->_filterProvider->getPageFilter()->filter($str);
        return $html;
    }
    /**
     * Retrieve product view page url
     *
     * @param int|ModelProduct $product
     * @return string|bool
     */
    public function getProductUrl($product)
    {
        if ($product instanceof ModelProduct) {
            return $product->getProductUrl();
        } elseif (is_numeric($product)) {
            return $this->productRepository->getById($product)->getProductUrl();
        }
        return false;
    }
    /**
     * Return brand config value by key and store
     *
     * @param string $key
     * @param \Magento\Store\Model\Store|int|string $store
     * @return string|null
     */
    public function getConfig($key, $store = null)
    {
        $store = $this->_storeManager->getStore($store);
        $websiteId = $store->getWebsiteId();

        $result = $this->scopeConfig->getValue(
            'lofgallery/'.$key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store);
        return $result;
    }
    public function getCategoryUrl($alias)
    {
        $url = $this->_storeManager->getStore()->getBaseUrl();
        $url_prefix = $this->getConfig('general_settings/url_prefix');
        $urlPrefix = '';
        if($url_prefix){
            $urlPrefix = $url_prefix.'/';
        }
        return $url . $urlPrefix . $alias;
    }
    public function getBannerUrl($alias)
    {
        $url = $this->_storeManager->getStore()->getBaseUrl();
        $url_prefix = $this->getConfig('general_settings/url_prefix');
        $urlPrefix = '';
        if($url_prefix){
            $urlPrefix = $url_prefix.'/';
        }
        return $url . $urlPrefix . $alias;
    }


    public function getAddToCartUrl(\Magento\Catalog\Model\Product $_product){
        $url = $this->_storeManager
        ->getStore()
        ->getUrl('productlist/index/quickview',["id"=>$_product->getId()]);
        return $url;
    }
    public function getSearchFormUrl(){
        $url = $this->_storeManager->getStore()->getBaseUrl();
        $url_prefix = $this->getConfig('general_settings/url_prefix');
        $urlPrefix = '';
        if($url_prefix){
            $urlPrefix = $url_prefix.'/';
        }
        return $url . $urlPrefix . 'search';
    }
    public function getSearchKey(){
        return $this->_request->getParam('s');
    }
    public function checkProductIsNew($_product = null) {
        $from_date = $_product->getNewsFromDate();
        $to_date = $_product->getNewsToDate();
        $is_new = false;
        $is_new = $this->isNewProduct($from_date, $to_date);
        $today = strtotime("now");

        if ($from_date && $to_date) {
            $from_date = strtotime($from_date);
            $to_date = strtotime($to_date);
            if ($from_date <= $today && $to_date >= $today) {
                $is_new = true;
            }
        }
        elseif ($from_date && !$to_date) {
            $from_date = strtotime($from_date);
            if ($from_date <= $today) {
                $is_new = true;
            }
        }elseif (!$from_date && $to_date) {
            $to_date = strtotime($to_date);
            if ($to_date >= $today) {
                $is_new = true;
            }
        }
        return $is_new;
    }
    public function isNewProduct( $created_date, $num_days_new = 3) {
        $check = false;

        $startTimeStamp = strtotime($created_date);
        $endTimeStamp = strtotime("now");

        $timeDiff = abs($endTimeStamp - $startTimeStamp);
        $numberDays = $timeDiff/86400;// 86400 seconds in one day

        // and you might want to convert to integer
        $numberDays = intval($numberDays);
        if($numberDays <= $num_days_new) {
            $check = true;
        }

        return $check;
    }

    public function getLatestPageUrl(){
        $url = $this->_storeManager->getStore()->getBaseUrl();
        $url_prefix = $this->getConfig('general_settings/url_prefix');
        $latestPageUrl = $this->getConfig('latest_page/route');
        $urlPrefix = '';
        if($url_prefix){
            $urlPrefix = $url_prefix.'/';
        }
        return $url . $urlPrefix . $latestPageUrl ;
    }
        public function getAllStores() {
        $allStores = $this->_storeManager->getStores();
        $stores = array();
        foreach ($allStores as $_eachStoreId => $val)
        {
            $stores[]  = $this->_storeManager->getStore($_eachStoreId)->getId();
        }
        return $stores;
    }

}