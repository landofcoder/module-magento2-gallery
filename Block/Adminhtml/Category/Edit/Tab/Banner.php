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
namespace Lof\Gallery\Block\Adminhtml\Category\Edit\Tab;

class Banner extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Catalog\Model\Product\LinkFactory
     */
    protected $_linkFactory;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory]
     */
    protected $_setsFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Type
     */
    protected $_type;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $_status;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_visibility;

    protected $_bannerFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context
     * @param \Magento\Backend\Helper\Data
     * @param \Magento\Catalog\Model\Product\LinkFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory
     * @param \Magento\Catalog\Model\ProductFactory
     * @param \Magento\Catalog\Model\Product\Type
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status
     * @param \Magento\Catalog\Model\Product\Visibility
     * @param \Magento\Framework\Registry
     * @param \Lof\Gallery\Model\Banner
     * @param array
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\Product\LinkFactory $linkFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Product\Type $type,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $status,
        \Magento\Catalog\Model\Product\Visibility $visibility,
        \Magento\Framework\Registry $coreRegistry,
        \Lof\Gallery\Model\Banner $bannerFactory,
        array $data = []
        ) {
        $this->_linkFactory = $linkFactory;
        $this->_setsFactory = $setsFactory;
        $this->_productFactory = $productFactory;
        $this->_type = $type;
        $this->_status = $status;
        $this->_visibility = $visibility;
        $this->_coreRegistry = $coreRegistry;
        $this->_bannerFactory = $bannerFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Set grid params
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('banner_content_grid');
        $this->setDefaultSort('banner_id');
        $this->setUseAjax(true);
        if ($this->getCategory() && $this->getCategory()->getCategoryId()) {
            $this->setDefaultFilter(['in_products' => 1]);
        }
        if ($this->isReadonly()) {
            $this->setFilterVisibility(false);
        }
    }

    protected function _prepareCollection()
    {
        $collection = $this->_bannerFactory->getCollection();


        if ($this->isReadonly()) {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = [0];
            }
            $collection->addFieldToFilter('main_table.banner_id', ['in' => $productIds]);
        }
        $category = $this->getCategory();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }




    /**
     * Add columns to grid
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {

        $this->addColumn(
         'in_products',
         [
         'type' => 'checkbox',
         'name' => 'in_products',
         'values' => $this->_getSelectedProducts(),
         'align' => 'center',
         'index' => 'banner_id',
         'header_css_class' => 'col-select',
         'column_css_class' => 'col-select'
         ]
         );

        $this->addColumn(
            'gbanner_id',
            [
            'header' => __('ID'),
            'sortable' => true,
            'index' => 'banner_id',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id'
            ]
            );
        $this->addColumn(
            'gtitle',
            [
            'header' => __('Title'),
            'index' => 'title',
            'header_css_class' => 'col-name',
            'column_css_class' => 'col-name'
            ]
            );

        $this->addColumn(
            'gimage',
            [
            'header'   => __('Image'),
            'type'     => 'action',
            'renderer' => 'Lof\Gallery\Block\Adminhtml\Banner\Renderer\ImageAction'
            ]
            );
        $this->addColumn(
            'gimage_url',
            [
            'header'   => __('Image Url'),
            'type'     => 'action',
            'renderer' => 'Lof\Gallery\Block\Adminhtml\Banner\Renderer\ImageUrlAction'
            ]
            );

        $this->addColumn(
            'gis_active',
            [
            'header' => __('Active'),
            'index' => 'is_active',
            'type' => 'options',
            'options' => $this->_status->getOptionArray(),
            'header_css_class' => 'col-status',
            'column_css_class' => 'col-status'
            ]
            );
        $this->addColumn(
            'position',
            [
            'header'                    => __('Position'),
            'name'                      => 'position',
            'type'                      => 'number',
            'validate_class'            => 'validate-number',
            'index'                     => 'position',
            'header_css_class'          => 'col-position',
            'column_css_class'          => 'col-position',
            'editable'                  => true,
            'edit_only'                 => true,
            'sortable'                  => false,
            'filter_condition_callback' => [$this, 'filterProductPosition']
            ]
            );

        $this->addColumn(
            'gaction',
            [
            'header'   => __('Action'),
            'type'     => 'action',
            'renderer' => 'Lof\Gallery\Block\Adminhtml\Banner\Renderer\BannerAction'
            ]
            );

        

        return parent::_prepareColumns();
    }



    /**
     * Retirve currently edited product model
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getCategory()
    {

        return $this->_coreRegistry->registry('current_category');
    }

    /**
     * Add filter
     *
     * @param object $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {

        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('main_table.banner_id', ['in' => $productIds]);
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('main_table.banner_id', ['nin' => $productIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Checks when this block is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return false;
    }

    /**
     * Prepare collection
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    

    

    /**
     * Rerieve grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        $category = $this->getCategory();
        return $this->_getData(
            'grid_url'
            ) ? $this->_getData(
            'grid_url'
            ) : $this->getUrl(
            'lofgallery/*/bannerGrid/category_id/'.$category->getCategoryId(),
            ['_current' => true]
            );
        }

        protected function _getSelectedProducts()
        {
            $products = $this->getProductsUpsell();

            if (!is_array($products)) {
                $products = array_keys($this->getSelectedBanner());
            }
            return $products;
        }

        public function getSelectedBanner()
        {
            $products = [];
            $category = $this->_coreRegistry->registry('current_category');
            if($category){
                $banner = $category->getBanner();

                if(!empty($banner)){
                    foreach ($this->_coreRegistry->registry('current_category')->getBanner() as $_banner) {
                        $products[$_banner['banner_id']] = ['position' => $_banner['position']];
                    }
                }
            }
            return $products;
        }

    /**
     * Apply `position` filter to cross-sell grid.
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection $collection
     * @param \Magento\Backend\Block\Widget\Grid\Column\Extended $column
     * @return $this
     */
    public function filterProductPosition($collection, $column)
    {
    //die($collection->getSelect());
        $condition = $column->getFilter()->getCondition();
        $category = $this->getCategory();
        $condition['category_id'] = $category->getCategoryId();
        $collection->addLinkAttributeToFilter($column->getIndex(), $condition);
        return $this;

        // $collection->addLinkAttributeToFilter($column->getIndex(), $column->getFilter()->getCondition());
        // return $this;
    }
}
