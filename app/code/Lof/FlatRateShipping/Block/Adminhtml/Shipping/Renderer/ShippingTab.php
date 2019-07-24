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
 * @package    Lof_FlatRateShipping
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\FlatRateShipping\Block\Adminhtml\Shipping\Renderer;
use Lof\FlatRateShipping\Model\ShippingmethodFactory;
class ShippingTab  extends \Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element

{
   
    /**
     * @var string
     */
    protected $_template = 'shipping/shippingtab.phtml';

    /**
     * Prepare global layout
     * Add "Add tier" button to layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
}