jQuery(document).ready(function($) {
    var mediaUploader;

    $('#upload_additional_photos_button').click(function(e) {
        e.preventDefault();

        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Photos',
            button: {
                text: 'Choose Photos'
            },
            multiple: true
        });

        mediaUploader.on('select', function() {
            var attachments = mediaUploader.state().get('selection').map(function(attachment) {
                attachment = attachment.toJSON();
                return attachment.id;
            });
            $('#additional_photos').val(attachments.join(','));
        });

        mediaUploader.open();
    });
});
