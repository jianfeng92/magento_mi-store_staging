/**
 * Copyright © 2016 Xunhuweb. All rights reserved.
 * See more information at http://www.hellomagento2.com
 */
define(
    [
        'Magento_Checkout/js/view/payment/default',
        'jquery',
        'Xunhuweb_Royalpay/js/action/set-payment-method',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Checkout/js/model/quote',
        'Magento_Customer/js/customer-data'
    ],
    function (Component, $, setPaymentMethodAction, additionalValidators, quote, customerData) {
        'use strict';
 
        return Component.extend({
            defaults: {
                template: 'Xunhuweb_Royalpay/payment/royalpaydirect'
            },
            /** Redirect to Royalpay*/
            continueToRoyalpay: function () {
                if (additionalValidators.validate()) {
                    this.selectPaymentMethod();
                    setPaymentMethodAction(this.messageContainer).done(
                        function () {
                            customerData.invalidate(['cart']);
                            $.mage.redirect(window.checkoutConfig.payment.royalpaydirect.redirectUrl);
                        }
                    );
                    return false;
                }
            },
            getRoyalpayLogoSrc: function () {
                return window.checkoutConfig.payment.royalpaydirect.royalpayLogoUrl;
            },
        });
    }
);