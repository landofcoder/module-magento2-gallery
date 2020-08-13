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
namespace Lof\Gallery\Controller;

class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * Event manager
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Page factory
     *
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $_bannerFactory;

    /**
     * Config primary
     *
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * Url
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * Response
     *
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * @var bool
     */
    protected $dispatched;

    /**
     *
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_helper;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Lof\Gallery\Model\Category
     */
    protected $_categoryFactory;

    /**
     * @param \Magento\Framework\App\ActionFactory       $actionFactory   
     * @param \Magento\Framework\Event\ManagerInterface  $eventManager    
     * @param \Magento\Framework\UrlInterface            $url             
     * @param \Lof\Gallery\Model\Banner                  $bannerFactory   
     * @param \Lof\Gallery\Model\Category                $categoryFactory 
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager    
     * @param \Magento\Framework\App\ResponseInterface   $response        
     * @param \Lof\Gallery\Helper\Data                   $data            
     * @param \Magento\Customer\Model\Session            $customerSession 
     * @param \Magento\Framework\Registry                $registry        
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\UrlInterface $url,
        \Lof\Gallery\Model\Banner $bannerFactory,
        \Lof\Gallery\Model\Category $categoryFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResponseInterface $response,
        \Lof\Gallery\Helper\Data $data,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Registry $registry
        ) {
        $this->actionFactory    = $actionFactory;
        $this->_eventManager    = $eventManager;
        $this->_url             = $url;
        $this->_bannerFactory   = $bannerFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_storeManager    = $storeManager;
        $this->_response        = $response;
        $this->_helper          = $data;
        $this->_coreRegistry    = $registry;
        $this->_customerSession = $customerSession;
    }

    /**
     * Validate and Match Cms Page and modify request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {


        $store = $this->_storeManager->getStore();
        if (!$this->dispatched) {
            $identifier = trim($request->getPathInfo(), '/');
            $origUrlKey = $identifier;


            $condition = new \Magento\Framework\DataObject(['identifier' => $identifier, 'continue' => true]);
            $this->_eventManager->dispatch(
                'lofgallery_controller_router_match_before',
                ['router' => $this, 'condition' => $condition]
                );
            $identifier = $condition->getIdentifier();

            if ($condition->getRedirectUrl()) {
                $this->response->setRedirect($condition->getRedirectUrl());
                $request->setDispatched(true);
                return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Redirect',
                    ['request' => $request]
                    );
            }
            $latestPageRoute = $this->_helper->getConfig('latest_page/route');
            $urlPrefix = $this->_helper->getConfig('general_settings/url_prefix');

            //Latest page  
            $identifiers = explode('/', $identifier);
            
            if(count($identifiers)==1 && $identifiers[0] == $urlPrefix){
                $identifier = $identifiers[0];
                $request->setModuleName('lofgallery')
                ->setControllerName('Category')
                ->setActionName('bannerGrid');
                $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);
                $request->setDispatched(true);
                $this->dispatched = true;
                return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Forward',
                    ['request' => $request]
                    );
            }    
            if(count($identifiers)==2 && $urlPrefix == $identifiers[0]){
                if(isset($identifiers[1]) && ($identifiers[1]) == $latestPageRoute){
                    $request->setModuleName('lofgallery')
                    ->setControllerName('Category')
                    ->setActionName('bannerGrid');
                    $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);
                    $request->setDispatched(true);
                    $this->dispatched = true;
                    return $this->actionFactory->create(
                        'Magento\Framework\App\Action\Forward',
                        ['request' => $request]
                        );
                }
            }
            //search 
            if(count($identifiers)==2 && $urlPrefix == $identifiers[0] && $identifiers[1]=='search'){
                $request->setModuleName('lofgallery')
                ->setControllerName('search')
                ->setActionName('result');
                $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);
                $request->setDispatched(true);
                $this->dispatched = true;
                return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Forward',
                    ['request' => $request]
                    );
            }

            //Category

            //Album detail

            if(count($identifiers) >= 2 && $urlPrefix == $identifiers[0]){
                $total = count($identifiers);
                $latest_alias =  $identifiers[$total - 1];
                $tmp_identifiers = $identifiers;
                unset($tmp_identifiers[0]);
                if(count($tmp_identifiers) > 1) {
                    $alias = implode("/",$tmp_identifiers);
                } else {
                    $alias = $latest_alias;
                }
                //Check is category exists or not
                if($alias) {
                    $category = $this->_categoryFactory->getCollection()
                    ->addFieldToFilter('identifier', $alias)
                    ->addFieldToFilter('is_active', 1)
                    ->getFirstItem();
                    if($category->getData()){
                        $this->_coreRegistry->register("lofgallery_category", $category);
                        $request->setModuleName('lofgallery')
                        ->setControllerName('category')
                        ->setActionName('bannerCategory');
                        $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);
                        $request->setDispatched(true);
                        $this->dispatched = true;
                        return $this->actionFactory->create(
                            'Magento\Framework\App\Action\Forward',
                            ['request' => $request]
                            );
                    }
                }
                //check banner is exists or not
                if($latest_alias) {
                    $banner = $this->_bannerFactory->getCollection()
                    ->addFieldToFilter('identifier', $latest_alias)
                    ->addFieldToFilter('is_active', 1)
                    ->getFirstItem();

                    if($banner->getData()){
                        $this->_coreRegistry->register("lofgallery_banner", $banner);
                        $request->setModuleName('lofgallery')
                        ->setControllerName('banner')
                        ->setActionName('detail')
                        ->setParam('key', $latest_alias);
                        $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);
                        $request->setDispatched(true);
                        $this->dispatched = true;
                        return $this->actionFactory->create(
                            'Magento\Framework\App\Action\Forward',
                            ['request' => $request]
                            );
                    }
                }
            }
            $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);
            $this->dispatched = true;
            return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Forward',
                    ['request' => $request]
                    ); 
        }
    }
}