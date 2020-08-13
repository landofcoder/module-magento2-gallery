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
namespace Lof\Gallery\Block\Adminhtml\Banner\Edit\Tab;

class Meta extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    protected $_templateLayout;

    /**
     * @var \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface
     */
    protected $pageLayoutBuilder;

    /**
     * [__construct description]
     * @param \Magento\Backend\Block\Template\Context                          $context           
     * @param \Magento\Framework\Registry                                      $registry          
     * @param \Magento\Framework\Data\FormFactory                              $formFactory       
     * @param \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder 
     * @param array                                                            $data              
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder,
        array $data = []
        ) {
        $this->pageLayoutBuilder = $pageLayoutBuilder;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        if ($this->_isAllowedAction('Lof_Gallery::banner_edit')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }
        $this->_eventManager->dispatch(
            'lof_check_license',
            ['obj' => $this,'ex'=>'Lof_Gallery']
        );
        if (!$this->getData('is_valid') && !$this->getData('local_valid')) {
            $isElementDisabled = true;
            $wysiwygConfig['enabled'] = $wysiwygConfig['add_variables'] = $wysiwygConfig['add_widgets'] = $wysiwygConfig['add_images'] = 0;
            $wysiwygConfig['plugins'] = [];

        }

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('form_');
        $model = $this->_coreRegistry->registry('lofgallery_banner');

        $fieldset = $form->addFieldset(
            'meta_fieldset',
            ['legend' => __('SEO'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField(
            'page_title',
            'text',
            [
                'name' => 'page_title',
                'label' => __('Page Title'),
                'title' => __('Page Title'),
                'disabled' => $isElementDisabled
            ]
        );
        
        $fieldset->addField(
            'meta_keywords',
            'textarea',
            [
                'name' => 'meta_keywords',
                'label' => __('Keywords'),
                'title' => __('Meta Keywords'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'meta_description',
            'textarea',
            [
                'name' => 'meta_description',
                'label' => __('Description'),
                'title' => __('Meta Description'),
                'disabled' => $isElementDisabled
            ]
        );

        //$this->_eventManager->dispatch('adminhtml_formbuilder_form_edit_tab_main_prepare_form', ['form' => $form]);

        if (!$model->getId()) {
            $model->setData('page_layout', '2columns-left');
        }
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('SEO');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('SEO');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
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
