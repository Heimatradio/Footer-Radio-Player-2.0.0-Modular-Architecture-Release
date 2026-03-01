jQuery(document).ready(function ($) {

    let frame;

    $('#frp-upload-cover').on('click', function (e) {

        e.preventDefault();

        if (frame) {
            frame.open();
            return;
        }

        frame = wp.media({
            title: 'Cover auswählen',
            button: { text: 'Dieses Bild verwenden' },
            multiple: false
        });

        frame.on('select', function () {

            const attachment = frame.state().get('selection').first().toJSON();

            $('#frp_cover_url').val(attachment.url);

            $('#frp-cover-preview').html(
                '<img src="' + attachment.url + '" style="max-width:120px;height:auto;">'
            );
        });

        frame.open();
    });

    $('#frp-remove-cover').on('click', function () {

        $('#frp_cover_url').val('');
        $('#frp-cover-preview').html('');
    });

});