<?php
get_header();
?>

<div class="service-listing-archive">
    <h1><?php post_type_archive_title(); ?></h1>

    <?php if (have_posts()) : ?>
        <div class="service-listings">
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    <div class="entry-summary">
                        <?php the_excerpt(); ?>
                        <a href="<?php the_permalink(); ?>" class="read-more" style="font-size: 12px;display: inline-block; padding: 5px 10px; background-color: #0073aa; color: #fff; text-decoration: none;border-radius: 3px;margin-bottom:20px;margin-top:-20px;"><?php _e('Read More', 'custom-tiered-subscriptions'); ?></a>
                    </div>
                    <?php
                    // Fetch custom fields
                    $location = get_post_meta(get_the_ID(), 'location', true);
                    $phone_number = get_post_meta(get_the_ID(), 'phone_number', true);
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
                </article>
            <?php endwhile; ?>
        </div>

        <?php the_posts_navigation(); ?>

    <?php else : ?>
        <p><?php _e('No service listings found.', 'custom-tiered-subscriptions'); ?></p>
    <?php endif; ?>
</div>

<?php
get_footer();
?>
