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
 * @package    Lof_Formbuilder
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\Formbuilder\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class ModelActions extends Column
{
    /** Url path */
    const FORM_URL_PATH_EDIT = 'formbuilder/model/edit';
    const FORM_URL_PATH_DELETE = 'formbuilder/model/delete';


    /**
     * @var string
     */
    private $editUrl;

    /**
     * @param ContextInterface   $context            
     * @param UiComponentFactory $uiComponentFactory          
     * @param array              $components         
     * @param array              $data               
     * @param [type]             $editUrl            
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = [],
        $editUrl = self::FORM_URL_PATH_EDIT
        ) {
        $this->editUrl = $editUrl;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['model_id'])) {
                    $item[$name]['edit'] = [
                    'href' => $this->urlBuilder->getUrl($this->editUrl, ['model_id' => $item['model_id']]),
                    'label' => __('Edit')
                    ];
                }
            }
        }
        return $dataSource;
    }
}
