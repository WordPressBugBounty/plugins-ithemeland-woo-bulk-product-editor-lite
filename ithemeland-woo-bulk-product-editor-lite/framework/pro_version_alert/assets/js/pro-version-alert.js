jQuery(document).ready(function ($) {
    "use strict";

    $(document).on('click', '.wcbel-pro-version-alert-dismiss-button', function () {
        $('#wcbel-pro-version-alert').slideUp(250);

        $.ajax({
            url: WCBEL_PRO_VERSION_ALERT.ajax_url,
            type: 'post',
            dataType: 'json',
            data: {
                action: 'wcbel_pro_version_alert_dismiss',
                nonce: WCBEL_PRO_VERSION_ALERT.ajax_nonce,
            },
            success: function (response) { },
            error: function () { }
        });
    });
});