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
namespace Lof\Gallery\Controller\Adminhtml\Import;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
 

    /** @var \Magento\Framework\App\Config\ConfigResource */
    protected $_configResource;

    /**
     * @param \Magento\Backend\App\Action\Context                          $context           
     * @param \Magento\Framework\View\Result\PageFactory                   $resultPageFactory 
         
     * @param \Magento\Framework\Filesystem                                $filesystem        
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManager      
     * @param \Magento\Framework\App\Config\ScopeConfigInterface           $scopeConfig       
     * @param \Magento\Framework\App\ResourceConnection                    $resource          
     * @param \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configResource    
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configResource
        ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_filesystem = $filesystem;
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_configResource = $configResource;
        $this->_resource = $resource;
    }
    private function _listDirectories($path, $fullPath = false)
    {
        $result = array();
        $data = array();
        $dir = opendir($path);
        if ($dir) {
            while ($entry = readdir($dir)) {

                if (substr($entry, 0, 1) == '.' || is_dir($path . DIRECTORY_SEPARATOR . $entry)){
                    continue;
                }
                if ($fullPath) {
                    $entry = $path . DIRECTORY_SEPARATOR . $entry;
                }
                $result['name'] = $entry;
                $result['tmp_name'] = tempnam($_SERVER['TMP'],'tmp');
                $result['size'] = filesize($path . DIRECTORY_SEPARATOR . $entry);
                $result['type'] = image_type_to_mime_type(exif_imagetype($path . DIRECTORY_SEPARATOR . $entry));
                array_push($data, $result);
            }
            unset($entry);
            closedir($dir);
        }
        return $data;
    }
    /**
     * Forward to edit
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $request = $this->getRequest()->getParams();
        $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
        ->getDirectoryRead(DirectoryList::MEDIA);
        $mediaFolder = 'lof/gallery/';
        $path = $mediaDirectory->getAbsolutePath($mediaFolder);
        $file = $request['data_import_folder'];
        $data = $this->_listDirectories($file);
        if ($data) {
            if($image = $this->uploadImage('file', $data)){

                $data['file'] = $image;
            }
        }

    }
    public function uploadImage($fieldId = 'file', $data)
    {                
        foreach ($data as $key => $value) {
        $_FILES[$fieldId] = $value;
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if (isset($_FILES[$fieldId]) && $_FILES[$fieldId]['name']!='') 
        {

            $uploader = $this->_objectManager->create(
                'Magento\Framework\File\Uploader',
                array('fileId' => $fieldId)
                );
                /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
                $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                ->getDirectoryRead(DirectoryList::MEDIA);
                $mediaFolder = 'lof/gallery/';
                try {
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); 
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(false);
                    $result = $uploader->save($mediaDirectory->getAbsolutePath($mediaFolder)
                        );
                    return $mediaFolder.$result['name'];
                } catch (\Exception $e) {
                    $this->_logger->critical($e);
                    $this->messageManager->addError($e->getMessage());
                    return $resultRedirect->setPath('*/*/edit', ['banner_id' => $this->getRequest()->getParam('banner_id')]);
                }
            }
            }
            return;
        }
    }
