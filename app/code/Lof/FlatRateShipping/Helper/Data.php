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


namespace Lof\FlatRateShipping\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\Session       $customerSession
     */
    
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $customerSession
    ) {
        parent::__construct($context);
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_customerSession = $customerSession;
    }

    /**
     * get shipping is enabled or not for system config.
     */
    public function getFlatRateShippingEnabled()
    {
        return $this->_scopeConfig->getValue(
            'carriers/lofflatrateshipping/active',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
     /**
      * get multi shipping is enabled or not for system config.
      */
    public function getMpmultishippingEnabled()
    {
        return $this->_scopeConfig->getValue(
            'carriers/mp_multi_shipping/active',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * get table rate shipping title from system config.
     */
    public function getFlatRateShippingTitle()
    {
        return $this->_scopeConfig->getValue(
            'carriers/lofflatrateshipping/title',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * get table rate shipping name from system config.
     */
    public function getFlatRateShippingName()
    {
        return $this->_scopeConfig->getValue(
            'carriers/lofflatrateshipping/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * get table rate shipping allow admin settings from system config.
     */
    public function getFlatRateShippingAllowadmin()
    {
        return $this->_scopeConfig->getValue(
            'carriers/lofflatrateshipping/allowadmin',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * get customer id from customer session.
     */
    public function getPartnerId()
    {
        $partnerId = $this->_customerSession->getCustomerId();
        return $partnerId;
    }
}
