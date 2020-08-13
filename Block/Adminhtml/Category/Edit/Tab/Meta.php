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
 * @category  Landofcoder
 * @package   Lof_Gallery
 * @copyright Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license   http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\Gallery\Block\Adminhtml\Category\Edit\Tab;

class Meta extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{


    protected function _prepareForm()
    {
        if ($this->_isAllowedAction('Lof_Gallery::category_edit')) {
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
        $model    = $this->_coreRegistry->registry('lofgallery_category');
        $fieldset = $form->addFieldset(
            'meta_fieldset',
            [
             'legend' => __('SEO'),
             'class'  => 'fieldset-wide',
            ]
        );

        $fieldset->addField(
            'page_title',
            'text',
            [
             'name'     => 'page_title',
             'label'    => __('Page Title'),
             'title'    => __('Page Title'),
             'disabled' => $isElementDisabled,
            ]
        );

        $fieldset->addField(
            'meta_keywords',
            'textarea',
            [
             'name'     => 'meta_keywords',
             'label'    => __('Keywords'),
             'title'    => __('Meta Keywords'),
             'disabled' => $isElementDisabled,
            ]
        );

        $fieldset->addField(
            'meta_description',
            'textarea',
            [
             'name'     => 'meta_description',
             'label'    => __('Description'),
             'title'    => __('Meta Description'),
             'disabled' => $isElementDisabled,
            ]
        );

        if (!$model->getId()) {
            $model->setData('page_layout', '2columns-left');
        }

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();

    }//end _prepareForm()


    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('SEO');

    }//end getTabLabel()


    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('SEO');

    }//end getTabTitle()


    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;

    }//end canShowTab()


    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;

    }//end isHidden()


    /**
     * Check permission for passed action
     *
     * @param  string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);

    }//end _isAllowedAction()


}//end class
