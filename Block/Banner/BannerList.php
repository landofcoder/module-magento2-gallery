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
namespace Lof\Gallery\Block\Banner;

class BannerList extends \Magento\Framework\View\Element\Template
{
	/**
     * @var AbstractCollection
     */
    protected $_collection;

	/**
     * @param AbstractCollection $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;
        return $this;
    }

    public function getCollection()
    {
    	return $this->_collection;
    }

    public function getConfig($key, $default = '')
    {
    	if($this->hasData($key)){
    		return $this->getData($key);
    	}
        return $default;
    }
}