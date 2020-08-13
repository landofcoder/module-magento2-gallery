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
namespace Lof\Gallery\Model\ResourceModel\Banner;

use \Lof\Gallery\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'banner_id';
    /**
     * Define resource model
     *
     * @return void
     */
    
     /**
     * Perform operations after collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        //$this->performAfterLoad('lof_gallery_category_store', 'category_id');
         $this->getProductsAfterLoad();
        return parent::_afterLoad();
    }


    protected function _construct()
    {
        $this->_init('Lof\Gallery\Model\Banner', 'Lof\Gallery\Model\ResourceModel\Banner');
    }

      /**
     * Returns pairs category_id - title
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('banner_id', 'title');
    }

    /**
     * @param array|int|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return $this
     */

        public function addStoreFilter($store, $withAdmin = true, $field="store_id")
    {
        $this->performAddStoreFilter($store, $withAdmin, $field);

        return $this;
    }

    public function addLinkAttributeToFilter($code, $condition)
    {

               if($code=='position'){
            $connection = $this->getConnection();
            $where = '';
            if(isset($condition['from'])){
                $where .= 'position >= ' . $condition['from'] . ' AND ';
            }
            if(isset($condition['to'])){
                $where .= ' position <= ' . $condition['to'] . ' AND ';
            }
            if($where!=''){
                $where .= ' category_id = ' . $condition['category_id'];
            }
            $select = 'SELECT banner_id FROM ' . $this->getTable('lof_gallery') . ' WHERE ' . $where;
            $postIds = $connection->fetchCol($select);
            $this->getSelect()->where('banner_id IN (?)', $postIds);
        }
        return $this;
    }

    public function addLinkAttributeToFilterProducts($code, $condition)
    {       
            if($code=='position'){
            $connection = $this->getConnection();
            $where = '';
            if(isset($condition['from'])){
                $where .= 'position >= ' . $condition['from'] . ' AND ';
            }
            if(isset($condition['to'])){
                $where .= ' position <= ' . $condition['to'] . ' AND ';
            }
            if($where!=''){
                $where .= ' entity_id = ' . $condition['entity_id'];
            }
            $select = 'SELECT banner_id FROM ' . $this->getTable('lof_gallery_banner_product') . ' WHERE ' . $where;
            $postIds = $connection->fetchCol($select);
            $this->getSelect()->where('banner_id IN (?)', $postIds);
        }
        return $this;
    }

        public function addBannerToFilter($productId)
    {
        $banner = [];
        if ($productId) {
            $connection = $this->getConnection();
            $select = $connection->select()
            ->from($this->getTable('lof_gallery_banner_product'))
            ->where(
                'entity_id = '.(int)$productId
                );
            $banner = $connection->fetchAll($select);
        }

        return $banner;

    }
    /**
     * Perform operations after collection load
     *
     * @param string $tableName
     * @param string $columnName
     * @return void
     */
    protected function getProductsAfterLoad()
    {
        $items = $this->getColumnValues("banner_id");
        if (count($items)) {
            $connection = $this->getConnection();
            $select = 'SELECT * FROM ' . $this->getTable('lof_gallery_banner_product');
            $products = $connection->fetchAll($select);
            foreach ($this as $item) {
                $select = 'SELECT entity_id FROM ' . $this->getTable('lof_gallery_banner_product') . ' WHERE banner_id = ' .  $item->getData("banner_id") . ' ORDER BY position DESC ';
                $products = $connection->fetchCol($select);
                $item->setData('products', $products);
            }
        }
    }


}