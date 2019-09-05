(function ($) {
    function tef_media_upload(button_class) {
        var _custom_media = true,
            _orig_send_attachment = wp.media.editor.send.attachment;
        $('body').on('click', button_class, function () {
            var button = this;
            _custom_media = true;
            wp.media.editor.send.attachment = function (props, attachment) {
                if (_custom_media) {
                    $('#tef-image').val(attachment.id);
                    $('#tef-term-image').html('<img class="tef-preview-image" src="" />');
                    $('#tef-term-image .tef-preview-image').attr('src', attachment.url);
                } else {
                    return _orig_send_attachment.apply(button, [props, attachment]);
                }
            }
            wp.media.editor.open(button);
            return false;
        });
    }
    tef_media_upload('#tef-add-media');
    $('body').on('click', '#tef-remove-media', function () {
        $('#tef-image').val('');
        $('#tef-term-image').html('<img class="tef-preview-image" src="" />');
    });
    $(document).ajaxComplete(
        function (event, xhr, settings) {
            var queryStringArr = settings.data.split('&');
            if ($.inArray('action=add-tag', queryStringArr) !== -1) {
                var xml = xhr.responseXML;
                $response = $(xml).find('term_id').text();
                if ($response !== '') {
                    $('#tef-term-image').html('');
                }
            }
        }
    );
})(window.jQuery);