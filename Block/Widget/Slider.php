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
namespace Lof\Gallery\Block\Widget;

class Slider extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
 
	protected $_galleryHelper;
 
	protected $_banner;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resource;

	/**
	 * @param \Magento\Framework\View\Element\Template\Context
 
	 * @param array
	 */
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Lof\Gallery\Helper\Data $galleryHelper,
		\Lof\Gallery\Model\Banner $banner,
		\Magento\Cms\Model\Block $blockModel,
        \Magento\Framework\App\ResourceConnection $resource,
		array $data = []
		) {
		$this->_galleryHelper = $galleryHelper;
		$this->_banner = $banner;
		$this->_blockModel = $blockModel;
		parent::__construct($context, $data);
        $this->_resource = $resource;
	}
	
	public function _toHtml(){
 
		$this->setTemplate('Lof_Gallery::widget/slider.phtml');
		$itemPerPage = $this->getConfig('limit', 30);
		$categories = $this->getConfig('categories');
		$categories = explode(",", $categories); 
		$collection = $this->_banner
		->getCollection()
		->addFieldToFilter("is_active", 1)
		->setPagesize($itemPerPage);

		$collection->getSelect()
        ->joinLeft(
            [
            'lg' => $this->_resource->getTableName('lof_gallery')],
            'lg.banner_id = main_table.banner_id',
            [
            'banner_id' => 'banner_id',
            'position' => 'position'
            ]
            )
        ->where('lg.category_id in (?)', $categories)
        ->limit($itemPerPage)
        ->group('main_table.banner_id');
		$this->setCollection($collection);
		return parent::_toHtml();
	}

	/**
     * @param AbstractCollection $collection
     * @return $this
     */
	public function setCollection($collection)
	{
		$this->_postCollection = $collection;
		return $this;
	}

	public function getCollection()
	{
		return $this->_postCollection;
	}

	public function getConfig($key, $default = '')
	{
		if($this->hasData($key)){
			return $this->getData($key);
		}
		return $default;
	}
}