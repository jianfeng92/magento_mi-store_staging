/**
 * Copyright © 2016 Xunhuweb. All rights reserved.
 * See more information at http://www.hellomagento2.com
 */
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'royalpayalidirect',
                component: 'Xunhuweb_Royalpayali/js/view/payment/method-renderer/royalpayali-direct-method'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);