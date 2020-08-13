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

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @var \Lof\Gallery\Model\ResourceModel\Category\Collection
     */
    protected $categoryCollection;

    /**
     * @var array
     */
    protected $_drawLevel;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;


    /**
     * @param \Magento\Backend\Block\Template\Context              $context
     * @param \Magento\Framework\Registry                          $registry
     * @param \Magento\Framework\Data\FormFactory                  $formFactory
     * @param \Magento\Store\Model\System\Store                    $systemStore
     * @param \Magento\Cms\Model\Wysiwyg\Config                    $wysiwygConfig
     * @param \Lof\Gallery\Model\ResourceModel\Category\Collection $categoryCollection
     * @param array                                                $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Lof\Gallery\Model\ResourceModel\Category\Collection $categoryCollection,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->systemStore        = $systemStore;
        $this->_wysiwygConfig      = $wysiwygConfig;
        $this->categoryCollection = $categoryCollection;

    }//end __construct()


    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('lofgallery_category');

        if ($this->_isAllowedAction('Lof_Gallery::category_edit')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }
        $wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId().time()]);
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
        $form->setHtmlIdPrefix('category_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('General Settings')]);
        if ($model->getId()) {
            $fieldset->addField('category_id', 'hidden', ['name' => 'category_id']);
        }

        $fieldset->addField(
            'name',
            'text',
            [
             'name'     => 'name',
             'label'    => __('Name'),
             'title'    => __('Name'),
             'required' => true,
             'disabled' => $isElementDisabled,
            ]
        );

        $fieldset->addField(
            'identifier',
            'text',
            [
             'name'     => 'identifier',
             'label'    => __('URL Key'),
             'title'    => __('URL Key'),
             'class'    => 'validate-identifier',
             'required' => true,
             'note'     => __('Relative to Web Site Base URL'),
             'disabled' => $isElementDisabled,
            ]
        );
    if($model->getId()){
        $categories[] = [
            'label' => __('Please select'),
            'value' => 0,
        ];

        $this->_drawLevel = $categories;

        $collection = $this->getCatCollection();
        $cats       = [];
        foreach ($collection as $_cat) {
            if(!$_cat->getParentId()) {
                $cat    = [
                    'label'     => $_cat->getName(),
                    'value'     => $_cat->getId(),
                    'id'        => $_cat->getId(),
                    'parent_id' => $_cat->getParentId(),
                    'level'     => 0,
                    'postion'   => $_cat->getCatPosition(),
                ];
                $cats[] = $this->drawItems($collection, $cat);
            }
        }

        $this->drawSpaces($cats);

        if (count($this->_drawLevel) > 1) {
            $fieldset->addField(
                'parent_id',
                'select',
                [
                    'name'     => 'parent_id',
                    'label'    => __('Parent Category'),
                    'title'    => __('Parent Category'),
                    'style'    => 'width: 210px;',
                    'values'   => $this->_drawLevel,
                    'disabled' => $isElementDisabled,
                ]
            );
        }

        $contentField = $fieldset->addField(
            'description',
            'editor',
            [
                'label'    => __('Description'),
                'title'    => __('Description'),
                'name'     => 'description',
                'style'    => 'height:20em;',
                'disabled' => $isElementDisabled,
                'config'   => $wysiwygConfig,
            ]
        );
    }


        if (!$this->_storeManager->isSingleStoreMode()) {
            $field    = $fieldset->addField(
                'store_id',
                'multiselect',
                [
                 'name'     => 'stores[]',
                 'label'    => __('Store View'),
                 'title'    => __('Store View'),
                 'required' => true,
                 'values'   => $this->systemStore->getStoreValuesForForm(false, true),
                 'disabled' => $isElementDisabled,
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                [
                 'name'  => 'stores[]',
                 'value' => $this->_storeManager->getStore(true)->getId(),
                ]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }//end if

        $fieldset->addField(
            'is_active',
            'select',
            [
             'label'    => __('Status'),
             'title'    => __('Status'),
             'name'     => 'is_active',
             'style'    => 'width: 198px;',
             'options'  => $model->getAvailableStatuses(),
             'disabled' => $isElementDisabled,
            ]
        );

        $fieldset->addField(
            'cat_position',
            'text',
            [
             'label'    => __('Position'),
             'title'    => __('Position'),
             'name'     => 'cat_position',
             'style'    => 'width: 198px;',
             'disabled' => $isElementDisabled,
            ]
        );

        $data = $model->getData();
        if (!$form->getId()) {
            $data['is_active'] = 1;
        }

        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();

    }//end _prepareForm()


    public function getCatCollection()
    {
        $model      = $this->_coreRegistry->registry('lofgallery_category');
        $collection = $this->categoryCollection
            ->addFieldToFilter('main_table.category_id', array('neq' => $model->getId()))
            ->setOrder('cat_position');
        return $collection;

    }//end getCatCollection()


    public function drawSpaces($cats)
    {
        if(is_array($cats)) {
            foreach ($cats as $k => $v) {
                $v['label']         = $this->_getSpaces($v['level']).$v['label'];
                $this->_drawLevel[] = $v;
                if(isset($v['optgroup']) && $children = $v['optgroup']) {
                    $this->drawSpaces($children);
                }
            }
        }

    }//end drawSpaces()


    protected function _getSpaces($n)
    {
        $s = '';
        for($i = 0; $i < $n; $i++) {
            $s .= '--- ';
        }

        return $s;

    }//end _getSpaces()


    public function drawItems($collection, $cat, $level = 0)
    {
        foreach ($collection as $_cat) {
            if($_cat->getParentId() == $cat['id']) {
                $cat1            = [
                                    'label'     => $_cat->getName(),
                                    'value'     => $_cat->getId(),
                                    'id'        => $_cat->getId(),
                                    'parent_id' => $_cat->getParentId(),
                                    'level'     => 0,
                                    'postion'   => $_cat->getCatPosition(),
                                   ];
                $children[]      = $this->drawItems($collection, $cat1, ($level + 1));
                $cat['optgroup'] = $children;
            }
        }

        $cat['level'] = $level;
        return $cat;

    }//end drawItems()


    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('General Settings');

    }//end getTabLabel()


    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('General Settings');

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
