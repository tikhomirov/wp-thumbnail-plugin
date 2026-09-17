jQuery(function ($) {
    var frame;

    function setPreview(url) {
        var $preview = $('#kama-thumb-no-photo-preview');

        if (url) {
            $preview.attr('src', url).show();
        } else {
            $preview.attr('src', '').hide();
        }
    }

    $('#kama-thumb-no-photo-select').on('click', function (event) {
        event.preventDefault();

        if (frame) {
            frame.open();
            return;
        }

        frame = wp.media({
            title: 'Choose placeholder image',
            button: { text: 'Use image' },
            multiple: false
        });

        frame.on('select', function () {
            var attachment = frame.state().get('selection').first().toJSON();
            $('#kama_thumb_no_photo_url').val(attachment.id);
            setPreview(attachment.url);
        });

        frame.open();
    });

    $('#kama-thumb-no-photo-clear').on('click', function (event) {
        event.preventDefault();
        $('#kama_thumb_no_photo_url').val('');
        setPreview('');
    });
});
