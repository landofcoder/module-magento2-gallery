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
namespace Lof\Gallery\Block\Adminhtml\Banner\Edit\Tab;

use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

class RelatedProducts extends Extended
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

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Catalog\Model\Product\LinkFactory $linkFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Product\Type $type
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $status
     * @param \Magento\Catalog\Model\Product\Visibility $visibility
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
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
        array $data = []
        ) {
        $this->_linkFactory = $linkFactory;
        $this->_setsFactory = $setsFactory;
        $this->_productFactory = $productFactory;
        $this->_type = $type;
        $this->_status = $status;
        $this->_visibility = $visibility;
        $this->_coreRegistry = $coreRegistry;
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
        $this->setId('related_product_grid');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        if ($this->getBanner() && $this->getBanner()->getBannerId()) {
            $this->setDefaultFilter(['in_products' => 1]);
        }
        if ($this->isReadonly()) {
            $this->setFilterVisibility(false);
        }
    }

    /**
     * Add filter
     *
     * @param Column $column
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
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Retirve currently edited product model
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getBanner()
    {
        return $this->_coreRegistry->registry('current_banner');
    }

    /**
     * Prepare collection
     *
     * @return Extended
     */
    protected function _prepareCollection()
    {
        $collection = $this->_linkFactory->create()->getProductCollection()->addAttributeToSelect('*');

        if ($this->isReadonly()) {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = [0];
            }
            $collection->addFieldToFilter('entity_id', ['in' => $productIds]);
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Checks when this block is readonly
     *
     * @return bool
     */
    public function isReadonly()
    {
        return $this->getBanner() && $this->getBanner()->getRelatedReadonly();
    }

    /**
     * Add columns to grid
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        if (!$this->isReadonly()) {
            $this->addColumn(
                'in_products',
                [
                'type' => 'checkbox',
                'name' => 'in_products',
                'values' => $this->_getSelectedProducts(),
                'align' => 'center',
                'index' => 'entity_id',
                'header_css_class' => 'col-select',
                'column_css_class' => 'col-select'
                ]
                );
        }

        $this->addColumn(
            'entity_id',
            [
            'header' => __('ID'),
            'sortable' => true,
            'index' => 'entity_id',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id'
            ]
            );

        $this->addColumn(
            'name',
            [
            'header' => __('Name'),
            'index' => 'name',
            'header_css_class' => 'col-name',
            'column_css_class' => 'col-name'
            ]
            );

        $this->addColumn(
            'type',
            [
            'header' => __('Type'),
            'index' => 'type_id',
            'type' => 'options',
            'options' => $this->_type->getOptionArray(),
            'header_css_class' => 'col-type',
            'column_css_class' => 'col-type'
            ]
            );

        $sets = $this->_setsFactory->create()->setEntityTypeFilter(
            $this->_productFactory->create()->getResource()->getTypeId()
            )->load()->toOptionHash();

        $this->addColumn(
            'set_name',
            [
            'header' => __('Attribute Set'),
            'index' => 'attribute_set_id',
            'type' => 'options',
            'options' => $sets,
            'header_css_class' => 'col-attr-name',
            'column_css_class' => 'col-attr-name'
            ]
            );

        $this->addColumn(
            'status',
            [
            'header' => __('Status'),
            'index' => 'status',
            'type' => 'options',
            'options' => $this->_status->getOptionArray(),
            'header_css_class' => 'col-status',
            'column_css_class' => 'col-status'
            ]
            );

        $this->addColumn(
            'visibility',
            [
            'header' => __('Visibility'),
            'index' => 'visibility',
            'type' => 'options',
            'options' => $this->_visibility->getOptionArray(),
            'header_css_class' => 'col-visibility',
            'column_css_class' => 'col-visibility'
            ]
            );

        $this->addColumn(
            'sku',
            [
            'header' => __('SKU'),
            'index' => 'sku',
            'header_css_class' => 'col-sku',
            'column_css_class' => 'col-sku'
            ]
            );

        $this->addColumn(
            'price',
            [
            'header' => __('Price'),
            'type' => 'currency',
            'currency_code' => (string)$this->_scopeConfig->getValue(
                \Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                ),
            'index' => 'price',
            'header_css_class' => 'col-price',
            'column_css_class' => 'col-price'
            ]
            );

        $this->addColumn(
            'position',
            [
            'header' => __('Position'),
            'name' => 'position',
            'type' => 'number',
            'validate_class' => 'validate-number',
            'index' => 'position',
            'editable' => !$this->getBanner()->getRelatedReadonly(),
            'edit_only' => !$this->getBanner()->getId(),
            'header_css_class' => 'col-position',
            'column_css_class' => 'col-position',
            'filter_condition_callback' => [$this, 'filterProductPosition']
            ]
            );

        return parent::_prepareColumns();
    }

    /**
     * Rerieve grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getData(
            'grid_url'
            ) ? $this->getData(
            'grid_url'
            ) : $this->getUrl(
            'lofgallery/*/relatedProductsGrid',
            ['_current' => true]
            );
        }

    /**
     * Retrieve selected related products
     *
     * @return array
     */
    protected function _getSelectedProducts()
    {
        $products = $this->getProductsRelated();
        if (!is_array($products)) {
            $products = array_keys($this->getSelectedRelatedProducts());
        }
        return $products;
    }

    /**
     * Retrieve related products
     *
     * @return array
     */
    public function getSelectedRelatedProducts()
    {
        $banner = $this->getBanner();
        $products = [];
        if(!empty($banner->getRelatedProducts())){
            foreach ($banner->getRelatedProducts() as $product) {
                $products[$product['entity_id']] = ['position' => $product['position']];
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
        $condition = $column->getFilter()->getCondition();
        $banner = $this->getBanner();
        $condition['banner_id'] = $banner->getBannerId();
        $collection->addLinkAttributeToFilter($column->getIndex(), $condition);
        return $this;
    }
}
