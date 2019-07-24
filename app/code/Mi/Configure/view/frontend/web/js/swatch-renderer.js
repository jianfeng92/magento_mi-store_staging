define([
    'jquery',
    'jquery/ui',
    'magento-swatch.renderer'
], function ($) {
    $.widget('mi.SwatchRenderer', $.mage.SwatchRenderer, {

        /**
         * Event for swatch options
         *
         * @param {Object} $this
         * @param {Object} $widget
         * @private
         */
        _OnClick: function ($this, $widget) {
            var $parent = $this.parents('.' + $widget.options.classes.attributeClass),
                $wrapper = $this.parents('.' + $widget.options.classes.attributeOptionsWrapper),
                $label = $parent.find('.' + $widget.options.classes.attributeSelectedOptionLabelClass),
                attributeId = $parent.attr('attribute-id'),
                $input = $parent.find('.' + $widget.options.classes.attributeInput);

            if ($widget.inProductList) {
                $input = $widget.productForm.find(
                    '.' + $widget.options.classes.attributeInput + '[name="super_attribute[' + attributeId + ']"]'
                );
            }

            if ($this.hasClass('disabled')) {
                return;
            }

            if ($this.hasClass('selected')) {
                $parent.removeAttr('option-selected').find('.selected').removeClass('selected');
                $input.val('');
                $label.text('');
                $this.attr('aria-checked', false);
            } else {
                $parent.attr('option-selected', $this.attr('option-id')).find('.selected').removeClass('selected');
                $label.text($this.attr('option-label'));
                $input.val($this.attr('option-id'));
                $input.attr('data-attr-name', this._getAttributeCodeById(attributeId));
                $this.addClass('selected');
                $widget._toggleCheckedAttributes($this, $wrapper);
            }

            $widget._Rebuild();
            // Custom Code starts
            if ($('body').hasClass('page-product-configurable')) {

                var iname = $widget.options.jsonConfig.sname[this.getProduct()];
                var impn = $widget.options.jsonConfig.smpn[this.getProduct()];
                var ipmn_label = $widget.options.jsonConfig.smpn_label[this.getProduct()];
                var $mpn = $('[data-role="mpn"]');
                if (impn) {
                    $mpn.show();
                    $mpn.find('.type').text(ipmn_label + ': ');
                    $mpn.find('.value').text(impn);
                } else {
                    $mpn.hide();
                    $mpn.find('.type').text('');
                    $mpn.find('.value').text('');
                }
                if (iname != '') {
                    $('[data-ui-id="page-title-wrapper"]').html(iname);
                }

            }
            // Custom code ends

            if ($widget.element.parents($widget.options.selectorProduct)
                .find(this.options.selectorProductPrice).is(':data(mage-priceBox)')
            ) {
                $widget._UpdatePrice();
            }

            $widget._loadMedia();
            $input.trigger('change');
        }

    });

    return $.mi.SwatchRenderer;
});