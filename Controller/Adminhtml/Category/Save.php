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
namespace Lof\Gallery\Controller\Adminhtml\Category;

class Save extends \Lof\Gallery\Controller\Adminhtml\Category
{
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
         \Magento\Framework\Registry $coreRegistry
        ) {
        $this->jsHelper = $jsHelper;
        parent::__construct($context,$coreRegistry);
    }
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if data sent
        $data = $this->getRequest()->getPostValue();
        
        $links = $this->getRequest()->getPost('links');
        $links = is_array($links) ? $links : [];
        if(!empty($links)){
            $banners = $this->jsHelper->decodeGridSerializedInput($links['banner']);
            $data['banners'] = $banners;
        }


        if ($data) {
            $id = $this->getRequest()->getParam('category_id');
            $model = $this->_objectManager->create('Lof\Gallery\Model\Category')->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addError(__('This Category no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            } 

            // init model and set data

            $model->setData($data);

            // try to save it
            try {
                // save the data
                $model->save();
                // display success message
                $this->messageManager->addSuccess(__('You saved the Category.'));
                // clear previously saved data from session
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);

                if($this->getRequest()->getParam("duplicate")){
                    unset($data['category_id']);
                    $data['identity'] = $data['identity'].time();

                    $form = $this->_objectManager->create('Lof\Gallery\Model\Category');
                    $form->setData($data);
                    try{
                        $form->save();
                        $this->messageManager->addSuccess(__('You duplicated this category.'));
                    } catch (\Magento\Framework\Exception\LocalizedException $e) {
                        $this->messageManager->addError($e->getMessage());
                    } catch (\RuntimeException $e) {
                        $this->messageManager->addError($e->getMessage());
                    } catch (\Exception $e) {
                        $this->messageManager->addException($e, __('Something went wrong while duplicating the category.'));
                    }
                }

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['category_id' => $model->getId()]);
                }
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // save data in session
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($data);
                // redirect to edit form
                return $resultRedirect->setPath('*/*/edit', ['category_id' => $this->getRequest()->getParam('category_id')]);
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
