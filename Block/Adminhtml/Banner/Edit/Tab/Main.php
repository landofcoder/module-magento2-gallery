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
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Convert\DataObject as ObjectConverter;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_videoType;
    protected $_systemStore;
    protected $_wysiwygConfig;
    /**
     * @var GroupRepositoryInterface
     */
    protected $groupRepository;
    protected $_viewHelper;
    /**
     * @var SearchCriteriaBuilder
     */
    protected $_searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Convert\DataObject
     */
    protected $_objectConverter;

    /**
     * [__construct description]
     * @param \Magento\Backend\Block\Template\Context                       $context               
     * @param \Magento\Framework\Registry                                   $registry              
     * @param \Magento\Framework\Data\FormFactory                           $formFactory           
     * @param GroupRepositoryInterface                                      $groupRepository       
     * @param ObjectConverter                                               $objectConverter       
     * @param SearchCriteriaBuilder                                         $searchCriteriaBuilder 
     * @param \Magento\Store\Model\System\Store                             $systemStore           
     * @param \Magento\Email\Model\ResourceModel\Template\CollectionFactory $templatesFactory      
     * @param \Magento\Email\Model\Template\Config                          $emailConfig           
     * @param array                                                         $data                  
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        GroupRepositoryInterface $groupRepository,
        ObjectConverter $objectConverter,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Email\Model\ResourceModel\Template\CollectionFactory $templatesFactory,
        \Magento\Email\Model\Template\Config $emailConfig,
        \Lof\Gallery\Helper\Data $viewHelper,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Lof\Gallery\Model\Config\Source\VideoType $videotype,
        array $data = []
        ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_viewHelper = $viewHelper;
        $this->_systemStore = $systemStore;
        $this->groupRepository = $groupRepository;
        $this->_objectConverter = $objectConverter;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_templatesFactory = $templatesFactory;
        $this->_emailConfig = $emailConfig;
        $this->_videoType = $videotype;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('lofgallery_banner');

        if ($this->_isAllowedAction('Lof_Gallery::banner_edit')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('banner_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Banner Information')]);
        if ($model->getId()) {
            $fieldset->addField('banner_id', 'hidden', ['name' => 'banner_id']);
        }

        $fieldset->addField(
            'title',
            'text', 
            [
            'name' => 'title',
            'label' => __('Banner Title'),
            'title' => __('Banner Title'),
            'required' => true,
            'disabled' => $isElementDisabled
            ]
            );
        $fieldset->addField(
            'identifier',
            'text',
            [
            'name' => 'identifier',
            'label' => __('Identifier'),
            'title' => __('Identifier'),
            'class' => 'validate-identifier',
            'required' => true,
            'note' => __('Relative to Web Site Base URL'),
            'disabled' => $isElementDisabled
            ]
            );
        $fieldset->addField(
            'link',
            'text',
            [
            'name' => 'link',
            'label' => __('Url image'),
            'title' => __('Url image'),
            'required' => false,
            'note' => __('Input path http:// or Choose image file'),
            'disabled' => $isElementDisabled
            ]
            );
        $fieldset->addField(
            'file',
            'image',
            [
            'name'     => 'file',
            'label'    => __('Image'),
            'required' => false,
            'disabled' => $isElementDisabled
            ]
            );
        $fieldset->addField(
            'show_image',
            'select',
            [
            'label' => __('Show Image'),
            'title' => __('Show Image'),
            'name' => 'show_image',
            'options' => ['image_file' => 'Show Image File', 'image_url' => 'Show Image URL'],
            'disabled' => $isElementDisabled
            ]
            );

        $fieldset->addField(
            'video_type',
            'select',
            [
                'label' => __('Video Types'),
                'title' => __('Video Types'),
                'name' => 'video_type',
                'options' => $this->_videoType->toOptionArray(),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'video_id',
            'text',
            [
                'name' => 'video_id',
                'label' => __('Video ID'),
                'title' => __('Video ID'),
                'after_element_html' => 'For Examples:<br/> 1. Youtube<br/> Link: https://www.youtube.com/watch?v=BBvsB5PcitQ<br/> VideoID: <strong>BBvsB5PcitQ</strong><br/>2. Vimeo<br/> Link: https://vimeo.com/145947876<br/> VideoID: <strong>145947876</strong>'
            ]
            ); 
        $fieldset->addField(
            'label',
            'text',
            [
            'name'     => 'label',
            'label'    => __('Label'),
            'required' => false,
            'disabled' => $isElementDisabled
            ]
            );
 
        $fieldset->addField(
                'open_link',
                'text',
                [
                'name' => 'open_link',
                'label' => __('Custom Open Link'),
                'title' => __('Custom Open Link'),
                'required' => false,
                'note' => __('Input custom link to click on image to open the link. Empty to not use it.'),
                'disabled' => $isElementDisabled
                ]
                );
        $wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId().time()]);
        $fieldset->addField(
            'description',
            'editor',
            [
            'name'     => 'description',
            'label'    => __('Description'),
            'style' => 'height:20em;',
            'disabled' => $isElementDisabled,
            'config' => $wysiwygConfig
            ]
            );
        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        $fieldset->addField(
            'created_at',
            'date',
            [
            'name'     => 'created_at',
            'label'    => __('Created time'),
            'required' => true,
            'date_format' => $dateFormat
            ]
            );

        $fieldset->addField(
            'is_active',
            'select',
            [
            'label' => __('Active'),
            'title' => __('Active'),
            'name' => 'is_active',
            'options' => $model->getAvailableStatuses(),
            'disabled' => $isElementDisabled
            ]
            );
        if (!$model->getId()) {
            $model->setData('submit_button_text', __('Click here'));
            $model->setData('is_active', $isElementDisabled ? '0' : '1');
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
        return __('Banner Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Banner Information');
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
