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
namespace Lof\Gallery\Block\Adminhtml\System\Config\Form\Field;

use Magento\Backend\Block\Template;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

class WidgetHeading extends Template implements RendererInterface
{
	/**
	 * @param  AbstractElement $element 
	 * @return string                   
	 */
	public function render(AbstractElement $element)
	{
		$html = '';
		$html .= '<div class="system-heading" style="text-align: center;background: #eb5202;padding: 10px;color: #FFF;font-weight: 600;font-size: 1.7rem;margin-bottom: 20px;margin-top: 20px;">';
		$html .= $element->getLabel();
		$html .= '</div>';
		return $html;
	}
}