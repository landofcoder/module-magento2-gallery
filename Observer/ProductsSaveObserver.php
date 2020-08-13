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
namespace Lof\Gallery\Observer;

use Magento\Framework\Event\ObserverInterface;

class ProductsSaveObserver implements ObserverInterface
{
    protected $_resource;

    /**
     * @var \Magento\Backend\Helper\Js
     */
    protected $jsHelper;


        /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    /**
     * @param \Magento\Backend\App\Action\Context
     * @param \Magento\Framework\Filesystem
     * @param \Magento\Backend\Helper\Js
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context, 
        \Magento\Backend\Helper\Js $jsHelper,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Registry $coreRegistry
        ) {
        $this->jsHelper = $jsHelper;
        $this->_resource = $resource; 
    }

    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {   
        
        $banners = [];
        $controller = $observer->getController();
        $links = $controller->getRequest()->getPost('links');
        $links = is_array($links) ? $links : [];
        
        if(isset($links) && !empty($links['banner'])){
           // $banners = $this->jsHelper->decodeGridSerializedInput($links['banner']);
            $banners = $links['banner'];
            $data['banners'] = $banners;
        }
      
        $connection = $this->_resource->getConnection();
        $productId = $controller->getRequest()->getParam('id');
          
        if($productId){
            $product_id = $productId;
        }else{
            $table = $this->_resource->getTableName('catalog_product_entity');
            $select = 'SELECT max(entity_id) as product_id FROM ' . $table.'';
            $productId = $connection->fetchAll($select);
            $product_id= $productId[0]['product_id'];
        }
         
        if($banners){
            $table = $this->_resource->getTableName('lof_gallery_banner_product');
            $where = ['entity_id = ?' => $product_id];
            $connection->delete($table, $where); 
            $data = [];
            
            foreach ($banners as $k => $_banner) {
                $data[] = [
                'banner_id' => $_banner['id'],
                'entity_id' => $product_id,
                'position' => $_banner['position']
                ];
            }  
            $connection->insertMultiple($table, $data);
        }
    }
}