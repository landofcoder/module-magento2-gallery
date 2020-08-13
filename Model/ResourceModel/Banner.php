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
class Banner extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Banner constructor.
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
        $this->_init('lof_gallery_banner', 'banner_id');
    }
    public function getIsUniqueBlockToStores(\Magento\Framework\Model\AbstractModel $object)
    {
        $select = $this->getConnection()->select()->from(
            ['lgb' => $this->getMainTable()]
            )->where(
            'lgb.identifier = ?',
            $object->getData('identifier')
            );
            if ($object->getId()) {
                $select->where('lgb.banner_id <> ?', $object->getId());
            }
            if ($this->getConnection()->fetchRow($select)) {
                return false;
            }

            return true;
    }
    /**
     * Perform operations after object save
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    { 
        if(!$object->getData("isfrontend")){
        // Products Related
            if($productsRelated = $object->getProductsRelated()){
                $table = $this->getTable('lof_gallery_banner_product');
                $where = ['banner_id = ?' => (int)$object->getId()];
                $this->getConnection()->delete($table, $where);
                $data = [];
                foreach ($productsRelated as $k => $_post) {
                    $data[] = [
                    'banner_id' => (int)$object->getId(),
                    'entity_id' => $k,
                    'position' => $_post['position']
                    ];
                } 
                $this->getConnection()->insertMultiple($table, $data);
            }
        }
        return parent::_afterSave($object);
    }
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if (!$this->getIsUniqueBlockToStores($object)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('A banner identifier with the same properties already exists in the selected store.')
                );
        }
        return $this;
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
            ->from($this->getTable('lof_gallery_banner_product'))
            ->where(
                'banner_id = '.(int)$id
                );
            $products = $connection->fetchAll($select);
            $object->setData('related_products', $products);
        }
        return parent::_afterLoad($object);
    }
}
