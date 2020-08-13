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

class Gallery extends \Magento\Backend\Block\Widget\Grid\Extended
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
        $this->setId('banner_produtcs_grid');
        $this->setDefaultSort('banner_id');
        $this->setUseAjax(true);

        if ($this->getRequest()->getParam('id')) {
            $this->setDefaultFilter(['in_products' => 1]);
        }
        if ($this->isReadonly()) {
            $this->setFilterVisibility(false);
        }
    }
// 
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
        //$category = $this->getCategory();
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
        if ($this->_isAllowedAction('Lof_Gallery::banner_edit')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }
        $this->_eventManager->dispatch(
            'lof_check_license',
            ['obj' => $this,'ex'=>'Lof_Gallery']
        );
        if (!$this->getData('is_valid') && !$this->getData('local_valid')) {
            $isElementDisabled = true;
            $wysiwygConfig['enabled'] = $wysiwygConfig['add_variables'] = $wysiwygConfig['add_widgets'] = $wysiwygConfig['add_images'] = 0;
            $wysiwygConfig['plugins'] = [];

        }
        $this->addColumn(
           'in_products',
           [
           'type' => 'checkbox',
           'name' => 'in_products',
           'values' => $this->_getSelectedProducts(),
           'align' => 'center',
           'index' => 'banner_id',
           'header_css_class' => 'col-select',
           'column_css_class' => 'col-select',
               'disabled' => $isElementDisabled
           ]
           );

        $this->addColumn(
            'gbanner_id',
            [
            'header' => __('ID'),
            'sortable' => true,
            'index' => 'banner_id',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id',
                'disabled' => $isElementDisabled
            ]
            );
        $this->addColumn(
            'gtitle',
            [
            'header' => __('Title'),
            'index' => 'title',
            'header_css_class' => 'col-name',
            'column_css_class' => 'col-name',
                'disabled' => $isElementDisabled
            ]
            );

        $this->addColumn(
            'gimage',
            [
            'header'   => __('Images'),
            'type'     => 'action',
            'renderer' => 'Lof\Gallery\Block\Adminhtml\Banner\Renderer\ImageAction',
                'disabled' => $isElementDisabled
            ]
            );
        $this->addColumn(
            'gimage_url',
            [
            'header'   => __('Image Url'),
            'type'     => 'action',
            'renderer' => 'Lof\Gallery\Block\Adminhtml\Banner\Renderer\ImageUrlAction',
                'disabled' => $isElementDisabled
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
            'column_css_class' => 'col-status',
                'disabled' => $isElementDisabled
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
            'filter_condition_callback' => [$this, 'filterProductPosition'],
            'disabled'                  => $isElementDisabled
            ]
            );

        $this->addColumn(
            'gaction',
            [
            'header'   => __('Action'),
            'type'     => 'action',
            'renderer' => 'Lof\Gallery\Block\Adminhtml\Banner\Renderer\BannerAction',
            'disabled' => $isElementDisabled
            ]
            );

        

        return parent::_prepareColumns();
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

            $bannerIds = $this->_getSelectedProducts();
            if (empty($bannerIds)) {
                $bannerIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('main_table.banner_id', ['in' => $bannerIds]);
            } else {
                if ($bannerIds) {
                    $this->getCollection()->addFieldToFilter('main_table.banner_id', ['nin' => $bannerIds]);
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
    

    protected function _getSelectedProducts()
    { 

        $productsbanner = $this->getProductsBanner();
        if (!is_array($productsbanner)) {
            $productsbanner = array_keys($this->getSelectedBanner());
        }

        return $productsbanner;
    }

    public function getSelectedBanner()
    {
        $productsbanner = [];
        $collection = $this->_bannerFactory->getCollection();
        $productId = $this->getRequest()->getParam('id');

        if($productId){
            $banner = $collection->addBannerToFilter($productId);
            if(!empty($banner)){
                foreach ($banner as $_banner) {
                    $productsbanner[$_banner['banner_id']] = ['position' => $_banner['position']];
                }
            }

        }
        return $productsbanner;
    }
        /**
     * Rerieve grid URL
     *
     * @return string
     */
        public function getGridUrl()
        {
            return $this->_getData(
                'grid_url'
                ) ? $this->_getData(
                'grid_url'
                ) : $this->getUrl(
                'lofgallery/*/galleryGrid/id/'.$this->getRequest()->getParam('id'),
                ['_current' => true]
                );
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
        $condition['entity_id'] = $this->getRequest()->getParam('id');
        $collection->addLinkAttributeToFilterProducts($column->getIndex(), $condition);

        return $this;

        // $collection->addLinkAttributeToFilter($column->getIndex(), $column->getFilter()->getCondition());
        // return $this;
    }
}
