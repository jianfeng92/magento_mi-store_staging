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
        $.widget('backorder.view', {
            options: {},
            _create: function () {
                var self = this;
                $(document).ready(function () {
                    var url = self.options.url;
                    var msg = self.options.msg;
                    var flag = self.options.flag;
                    var productId = self.options.productId;
                    var addToCartButtonLabel = $("#product-addtocart-button span").text();
                    var stockLabel = $(".product-info-stock-sku .stock").html();             
                    var backorderLabel = 'Back Order';
                    var backorderLabelHtml = '<span class="in-stock-status back-order-status">' + backorderLabel + '</span>';
                    var outofstockLabel = 'Out of stock';
                    var outofstockLabelHtml = '<span class="out-of-stock-status">Out of stock</span>';
                    var limitedstockLabel = '<span class="limited-stock-status">Limited Stock</span>';
                    msg = msg.replace(/\n/g, "<br />");
                    var count = 0;
                    var isbackorder = flag;
                    if (isbackorder == 1) {
                        setbackorderLabel();
                        $(".product-info-price").after(msg);
                    }
                    else if (isbackorder == 2) {
                        setLimitedStockLabel();
                    }
                    else if (isbackorder == 3) {
                        setOutOfStockLabel();
                    }
                    
                    $('#product-addtocart-button').click(function () {
                        count = 0;
                    });
                    $('#product-addtocart-button span').bind("DOMSubtreeModified",function () {
                        var title = $(this).text();
                        if (isbackorder == 1) {
                            if (title == addToCartButtonLabel) {
                                count++;
                                if (count == 1) {
                                    setbackorderLabel();
                                }
                            }
                        } 
                        else if (isbackorder == 2) {
                            if (title == addToCartButtonLabel) {
                                count++;
                                if (count == 1) {
                                    setLimitedStockLabel();
                                }
                            }
                        }
                        else if (isbackorder == 3) {
                            if (title == addToCartButtonLabel) {
                                count++;
                                if (count == 1) {
                                    setOutOfStockLabel();
                                }
                            }
                        }
                        
                    });
                    $('#product-options-wrapper .super-attribute-select').change(function () {
                        //$(".lof-msg-box").remove();
                        if($('#backorder_eta').length < 1)
                        $(".product-info-price").after('<div id="backorder_eta"></div>');
                        setDefaultLabel();
                        var flag = 1;
                        setTimeout(function () {
                            $("#product_addtocart_form input[type='hidden']").each(function () {
                                $('#product-options-wrapper .super-attribute-select').each(function () {
                                    if ($(this).val() == "") {
                                        flag = 0;
                                    }
                                });
                                var name = $(this).attr("name");
                                if (name == "selected_configurable_option") {
                                    var productId = $(this).val();
                                    if (productId != "" && flag ==1) {
                                        $(".lof-loading-mask").removeClass("lof-display-none");
                                        $.ajax({
                                            url: url,
                                            type: 'POST',
                                            data: { product_id : productId },
                                            dataType: 'json',
                                            success: function (data) {
                                                if (data.backorder == 1) {
                                                    setbackorderLabel();
                                                    isbackorder = 1;
                                                    $("div#backorder_eta").html(data.msg);
                                                }
                                                else if (data.backorder == 2) {
                                                    setLimitedStockLabel();
                                                    isbackorder = 2;
                                                }
                                                else if (data.backorder == 3) {
                                                    setOutOfStockLabel();
                                                    isbackorder = 3;
                                                }
                                                else {
                                                    setDefaultLabel();
                                                    isbackorder = 0;
                                                }
                                                $(".lof-loading-mask").addClass("lof-display-none");
                                            }
                                        });
                                    }
                                }
                            });
                            showButton();
                        }, 0);
                    });
    
                    $('body').on('click', '#product-options-wrapper .swatch-option', function () {
                        var flag = 1;
                        var attributeInfo = {};
                        $(".lof-msg-box").remove();
                        if($('#backorder_eta').length < 1)
                        $(".product-info-price").after('<div id="backorder_eta"></div>');
                        setDefaultLabel();
                        setTimeout(function () {
                            $('#product-options-wrapper .swatch-attribute').each(function () {
                                if($(this).attr('option-selected')) {
                                    var selectedOption = $(this).attr("option-selected");
                                    var attributeId = $(this).attr("attribute-id");
                                    attributeInfo[attributeId] = selectedOption;
                                } else {
                                    flag = 0;
                                    showButton();
                                }
                            });
                            if(flag == 1) {
                                 var selected_options = {};
                                jQuery('div.swatch-attribute').each(function(k,v){
                                    var attribute_id    = jQuery(v).attr('attribute-id');
                                    var option_selected = jQuery(v).attr('option-selected');
                                    //console.log(attribute_id, option_selected);
                                    if(!attribute_id || !option_selected){ return;}
                                    selected_options[attribute_id] = option_selected;
                                });
    
                                var product_id_index = jQuery('[data-role=swatch-options]').data('miSwatchRenderer').options.jsonConfig.index;
                                var found_ids = [];
                                jQuery.each(product_id_index, function(product_id,attributes){
                                    var productIsSelected = function(attributes, selected_options){
                                        return _.isEqual(attributes, selected_options);
                                    }
                                    if(productIsSelected(attributes, selected_options)){
                                        found_ids.push(product_id);
                                    } 
                                });
        
                                $(".lof-loading-mask").removeClass("lof-display-none");
                                $.ajax({
                                    url: url,
                                    type: 'POST',
                                    data: { type : 1, product_id : found_ids[0], info : attributeInfo },
                                    dataType: 'json',
                                    success: function(data) {
                                        if (data.backorder == 1) {
                                            setbackorderLabel();
                                            isbackorder = 1;
                                            $("div#backorder_eta").html(data.msg)
                                           
                                        } 
                                        else if (data.backorder == 2) {
                                            setLimitedStockLabel();
                                            isbackorder = 2;
                                        }
                                        else if (data.backorder == 3) {
                                            setOutOfStockLabel();
                                            isbackorder = 3;
                                        } 
                                        else {
                                            setDefaultLabel();
                                            isbackorder = 0;
                                        }
                                        $(".lof-loading-mask").addClass("lof-display-none");
                                    }
                                });
                            }
                        }, 0);
                    });
                    function setbackorderLabel()
                    {
                        $("#product-addtocart-button").show().attr("title",backorderLabel);
                        $('.field.qty').show();
                        $("#product-addtocart-button span, .float-cart-action").text(backorderLabel);
                        $(".product-info-stock-sku .stock").html(backorderLabelHtml);
                    }
                    
                    function setLimitedStockLabel()
                    {
                        $("#product-addtocart-button").show().attr("title",addToCartButtonLabel);
                        $('.field.qty').show();
                        $("#product-addtocart-button span, .float-cart-action").text(addToCartButtonLabel);
                        $(".product-info-stock-sku .stock").html(limitedstockLabel);
                        $('div.availability.only').remove();
                        $('div.lof-availability-block').remove();
                    }	
                    
                    function setOutOfStockLabel()
                    {
                        $("#product-addtocart-button").attr("title",outofstockLabel).hide();
                        $("#product-addtocart-button span, .float-cart-action").text(outofstockLabel);
                        $('.field.qty').hide();
                        $(".product-info-stock-sku .stock").html(outofstockLabelHtml);
                        $('div.availability.only').remove();
                        $('div.lof-availability-block').remove();
                    }			
                    
                    
                    function setDefaultLabel()
                    {
                        $("#product-addtocart-button").attr("title",addToCartButtonLabel);
                        $("#product-addtocart-button span, .float-cart-action").text(addToCartButtonLabel);
                        $(".product-info-stock-sku .stock").html(stockLabel);
                        $('div.lof-availability-block').remove();
                    }
                    function showButton() {
                        $("#product-addtocart-button").show();
                        $('.field.qty').show();
                    }
                });
            }
        });
        return $.backorder.view;
    });
    
    