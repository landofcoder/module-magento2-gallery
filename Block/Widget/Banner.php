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
namespace Lof\Gallery\Block\Widget;

class Banner extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
 
	protected $_galleryHelper;
 
	protected $_banner;
	/**
     * Catalog config
     *
     * @var \Magento\Catalog\Model\Config
     */
    protected $_catalogConfig;
	 /**
     * Catalog product visibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $catalogProductVisibility;

	/**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactor;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resource;

	/**
	 * @param \Magento\Framework\View\Element\Template\Context
 
	 * @param array
	 */
	public function __construct(

		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
		\Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
		\Lof\Gallery\Helper\Data $galleryHelper,
		\Lof\Gallery\Model\Banner $banner,
		\Magento\Cms\Model\Block $blockModel,
		\Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
		array $data = []
		) {
		$this->_galleryHelper = $galleryHelper;
		$this->_banner = $banner;
		$this->productCollectionFactory = $productCollectionFactory;
		$this->catalogProductVisibility = $catalogProductVisibility;
		$this->_blockModel = $blockModel;
		$this->_catalogConfig = $context->getCatalogConfig();
		parent::__construct($context, $data);
        $this->_resource = $resource;
	}
	public function getProductsRelated($productsIds) {
    	$collection = $this->productCollectionFactory->create();
            $collection->addAttributeToSelect('*');
            $collection->addAttributeToFilter('entity_id',array('in' => $productsIds));;  
        return $collection;
	}
	 /**
     * Add all attributes and apply pricing logic to products collection
     * to get correct values in different products lists.
     * E.g. crosssells, upsells, new products, recently viewed
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function _addProductAttributesAndPrices(
        \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
    ) {
        return $collection
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect($this->_catalogConfig->getProductAttributes())
            ->addUrlRewrite();
    }
	public function _toHtml(){

		$layoutMode = $this->getConfig('layout_mode');
		$product_related = '';
		if($layoutMode == 'owl'){
			$layout = 'Lof_Gallery::widget/gallery.phtml';
		}elseif ($layoutMode == 'masonry'){
			$layout = 'Lof_Gallery::widget/gallery_masonry.phtml';
		}elseif ($layoutMode == 'lookbook'){
			$layout = 'Lof_Gallery::widget/gallery_lookbook.phtml';
			$product_related = "['lgpr' => ".$this->getTable('lof_gallery_banner_product')."],'lg.banner_id = main_table.banner_id',";
		}else{	
			$layout = 'Lof_Gallery::widget/gallery_grid.phtml';	
		}
		$this->setTemplate($layout);
		$itemPerPage = $this->getConfig('limit', 20);
		$categories = $this->getConfig('categories');
		$categories = explode(",", $categories);


		$store = $this->_storeManager->getStore();
		$collection = $this->_banner
		->getCollection()
		->addFieldToFilter("is_active", 1)
		->setPagesize($itemPerPage);

		$collection->getSelect()
        ->joinLeft(
            [
            'lg' => $this->_resource->getTableName('lof_gallery')],
            'lg.banner_id = main_table.banner_id',
            [
            'banner_id' => 'banner_id',
            'position' => 'position'
            ]
            )
        ->where('lg.category_id in (?)', $categories)
        ->limit($itemPerPage)
        ->order('lg.position ASC')
        ->group('main_table.banner_id');
		$this->setCollection($collection);
		return parent::_toHtml();
	}
	 public function getBanner(){
        $banner = $this->_coreRegistry->registry('current_banner');
        return $banner;
    }

    /**
     * Return HTML block with price
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
	public function getLofProductPriceHtml(
		\Magento\Catalog\Model\Product $product,
		$priceType = null,
		$renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
		array $arguments = []
		) {
		if (!isset($arguments['zone'])) {
			$arguments['zone'] = $renderZone;
		}
		$arguments['price_id'] = isset($arguments['price_id'])
		? $arguments['price_id']
		: 'old-price-' . $product->getId() . '-' . $priceType;
		$arguments['include_container'] = isset($arguments['include_container'])
		? $arguments['include_container']
		: true;
		$arguments['display_minimal_price'] = isset($arguments['display_minimal_price'])
		? $arguments['display_minimal_price']
		: true;
		$priceRender = $this->getLayout()->getBlock('product.price.render.default');

		$price = '';
		if ($priceRender) {
			$price = $priceRender->render(
				\Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
				$product,
				$arguments
				);
		}
		return $price;
	}

	public function getShortDescription($description) {
		$content = substr($description,0,120);

		$content = explode(' ',$content);
		array_pop($content);
		$content = implode(" ",$content);
		$content = $content.' ...';
		return $content;
	}
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
		return $default;
	}
	 public function getTabs(){
        $tabs = $this->getConfig('tabs');
        if($tabs){
            if(base64_decode($tabs, true) == true){
                $tabs = str_replace(" ", "+", $tabs);
                $tabs = base64_decode($tabs);
                if(base64_decode($tabs, true) == true) {
                    $tabs = base64_decode($tabs);
                }
            }
            $tabs = unserialize($tabs);
            if(isset($tabs['__empty'])) unset($tabs['__empty']);
          	$loops= array();
          	$i=0;
          	foreach ($tabs as $key => $tab) {
          		$loops[$i] = $tab;
          		$i++;
          	}
            return $loops;
        }
        return false;
    }

    public function getCol($i) {
    	$lg_column_item     = (int)$this->getConfig('lg_column_item',1);
		$md_column_item     = (int)$this->getConfig('md_column_item',1);
		$sm_column_item     = (int)$this->getConfig('sm_column_item',1);
		$xs_column_item     = (int)$this->getConfig('xs_column_item',1);

		$lg_column          = 12/$lg_column_item;
		$md_column          = 12/$md_column_item;
		$sm_column          = 12/$sm_column_item;
		$xs_column          = 12/$xs_column_item;

    	$loop = $this->getTabs();
  		if(isset($loop[$i])) {
  			$loop[$i]["large_desktop"]?$lg_column = 12/$loop[$i]["large_desktop"]:1;
  			$loop[$i]["desktop"]?$md_column = 12/$loop[$i]["desktop"]:1;
  			$loop[$i]["tablet"]?$sm_column = 12/$loop[$i]["tablet"]:1;
  			$loop[$i]["mobile"]?$xs_column = 12/$loop[$i]["mobile"]:1;
    		$class = 'class="col-lg-'.$lg_column.' col-md-'.$md_column.' col-sm-'.$sm_column.' col-xs-'.$xs_column.' "';
    	} else {
    		$class = 'class="col-lg-'.$lg_column.' col-md-'.$md_column.' col-sm-'.$sm_column.' col-xs-'.$xs_column.' "';
    	}
    	return $class;
    }
}