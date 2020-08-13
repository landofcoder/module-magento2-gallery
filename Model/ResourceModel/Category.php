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
namespace Lof\Gallery\Model\ResourceModel;

/**
 * CMS block model
 */
class Category extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Category constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->_storeManager = $storeManager;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('lof_gallery_banner_category', 'category_id');
    }

        /**
     * Perform operations after object save
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
       //print_r($object->getData());die();
        // Category posts
        if($banner = $object->getData('banners')){
            $table = $this->getTable('lof_gallery');
            $where = ['category_id = ?' => (int)$object->getId()];
            $this->getConnection()->delete($table, $where); 
            $data = [];
            
            foreach ($banner as $k => $_banner) {
                $data[] = [
                'banner_id' => $k,
                'category_id' => (int)$object->getId(),
                'position' => $_banner['position']
                ];
            }
            $this->getConnection()->insertMultiple($table, $data);
        }

        if($stores = $object->getStores()){
            $table = $this->getTable('lof_gallery_category_store');
            $where = ['category_id = ?' => (int)$object->getId()];
            $this->getConnection()->delete($table, $where);
            if ($stores) {
                $data = [];
                foreach ($stores as $storeId) {
                    $data[] = ['category_id' => (int)$object->getId(), 'store_id' => (int)$storeId];
                }
                try{
                    $this->getConnection()->insertMultiple($table, $data);
                }catch(\Exception $e){
                    die($e->getMessage());
                }
            }
        }

        return parent::_afterSave($object);


    }


    /**
     * Perform operations after object load
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($id = $object->getId()) {
            $connection = $this->getConnection();
            $select = $connection->select()
            ->from($this->getTable('lof_gallery'))
            ->where(
                'category_id = '.(int)$id
                );
            $banner = $connection->fetchAll($select);
            $object->setData('banner', $banner);
        }

        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', $stores);
            $object->setData('stores', $stores);
        }
        if ($id = $object->getId()) {
            $connection = $this->getConnection();
            $select = $connection->select()
            ->from($this->getTable('lof_gallery_category_store'))
            ->where(
                'category_id = '.(int)$id
                );
            $categories = $connection->fetchAll($select);
            $object->setData('categories', $categories);
        }

            return parent::_afterLoad($object);

    }
        /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function lookupStoreIds($id)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('lof_gallery_category_store'),
            'store_id'
            )->where(
            'category_id = :category_id'
            );
            $binds = [':category_id' => (int)$id];
            //die(print_r($connection->fetchCol($select, $binds)->getData());
            return $connection->fetchCol($select, $binds);
    }
            public function getIsUniqueBlockToStores(\Magento\Framework\Model\AbstractModel $object)
    {
        $select = $this->getConnection()->select()->from(
            ['lgbc' => $this->getMainTable()]
            )->where(
            'lgbc.identifier = ?',
            $object->getData('identifier')
            );
            if ($object->getId()) {
                $select->where('lgbc.category_id <> ?', $object->getId());
            }
            if ($this->getConnection()->fetchRow($select)) {
                return false;
            }

            return true;
        }
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if (!$this->getIsUniqueBlockToStores($object)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('A category identifier with the same properties already exists in the selected store.')
                );
        }
        return $this;
    }
    
}
