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
namespace Lof\Gallery\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Eav\Api\AttributeSetRepositoryInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Phrase;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Modal;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;

class Banner extends AbstractModifier
{
    const DATA_SCOPE = '';
    const DATA_SCOPE_BANNER = 'banner';
    const GROUP_BANNER = 'banner';
    const GROUP_CONTENT = 'content';
    const SORT_ORDER = 20;

    /**
     * @var string
     */
    private static $previousGroup = 'search-engine-optimization';

    /**
     * @var int
     */
    private static $sortOrder = 90;

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;


    /**
     * @var ImageHelper
     */
    protected $imageHelper;

    /**
     * @var Status
     */
    protected $status;

    /**
     * @var AttributeSetRepositoryInterface
     */
    protected $attributeSetRepository;

    /**
     * @var string
     */
    protected $scopeName;

    protected $_resource;
    /**
     * @var string
     */
    protected $scopePrefix;

    /**
     * @var \Magento\Catalog\Ui\Component\Listing\Columns\Price
     */
    private $priceModifier;
    /**
     * Request instance
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;
    /**
     * @param LocatorInterface $locator
     * @param UrlInterface $urlBuilder
     * @param BannerLinkRepositoryInterface $bannerLinkRepository
     * @param ProductRepositoryInterface $productRepository
     * @param ImageHelper $imageHelper
     * @param Status $status
     * @param AttributeSetRepositoryInterface $attributeSetRepository
     * @param string $scopeName
     * @param string $scopePrefix
     */
    public function __construct(
        LocatorInterface $locator,
        UrlInterface $urlBuilder,
        ImageHelper $imageHelper,
        Status $status,
        AttributeSetRepositoryInterface $attributeSetRepository,
        \Magento\Framework\App\ResourceConnection $resource,
        RequestInterface $request,
        $scopeName = 'product_form.product_form',
        $scopePrefix = ''
    ) {
        $this->locator = $locator;
        $this->urlBuilder = $urlBuilder;
        $this->imageHelper = $imageHelper;
        $this->status = $status;
        $this->attributeSetRepository = $attributeSetRepository;
        $this->scopeName = $scopeName;
        $this->scopePrefix = $scopePrefix;
        $this->request = $request;
        $this->_resource = $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        if (!$this->locator->getProduct()->getId()) {
            return $meta;
        }
        
         $meta = array_replace_recursive(
            $meta,
            [
                static::GROUP_BANNER => [
                    'children' => [
                        $this->scopePrefix . static::DATA_SCOPE_BANNER => $this->getBannerFieldset(),
                    ],
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Banner'),
                                'collapsible' => true,
                                'componentType' => Fieldset::NAME,
                                'dataScope' => static::DATA_SCOPE,
                                'sortOrder' =>
                                    $this->getNextGroupSortOrder(
                                        $meta,
                                        self::$previousGroup,
                                        self::$sortOrder
                                    ),
                            ],
                        ],

                    ],
                ],
            ]
        );
        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->locator->getProduct();
        $productId = $product->getId();

        if (!$productId) {
            return $data;
        }

        $priceModifier = $this->getPriceModifier();
        /**
         * Set field name for modifier
         */
        $priceModifier->setData('name', 'price');

        foreach ($this->getDataScopes() as $dataScope) {
            $connection = $this->_resource->getConnection();
            if($productId){
                $select = 'SELECT *  FROM  ' . $this->_resource->getTableName('lof_gallery_banner_product').' lp,'.$this->_resource->getTableName('lof_gallery_banner').' lb WHERE lp.banner_id = lb.banner_id AND lp.entity_id ='.$productId;
                $banner = $connection->fetchAll($select);
                
            }
            $dataBanner = array();
            foreach ($banner as $key => $_banner) {
                $dataBanner[$key]['id'] = $_banner['banner_id'];
                $dataBanner[$key]['title'] = $_banner['title'];
                $dataBanner[$key]['position'] = $_banner['position'];
            }
            $data[$productId]['links'][$dataScope] =  $dataBanner;
        }
        
        $data[$productId][self::DATA_SOURCE_DEFAULT]['current_product_id'] = $productId;
        $data[$productId][self::DATA_SOURCE_DEFAULT]['current_store_id'] = $this->locator->getStore()->getId();
        return $data;
    }

    /**
     * Get price modifier
     *
     * @return \Magento\Catalog\Ui\Component\Listing\Columns\Price
     * @deprecated
     */
    private function getPriceModifier()
    {
        if (!$this->priceModifier) {
            $this->priceModifier = ObjectManager::getInstance()->get(
                \Magento\Catalog\Ui\Component\Listing\Columns\Price::class
            );
        }
        return $this->priceModifier;
    }


    /**
     * Retrieve all data scopes
     *
     * @return array
     */
    protected function getDataScopes()
    {
        return [
            static::DATA_SCOPE_BANNER
        ];
    }

    /**
     * Prepares config for the Banner products fieldset
     *
     * @return array
     */
    protected function getBannerFieldset()
    {
        //var_dump($this->scopePrefix . static::DATA_SCOPE_BANNER);
        $content = __(
            'Banner products are shown to customers in addition to the item the customer is looking at.'
        );

        return [
            'children' => [
                'button_set' => $this->getButtonSet(
                    $content,
                    __('Add Banner'),
                    $this->scopePrefix . static::DATA_SCOPE_BANNER
                ),
                'modal' => $this->getGenericModal(
                    __('Add Banner'),
                    $this->scopePrefix . static::DATA_SCOPE_BANNER
                ),
                static::DATA_SCOPE_BANNER => $this->getGrid($this->scopePrefix . static::DATA_SCOPE_BANNER),
            ],
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__fieldset-section',
                        'label' => __('Banner Products'),
                        'collapsible' => false,
                        'componentType' => Fieldset::NAME,
                        'dataScope' => '',
                        'sortOrder' => 10,
                    ],
                ],
            ]
        ];
    }

    /**
     * Retrieve button set
     *
     * @param Phrase $content
     * @param Phrase $buttonTitle
     * @param string $scope
     * @return array
     */
    protected function getButtonSet(Phrase $content, Phrase $buttonTitle, $scope)
    {
        $modalTarget = $this->scopeName . '.' . static::GROUP_BANNER . '.' . $scope . '.modal';
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => 'container',
                        'componentType' => 'container',
                        'label' => false,
                        'content' => $content,
                        'template' => 'ui/form/components/complex',
                    ],
                ],
            ],
            'children' => [
                'button_' . $scope => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement' => 'container',
                                'componentType' => 'container',
                                'component' => 'Magento_Ui/js/form/components/button',
                                'actions' => [
                                    [
                                        'targetName' => $modalTarget,
                                        'actionName' => 'toggleModal',
                                    ]
                                ],
                                'title' => $buttonTitle,
                                'provider' => null,
                            ],
                        ],
                    ],

                ],
            ],
        ];
    }

    /**
     * Prepares config for modal slide-out panel
     *
     * @param Phrase $title
     * @param string $scope
     * @return array
     */
    protected function getGenericModal(Phrase $title, $scope)
    {
        $listingTarget = $scope . '_product_listing';
        //$listingTarget = 'banner_product_listing';
        $modal = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Modal::NAME,
                        'dataScope' => '',
                        'options' => [
                            'title' => $title,
                            'buttons' => [
                                [
                                    'text' => __('Cancel'),
                                    'actions' => [
                                        'closeModal'
                                    ]
                                ],
                                [
                                    'text' => __('Add Banners'),
                                    'class' => 'action-primary',
                                    'actions' => [
                                        [
                                            'targetName' => 'index = ' . $listingTarget,
                                            'actionName' => 'save'
                                        ],
                                        'closeModal'
                                    ]
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'children' => [
                $listingTarget => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'autoRender' => true,
                                'componentType' => 'insertListing',
                                'dataScope' => $listingTarget,
                                'externalProvider' => $listingTarget . '.' . $listingTarget . '_data_source',
                                'selectionsProvider' => $listingTarget . '.' . $listingTarget . '.product_columns.ids',
                                'ns' => $listingTarget,
                                'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                                'realTimeLink' => true,
                                'dataLinks' => [
                                    'imports' => false,
                                    'exports' => true
                                ],
                                'behaviourType' => 'simple',
                                'externalFilterMode' => true,
                                'imports' => [
                                    'productId' => '${ $.provider }:data.product.current_product_id',
                                    'storeId' => '${ $.provider }:data.product.current_store_id',
                                ],
                                'exports' => [
                                    'productId' => '${ $.externalProvider }:params.current_product_id',
                                    'storeId' => '${ $.externalProvider }:params.current_store_id',
                                ]
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $modal;
    }

    /**
     * Retrieve grid
     *
     * @param string $scope
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function getGrid($scope)
    {
        //$dataProvider = $scope . '_product_listing';
        $dataProvider = 'banner_product_listing';
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__field-wide',
                        'componentType' => DynamicRows::NAME,
                        'label' => null,
                        'columnsHeader' => false,
                        'columnsHeaderAfterRender' => true,
                        'renderDefaultRecord' => false,
                        'template' => 'ui/dynamic-rows/templates/grid',
                        'component' => 'Magento_Ui/js/dynamic-rows/dynamic-rows-grid',
                        'addButton' => false,
                        'recordTemplate' => 'record',
                        'dataScope' => 'data.links',
                        'deleteButtonLabel' => __('Remove'),
                        'dataProvider' => $dataProvider,
                        'map' => [
                            'id' => 'banner_id',
                            'title' => 'title',
                        ],
                        'links' => [
                            'insertData' => '${ $.provider }:${ $.dataProvider }'
                        ],
                        'sortOrder' => 2,
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => 'container',
                                'isTemplate' => true,
                                'is_collection' => true,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'dataScope' => '',
                            ],
                        ],
                    ],
                    'children' => $this->fillMeta(),
                ],
            ],
        ];
    }

    /**
     * Retrieve meta column
     *
     * @return array
     */
    protected function fillMeta()
    {
        return [
            'id' => $this->getTextColumn('id', false, __('ID'), 0),
            
            /*'images' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => Field::NAME,
                            'formElement' => Input::NAME,
                            'elementTmpl' => 'ui/dynamic-rows/cells/thumbnail',
                            'dataType' => Text::NAME,
                            'dataScope' => 'images',
                            'fit' => true,
                            'label' => __('Images'),
                            'sortOrder' => 10,
                        ],
                    ],
                ],
            ],*/
            'title' => $this->getTextColumn('title', false, __('Title'), 20),
            'position' => $this->getTextColumn('position', false, __('Position'), 60),
            'actionDelete' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'additionalClasses' => 'data-grid-actions-cell',
                            'componentType' => 'actionDelete',
                            'dataType' => Text::NAME,
                            'label' => __('Actions'),
                            'sortOrder' => 70,
                            'fit' => true,
                        ],
                    ],
                ],
            ],
            
        ];
    }

    /**
     * Retrieve text column structure
     *
     * @param string $dataScope
     * @param bool $fit
     * @param Phrase $label
     * @param int $sortOrder
     * @return array
     */
    protected function getTextColumn($dataScope, $fit, Phrase $label, $sortOrder)
    {
        $column = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Field::NAME,
                        'formElement' => Input::NAME,
                        'elementTmpl' => 'ui/dynamic-rows/cells/text',
                        'component' => 'Magento_Ui/js/form/element/text',
                        'dataType' => Text::NAME,
                        'dataScope' => $dataScope,
                        'fit' => $fit,
                        'label' => $label,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];

        return $column;
    }
}
