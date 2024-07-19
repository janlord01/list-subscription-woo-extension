<?php
class Tiered_Subscriptions {
    public function __construct() {
       
        
        // add_action('woocommerce_order_status_completed', array($this, 'grant_access_based_on_tier'));
        // add_action('woocommerce_order_status_changed', array($this, 'grant_access_based_on_tier', 99, 4 ));
        
        add_action('woocommerce_thankyou', array($this, 'grant_access_based_on_tier'));
        add_filter('manage_users_columns', array($this, 'add_subscription_column'));
        add_action('manage_users_custom_column', array($this, 'show_subscription_column_data'), 10, 3);


        // Add custom fields to the user profile page
        add_action('show_user_profile', array($this, 'show_custom_user_profile_fields'));
        add_action('edit_user_profile', array($this, 'show_custom_user_profile_fields'));

        // Save custom fields on the user profile page
        add_action('personal_options_update', array($this, 'save_custom_user_profile_fields'));
        add_action('edit_user_profile_update', array($this, 'save_custom_user_profile_fields'));

        // Add Upload capability
        add_action('init', array($this, 'add_upload_capability_to_subscriber'));

    }

    public function grant_access_based_on_tier($order_id) {
            
            $this->custom_log('Order status completed. Order ID: ' . $order_id);

            $order = wc_get_order($order_id);
            foreach ($order->get_items() as $item) {
                $product_id = $item->get_product_id();
                $user_id = $order->get_user_id();

                if (!$user_id) {
                    $this->custom_log('No user ID associated with order ID: ' . $order_id);
                    return;
                }

                $subscription_tier = get_post_meta($product_id, 'subscription_tier', true);

                if ($subscription_tier) {
                    update_user_meta($user_id, 'subscription_tier', $subscription_tier);
                } else {
                    $this->custom_log('No subscription tier found for product ID: ' . $product_id);
                }
            }
        
    }
    public function add_subscription_column($columns) {
        $columns['subscription'] = 'Subscription';
        return $columns;
    }

    public function show_subscription_column_data($value, $column_name, $user_id) {
        if ('subscription' == $column_name) {
            $subscription_tier = get_user_meta($user_id, 'subscription_tier', true);
            return ucfirst($subscription_tier);
        }
        return $value;
    }
     // Show custom fields on the user profile page
     public function show_custom_user_profile_fields($user) {
        ?>
        <h3><?php _e('Subscription Information', 'textdomain'); ?></h3>
        <table class="form-table">
            <tr>
                <th><label for="subscription_tier"><?php _e('Subscription Tier', 'textdomain'); ?></label></th>
                <td>
                    <input type="text" name="subscription_tier" id="subscription_tier" value="<?php echo esc_attr(get_user_meta($user->ID, 'subscription_tier', true)); ?>" class="regular-text" /><br />
                    <span class="description"><?php _e('Please enter the subscription tier.', 'textdomain'); ?></span>
                </td>
            </tr>
        </table>
        <?php
    }

    // Save custom fields on the user profile page
    public function save_custom_user_profile_fields($user_id) {
        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }

        update_user_meta($user_id, 'subscription_tier', $_POST['subscription_tier']);
    }

    function add_upload_capability_to_subscriber() {
        $role = get_role('customer'); // Get the subscriber role
        if ($role && !$role->has_cap('upload_files')) {
            $role->add_cap('upload_files'); // Add the upload_files capability
        }
    }

    private function custom_log($message) {
        $log_file = plugin_dir_path(__FILE__) . 'debug.log';
        $current_time = date('Y-m-d H:i:s');
        file_put_contents($log_file, $current_time . ' - ' . $message . PHP_EOL, FILE_APPEND);
    }
}

function init_custom_tiered_subscriptions() {
    $tiered_subscriptions = new Tiered_Subscriptions();
}
add_action('plugins_loaded', 'init_custom_tiered_subscriptions');



