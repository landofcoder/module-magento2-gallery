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

class Tab extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{

    /**
     * Instance of pager block
     *
     * @var \Magento\Catalog\Block\Product\Widget\Html\Pager
     */
    protected $pager;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

   
    /**
     * Report Product collection factory
     *
     * @var \Magento\Reports\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_reportCollection;

    /**
     * @var \Magento\Rule\Model\Condition\Sql\Builder
     */
    protected $sqlBuilder;

    /**
     * @var \Magento\CatalogWidget\Model\Rule
     */
    protected $rule;

    /**
     * @var \Magento\Widget\Helper\Conditions
     */
    protected $conditionsHelper;
    /**
     * @var \Magento\Cms\Model\Block
     */
    protected $_blockModel;

    protected $_conditionCollection;

   /**
    * @param \Magento\Framework\App\Http\Context                       $httpContext              
    * @param \Magento\Rule\Model\Condition\Sql\Builder                 $sqlBuilder                                 
    * @param \Magento\Widget\Helper\Conditions                         $conditionsHelper                    
    * @param \Magento\Cms\Model\Block                                  $blockModel               
    * @param array                                                     $data                     
    */
   public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
    \Magento\Framework\App\Http\Context $httpContext,
    \Magento\CatalogWidget\Model\Rule $rule,
    \Magento\Widget\Helper\Conditions $conditionsHelper,
    \Magento\Cms\Model\Block $blockModel,
    array $data = []
    ) {
    $this->httpContext = $httpContext;
    $this->rule = $rule;
    $this->conditionsHelper = $conditionsHelper;
    $this->_blockModel = $blockModel;
    parent::__construct($context);
}



    /**
     * Get key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $conditions = $this->getData('tabs');
        if($tabs = $this->getConfig('tabs')){
            $conditions = $conditions.".".md5($tabs);
        }
        return [
        'LOF_GALLERY_LIST_TAB_WIDGET',
        $this->_storeManager->getStore()->getId(),
        $this->_design->getDesignTheme()->getId(),
        $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP),
        $conditions
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeToHtml()
    {
        $this->_eventManager->dispatch(
            'lof_gallery_list_collection',
            ['gallery_list' => $this]
            );
        return parent::_beforeToHtml();
    }
    /**
     * @return \Magento\Rule\Model\Condition\Combine
     */
    protected function getConditions()
    {
        $conditions = $this->getData('conditions_encoded')
        ? $this->getData('conditions_encoded')
        : $this->getData('conditions');
        if ($conditions) {
            $conditions = $this->conditionsHelper->decode($conditions);
        }

        $this->rule->loadPost(['conditions' => $conditions]);
        return $this->rule->getConditions();
    }

    public function getConfig($key, $default = '')
    {
        if($this->hasData($key) && $this->getData($key))
        {
            return $this->getData($key);
        }
        return $default;
        
    }

    public function getCmsBlockModel(){
        return $this->_blockModel;
    }

    public function getTabs(){
        $tabs = $this->getConfig('tabs');
        if($tabs){
            $a = '';
            if(base64_decode($tabs, true) == true){
                $tabs = str_replace(" ", "+", $tabs);
                $tabs = base64_decode($tabs);
                if(base64_decode($tabs, true) == true) {
                    $tabs = base64_decode($tabs);
                }
            }
            try{
                $tabs = unserialize($tabs);
            }catch(\Exception $e){
                die($this->getConfig('tabs'));
            }
            if(isset($tabs['__empty'])) unset($tabs['__empty']);
            usort($tabs,function($a, $b){
                if ($a["position"] == $b["position"]) {
                    return 0;
                }
                return ($a["position"] < $b["position"]) ? -1 : 1;
            }); 
            return $tabs;
        }
        return false;
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Magento\Catalog\Model\Product::CACHE_TAG];
    }


    public function getAjaxUrl(){
        return $this->getUrl('productlist/index/product');
    }
}
