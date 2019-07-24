<?php
/**
 * Copyright © 2016 Xunhuweb. All rights reserved.
 * See more information at http://www.hellomagento2.com
 */
namespace Xunhuweb\Royalpayali\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use Xunhuweb\Royalpayali\Model\Royalpayali;

class RoyalpayaliConfigProvider implements ConfigProviderInterface
{
    /**
     * @var PaymentHelper
     */
    protected $_paymentHelper;

    /**
     * @var \Xunhuweb\Royalpayali\Model\Royalpayali
     */
    protected $_royalpayaliDirect;

    /**
     * @param PaymentHelper $paymentHelper
     */
    public function __construct(
        PaymentHelper $paymentHelper
    ) {
        $this->_paymentHelper = $paymentHelper;
        $this->_royalpayaliDirect = $this->_paymentHelper->getMethodInstance(Royalpayali::ALIPAY_DIRECT_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $config = [
            'payment' => [
                'royalpayalidirect' => [
                    'redirectUrl' => $this->getRedirectUrl(),
                    'royalpayaliLogoUrl' => $this->getRoyalpayaliLogoUrl()
                ]
            ]
        ];

        return $config;
    }

    /**
     * Return redirect URL for method
     *
     * @return string url
     */
    protected function getRedirectUrl()
    {
        return $this->_royalpayaliDirect->getRedirectUrl();
    }

    protected function getRoyalpayaliLogoUrl()
    {
        return $this->_royalpayaliDirect->getRoyalpayaliLogoUrl();
    }
}
