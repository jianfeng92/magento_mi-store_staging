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

use Lof\FlatRateShipping\Model\ShippingFactory;
use Magento\Cms\Model\Wysiwyg as WysiwygModel;
use Magento\Framework\App\RequestInterface;
use Psr\Log\LoggerInterface as Logger;
use Magento\Framework\Registry;

class Builder
{
    /**
     * @var \Lof\FlatRateShipping\Model\ShippingFactory
     */
    protected $_shippingFactory;

    /**
     * @param ShippingFactory $shippingFactory
     */
    public function __construct(
        ShippingFactory $shippingFactory
    ) {
        $this->_shippingFactory = $shippingFactory;
    }

    /**
     * Build mpshipping based on user request
     *
     * @param RequestInterface $request
     * @return \Lof\FlatRateShipping\Model\Shipping
     */
    public function build(RequestInterface $request)
    {
        $rowId = (int)$request->getParam('id');
        $shipping = $this->_shippingFactory->create();
        if ($rowId) {
            try {
                $shipping->load($rowId);
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        return $shipping;
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_FlatRateShipping::shipping');
    }
}
