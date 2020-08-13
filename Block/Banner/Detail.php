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
 namespace Lof\Gallery\Block\Banner;
 use Magento\Framework\App\Filesystem\DirectoryList;

 class Detail extends \Magento\Catalog\Block\Product\AbstractProduct implements \Magento\Widget\Block\BlockInterface
 {
    protected $_collection;
    protected $urlHelper;
    protected $_resource;
    protected $_galleryHelper;
    protected $_banner;
    protected $_category;
    protected $_productCollectionFactory;
    /**
     * @param \Magento\Framework\View\Element\Template\Context 
     * @param array
     */
    public function __construct(
        //\Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Lof\Gallery\Helper\Data $galleryHelper,
        \Lof\Gallery\Model\Banner $banner,
        \Lof\Gallery\Model\Category $category,
        \Magento\Cms\Model\Block $blockModel,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
        ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->urlHelper = $urlHelper;
        $this->_galleryHelper = $galleryHelper;
        $this->_banner = $banner;
        $this->_category = $category;
        $this->_blockModel = $blockModel;
        $this->_resource = $resource;
        parent::__construct($context, $data);
    }
        /**
     * Retrieve Toolbar block
     *
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
       /* public function getToolbarBlock()
        {
            $block = $this->getLayout()->getBlock('lofgallery_toolbar');
            if ($block) {
                return $block;
            }
        }*/
        /**
     * @param AbstractCollection $collection
     * @return $this
     */
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
        protected function _addBreadcrumbs()
        {
            $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
            $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
            $banner = $this->getBanner();
            $identifier_category = $this->getRequest()->getParam('key');
            $category = $this->_category->getCollection()
            ->addFieldToFilter('identifier',$identifier_category )
            ->addFieldToFilter('is_active', 1)
            ->getFirstItem();
            $show_breadcrumbs = $this->getConfig('latest_page/show_breadcrumbs');

            if($show_breadcrumbs && $breadcrumbsBlock){
                $breadcrumbsBlock->addCrumb(
                    'home',
                    [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link'  => $baseUrl
                    ]
                    );
                if(count($category->getData()) > 0){
                    $breadcrumbsBlock->addCrumb(
                        'category',
                        [
                        'label' => __($category['name']),
                        'title' => __($category['name']),
                        'link'  => $this->_galleryHelper->getCategoryUrl($category['identifier'])
                        ]
                        ); 
                }else{
                    $breadcrumbsBlock->addCrumb(
                        'latest',
                        [
                        'label' => __('Gallery'),
                        'title' => __('Gallery'),
                        'link'  => $this->_galleryHelper->getLatestPageUrl()
                        ]
                        );
                }
                $breadcrumbsBlock->addCrumb(
                    'lofgallery',
                    [
                    'label' => $banner->getTitle(),
                    'title' => $banner->getTitle(),
                    'link'  => ''
                    ]
                    );
            }
        }
        protected function _prepareLayout()
        {   
            $this->_addBreadcrumbs();
            $banner = $this->getBanner();

            $page_title = $banner->getPageTitle();
            $banner_title = $banner->getTitle();
            if(!$page_title) {
                $page_title = $banner->getTitle();
            }
            $meta_description = $banner->getMetaDescription();
            $meta_keywords = $banner->getMetaKeywords();
            $this->pageConfig->addBodyClass('gallery-banner-' . $banner->getIdentifier());
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
        public function _beforeToHtml(){


            $banner = $this->getBanner();
            $banners = $banner->getBannerId();
            $collection = $this->_banner
            ->getCollection()
            ->setCurPage(1);

            $collection->getSelect()
            ->where('banner_id = ?',$banners);
            $this->setCollection($collection);
            return parent::_beforeToHtml();
        }

        public function getBanner(){
            $banner = $this->_coreRegistry->registry('lofgallery_banner');
            return $banner;
        }
        public function getDataProduct()
        {

            $directory = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);

            $banner = $this->getBanner();
            $banner_id = $banner->getBannerId();
            $connection = $this->_resource->getConnection();
            $table = $this->_resource->getTableName('lof_gallery_banner_product');

            $select = 'SELECT entity_id as id FROM ' . $table.' where banner_id = '.$banner_id.'';
            $entity = $connection->fetchAll($select);

            $collection = $this->_productCollectionFactory->create();
            $collection->addAttributeToSelect('*');
            $collection->addAttributeToFilter('entity_id',array('in' => $entity));
            return $collection;
        }

        public function getAddToCartUrl($product, $additional = [])
        {
            if ($product->getTypeInstance()->hasRequiredOptions($product)) {
                if (!isset($additional['_escape'])) {
                    $additional['_escape'] = true;
                }
                if (!isset($additional['_query'])) {
                    $additional['_query'] = [];
                }
                $additional['_query']['options'] = 'cart';

                return $this->getProductUrl($product, $additional);
            }
            return $this->_cartHelper->getAddUrl($product, $additional);
        }
        public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
        {
            $url = $this->getAddToCartUrl($product);
            return [
            'action' => $url,
            'data' => [
            'product' => $product->getEntityId(),
            \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED =>
            $this->urlHelper->getEncodedUrl($url),
            ]
            ];
        }


    }