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
namespace Lof\Gallery\Block\Product;

/**
 * Product view other block
 */
class View extends \Magento\Framework\View\Element\Template
{   
    /**
     * [$_bannerFactory description]
     * @var [type]
     */
    protected $_bannerFactory;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resource;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\Gallery\Helper\Data $helper,
        \Lof\Gallery\Model\Banner $bannerFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
        ) {
        $this->_helper   = $helper;
        $this->_bannerFactory = $bannerFactory;
        $this->_registry = $registry;
        parent::__construct($context, $data);
        $this->setTabTitle();
        $this->_resource = $resource;
    }

    /**
     * Get product object
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->_registry->registry('product');
    }
    public function getDataProduct(){
        $itemPerPage = $this->getConfig('general_settings/limit',10);
        $product = $this->getProduct()->getId();

        $store = $this->_storeManager->getStore();
        $collection = $this->_bannerFactory
        ->getCollection()
        ->addFieldToFilter("is_active", 1)
        ->setPagesize($itemPerPage);

        $collection->getSelect()
        ->joinLeft(
                [
                    'lg' => $this->_resource->getTableName('lof_gallery_banner_product')],
                    'lg.banner_id = main_table.banner_id',
                [
                    'banner_id' => 'banner_id',
                    'position' => 'position'
                ]
        )
        ->where('lg.entity_id = (?)', $product)
        ->order('lg.position ASC')
        ->limit($itemPerPage)
        ->group('main_table.banner_id');


        $this->setCollection($collection);
        return $this;
    }

    /**
     * @param AbstractCollection $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->_bannerCollection = $collection;
        return $this;
    }

    public function getCollection()
    {
        return $this->_bannerCollection;
    }
    public function getConfig($key, $default = '')
    {
        if($this->hasData($key)){
            return $this->getData($key);
        }
        $result = $this->_helper->getConfig($key);
        if($result!=NULL) return $result;
        return $default;
    }
}
