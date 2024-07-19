<?php
get_header();
?>
<style>
    .service-listing-single{
        width: 100%;
    }
</style>

<div class="service-listing-single">
    <?php while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="entry-header">
                <h3><?php the_title(); ?></h3>
            </header>

            <div class="entry-content">
                <?php
                    $profile_photo = get_post_meta(get_the_ID(), 'profile_photo', true);
                    $name = get_post_meta(get_the_ID(), 'name', true);

                ?>
                <div class="profile" style="border: 1px solid #c2c2c2; padding: 10px;max-width: 180px;">
                    <p><b>Profile:</b></p>
                    
                    <img src="<?php echo esc_html($profile_photo)?>" width="150" class="profile_photo" />
                    <?php echo esc_html($name)?>
                </div>
                <?php the_content(); ?>
                <?php 
                    $services = get_post_meta(get_the_ID(), 'services', true) ?? [];
                if($services){
                ?>
                <div id="service_list" style="border: 1px solid #c2c2c2; padding: 10px;max-width: 240px;">
                    <p><b>Services Offer:</b></p>
                    <div class="service-item">
                    <?php
                    $services = get_post_meta(get_the_ID(), 'services', true) ?? [];
                    
                    foreach ($services as $index => $service) {
                        ?>
                        
                            <p style="margin-top:-20px;"><?php echo esc_attr($service['name']);  ?> &nbsp; <?php echo get_woocommerce_currency_symbol(); ?><?php echo esc_attr($service['price']); ?></p>
                        
                        <?php
                    }
                    ?>
                    </div>
                </div>
                <?php } ?>
                <div class="categories" style="border: 1px solid #c2c2c2; padding: 10px;max-width: 240px;">
                    <p><b>Categories:</b></p>
                    <?php
                    $categories = get_the_terms(get_the_ID(), 'category');
                    if ($categories && !is_wp_error($categories)) {
                        echo '<ul>';
                        foreach ($categories as $category) {
                            echo '<li style="display:inline-block;margin-right:5px;background:gray;border-radius:30px;padding: 3px 10px;color:#fff;font-size:14px;">' . $category->name . '</li>';
                        }
                        echo '</ul>';
                    }
                    ?>
                </div>

                <div class="other-data">
                    <?php
                    $location = get_post_meta(get_the_ID(), 'location', true);
                    $phone_number = get_post_meta(get_the_ID(), 'phone_number', true);
                    $booking_platform = get_post_meta(get_the_ID(), 'booking_platform', true);
                    $additional_photos = get_post_meta($listing_id, 'additional_photos', true) ?? [];

                    ?>
                    <?php if (!empty($location)) : ?>
                            
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: red;">
                                <path d="M12 2C8.1 2 5 5.1 5 9C5 12.9 12 22 12 22C12 22 19 12.9 19 9C19 5.1 15.9 2 12 2ZM12 11.5C10.6 11.5 9.5 10.4 9.5 9C9.5 7.6 10.6 6.5 12 6.5C13.4 6.5 14.5 7.6 14.5 9C14.5 10.4 13.4 11.5 12 11.5Z" fill="currentColor"/>
                            </svg>
                            <?php echo esc_html($location); ?>
                        &nbsp;
                    <?php endif; ?>
                    <?php if (!empty($phone_number)) : ?>
                        
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: green;">
                                <path d="M6.62 10.79C8.06 13.94 10.06 15.94 13.21 17.38L15.71 14.88C15.89 14.7 16.14 14.64 16.36 14.7C17.52 14.96 18.74 15.1 20 15.1C20.55 15.1 21 15.55 21 16.1V20C21 20.55 20.55 21 20 21C10.61 21 3 13.39 3 4C3 3.45 3.45 3 4 3H7.9C8.45 3 8.9 3.45 8.9 4C8.9 5.26 9.04 6.48 9.3 7.64C9.36 7.86 9.3 8.11 9.12 8.29L6.62 10.79Z" fill="currentColor"/>
                            </svg>
                            <?php echo esc_html($phone_number); ?>
                        
                    <?php endif; ?>
                    <br />
                    <a href="<?php echo esc_html($booking_platform); ?>" class="read-more" style="font-size: 14px;display: inline-block; padding: 5px 10px; background-color: #0073aa; color: #fff; text-decoration: none;border-radius: 3px;margin-bottom:20px;margin-top:20px;"><?php _e('Book Now', 'custom-tiered-subscriptions'); ?></a>
                    <div class="additional_photos_container">
                <?php
                        for ($i = 0; $i < 5; $i++) {
                            $additional_photo = $additional_photos[$i] ?? '';
                            ?>
                            <div class="photo-card">
                                <div class="photo-card-left">
                                    <img id="additional_photo_<?php echo $i; ?>" src="<?php echo esc_attr($additional_photo) ?>" width="150" /><br>
                                </div>
                            </div>
                            
                            <?php
                        }
                        ?>


                    </div>
                </div>
            </div><!-- .entry-content -->
        </article><!-- #post-## -->
    <?php endwhile; ?>
</div>

<?php
get_footer();
?>
