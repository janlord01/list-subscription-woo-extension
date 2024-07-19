jQuery(document).ready(function($){
    // Handle profile photo upload
    $('#upload_profile_photo_button').click(function(e) {
        e.preventDefault();
        var button = $(this);
        var custom_uploader = wp.media({
            title: 'Select Profile Photo',
            button: {
                text: 'Use this photo'
            },
            multiple: false
        }).on('select', function() {
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            $('#profile_photo').val(attachment.url);
        }).open();
    });

    // Handle additional photos upload
    $('.upload_additional_photo_button').click(function(e) {
        e.preventDefault();
        var button = $(this);
        var custom_uploader = wp.media({
            title: 'Select Photo',
            button: {
                text: 'Use this photo'
            },
            multiple: false
        }).on('select', function() {
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            button.prev('.additional_photo').val(attachment.url);
        }).open();
    });

    // Add new service field
    $('#add_service').click(function(e) {
        e.preventDefault();
        var serviceList = $('#service_list');
        var count = serviceList.children('.service-item').length;
        serviceList.append(
            '<div class="service-item">' +
            '<input type="text" name="services[' + count + '][name]" placeholder="Service Name" />' +
            '<input type="text" name="services[' + count + '][price]" placeholder="Price" />' +
            '<button class="remove-service button">Remove</button>' +
            '</div>'
        );
    });

    // Remove service field
    $(document).on('click', '.remove-service', function(e) {
        e.preventDefault();
        $(this).parent('.service-item').remove();
    });
});

