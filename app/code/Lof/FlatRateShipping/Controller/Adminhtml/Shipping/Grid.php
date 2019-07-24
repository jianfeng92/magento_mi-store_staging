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
 * @package    Lof_FlatRateShipping
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\FlatRateShipping\Controller\Adminhtml\Shipping;

class Grid extends \Lof\FlatRateShipping\Controller\Adminhtml\Shipping
{ /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $_resultLayoutFactory;

    /**
     * @param \Magento\Backend\App\Action\Context                      $context
     * @param \Magento\Framework\View\Result\LayoutFactory             $resultLayoutFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
         \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($context, $coreRegistry);
        $this->_resultLayoutFactory = $resultLayoutFactory;
    }
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultLayout = $this->_resultLayoutFactory->create();
        $resultLayout->getLayout()->getBlock('shipping.shipping.edit.tab.shipping');
        return $resultLayout;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_FlatRateShipping::shipping');
    }
}
