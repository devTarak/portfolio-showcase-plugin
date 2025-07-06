<?php
// If uninstall not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Delete all portfolio posts and their meta
$portfolios = get_posts([
    'post_type'      => 'portfolio',
    'numberposts'    => -1,
    'post_status'    => 'any',
    'fields'         => 'ids',
]);

foreach ( $portfolios as $portfolio_id ) {
    // Delete post and all associated meta
    wp_delete_post( $portfolio_id, true );
}

// Delete portfolio custom taxonomy terms
$terms = get_terms([
    'taxonomy'   => 'portfolio_category',
    'hide_empty' => false,
    'fields'     => 'ids',
]);
if ( ! is_wp_error( $terms ) ) {
    foreach ( $terms as $term_id ) {
        wp_delete_term( $term_id, 'portfolio_category' );
    }
}