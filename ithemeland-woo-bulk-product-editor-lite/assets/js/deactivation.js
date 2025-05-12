jQuery(document).ready(function ($) {
    "use strict";

    $(document).on('click', '#deactivate-ithemeland-woo-bulk-product-editor-lite', function (event) {
        event.preventDefault();
        $('#wcbe-deactivation-popup').show();
    });

    $(document).on('click', '#wcbe-deactivation-popup-close', function () {
        $('#wcbe-deactivation-popup').hide();
    });

    $(document).on('click', '#wcbe-deactivation-popup-deactivate', function () {
        $('.wcbe-deactivation-loading').show();
        if ('license_plugin' === $('.wcbe-deactivation-option:checked').val()) {
            $.ajax({
                url: WCBE_DATA.ajax_url,
                type: 'post',
                dataType: 'json',
                data: {
                    action: 'wcbe_deactivation_plugin',
                    nonce: WCBE_DATA.ajax_nonce,
                },
                success: function (response) {
                    window.location.href = $('#deactivate-ithemeland-woo-bulk-product-editor-lite').attr('href');
                },
                error: function () {
                    window.location.href = $('#deactivate-ithemeland-woo-bulk-product-editor-lite').attr('href');
                }
            })
        } else {
            window.location.href = $('#deactivate-ithemeland-woo-bulk-product-editor-lite').attr('href');
        }
    });
});