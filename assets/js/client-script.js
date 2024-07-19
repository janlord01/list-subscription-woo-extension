jQuery(document).ready(function($){
    // $('.upload_additional_photo_button').on('click', function(e) {
    //     e.preventDefault();
    //     var photoId = $(this).data('photo-id');
    //     var formData = new FormData();
    //     formData.append('action', 'update_additional_photo');
    //     formData.append('photo_id', photoId);
    //     formData.append('additional_photo', $('.additional_photo')[photoId].files[0]);

    //     $.ajax({
    //         type: 'POST',
    //         url: ajaxurl,
    //         data: formData,
    //         contentType: false,
    //         processData: false,
    //         success: function(response) {
    //             $('#additional_photo_' + photoId).attr('src', response.url);
    //             alert('Photo ' + (photoId + 1) + ' updated successfully!');
    //         },
    //         error: function(error) {
    //             alert('Error updating photo ' + (photoId + 1) + ': ' + error.responseText);
    //         }
    //     });
    // });

    // Add new service field
    $('#add_service').click(function(e) {
        e.preventDefault();
        var serviceList = $('#service_list');
        var count = serviceList.children('.service-item').length;
        var basicSubscriber = $('body').hasClass('basic-subscriber'); // Check if user is basic subscriber
        
        var priceInput = basicSubscriber ? 'readonly' : ''; // Disable price input for basic subscribers
        
        serviceList.append(
            '<div class="service-item">' +
            '<input type="text" name="services[' + count + '][name]" placeholder="Service Name" />' +
            '<input type="text" name="services[' + count + '][price]" placeholder="Price" ' + priceInput + ' />' +
            '<button class="remove-service button" style="background:#96201c;margin-bottom:10px;">Remove</button>' +
            '</div>'
        );
    });

    // Remove service field
    $(document).on('click', '.remove-service', function(e) {
        e.preventDefault();
        $(this).parent('.service-item').remove();
    });

    
});
