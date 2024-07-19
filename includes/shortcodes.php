<?php
function restrict_content_shortcode($atts, $content = null) {
    if (is_user_logged_in()) {
        $user = wp_get_current_user();
        if (in_array('basic_subscriber', $user->roles) || in_array('gold_subscriber', $user->roles) || in_array('platinum_subscriber', $user->roles)) {
            return $content;
        } else {
            return 'You do not have access to this content.';
        }
    } else {
        return 'Please log in to view this content.';
    }
}
add_shortcode('restrict_content', 'restrict_content_shortcode');

function user_dashboard_shortcode() {

    if (is_user_logged_in()) {
    ob_start();
    $user = wp_get_current_user();
    
    $subs = get_user_meta(get_current_user_id(), 'subscription_tier', true);

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_listing'])) {
        $listing_id = absint($_POST['listing_id']);

        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $name = $first_name . ' ' . $last_name;
        $company_name = sanitize_text_field($_POST['company_name']);
        $phone_number = sanitize_text_field($_POST['phone_number']);
        $location = sanitize_text_field($_POST['location']);
        $service_title = sanitize_text_field($_POST['service_title']);
        $service_description = sanitize_textarea_field($_POST['service_description']);
        $booking_link = isset($_POST['booking_link']) ? esc_url_raw($_POST['booking_link']) : '';
        $social_media_facebook = isset($_POST['social_media_facebook']) ? esc_url_raw($_POST['social_media_facebook']) : '';
        $social_media_tiktok = isset($_POST['social_media_tiktok']) ? esc_url_raw($_POST['social_media_tiktok']) : '';
        $social_media_instagram = isset($_POST['social_media_instagram']) ? esc_url_raw($_POST['social_media_instagram']) : '';
        $social_media_twitter = isset($_POST['social_media_twitter']) ? esc_url_raw($_POST['social_media_twitter']) : '';

        if (isset($_POST['services'])) {
            $services = array_map(function ($service) {
                return [
                    'name' => sanitize_text_field($service['name']),
                    'price' => sanitize_text_field($service['price'])
                ];
            }, $_POST['services']);
        }
        $categories = isset($_POST['categories']) ? array_map('intval', $_POST['categories']) : array();

        $post_data = array(
            'ID' => $listing_id,
            'post_title' => $company_name,
            'post_author' => get_current_user_id(),
            'post_content' => $service_description,
        );

        $post_id = wp_update_post($post_data);

        if ($post_id) {
            update_post_meta($post_id, 'first_name', $first_name);
            update_post_meta($post_id, 'last_name', $last_name);
            update_post_meta($post_id, 'name', $name);
            update_post_meta($post_id, 'phone_number', $phone_number);
            update_post_meta($post_id, 'location', $location);
            update_post_meta($post_id, 'service_title', $service_title);
            update_post_meta($post_id, 'services', $services);

            wp_set_post_terms($post_id, $categories, 'category');
            if ($subs === 'gold') {
                if (isset($_POST['profile_photo_nonce']) && wp_verify_nonce($_POST['profile_photo_nonce'], 'profile_photo_upload')) {

                    // Check if file is uploaded and handle the upload
                    if (isset($_FILES['profile_photo']) && !empty($_FILES['profile_photo']['name'])) {
                        require_once(ABSPATH . 'wp-admin/includes/file.php');
                        require_once(ABSPATH . 'wp-admin/includes/media.php');
                        require_once(ABSPATH . 'wp-admin/includes/image.php');
            
                        // Upload the file
                        $profile_photo_id = media_handle_upload('profile_photo', $post_id); // Use 0 if you don't have a specific post ID
            
                        // Check for upload errors
                        if (is_wp_error($profile_photo_id)) {
                            echo '<p class="error-message">Error uploading profile photo. Please try again.</p>';
                        } else {
                            // Success, update post meta with the attachment ID
                            $profile_photo_url = wp_get_attachment_url($profile_photo_id);
                            update_post_meta($post_id, 'profile_photo', $profile_photo_url);
                            echo '<p class="success-message">Profile photo uploaded successfully!</p>';
                        }
                    } else {
                        // echo '<p class="error-message">No Profile Pciture Upload.</p>';
                    }
                } else {
                    echo '<p class="error-message">Security check failed. Please try again.</p>';
                }
            }
            if ($subs === 'platinum') {
                update_post_meta($post_id, 'booking_link', $booking_link);
                update_post_meta($post_id, 'social_media_facebook', $social_media_facebook);
                update_post_meta($post_id, 'social_media_tiktok', $social_media_tiktok);
                update_post_meta($post_id, 'social_media_instagram', $social_media_instagram);
                update_post_meta($post_id, 'social_media_twitter', $social_media_twitter);
                if (isset($_POST['profile_photo_nonce']) && wp_verify_nonce($_POST['profile_photo_nonce'], 'profile_photo_upload')) {

                    // Check if file is uploaded and handle the upload
                    if (isset($_FILES['profile_photo']) && !empty($_FILES['profile_photo']['name'])) {
                        require_once(ABSPATH . 'wp-admin/includes/file.php');
                        require_once(ABSPATH . 'wp-admin/includes/media.php');
                        require_once(ABSPATH . 'wp-admin/includes/image.php');
            
                        // Upload the file
                        $profile_photo_id = media_handle_upload('profile_photo', $post_id); // Use 0 if you don't have a specific post ID
            
                        // Check for upload errors
                        if (is_wp_error($profile_photo_id)) {
                            echo '<p class="error-message">Error uploading profile photo. Please try again.</p>';
                        } else {
                            // Success, update post meta with the attachment ID
                            $profile_photo_url = wp_get_attachment_url($profile_photo_id);
                            update_post_meta($post_id, 'profile_photo', $profile_photo_url);
                            echo '<p class="success-message">Profile photo uploaded successfully!</p>';
                        }
                    } else {
                        // echo '<p class="error-message">No Profile Pciture Upload.</p>';
                    }
                } else {
                    echo '<p class="error-message">Security check failed. Please try again.</p>';
                }
                if (isset($_POST['additional_photo_nonce']) && wp_verify_nonce($_POST['additional_photo_nonce'], 'additional_photo_upload')) {
                    $additional_photos = array();
                    require_once(ABSPATH . 'wp-admin/includes/file.php');
                    require_once(ABSPATH . 'wp-admin/includes/media.php');
                    require_once(ABSPATH . 'wp-admin/includes/image.php');
                
                    foreach ($_FILES['additional_photos']['name'] as $key => $value) {
                        if ($_FILES['additional_photos']['name'][$key]) {
                            $file = array(
                                'name'     => $_FILES['additional_photos']['name'][$key],
                                'type'     => $_FILES['additional_photos']['type'][$key],
                                'tmp_name' => $_FILES['additional_photos']['tmp_name'][$key],
                                'error'    => $_FILES['additional_photos']['error'][$key],
                                'size'     => $_FILES['additional_photos']['size'][$key]
                            );
                
                            // Temporarily reassign the single file to $_FILES for media_handle_upload()
                            $original_files = $_FILES;
                            $_FILES = array("upload_attachment" => $file);
                
                            $attachment_id = media_handle_upload("upload_attachment", $post_id);
                
                            // Restore the original $_FILES
                            $_FILES = $original_files;
                
                            if (is_wp_error($attachment_id)) {
                                echo '<p class="error-message">Error uploading additional photo: ' . $attachment_id->get_error_message() . '</p>';
                            } else {
                                $additional_photos[] = wp_get_attachment_url($attachment_id);
                            }
                        }
                    }
                
                    if (!empty($additional_photos)) {
                        update_post_meta($post_id, 'additional_photos', $additional_photos);
                    }
                }                        
                
                
            }

            // echo '<a href="' . esc_url(add_query_arg('edit_listing', $post_id, home_url('/dashboard/'))) . '" class="button">Add more photos</a>';
            echo '<p class="success-message">Listing updated successfully!</p>';
        } else {
            echo '<p class="error-message">Error adding listing. Please try again.</p>';
        }
    }
    
    
    
    ?>

    <div class="user-container clearfix">
        <div class="user-sidebar">
            <ul>
                <li>
                    <a href="<?php echo get_permalink(get_page_by_path('dashboard')); ?>">
                        <!-- Dashboard SVG Icon -->
                        <svg width="16" height="16"  viewBox="0 -0.5 25 25" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M9.918 10.0005H7.082C6.66587 9.99708 6.26541 10.1591 5.96873 10.4509C5.67204 10.7427 5.50343 11.1404 5.5 11.5565V17.4455C5.5077 18.3117 6.21584 19.0078 7.082 19.0005H9.918C10.3341 19.004 10.7346 18.842 11.0313 18.5502C11.328 18.2584 11.4966 17.8607 11.5 17.4445V11.5565C11.4966 11.1404 11.328 10.7427 11.0313 10.4509C10.7346 10.1591 10.3341 9.99708 9.918 10.0005Z" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M9.918 4.0006H7.082C6.23326 3.97706 5.52559 4.64492 5.5 5.4936V6.5076C5.52559 7.35629 6.23326 8.02415 7.082 8.0006H9.918C10.7667 8.02415 11.4744 7.35629 11.5 6.5076V5.4936C11.4744 4.64492 10.7667 3.97706 9.918 4.0006Z" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M15.082 13.0007H17.917C18.3333 13.0044 18.734 12.8425 19.0309 12.5507C19.3278 12.2588 19.4966 11.861 19.5 11.4447V5.55666C19.4966 5.14054 19.328 4.74282 19.0313 4.45101C18.7346 4.1592 18.3341 3.9972 17.918 4.00066H15.082C14.6659 3.9972 14.2654 4.1592 13.9687 4.45101C13.672 4.74282 13.5034 5.14054 13.5 5.55666V11.4447C13.5034 11.8608 13.672 12.2585 13.9687 12.5503C14.2654 12.8421 14.6659 13.0041 15.082 13.0007Z" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M15.082 19.0006H17.917C18.7661 19.0247 19.4744 18.3567 19.5 17.5076V16.4936C19.4744 15.6449 18.7667 14.9771 17.918 15.0006H15.082C14.2333 14.9771 13.5256 15.6449 13.5 16.4936V17.5066C13.525 18.3557 14.2329 19.0241 15.082 19.0006Z" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="<?php echo get_permalink(get_page_by_path('subscription')); ?>">
                        <!-- Subscription SVG Icon -->
                        <svg width="16" height="16" fill="#000000" viewBox="0 0 32 32" id="icon" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <style>.cls-1{fill:none;}</style>
                            </defs>
                            <title>renew</title>
                            <path d="M12,10H6.78A11,11,0,0,1,27,16h2A13,13,0,0,0,6,7.68V4H4v8h8Z"></path>
                            <path d="M20,22h5.22A11,11,0,0,1,5,16H3a13,13,0,0,0,23,8.32V28h2V20H20Z"></path>
                            <g id="_Transparent_Rectangle_" data-name="<Transparent Rectangle>">
                                <rect class="cls-1" width="32" height="32"></rect>
                            </g>
                        </svg>
                        Subscription
                    </a>
                </li>
                <li>
                    <a href="<?php echo get_permalink(get_page_by_path('add-listing')); ?>">
                        <!-- Add Listing SVG Icon -->
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle opacity="0.5" cx="12" cy="12" r="10" stroke="#1C274C" stroke-width="1.5"></circle>
                            <path d="M15 12L12 12M12 12L9 12M12 12L12 9M12 12L12 15" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"></path>
                        </svg>
                        Add Listing
                    </a>
                </li>
                <li>
                    <a href="<?php echo wp_logout_url(); ?>">
                        <!-- Logout SVG Icon -->
                        <svg width="16" height="16" viewBox="0 -0.5 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.75 9.874C11.75 10.2882 12.0858 10.624 12.5 10.624C12.9142 10.624 13.25 10.2882 13.25 9.874H11.75ZM13.25 4C13.25 3.58579 12.9142 3.25 12.5 3.25C12.0858 3.25 11.75 3.58579 11.75 4H13.25ZM9.81082 6.66156C10.1878 6.48991 10.3542 6.04515 10.1826 5.66818C10.0109 5.29121 9.56615 5.12478 9.18918 5.29644L9.81082 6.66156ZM5.5 12.16L4.7499 12.1561L4.75005 12.1687L5.5 12.16ZM12.5 19L12.5086 18.25C12.5029 18.25 12.4971 18.25 12.4914 18.25L12.5 19ZM19.5 12.16L20.2501 12.1687L20.25 12.1561L19.5 12.16ZM15.8108 5.29644C15.4338 5.12478 14.9891 5.29121 14.8174 5.66818C14.6458 6.04515 14.8122 6.48991 15.1892 6.66156L15.8108 5.29644ZM13.25 9.874V4H11.75V9.874H13.25ZM9.18918 5.29644C6.49843 6.52171 4.7655 9.19951 4.75001 12.1561L6.24999 12.1639C6.26242 9.79237 7.65246 7.6444 9.81082 6.66156L9.18918 5.29644ZM4.75005 12.1687C4.79935 16.4046 8.27278 19.7986 12.5086 19.75L12.4914 18.25C9.08384 18.2892 6.28961 15.5588 6.24995 12.1513L4.75005 12.1687ZM12.4914 19.75C16.7272 19.7986 20.2007 16.4046 20.2499 12.1687L18.7501 12.1513C18.7104 15.5588 15.9162 18.2892 12.5086 18.25L12.4914 19.75ZM20.25 12.1561C20.2345 9.19951 18.5016 6.52171 15.8108 5.29644L15.1892 6.66156C17.3475 7.6444 18.7376 9.79237 18.75 12.1639L20.25 12.1561Z" fill="#000000"></path>
                        </svg>
                        Logout
                    </a>
                </li>
            </ul>
        </div>

        <div class="user-content">
            <!-- <h2>Dashboard</h2> -->
            <?php if(!isset($_GET['edit_listing'])){ ?>
            <h3>Existing Listings:</h3>
            <table class="user-listings-table">
                <thead>
                    <tr>
                        <th>Listing</th>
                        <!-- <th>Description</th>
                        <th>Price</th> -->
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $args = array(
                        'post_type' => 'service_listing',
                        'posts_per_page' => -1,
                        'author' => get_current_user_id(),
                    );
                    $listings = new WP_Query($args);

                    if ($listings->have_posts()) :
                        while ($listings->have_posts()) : $listings->the_post();
                            ?>
                            <tr>
                                <td data-label="Title"><?php the_title(); ?></td>
                                <!-- <td data-label="Description"><?php the_content(); ?></td>
                                <td data-label="Price"><?php echo get_post_meta(get_the_ID(), 'service_price', true); ?></td> -->
                                <td data-label="Actions">
                                    <a href="<?php echo add_query_arg('edit_listing', get_the_ID(), get_permalink(get_page_by_path('dashboard'))); ?>" class="button">Edit</a>
                                </td>
                            </tr>
                            <?php
                        endwhile;
                    else :
                        ?>
                        <tr>
                            <td colspan="4">No listings found.</td>
                        </tr>
                    <?php endif;
                    wp_reset_postdata();
                    ?>
                </tbody>
            </table>

            <?php
            }
            // Check if edit action is triggered
            if (isset($_GET['edit_listing'])) {
                $listing_id = absint($_GET['edit_listing']);
                $listing = get_post($listing_id);

                // Check if the listing exists and current user is the author
                if ($listing && $listing->post_author == get_current_user_id()) {
                    $company_name = $listing->post_title;
                    $bio = $listing->post_content;
                    $first_name = get_post_meta($listing_id, 'first_name', true);
                    $last_name = get_post_meta($listing_id, 'last_name', true);
                    $phone_number = get_post_meta($listing_id, 'phone_number', true);
                    $location = get_post_meta($listing_id, 'location', true);
                    ?>
                    <h3>Edit Listing: <span style="text-decoration: underline;"><?php echo $company_name; ?></span></h3>
                
                <!-- Form for adding new listing -->
                    <form method="post" action="" enctype="multipart/form-data">
                        <?php
                        $subs = get_user_meta(get_current_user_id(), 'subscription_tier', true);
                        if($subs){
                            // print_r($subs);
                            // exit;
                        ?>
                        <input type="hidden" name="listing_id" value="<?php echo $listing_id; ?>">
                        <input type="text" name="first_name" placeholder="First Name" value="<?php echo esc_attr($first_name); ?>" required>
                        <input type="text" name="last_name" placeholder="Last Name" value="<?php echo esc_attr($last_name); ?>" required>
                        <input type="text" name="company_name" placeholder="Company Name" value="<?php echo esc_attr($company_name); ?>" required>
                        <input type="text" name="phone_number" placeholder="Phone Number" value="<?php echo esc_attr($phone_number); ?>" required>
                        <input type="text" name="location" placeholder="Location" value="<?php echo esc_attr($location); ?>" required>
                        <?php
                             // Get all categories
                        $categories = get_categories(array(
                            'taxonomy' => 'category',
                            'hide_empty' => 0,
                        ));
                        // Get the categories assigned to the post
                        $assigned_categories = wp_get_post_terms($listing_id, 'category', array('fields' => 'ids'));
                        // Display checkboxes for each category
                        foreach ($categories as $category) {
                            ?>
                            <input type="checkbox" name="categories[]" value="<?php echo $category->term_id; ?>" 
                            <?php 
                            // Check if the category is assigned to the post
                            if (in_array($category->term_id, $assigned_categories)) {
                                echo 'checked';
                            } 
                            ?>> <?php echo $category->name; ?><br>
                            <?php
                        }
                        ?>
                        
                        <div class="meta-box-row service-box">
                            <label for="services">Services:</label>
                            <div id="service_list">
                                <?php
                                $services = get_post_meta($listing_id, 'services', true) ?? [];
                                foreach ($services as $index => $service) {
                                    ?>
                                    <div class="service-item">
                                        <input type="text" name="services[<?php echo $index; ?>][name]" value="<?php echo esc_attr($service['name']); ?>" placeholder="Service Name">
                                        <input type="text" name="services[<?php echo $index; ?>][price]" value="<?php echo esc_attr($service['price']); ?>" placeholder="Price">
                                        <button class="remove-service button" style="background-color: #96201c;margin-bottom:10px;">Remove</button>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                            <button id="add_service" class="button" style="background-color: gray;" >Add New Service</button>
                        </div>
                        
                        <?php
                        $user_id = get_current_user_id();
                        if ($subs === 'gold' || $subs == 'platinum') : ?>
                            <!-- <input type="text" name="service_price" placeholder="Service Price" required> -->
                            <!-- <textarea name="bio" placeholder="Bio"></textarea> -->

                            <div class="meta-box-row meta-box">
                            <label for="service_description" style="margin-top: 20px;"> Bio
                            <textarea name="service_description" placeholder="Bio"  required><?php echo esc_html($listing->post_content); ?></textarea>
                            </label>
                            </div>
                            <div class="meta-box-row meta-box">
                                <?php wp_nonce_field('profile_photo_upload', 'profile_photo_nonce'); ?>
                                
                                <img src="<?php echo esc_attr(get_post_meta($listing_id, 'profile_photo', true)); ?>" width="150" /><br>
                                <label for="profile_photo"> Change Profile Photo
                                <input type="file" name="profile_photo" placeholder="Change Profile Photo" accept="image/*">
                                </label>
                            </div>
                        <?php endif; ?>
                            
                        <?php if ($subs == 'platinum') : ?>

                            <div class="meta-box-row meta-box">
                                <input type="text" name="booking_link" placeholder="Link to Booking Platform" value="<?php echo esc_attr(get_post_meta($listing_id, 'booking_link', true)); ?>">
                                <input type="text" name="social_media_facebook" placeholder="Facebook Link" value="<?php echo esc_attr(get_post_meta($listing_id, 'social_media_facebook', true)); ?>" >
                                <input type="text" name="social_media_tiktok" placeholder="TikTok Link" value="<?php echo esc_attr(get_post_meta($listing_id, 'social_media_tiktok', true)); ?>">
                                <input type="text" name="social_media_instagram" placeholder="Instagram Link" value="<?php echo esc_attr(get_post_meta($listing_id, 'social_media_instagram', true)); ?>">
                                <input type="text" name="social_media_twitter" placeholder="Twitter Link" value="<?php echo esc_attr(get_post_meta($listing_id, 'social_media_twitter', true)); ?>"> <br />
                            </div>
                            <div class="meta-box-row meta-box">
                            <h3 for="additional_photos">Additional Photos:</h3>
                            <?php
                            $additional_photos = get_post_meta($listing_id, 'additional_photos', true) ?? [];
                            for ($i = 0; $i < 5; $i++) {
                                $additional_photo = $additional_photos[$i] ?? '';
                                ?>
                                <div class="photo-card">
                                    <div class="photo-card-left">
                                        <img id="additional_photo_<?php echo $i; ?>" src="<?php echo esc_attr($additional_photo) ?>" width="150" /><br>
                                    </div>
                                    <div class="photo-card-right">
                                        <h5>Change Photo <?php echo $i + 1; ?></h5>
                                        <input type="file" class="additional_photo" name="additional_photos[]" /> <br>
                                        <input type="button" class="upload_additional_photo_button button" data-photo-id="<?php echo $i; ?>" value="Save Photo" /> <br>
                                    </div>
                                    <div style="clear:both;"></div>
                                </div>
                                
                                <?php
                            }
                            ?>
                            </div>
                        <?php endif; ?>

                        <input type="submit" name="update_listing" value="Update Listing">
                        <a href="<?php echo get_permalink(get_page_by_path('dashboard')); ?>" class="button">Cancel</a>
                    </form>
                    <?php
                        }
                } else {
                    echo '<p class="error-message">You do not have permission to edit this listing.</p>';
                }
            }
            ?>
        </div>
    </div>
    <style>
        .meta-box-row.service-box, .meta-box {
            border: solid 1px #c2c2c2;
            padding: 10px;
            margin-top: 20px;
        }
        .photo-card {
            overflow: hidden; /* Clearfix to contain floated elements */
            margin-bottom: 20px; /* Example margin for spacing */
        }

        .photo-card-left {
            float: left;
            width: 150px; /* Adjust as needed */
            margin-right: 20px; /* Example margin for spacing */
        }

        .photo-card-right {
            float: left;
            width: calc(100% - 170px); /* Adjust based on left column width and margins */
        }

        .photo-card-right h5 {
            margin-top: 0; /* Remove default margin for heading */
        }

        .photo-card-right input[type="file"],
        .photo-card-right input[type="button"] {
            margin-top: 5px; /* Example margin for spacing */
        }

    </style>
    <script>
        jQuery(document).ready(function($) {
            $('.upload_additional_photo_button').on('click', function(e) {
                e.preventDefault();
                var photoId = $(this).data('photo-id');
                var formData = new FormData();
                formData.append('action', 'update_additional_photo');
                formData.append('listing_id', <?php echo $listing_id; ?>);
                formData.append('photo_id', photoId);
                formData.append('additional_photo', $('.additional_photo')[photoId].files[0]);

                $.ajax({
                    type: 'POST',
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#additional_photo_' + photoId).attr('src', response.data.url);
                        alert('Photo ' + (photoId + 1) + ' updated successfully!');
                    },
                    error: function(error) {
                        alert('Error updating photo ' + (photoId + 1) + ': ' + error.responseText);
                    }
                });
            });
        });
    </script>
    <?php
    return ob_get_clean();
    }else {
        return 'Please log in to view this content.';
    }
}
add_shortcode('user_dashboard', 'user_dashboard_shortcode');

add_action('wp_ajax_update_additional_photo', 'update_additional_photo');
function update_additional_photo() {
    $photo_id = $_POST['photo_id'];
    $listing_id = $_POST['listing_id']; // Ensure to sanitize and validate listing_id
    $additional_photos = get_post_meta($listing_id, 'additional_photos', true) ?? [];

    // Handle file upload
    $file = $_FILES['additional_photo'];
    $upload_overrides = array('test_form' => false);
    $movefile = wp_handle_upload($file, $upload_overrides);

    if ($movefile && !isset($movefile['error'])) {
        $additional_photos[$photo_id] = $movefile['url'];
        update_post_meta($listing_id, 'additional_photos', $additional_photos);
        wp_send_json_success(array('url' => $movefile['url']));
    } else {
        wp_send_json_error($movefile['error']);
    }
    wp_die();
}



function user_add_listing_shortcode() {
    if (is_user_logged_in()) {
        ob_start();
        $user = wp_get_current_user();
        $subs = get_user_meta(get_current_user_id(), 'subscription_tier', true);
        $current_user_id = get_current_user_id();
        add_upload_capability_to_user($current_user_id);
        ?>
        <div class="user-container clearfix">
            <div class="user-sidebar">
                <ul>
                    <li>
                        <a href="<?php echo get_permalink(get_page_by_path('dashboard')); ?>">
                            <!-- Dashboard SVG Icon -->
                            <svg width="16" height="16"  viewBox="0 -0.5 25 25" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M9.918 10.0005H7.082C6.66587 9.99708 6.26541 10.1591 5.96873 10.4509C5.67204 10.7427 5.50343 11.1404 5.5 11.5565V17.4455C5.5077 18.3117 6.21584 19.0078 7.082 19.0005H9.918C10.3341 19.004 10.7346 18.842 11.0313 18.5502C11.328 18.2584 11.4966 17.8607 11.5 17.4445V11.5565C11.4966 11.1404 11.328 10.7427 11.0313 10.4509C10.7346 10.1591 10.3341 9.99708 9.918 10.0005Z" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M9.918 4.0006H7.082C6.23326 3.97706 5.52559 4.64492 5.5 5.4936V6.5076C5.52559 7.35629 6.23326 8.02415 7.082 8.0006H9.918C10.7667 8.02415 11.4744 7.35629 11.5 6.5076V5.4936C11.4744 4.64492 10.7667 3.97706 9.918 4.0006Z" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M15.082 13.0007H17.917C18.3333 13.0044 18.734 12.8425 19.0309 12.5507C19.3278 12.2588 19.4966 11.861 19.5 11.4447V5.55666C19.4966 5.14054 19.328 4.74282 19.0313 4.45101C18.7346 4.1592 18.3341 3.9972 17.918 4.00066H15.082C14.6659 3.9972 14.2654 4.1592 13.9687 4.45101C13.672 4.74282 13.5034 5.14054 13.5 5.55666V11.4447C13.5034 11.8608 13.672 12.2585 13.9687 12.5503C14.2654 12.8421 14.6659 13.0041 15.082 13.0007Z" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M15.082 19.0006H17.917C18.7661 19.0247 19.4744 18.3567 19.5 17.5076V16.4936C19.4744 15.6449 18.7667 14.9771 17.918 15.0006H15.082C14.2333 14.9771 13.5256 15.6449 13.5 16.4936V17.5066C13.525 18.3557 14.2329 19.0241 15.082 19.0006Z" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo get_permalink(get_page_by_path('subscription')); ?>">
                            <!-- Subscription SVG Icon -->
                            <svg width="16" height="16" fill="#000000" viewBox="0 0 32 32" id="icon" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <style>.cls-1{fill:none;}</style>
                                </defs>
                                <title>renew</title>
                                <path d="M12,10H6.78A11,11,0,0,1,27,16h2A13,13,0,0,0,6,7.68V4H4v8h8Z"></path>
                                <path d="M20,22h5.22A11,11,0,0,1,5,16H3a13,13,0,0,0,23,8.32V28h2V20H20Z"></path>
                                <g id="_Transparent_Rectangle_" data-name="<Transparent Rectangle>">
                                    <rect class="cls-1" width="32" height="32"></rect>
                                </g>
                            </svg>
                            Subscription
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo get_permalink(get_page_by_path('add-listing')); ?>">
                            <!-- Add Listing SVG Icon -->
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle opacity="0.5" cx="12" cy="12" r="10" stroke="#1C274C" stroke-width="1.5"></circle>
                                <path d="M15 12L12 12M12 12L9 12M12 12L12 9M12 12L12 15" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"></path>
                            </svg>
                            Add Listing
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo wp_logout_url(); ?>">
                            <!-- Logout SVG Icon -->
                            <svg width="16" height="16" viewBox="0 -0.5 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M11.75 9.874C11.75 10.2882 12.0858 10.624 12.5 10.624C12.9142 10.624 13.25 10.2882 13.25 9.874H11.75ZM13.25 4C13.25 3.58579 12.9142 3.25 12.5 3.25C12.0858 3.25 11.75 3.58579 11.75 4H13.25ZM9.81082 6.66156C10.1878 6.48991 10.3542 6.04515 10.1826 5.66818C10.0109 5.29121 9.56615 5.12478 9.18918 5.29644L9.81082 6.66156ZM5.5 12.16L4.7499 12.1561L4.75005 12.1687L5.5 12.16ZM12.5 19L12.5086 18.25C12.5029 18.25 12.4971 18.25 12.4914 18.25L12.5 19ZM19.5 12.16L20.2501 12.1687L20.25 12.1561L19.5 12.16ZM15.8108 5.29644C15.4338 5.12478 14.9891 5.29121 14.8174 5.66818C14.6458 6.04515 14.8122 6.48991 15.1892 6.66156L15.8108 5.29644ZM13.25 9.874V4H11.75V9.874H13.25ZM9.18918 5.29644C6.49843 6.52171 4.7655 9.19951 4.75001 12.1561L6.24999 12.1639C6.26242 9.79237 7.65246 7.6444 9.81082 6.66156L9.18918 5.29644ZM4.75005 12.1687C4.79935 16.4046 8.27278 19.7986 12.5086 19.75L12.4914 18.25C9.08384 18.2892 6.28961 15.5588 6.24995 12.1513L4.75005 12.1687ZM12.4914 19.75C16.7272 19.7986 20.2007 16.4046 20.2499 12.1687L18.7501 12.1513C18.7104 15.5588 15.9162 18.2892 12.5086 18.25L12.4914 19.75ZM20.25 12.1561C20.2345 9.19951 18.5016 6.52171 15.8108 5.29644L15.1892 6.66156C17.3475 7.6444 18.7376 9.79237 18.75 12.1639L20.25 12.1561Z" fill="#000000"></path>
                            </svg>
                            Logout
                        </a>
                    </li>
                </ul>
            </div>
            <div class="user-content">
                
                <!-- Form for adding new listing -->
                <?php if($subs){ ?>
                <form method="post" action="" enctype="multipart/form-data">
                <?php if($subs == 'basic' || $subs == 'gold' || $subs == 'platinum') : ?>
                <input type="text" name="first_name" placeholder="First Name" required>
                <input type="text" name="last_name" placeholder="Last Name" required>
                <input type="text" name="company_name" placeholder="Company Name" required>
                <input type="text" name="phone_number" placeholder="Phone Number" required>
                <input type="text" name="location" placeholder="Location" required>

                <!-- Category selection -->
                <div class="meta-box-row" style="margin-bottom: 20px;">
                        <label for="categories">Categories:</label><br>
                        <?php
                        // Get all categories
                        $categories = get_categories(array(
                            'taxonomy' => 'category',
                            'hide_empty' => 0,
                        ));

                        // Display checkboxes for each category
                        foreach ($categories as $category) {
                            ?>
                            <input type="checkbox" name="categories[]" value="<?php echo $category->term_id; ?>"> <?php echo $category->name; ?><br>
                            <?php
                        }
                        ?>
                </div>
                
                <div class="meta-box-row" style="margin-bottom: 20px;">
                    <label for="services">Services:</label>
                    <div id="service_list">
                        <?php
                        $services = isset($_POST['services']) ? $_POST['services'] : [];
                        foreach ($services as $index => $service) {
                            ?>
                            <div class="service-item">
                                <input type="text" name="services[<?php echo $index; ?>][name]" value="<?php echo esc_attr($service['name']); ?>" placeholder="Service Name">
                                <input type="text" name="services[<?php echo $index; ?>][price]" value="<?php echo esc_attr($service['price']); ?>" placeholder="Price">
                                <button class="remove-service button" style="background:#96201c;margin-bottom:10px;">Remove</button>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <button id="add_service" class="button" style="background-color: gray;">Add Service</button>
                </div>
                
                <?php
                endif;

                if ($subs == 'gold' || $subs == 'platinum') : ?>
                <?php wp_nonce_field('profile_photo_upload', 'profile_photo_nonce'); ?>
                    <textarea name="service_description" placeholder="Bio" required></textarea>
                    <label for="profile_photo" >Profile Photo
                    <input type="file" name="profile_photo" accept="image/*"></label> <br>
                <?php endif; ?>

                <?php if ($subs == 'platinum') : ?> 
                    <input type="text" name="booking_link" placeholder="Link to Booking Platform">
                    <input type="text" name="social_media_facebook" placeholder="Facebook Link">
                    <input type="text" name="social_media_tiktok" placeholder="TikTok Link">
                    <input type="text" name="social_media_instagram" placeholder="Instagram Link">
                    <input type="text" name="social_media_twitter" placeholder="Twitter Link">
                    <div class="meta-box-row">
                        <label for="additional_photos">Additional Photos:</label>
                        <?php wp_nonce_field('additional_photo_upload', 'additional_photo_nonce'); ?>
                        <?php
                        for ($i = 0; $i < 5; $i++) {
                            ?>
                            <input type="file" class="additional_photo" name="additional_photos[]" />
                            <?php
                        }
                        ?>
                    </div>
                    
                <?php endif; ?>


                <input type="submit" class="listing_button_submit" style="margin-top: 20px;" name="submit_listing" value="Submit Listing">
            </form>
            
            <?php
                 }
            // Handle form submissions for adding new listings
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_listing'])) {
                $first_name = sanitize_text_field($_POST['first_name']);
                $last_name = sanitize_text_field($_POST['last_name']);
                $name = $first_name . ' ' . $last_name;
                $company_name = sanitize_text_field($_POST['company_name']);
                $phone_number = sanitize_text_field($_POST['phone_number']);
                $location = sanitize_text_field($_POST['location']);
                $service_title = sanitize_text_field($_POST['service_title']);
                $service_description = sanitize_textarea_field($_POST['service_description']);
                // $bio = isset($_POST['bio']) ? sanitize_textarea_field($_POST['bio']) : '';
                $booking_link = isset($_POST['booking_link']) ? esc_url_raw($_POST['booking_link']) : '';
                $social_media_facebook = isset($_POST['social_media_facebook']) ? esc_url_raw($_POST['social_media_facebook']) : '';
                $social_media_tiktok = isset($_POST['social_media_tiktok']) ? esc_url_raw($_POST['social_media_tiktok']) : '';
                $social_media_instagram = isset($_POST['social_media_instagram']) ? esc_url_raw($_POST['social_media_instagram']) : '';
                $social_media_twitter = isset($_POST['social_media_twitter']) ? esc_url_raw($_POST['social_media_twitter']) : '';

                if (isset($_POST['services'])) {
                    $services = array_map(function ($service) {
                        return [
                            'name' => sanitize_text_field($service['name']),
                            'price' => sanitize_text_field($service['price'])
                        ];
                    }, $_POST['services']);
                    // update_post_meta($post_id, 'services', $services);
                }

                // Sanitize and handle categories
                $categories = isset($_POST['categories']) ? array_map('intval', $_POST['categories']) : array();

                $post_data = array(
                    'post_title' => $company_name,
                    'post_content' => $service_description,
                    'post_type' => 'service_listing',
                    'post_status' => 'publish',
                    'post_author' => get_current_user_id(),
                );

                $post_id = wp_insert_post($post_data);

                if ($post_id) {
                    update_post_meta($post_id, 'first_name', $first_name);
                    update_post_meta($post_id, 'last_name', $last_name);
                    update_post_meta($post_id, 'name', $name);
                    update_post_meta($post_id, 'phone_number', $phone_number);
                    update_post_meta($post_id, 'location', $location);
                    update_post_meta($post_id, 'service_title', $service_title);
                    update_post_meta($post_id, 'services', $services);

                    wp_set_post_terms($post_id, $categories, 'category');

                    if ($subs === 'gold') {
                        if (isset($_POST['profile_photo_nonce']) && wp_verify_nonce($_POST['profile_photo_nonce'], 'profile_photo_upload')) {
        
                            // Check if file is uploaded and handle the upload
                            if (isset($_FILES['profile_photo']) && !empty($_FILES['profile_photo']['name'])) {
                                require_once(ABSPATH . 'wp-admin/includes/file.php');
                                require_once(ABSPATH . 'wp-admin/includes/media.php');
                                require_once(ABSPATH . 'wp-admin/includes/image.php');
                    
                                // Upload the file
                                $profile_photo_id = media_handle_upload('profile_photo', $post_id); // Use 0 if you don't have a specific post ID
                    
                                // Check for upload errors
                                if (is_wp_error($profile_photo_id)) {
                                    echo '<p class="error-message">Error uploading profile photo. Please try again.</p>';
                                } else {
                                    // Success, update post meta with the attachment ID
                                    $profile_photo_url = wp_get_attachment_url($profile_photo_id);
                                    update_post_meta($post_id, 'profile_photo', $profile_photo_url);
                                    echo '<p class="success-message">Profile photo uploaded successfully!</p>';
                                }
                            } else {
                                // echo '<p class="error-message">No Profile Pciture Upload.</p>';
                            }
                        } else {
                            echo '<p class="error-message">Security check failed. Please try again.</p>';
                        }
                    }
                    if ($subs === 'platinum') {
                        update_post_meta($post_id, 'booking_link', $booking_link);
                        update_post_meta($post_id, 'social_media_facebook', $social_media_facebook);
                        update_post_meta($post_id, 'social_media_tiktok', $social_media_tiktok);
                        update_post_meta($post_id, 'social_media_instagram', $social_media_instagram);
                        update_post_meta($post_id, 'social_media_twitter', $social_media_twitter);
                        if (isset($_POST['profile_photo_nonce']) && wp_verify_nonce($_POST['profile_photo_nonce'], 'profile_photo_upload')) {
        
                            // Check if file is uploaded and handle the upload
                            if (isset($_FILES['profile_photo']) && !empty($_FILES['profile_photo']['name'])) {
                                require_once(ABSPATH . 'wp-admin/includes/file.php');
                                require_once(ABSPATH . 'wp-admin/includes/media.php');
                                require_once(ABSPATH . 'wp-admin/includes/image.php');
                    
                                // Upload the file
                                $profile_photo_id = media_handle_upload('profile_photo', $post_id); // Use 0 if you don't have a specific post ID
                    
                                // Check for upload errors
                                if (is_wp_error($profile_photo_id)) {
                                    echo '<p class="error-message">Error uploading profile photo. Please try again.</p>';
                                } else {
                                    // Success, update post meta with the attachment ID
                                    $profile_photo_url = wp_get_attachment_url($profile_photo_id);
                                    update_post_meta($post_id, 'profile_photo', $profile_photo_url);
                                    echo '<p class="success-message">Profile photo uploaded successfully!</p>';
                                }
                            } else {
                                // echo '<p class="error-message">No Profile Pciture Upload.</p>';
                            }
                        } else {
                            echo '<p class="error-message">Security check failed. Please try again.</p>';
                        }
                        if (isset($_POST['additional_photo_nonce']) && wp_verify_nonce($_POST['additional_photo_nonce'], 'additional_photo_upload')) {
                            $additional_photos = array();
                            require_once(ABSPATH . 'wp-admin/includes/file.php');
                            require_once(ABSPATH . 'wp-admin/includes/media.php');
                            require_once(ABSPATH . 'wp-admin/includes/image.php');
                        
                            foreach ($_FILES['additional_photos']['name'] as $key => $value) {
                                if ($_FILES['additional_photos']['name'][$key]) {
                                    $file = array(
                                        'name'     => $_FILES['additional_photos']['name'][$key],
                                        'type'     => $_FILES['additional_photos']['type'][$key],
                                        'tmp_name' => $_FILES['additional_photos']['tmp_name'][$key],
                                        'error'    => $_FILES['additional_photos']['error'][$key],
                                        'size'     => $_FILES['additional_photos']['size'][$key]
                                    );
                        
                                    // Temporarily reassign the single file to $_FILES for media_handle_upload()
                                    $original_files = $_FILES;
                                    $_FILES = array("upload_attachment" => $file);
                        
                                    $attachment_id = media_handle_upload("upload_attachment", $post_id);
                        
                                    // Restore the original $_FILES
                                    $_FILES = $original_files;
                        
                                    if (is_wp_error($attachment_id)) {
                                        echo '<p class="error-message">Error uploading additional photo: ' . $attachment_id->get_error_message() . '</p>';
                                    } else {
                                        $additional_photos[] = wp_get_attachment_url($attachment_id);
                                    }
                                }
                            }
                        
                            if (!empty($additional_photos)) {
                                update_post_meta($post_id, 'additional_photos', $additional_photos);
                            }
                        }                        
                        
                        
                    }

                    // echo '<a href="' . esc_url(add_query_arg('edit_listing', $post_id, home_url('/dashboard/'))) . '" class="button">Add more photos</a>';
                    echo '<p class="success-message">Listing added successfully!</p>';
                } else {
                    echo '<p class="error-message">Error adding listing. Please try again.</p>';
                }
            }
            ?>
        </div>
        <?php
        return ob_get_clean();
    } else {
        return 'Please log in to view this content.';
    }
    
}
add_shortcode('user_add_listing', 'user_add_listing_shortcode');


function user_subscription_shortcode() {
    ob_start();
    ?>

    <div class="user-container clearfix">
        <div class="user-sidebar">
                <ul>
                    <li>
                        <a href="<?php echo get_permalink(get_page_by_path('dashboard')); ?>">
                            <!-- Dashboard SVG Icon -->
                            <svg width="16" height="16"  viewBox="0 -0.5 25 25" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M9.918 10.0005H7.082C6.66587 9.99708 6.26541 10.1591 5.96873 10.4509C5.67204 10.7427 5.50343 11.1404 5.5 11.5565V17.4455C5.5077 18.3117 6.21584 19.0078 7.082 19.0005H9.918C10.3341 19.004 10.7346 18.842 11.0313 18.5502C11.328 18.2584 11.4966 17.8607 11.5 17.4445V11.5565C11.4966 11.1404 11.328 10.7427 11.0313 10.4509C10.7346 10.1591 10.3341 9.99708 9.918 10.0005Z" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M9.918 4.0006H7.082C6.23326 3.97706 5.52559 4.64492 5.5 5.4936V6.5076C5.52559 7.35629 6.23326 8.02415 7.082 8.0006H9.918C10.7667 8.02415 11.4744 7.35629 11.5 6.5076V5.4936C11.4744 4.64492 10.7667 3.97706 9.918 4.0006Z" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M15.082 13.0007H17.917C18.3333 13.0044 18.734 12.8425 19.0309 12.5507C19.3278 12.2588 19.4966 11.861 19.5 11.4447V5.55666C19.4966 5.14054 19.328 4.74282 19.0313 4.45101C18.7346 4.1592 18.3341 3.9972 17.918 4.00066H15.082C14.6659 3.9972 14.2654 4.1592 13.9687 4.45101C13.672 4.74282 13.5034 5.14054 13.5 5.55666V11.4447C13.5034 11.8608 13.672 12.2585 13.9687 12.5503C14.2654 12.8421 14.6659 13.0041 15.082 13.0007Z" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M15.082 19.0006H17.917C18.7661 19.0247 19.4744 18.3567 19.5 17.5076V16.4936C19.4744 15.6449 18.7667 14.9771 17.918 15.0006H15.082C14.2333 14.9771 13.5256 15.6449 13.5 16.4936V17.5066C13.525 18.3557 14.2329 19.0241 15.082 19.0006Z" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo get_permalink(get_page_by_path('subscription')); ?>">
                            <!-- Subscription SVG Icon -->
                            <svg width="16" height="16" fill="#000000" viewBox="0 0 32 32" id="icon" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <style>.cls-1{fill:none;}</style>
                                </defs>
                                <title>renew</title>
                                <path d="M12,10H6.78A11,11,0,0,1,27,16h2A13,13,0,0,0,6,7.68V4H4v8h8Z"></path>
                                <path d="M20,22h5.22A11,11,0,0,1,5,16H3a13,13,0,0,0,23,8.32V28h2V20H20Z"></path>
                                <g id="_Transparent_Rectangle_" data-name="<Transparent Rectangle>">
                                    <rect class="cls-1" width="32" height="32"></rect>
                                </g>
                            </svg>
                            Subscription
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo get_permalink(get_page_by_path('add-listing')); ?>">
                            <!-- Add Listing SVG Icon -->
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle opacity="0.5" cx="12" cy="12" r="10" stroke="#1C274C" stroke-width="1.5"></circle>
                                <path d="M15 12L12 12M12 12L9 12M12 12L12 9M12 12L12 15" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"></path>
                            </svg>
                            Add Listing
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo wp_logout_url(); ?>">
                            <!-- Logout SVG Icon -->
                            <svg width="16" height="16" viewBox="0 -0.5 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M11.75 9.874C11.75 10.2882 12.0858 10.624 12.5 10.624C12.9142 10.624 13.25 10.2882 13.25 9.874H11.75ZM13.25 4C13.25 3.58579 12.9142 3.25 12.5 3.25C12.0858 3.25 11.75 3.58579 11.75 4H13.25ZM9.81082 6.66156C10.1878 6.48991 10.3542 6.04515 10.1826 5.66818C10.0109 5.29121 9.56615 5.12478 9.18918 5.29644L9.81082 6.66156ZM5.5 12.16L4.7499 12.1561L4.75005 12.1687L5.5 12.16ZM12.5 19L12.5086 18.25C12.5029 18.25 12.4971 18.25 12.4914 18.25L12.5 19ZM19.5 12.16L20.2501 12.1687L20.25 12.1561L19.5 12.16ZM15.8108 5.29644C15.4338 5.12478 14.9891 5.29121 14.8174 5.66818C14.6458 6.04515 14.8122 6.48991 15.1892 6.66156L15.8108 5.29644ZM13.25 9.874V4H11.75V9.874H13.25ZM9.18918 5.29644C6.49843 6.52171 4.7655 9.19951 4.75001 12.1561L6.24999 12.1639C6.26242 9.79237 7.65246 7.6444 9.81082 6.66156L9.18918 5.29644ZM4.75005 12.1687C4.79935 16.4046 8.27278 19.7986 12.5086 19.75L12.4914 18.25C9.08384 18.2892 6.28961 15.5588 6.24995 12.1513L4.75005 12.1687ZM12.4914 19.75C16.7272 19.7986 20.2007 16.4046 20.2499 12.1687L18.7501 12.1513C18.7104 15.5588 15.9162 18.2892 12.5086 18.25L12.4914 19.75ZM20.25 12.1561C20.2345 9.19951 18.5016 6.52171 15.8108 5.29644L15.1892 6.66156C17.3475 7.6444 18.7376 9.79237 18.75 12.1639L20.25 12.1561Z" fill="#000000"></path>
                            </svg>
                            Logout
                        </a>
                    </li>
                </ul>
            </div>

        <div class="user-content">
            <h2>Subscription</h2>
            <div class="subscription-tiers">
                <?php
                // Get current user ID
                $user_id = get_current_user_id();

                // Query subscription products
                $args = array(
                    'post_type' => 'product',
                    'posts_per_page' => -1,
                    'meta_query' => array(
                        array(
                            'key' => '_subscription_tier',
                            'value' => array('basic', 'gold', 'platinum'),
                            'compare' => 'IN',
                        ),
                    ),
                );
                $products = new WP_Query($args);

                if ($products->have_posts()) :
                    while ($products->have_posts()) :
                        $products->the_post();
                        
                        // Get the product ID
                        $product_id = get_the_ID();
                        
                        // Check if the user has purchased this product
                        $has_purchased = wc_customer_bought_product('', $user_id, $product_id);
                        
                        ?>
                        <div class="subscription-tier">
                            <div class="subscription-header">
                                <h3><?php the_title(); ?></h3>
                            </div>
                            <div class="subscription-body">
                                <p><?php the_excerpt(); ?></p>
                                <p class="price"><?php echo wc_price(get_post_meta($product_id, '_price', true)); ?></p>
                            </div>
                            <div class="subscription-footer">
                                <?php if (!$has_purchased) : ?>
                                    <a href="<?php echo wc_get_checkout_url(); ?>?add-to-cart=<?php echo $product_id; ?>" class="button">Subscribe</a>
                                <?php else : ?>
                                    <span class="subscribed">Subscribed</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php
                    endwhile;
                else :
                    ?>
                    <p>No subscription products found.</p>
                <?php endif;
                wp_reset_postdata();
                ?>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('user_subscription', 'user_subscription_shortcode');

function add_upload_capability_to_user($user_id) {
    $user = get_user_by('ID', $user_id); // Get the user by ID
    if ($user && !$user->has_cap('upload_files')) {
        $user->add_cap('upload_files'); // Add the upload_files capability to this user
    }

    // print_r($user);
    // exit;
}









?>

