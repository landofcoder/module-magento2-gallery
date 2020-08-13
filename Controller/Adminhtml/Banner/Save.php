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
namespace Lof\Gallery\Controller\Adminhtml\Banner;
use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Lof\Gallery\Controller\Adminhtml\Banner
{
    /**
     * @var \Magento\Backend\Helper\Js
     */
    protected $jsHelper;
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_fileSystem;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
         \Magento\Backend\Helper\Js $jsHelper,
        \Magento\Framework\Filesystem $filesystem
        ) {
        $this->_fileSystem = $filesystem;
        $this->jsHelper = $jsHelper;
        parent::__construct($context, $coreRegistry);
    }
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if data sent
        $data = $this->getRequest()->getPostValue();
        $links = $this->getRequest()->getPost('links');
        $links = is_array($links) ? $links : [];

        if(!empty($links) && isset($links['related'])){
            $productsRelated = $this->jsHelper->decodeGridSerializedInput($links['related']);
            $data['products_related'] = $productsRelated;
        }

        if ($data) {
            $id = $this->getRequest()->getParam('banner_id');
            $model = $this->_objectManager->create('Lof\Gallery\Model\Banner')->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addError(__('This banner no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            } 
            $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
            ->getDirectoryRead(DirectoryList::MEDIA);
            $mediaFolder = 'lof/gallery/';
            $path = $mediaDirectory->getAbsolutePath($mediaFolder);

            // Delete, Upload Image
            
            $imagePath = $mediaDirectory->getAbsolutePath($model->getImage());
            if(isset($data['file']['delete']) && file_exists($imagePath.$mediaFolder)){
                //unlink($imagePath.$mediaFolder);
                $data['file'] = '';
            }
            if(isset($data['file']) && is_array($data['file'])){
                unset($data['file']);
            }
            if($image = $this->uploadImage('file')){
                
                $data['file'] = $image;
            }
            // init model and set data
            $model->setData($data);
            // try to save it
            try {
                // save the data
                $model->save();
                // display success message
                $this->messageManager->addSuccess(__('You saved the banner.'));
                // clear previously saved data from session
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);

                if($this->getRequest()->getParam("duplicate")){
                    unset($data['banner_id']);
                    $data['identifier'] = $data['identifier'] . time();

                    $banner = $this->_objectManager->create('Lof\Gallery\Model\Banner');
                    $banner->setData($data);
                    try{
                        $banner->save();
                        $this->messageManager->addSuccess(__('You duplicated this banner.'));
                    } catch (\Magento\Framework\Exception\LocalizedException $e) {
                        $this->messageManager->addError($e->getMessage());
                    } catch (\RuntimeException $e) {
                        $this->messageManager->addError($e->getMessage());
                    } catch (\Exception $e) {
                        $this->messageManager->addException($e, __('Something went wrong while duplicating the banner.'));
                    }
                }


                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['banner_id' => $model->getId()]);
                }
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // save data in session
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($data);
                // redirect to edit form
                return $resultRedirect->setPath('*/*/edit', ['banner_id' => $this->getRequest()->getParam('banner_id')]);
            }
        }
        return $resultRedirect->setPath('*/*/');
    }

    public function uploadImage($fieldId = 'file')
    {                

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if (isset($_FILES[$fieldId]) && $_FILES[$fieldId]['name']!='') 
        {
            $uploader = $this->_objectManager->create(
                'Magento\Framework\File\Uploader',
                array('fileId' => $fieldId)
                );
            $path = $this->_fileSystem->getDirectoryRead(
                DirectoryList::MEDIA
                )->getAbsolutePath(
                'catalog/category/'
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
            return;
        }
}
