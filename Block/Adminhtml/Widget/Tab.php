<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_Gallery
 * @copyright  Copyright (c) 2014 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\Gallery\Block\Adminhtml\Widget;

class Tab extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
        ){
        parent::__construct($context, $data);

    }
    /** Rows cache
     *
     * @var array|null
     */
    private $_arrayRowsCache;
    /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender()
    {
    	$this->addColumn(
    		'large_desktop',
    		['label' => __('Large Desktop'),'style' => 'width:100px']
    		);
    	$this->addColumn(
    		'desktop',
    		['label' => __('Desktop'),'style' => 'width:100px']
    		);
        $this->addColumn(
            'tablet',
            [
            'label' => __('Tablet'),
            'style' => 'width:100px']
            );
         $this->addColumn(
            'mobile',
            [
            'label' => __('Mobile'),
            'style' => 'width:100px']
            );
 
        $this->_addAfter = false;
    }


    /**
     * Prepare existing row data object
     *
     * @param \Magento\Framework\DataObject $row
     * @return void
     */

    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $optionExtraAttr = [];
        $row->setData(
            'option_extra_attrs',
            $optionExtraAttr
            );
    }

    /**
     * Obtain existing data from form element
     *
     * Each row will be instance of Varien_Object
     *
     * @return array
     */
   public function getArrayRows()
    {
        if (null !== $this->_arrayRowsCache) {
            return $this->_arrayRowsCache;
        }
        $result = [];
        $temp = []; // save item position
        /** @var \Magento\Framework\Data\Form\Element\AbstractElement */
        $element = $this->getElement();
        $value = $element->getValue();
        if(is_array($value)){
            unset($value['__empty']);
        }
        if(!is_array($value)){
            if(base64_decode($value, true) == true){
                $value = base64_decode($value);
                if(base64_decode($value, true) == true) {
                    $value = base64_decode($value);
                }
            }
            $value = unserialize($value);
        }
        if ( $value && is_array($value) ) {
            foreach ($value as $rowId => $row) {
                if(is_array($row)){
                    $rowColumnValues = [];
                    foreach ($row as $key => $row_value) {
                        $row[$key] = $this->escapeHtml($row_value);
                        $row[$key] = htmlspecialchars_decode($row_value);
                        $rowColumnValues[$this->_getCellInputElementId($rowId, $key)] = $row[$key];
                    }
                    $temp[$rowId] = $rowId;
                    $row['_id'] = $rowId;
                    $row['column_values'] = $rowColumnValues;
                    $result[$rowId] = new \Magento\Framework\DataObject($row);
                    $this->_prepareArrayRow($result[$rowId]);
                }
            }
        }
        //asort($temp);
        $rows = [];
        foreach ($temp as $k => $v) {
            $rows[$k] = $result[$k];
        }
        $this->_arrayRowsCache = $rows;
        return $this->_arrayRowsCache;
    }



    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_isPreparedToRender) {
            $this->_prepareToRender();
            $this->_isPreparedToRender = true;
        }
        if (empty($this->_columns)) {
            throw new Exception('At least one column must be defined.');
        }
        return parent::_toHtml();
    }
}