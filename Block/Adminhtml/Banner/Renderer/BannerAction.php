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
namespace Lof\Gallery\Block\Adminhtml\Banner\Renderer;
use Magento\Framework\UrlInterface;

class BannerAction extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{	
	/**
	 * @var \Magento\Store\Model\StoreManager
	 */
	protected $_storeManager;



	/**
	 * @var Magento\Framework\UrlInterface
	 */
	protected $_urlBuilder;

	/**
	 * @param \Magento\Backend\Block\Context
	 * @param \Magento\Framework\Url
	 * @param \Magento\Store\Model\StoreManager
	 */
	public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Framework\Url $url,
        \Magento\Store\Model\StoreManager $storeManager
        ){
		$this->_storeManager        = $storeManager;
		$this->_urlBuilder = $url;
        parent::__construct($context);
	}

	public function _getValue(\Magento\Framework\DataObject $row){
		// $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		// return sprintf("<img src='%s' style='width:70px;' />" , $mediaUrl.$row['file']);
		$editUrl = $this->_urlBuilder->getUrl(
                                'lofgallery/banner/edit',
                                [
                                    'banner_id' => $row['banner_id']
                                ]
                            );
		return sprintf("<a target='_blank' href='%s'>Edit</a>", $editUrl);
	}

	
}