jQuery(function ($) {
    'use strict';
    var data = window.WCBEL_AI_INTEREST;
    var dialog = document.getElementById('wcbel-ai-dialog');
    if (!dialog || !data || typeof dialog.showModal !== 'function') { return; }
    var box = $(dialog), busy = false, confirmed = data.subscribed === true || data.subscribed === 1 || data.subscribed === '1', opener;
    if (confirmed) {
        box.find('.ai-intro').prop('hidden', true);
        box.find('.ai-thanks').prop('hidden', false);
    }
    $('.wcbel-ai-open').on('click', function () {
        opener = this;
        dialog.showModal();
    });
    box.find('.ai-close, .ai-later, .ai-done').on('click', function () { dialog.close(); });
    dialog.addEventListener('close', function () { if (opener) { opener.focus(); } });
    box.find('.ai-notify').on('click', function () {
        if (busy || confirmed) { return; }
        busy = true;
        var button = $(this);
        box.find('.ai-error').prop('hidden', true);
        button.prop('disabled', true).attr('aria-busy', 'true').attr('aria-label', data.sending).addClass('ai-is-sending');
        $.ajax({url: data.url, type: 'POST', dataType: 'json', timeout: 60000,
            data: {action: data.action, nonce: data.nonce}
        }).done(function (response) {
            if (!response || response.success !== true) { failed(); return; }
            confirmed = true;
            box.find('.ai-intro').prop('hidden', true);
            box.find('.ai-thanks').prop('hidden', false).trigger('focus');
        }).fail(failed).always(function () {
            busy = false;
            button.prop('disabled', false).removeAttr('aria-busy aria-label').removeClass('ai-is-sending');
        });
        function failed() { box.find('.ai-error').text(data.error).prop('hidden', false); }
    });
});
