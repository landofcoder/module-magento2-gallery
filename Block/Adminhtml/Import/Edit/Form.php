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
namespace Lof\Gallery\Block\Adminhtml\Import\Edit;
use Magento\Config\Model\Config\Source\Yesno;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $_yesno;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Ves\Setup\Model\System\Config\Source\Export\ExportFolders
     */
    protected $_exportFolders;

    /**
     * @var Yesno
     */
    protected $_yesNo;

    /**
     * @var \Ves\Setup\Model\System\Config\Source\Import
     */
    protected $_importFiles;

    /**
     * @param \Magento\Backend\Block\Template\Context                    $context       
     * @param \Magento\Framework\Registry                                $registry      
     * @param \Magento\Framework\Data\FormFactory                        $formFactory    
     * @param \Magento\Store\Model\System\Store                          $systemStore    
     * @param Yesno                                                      $yesNo         
     * @param array                                                      $data          
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Config\Model\Config\Source\Yesno $yesno,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_yesno = $yesno;
        $this->_systemStore = $systemStore;
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /**
         * Checking if user have permission to save information
         */
        if($this->_isAllowedAction('Lof_Gallery::gallery_import')){
            $isElementDisabled = false;
        }else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
                [
                    'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data'
                    ]
                ]
            );

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Lof Gallery Import')]);

        $fieldset->addField(
            'data_import_folder',
            'text',
            [
                'name' => 'data_import_folder',
                'label' => __('Upload Custom File'),
                'title' => __('Upload Custom File')
            ]
        );

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}