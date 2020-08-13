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
namespace Lof\Gallery\Model\ResourceModel\Category;

use \Lof\Gallery\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection
{
	/**
     * @var string
     */
    protected $_idFieldName = 'category_id';

    /**
     * Perform operations after collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $this->performAfterLoad('lof_gallery_category_store', 'category_id');

        return parent::_afterLoad();
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lof\Gallery\Model\Category', 'Lof\Gallery\Model\ResourceModel\Category');
        $this->_map['fields']['store'] = 'store_table.store_id';
    }

    /**
     * Returns pairs category_id - title
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('category_id', 'name');
    }

    /**
     * Add filter by store
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        $this->performAddStoreFilter($store, $withAdmin);
        $this->joinStoreRelationTable('lof_gallery_category_store', 'category_id');
        return $this;
    }
}