# Custom Tiered Subscriptions Plugin

## Plugin Information
- **Plugin Name:** Custom Tiered Subscriptions
- **Description:** Adds tiered subscription capabilities to WooCommerce with Basic, Gold, and Platinum tiers.
- **Version:** 1.0
- **Author:** Janlord Luga

## Features
- **Subscription Tiers:** Basic, Gold, and Platinum.
- **Custom Post Type:** Service Listings.
- **Meta Boxes:** Custom meta boxes for subscription tier selection and service listing details.
- **Shortcodes:** Shortcodes for user search function.
- **Custom Pages:** Automatic creation and deletion of custom pages for dashboard, subscription, and add listing.
- **Custom Roles:** Custom user roles for each subscription tier.
- **Redirects:** Custom login redirects based on user roles.
- **Scripts and Styles:** Enqueues necessary scripts and styles for admin and client side.

## Installation
1. Upload the `custom-tiered-subscriptions` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. The plugin will automatically create necessary pages and user roles.

## Usage
### Creating Service Listings
1. Navigate to the 'Listings' menu in the WordPress admin dashboard.
2. Click 'Add New' to create a new listing.
3. Fill in the details such as Name, Phone Number, Location, Profile Photo, Booking Platform, Services, Social Media links, and Additional Photos.
4. Select the subscription tier (Basic, Gold, or Platinum) from the 'Subscription Tier' meta box.
5. Publish the listing.

### Shortcodes
- **Dashboard:** `[user_dashboard]`
- **Subscription:** `[user_subscription]`
- **Add Listing:** `[user_add_listing]`

## Custom Roles
- **Basic Subscriber**
- **Gold Subscriber**
- **Platinum Subscriber**

## Custom Pages
The following pages are created upon plugin activation and deleted upon deactivation:
- **Dashboard**
- **Subscription**
- **Add Listing**

## Meta Boxes
### Subscription Tier Meta Box
Displays a dropdown to select the subscription tier for products.

### Service Listing Meta Box
Provides fields to enter details for service listings including:
- Name
- Phone Number
- Location
- Profile Photo
- Booking Platform
- Services
- Social Media Links
- Additional Photos

## Custom Templates
The plugin includes custom templates for the service listing archive and single listing pages:
- **Archive Template:** `archive-service_listing.php`
- **Single Template:** `single-service_listing.php`

## Scripts and Styles
### Admin Side
- **JavaScript:** `assets/js/admin-script.js`
- **CSS:** `assets/css/admin-style.css`

### Client Side
- **JavaScript:** `assets/js/client-script.js`
- **CSS:** `assets/css/style.css`

## Activation and Deactivation Hooks
### Activation
- Creates custom roles.
- Creates necessary pages.

### Deactivation
- Removes custom roles.
- Deletes custom pages.

## Code Structure
### Main Plugin File: `custom-tiered-subscriptions.php`
- Prevents direct access.
- Starts session.
- Includes necessary files.
- Initializes the plugin.
- Registers activation and deactivation hooks.
- Enqueues scripts and styles.
- Handles custom login redirects.
- Registers custom post type and meta boxes.

### Included Files
- **Class File:** `includes/class-tiered-subscriptions.php`
- **Shortcodes File:** `includes/shortcodes.php`
- **Admin Script:** `assets/js/admin-script.js`
- **Client Script:** `assets/js/client-script.js`
- **Admin Style:** `assets/css/admin-style.css`
- **Client Style:** `assets/css/style.css`
- **Archive Template:** `temp/archive-service_listing.php`
- **Single Template:** `temp/single-service_listing.php`

## Functions
### Session Handling
- `start_session`

### Page Creation
- `create_user_pages`
- `delete_user_pages`

### Meta Boxes
- `add_subscription_tier_meta_box`
- `display_subscription_tier_meta_box`
- `save_subscription_tier_meta_box`
- `add_service_listing_meta_box`
- `render_service_listing_meta_box`
- `save_service_listing_meta_box`

### Post Type Registration
- `custom_register_post_type`

### Scripts and Styles Enqueuing
- `enqueue_admin_scripts`
- `custom_tiered_subscriptions_enqueue_styles`
- `custom_enqueue_theme_styles`

### Login Redirect
- `custom_login_redirect`

### Role Management
- `create_custom_roles`
- `remove_custom_roles`

### Template Handling
- `custom_service_listing_templates`
