jQuery(document).ready(function ($) {
    "use strict";

    $(document).on('click', '.wcbel-renew-license-alert-dismiss-button', function () {
        $('#wcbel-renew-license-alert').slideUp(250);

        $.ajax({
            url: WCBEL_PRO_VERSION_ALERT.ajax_url,
            type: 'post',
            dataType: 'json',
            data: {
                action: 'wcbel_renew_license_alert_dismiss',
                nonce: WCBEL_PRO_VERSION_ALERT.ajax_nonce,
            },
            success: function (response) { },
            error: function () { }
        });
    });
});