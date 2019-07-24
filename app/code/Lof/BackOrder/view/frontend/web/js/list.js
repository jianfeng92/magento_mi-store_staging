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
 * @package    Lof_backorder
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
 
define([
"jquery",
"jquery/ui",
], function ($) {
    'use strict';
    $.widget('backorder.list', {
        options: {},
        _create: function () {
            var self = this;
            $(document).ready(function () {
                var backorderInfo = self.options.backorderInfo;
                var count = 0;
                var isbackorder = 0;
                var backorderLabel = "Back Order";
                    $(".products ol.product-items > li.product-item").each(function () {
                        var productLink = $(this).find(".product-item-link").attr("href");
                        if (backorderInfo[productLink]['backorder'] == 1) {
                            setBackOrderLabel($(this));
                        }
						else if (backorderInfo[productLink]['backorder'] == 2) {
                            setBackOrderLabel($(this));
                        }
                    });
                    $('.action.tocart').click(function () {
                        var url = $(this).parents(".product-item-info").find(".product-item-link").attr("href");
                        isbackorder = backorderInfo[url]['backorder'];
                        count = 0;
                    });
                    $('.action.tocart span').bind("DOMSubtreeModified",function () {
                        var title = $(this).text();
                        if (isbackorder == 1) {
                            if (title == "Add to Cart") {
                                count++;
                                if (count == 1) {
                                    $(this).parent().attr("title",backorderLabel);
                                    $(this).text(backorderLabel);
                                }
                            }
                        }
						
                    });
                   
					
					function setBackOrderLabel(currentObject)
                    {
                        currentObject.find(".action.tocart.primary").attr("title",backorderLabel);
                        currentObject.find(".action.tocart.primary").find("span").text(backorderLabel);
                    }
            });
        }
    });
    return $.backorder.list;
});

