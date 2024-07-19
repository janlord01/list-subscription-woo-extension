<?php
/*
Plugin Name: Custom Tiered Subscriptions
Description: Adds tiered subscription capabilities woocommerce extension. Tier have basic, gold and platinum
Version: 1.0
Author: Janlord Luga
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Start session
function start_session() {
    if (!session_id()) {
        session_start();
    }
}
add_action('init', 'start_session', 1);

// Include necessary files
include_once plugin_dir_path(__FILE__) . 'includes/class-tiered-subscriptions.php';
include_once plugin_dir_path(__FILE__) . 'includes/shortcodes.php';

// Initialize the plugin
// function init_custom_tiered_subscriptions() {
//     $tiered_subscriptions = new Tiered_Subscriptions();
// }
// add_action('plugins_loaded', 'init_custom_tiered_subscriptions');

// Add this function to your main plugin file (custom-tiered-subscriptions.php)

// Add this to your main plugin file (custom-tiered-subscriptions.php)

// Function to create pages
function create_user_pages() {
    // Get the ID of the admin user (or any other user you want to assign as the author)
    $user_id = get_current_user_id();
    
    // Array of pages to create
    $pages = array(
        'Dashboard' => '[user_dashboard]',
        'Subscription' => '[user_subscription]',
        'Add Listing' => '[user_add_listing]'
    );

    foreach ($pages as $title => $content) {
        // Check if page already exists
        if (null == get_page_by_title($title)) {
            // Create the page
            wp_insert_post(array(
                'post_title'    => $title,
                'post_content'  => $content,
                'post_status'   => 'publish',
                'post_author'   => $user_id,
                'post_type'     => 'page',
            ));
        }
    }
}

// Function to delete pages
function delete_user_pages() {
    // Array of pages to delete
    $pages = array('Dashboard', 'Subscription', 'Add Listing');

    foreach ($pages as $title) {
        // Get the page by title
        $page = get_page_by_title($title);
        if ($page) {
            // Delete the page
            wp_delete_post($page->ID, true);
        }
    }
}

// Register the activation hook
register_activation_hook(__FILE__, 'create_user_pages');

// Register the deactivation hook
register_deactivation_hook(__FILE__, 'delete_user_pages');



// Add custom meta box for subscription tier
add_action('add_meta_boxes', 'add_subscription_tier_meta_box');
function add_subscription_tier_meta_box() {
    add_meta_box(
        'subscription_tier_meta_box',
        'Subscription Tier',
        'display_subscription_tier_meta_box',
        'product',
        'side',
        'default'
    );
}

function display_subscription_tier_meta_box($post) {
    $subscription_tier = get_post_meta($post->ID, 'subscription_tier', true);
    wp_nonce_field('save_subscription_tier_meta_box', 'subscription_tier_meta_box_nonce');
    ?>
    <label for="subscription_tier">Select Subscription Tier:</label>
    <select name="subscription_tier" id="subscription_tier">
        <option value="">None</option>
        <option value="basic" <?php selected($subscription_tier, 'basic'); ?>>Basic</option>
        <option value="gold" <?php selected($subscription_tier, 'gold'); ?>>Gold</option>
        <option value="platinum" <?php selected($subscription_tier, 'platinum'); ?>>Platinum</option>
    </select>
    <?php
}

// Save subscription tier meta box data
add_action('save_post', 'save_subscription_tier_meta_box');
function save_subscription_tier_meta_box($post_id) {
    if (!isset($_POST['subscription_tier_meta_box_nonce']) || !wp_verify_nonce($_POST['subscription_tier_meta_box_nonce'], 'save_subscription_tier_meta_box')) {
        return;
    }
    if (isset($_POST['subscription_tier'])) {
        update_post_meta($post_id, 'subscription_tier', sanitize_text_field($_POST['subscription_tier']));
    }
}

// Register custom post type for service listings
function custom_register_post_type() {
    $labels = array(
        'name'               => _x('Listings', 'post type general name', 'custom-tiered-subscriptions'),
        'singular_name'      => _x('Listing', 'post type singular name', 'custom-tiered-subscriptions'),
        'menu_name'          => _x('Listings', 'admin menu', 'custom-tiered-subscriptions'),
        'name_admin_bar'     => _x('Listing', 'add new on admin bar', 'custom-tiered-subscriptions'),
        'add_new'            => _x('Add New', 'listing', 'custom-tiered-subscriptions'),
        'add_new_item'       => __('Add New Listing', 'custom-tiered-subscriptions'),
        'new_item'           => __('New Listing', 'custom-tiered-subscriptions'),
        'edit_item'          => __('Edit Listing', 'custom-tiered-subscriptions'),
        'view_item'          => __('View Listing', 'custom-tiered-subscriptions'),
        'all_items'          => __('All Listings', 'custom-tiered-subscriptions'),
        'search_items'       => __('Search Listings', 'custom-tiered-subscriptions'),
        'parent_item_colon'  => __('Parent Listings:', 'custom-tiered-subscriptions'),
        'not_found'          => __('No listings found.', 'custom-tiered-subscriptions'),
        'not_found_in_trash' => __('No listings found in Trash.', 'custom-tiered-subscriptions')
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __('Manage listings.', 'custom-tiered-subscriptions'),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'service_listing'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'author', 'thumbnail', 'custom-fields'),
        'menu_icon'          => 'dashicons-cart',
        'taxonomies'         => array('category'), // Add categories support
    );

    register_post_type('service_listing', $args);
}
add_action('init', 'custom_register_post_type');

// Add custom meta boxes for service listing details
add_action('add_meta_boxes', 'add_service_listing_meta_box');
function add_service_listing_meta_box() {
    add_meta_box(
        'service_listing_meta_box',
        'Listing Details',
        'render_service_listing_meta_box',
        'service_listing',
        'normal',
        'high'
    );
}

function render_service_listing_meta_box($post) {
    wp_nonce_field('save_service_listing_meta_box', 'service_listing_meta_box_nonce');
    $meta = get_post_meta($post->ID);

    ?>
    <div class="meta-box-row">
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" value="<?php echo esc_attr($meta['name'][0] ?? ''); ?>">
    </div>
    <div class="meta-box-row">
        <label for="phone_number">Phone Number:</label>
        <input type="text" name="phone_number" id="phone_number" value="<?php echo esc_attr($meta['phone_number'][0] ?? ''); ?>">
    </div>
    <div class="meta-box-row">
        <label for="location">Location:</label>
        <input type="text" name="location" id="location" value="<?php echo esc_attr($meta['location'][0] ?? ''); ?>">
    </div>
    <div class="meta-box-row">
        <label for="profile_photo">Profile Photo:</label>
        <input type="text" name="profile_photo" id="profile_photo" value="<?php echo esc_attr($meta['profile_photo'][0] ?? ''); ?>">
        <input type="button" id="upload_profile_photo_button" class="button" value="Upload Photo" />
    </div>
    <div class="meta-box-row">
        <label for="booking_platform">Booking Platform:</label>
        <input type="text" name="booking_platform" id="booking_platform" value="<?php echo esc_attr($meta['booking_platform'][0] ?? ''); ?>">
    </div>
    <div class="meta-box-row">
        <label for="services">Services:</label>
        <div id="service_list">
            <?php
            $services = get_post_meta($post->ID, 'services', true) ?? [];
            foreach ($services as $index => $service) {
                ?>
                <div class="service-item">
                    <input type="text" name="services[<?php echo $index; ?>][name]" value="<?php echo esc_attr($service['name']); ?>" placeholder="Service Name">
                    <input type="text" name="services[<?php echo $index; ?>][price]" value="<?php echo esc_attr($service['price']); ?>" placeholder="Price">
                    <button class="remove-service button">Remove</button>
                </div>
                <?php
            }
            ?>
        </div>
        <button id="add_service" class="button">Add Service</button>
    </div>
    <div class="meta-box-row">
        <label>Social Media:</label>
        <div>
            <img src="<?php echo plugin_dir_url(__FILE__); ?>assets/images/facebook.svg" width="30" alt="Facebook" />
            <input type="text" name="social_media[facebook]" value="<?php echo esc_attr(get_post_meta($post->ID, 'social_media_facebook', true)); ?>" placeholder="Facebook URL" />
        </div>
        <div>
            <img src="<?php echo plugin_dir_url(__FILE__); ?>assets/images/tiktok.png" width="30" alt="TikTok" />
            <input type="text" name="social_media[tiktok]" value="<?php echo esc_attr(get_post_meta($post->ID, 'social_media_tiktok', true)); ?>" placeholder="TikTok URL" />
        </div>
        <div>
            <img src="<?php echo plugin_dir_url(__FILE__); ?>assets/images/instagram.webp" width="30" alt="Instagram" />
            <input type="text" name="social_media[instagram]" value="<?php echo esc_attr(get_post_meta($post->ID, 'social_media_instagram', true)); ?>" placeholder="Instagram URL" />
        </div>
        <div>
            <img src="<?php echo plugin_dir_url(__FILE__); ?>assets/images/x.webp" width="30" alt="Twitter" />
            <input type="text" name="social_media[twitter]" value="<?php echo esc_attr(get_post_meta($post->ID, 'social_media_twitter', true)); ?>" placeholder="Twitter URL" />
        </div>
    </div>
    <div class="meta-box-row">
        <label for="additional_photos">Additional Photos:</label>
        <?php
        $additional_photos = get_post_meta($post->ID, 'additional_photos', true) ?? [];
        for ($i = 0; $i < 5; $i++) {
            $additional_photo = $additional_photos[$i] ?? '';
            ?>
            <input type="text" class="additional_photo" name="additional_photos[]" value="<?php echo esc_attr($additional_photo); ?>" />
            <input type="button" class="upload_additional_photo_button button" value="Upload Photo" />
            <?php
        }
        ?>
    </div>
    <?php
}


// Save service listing meta box data
add_action('save_post', 'save_service_listing_meta_box');
function save_service_listing_meta_box($post_id) {
    if (!isset($_POST['service_listing_meta_box_nonce']) || !wp_verify_nonce($_POST['service_listing_meta_box_nonce'], 'save_service_listing_meta_box')) {
        return;
    }

    $fields = ['name', 'phone_number', 'location', 'profile_photo', 'booking_platform'];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }

    if (isset($_POST['services'])) {
        $services = array_map(function ($service) {
            return [
                'name' => sanitize_text_field($service['name']),
                'price' => sanitize_text_field($service['price'])
            ];
        }, $_POST['services']);
        update_post_meta($post_id, 'services', $services);
    }

    $social_media = ['facebook', 'tiktok', 'instagram', 'twitter'];
    foreach ($social_media as $platform) {
        if (isset($_POST['social_media'][$platform])) {
            update_post_meta($post_id, 'social_media_' . $platform, sanitize_text_field($_POST['social_media'][$platform]));
        }
    }

    if (isset($_POST['additional_photos'])) {
        $additional_photos = array_map('sanitize_text_field', $_POST['additional_photos']);
        update_post_meta($post_id, 'additional_photos', $additional_photos);
    }
}


// Enqueue scripts and styles
add_action('admin_enqueue_scripts', 'enqueue_admin_scripts');
function enqueue_admin_scripts() {
    wp_enqueue_media();
    wp_enqueue_script('admin-script', plugin_dir_url(__FILE__) . 'assets/js/admin-script.js', array('jquery'), null, true);
    wp_enqueue_style('admin-style', plugin_dir_url(__FILE__) . 'assets/css/admin-style.css');
}
function custom_tiered_subscriptions_enqueue_styles() {
    wp_enqueue_script('client-script', plugin_dir_url(__FILE__) . 'assets/js/client-script.js', array('jquery'), null, true);

    wp_enqueue_style('custom-tiered-subscriptions-style', plugin_dir_url(__FILE__) . 'assets/css/style.css');
}
add_action('wp_enqueue_scripts', 'custom_tiered_subscriptions_enqueue_styles');

function custom_login_redirect($redirect_to, $request, $user) {
    // Check if the user has logged in
    if (isset($user->roles) && is_array($user->roles)) {
        // Check for specific roles and set the redirect URL
        if (in_array('basic_subscriber', $user->roles)) {
            $redirect_to = home_url('/dashboard');
        } elseif (in_array('gold_subscriber', $user->roles)) {
            $redirect_to = home_url('/dashboard');
        } elseif (in_array('platinum_subscriber', $user->roles)) {
            $redirect_to = home_url('/dashboard');
        }
    }
    return $redirect_to;
}
add_filter('login_redirect', 'custom_login_redirect', 10, 3);



register_activation_hook(__FILE__, 'create_custom_roles');
function create_custom_roles() {
    add_role('basic_subscriber', 'Basic Subscriber', array('read' => true));
    add_role('gold_subscriber', 'Gold Subscriber', array('read' => true));
    add_role('platinum_subscriber', 'Platinum Subscriber', array('read' => true));
}

register_deactivation_hook(__FILE__, 'remove_custom_roles');
function remove_custom_roles() {
    remove_role('basic_subscriber');
    remove_role('gold_subscriber');
    remove_role('platinum_subscriber');
}

// Hook into template_include to load custom templates
add_filter('template_include', 'custom_service_listing_templates');

function custom_service_listing_templates($template) {
    if (is_post_type_archive('service_listing')) {
        // Custom archive template
        $custom_archive_template = plugin_dir_path(__FILE__) . 'temp/archive-service_listing.php';
        if (file_exists($custom_archive_template)) {
            return $custom_archive_template;
        }
    } elseif (is_singular('service_listing')) {
        // Custom single template
        $custom_single_template = plugin_dir_path(__FILE__) . 'temp/single-service_listing.php';
        if (file_exists($custom_single_template)) {
            return $custom_single_template;
        }
    }
    return $template;
}
function custom_enqueue_theme_styles() {
    wp_enqueue_style('theme-styles', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'custom_enqueue_theme_styles');



?>