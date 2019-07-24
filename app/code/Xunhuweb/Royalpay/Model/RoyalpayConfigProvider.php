<?php
/**
 * Copyright © 2016 Xunhuweb. All rights reserved.
 * See more information at http://www.hellomagento2.com
 */
namespace Xunhuweb\Royalpay\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use Xunhuweb\Royalpay\Model\Royalpay;

class RoyalpayConfigProvider implements ConfigProviderInterface
{
    /**
     * @var PaymentHelper
     */
    protected $_paymentHelper;

    /**
     * @var \Xunhuweb\Royalpay\Model\Royalpay
     */
    protected $_royalpayDirect;

    /**
     * @param PaymentHelper $paymentHelper
     */
    public function __construct(
        PaymentHelper $paymentHelper
    ) {
        $this->_paymentHelper = $paymentHelper;
        $this->_royalpayDirect = $this->_paymentHelper->getMethodInstance(Royalpay::ALIPAY_DIRECT_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $config = [
            'payment' => [
                'royalpaydirect' => [
                    'redirectUrl' => $this->getRedirectUrl(),
                    'royalpayLogoUrl' => $this->getRoyalpayLogoUrl()
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
        return $this->_royalpayDirect->getRedirectUrl();
    }

    protected function getRoyalpayLogoUrl()
    {
        return $this->_royalpayDirect->getRoyalpayLogoUrl();
    }
}
