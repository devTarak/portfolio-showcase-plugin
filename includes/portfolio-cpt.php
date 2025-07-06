<?php
// Register Portfolio Custom Post Type
function portfolio_register_post_type() {
    $args = array(
        'labels' => array(
            'name'                  => 'Portfolios',
            'singular_name'         => 'Portfolio',
            'add_new_item'          => 'Add New Portfolio',
            'edit_item'             => 'Edit Portfolio',
            'new_item'              => 'New Portfolio',
            'view_item'             => 'View Portfolio',
            'search_items'          => 'Search Portfolios',
            'not_found'             => 'No portfolios found',
            'not_found_in_trash'    => 'No portfolios found in Trash',
            'all_items'             => 'All Portfolios',
            'archives'              => 'Portfolio Archives',
            'insert_into_item'      => 'Insert into portfolio',
            'uploaded_to_this_item' => 'Uploaded to this portfolio',
            'menu_name'             => 'Portfolios',
        ),
        'public'             => true,            // Ensure 'public' is true
        'show_ui'            => true,            // Show the portfolio UI in the admin
        'show_in_menu'       => true,            // Show portfolio in the admin menu
        'show_in_nav_menus'  => true,            // Allow it in navigation menus
        'has_archive'        => true,            // Enable archives for portfolio
        'rewrite'            => array('slug' => 'portfolio'),
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'taxonomies'         => array('portfolio_category'),  // Add category taxonomy support
        'show_in_rest'       => true,            // Enable REST API support (useful for the frontend)
        'menu_icon'          => 'dashicons-portfolio',  // Portfolio icon in admin menu
        'capability_type'    => 'post',          // This should be the same as default post type
        'map_meta_cap'       => true,            // Map capabilities for meta boxes
        'rest_base'          => 'portfolio',     // REST API endpoint base
    );
    
    register_post_type('portfolio', $args);
}

add_action('init', 'portfolio_register_post_type');

// Disable Gutenberg for Portfolio Custom Post Type
function disable_gutenberg_for_portfolio($use_block_editor, $post_type) {
    if ('portfolio' === $post_type) {
        return false; // Disable Gutenberg for portfolio post type
    }
    return $use_block_editor; // Keep Gutenberg enabled for all other post types
}
add_filter('use_block_editor_for_post_type', 'disable_gutenberg_for_portfolio', 10, 2);

// Register Portfolio Taxonomy
function register_portfolio_taxonomy() {
    $labels = [
        'name'              => 'Portfolio Categories',
        'singular_name'     => 'Portfolio Category',
        'search_items'      => 'Search Categories',
        'all_items'         => 'All Categories',
        'parent_item'       => 'Parent Category',
        'parent_item_colon' => 'Parent Category:',
        'edit_item'         => 'Edit Category',
        'update_item'       => 'Update Category',
        'add_new_item'      => 'Add New Category',
        'new_item_name'     => 'New Category Name',
        'menu_name'         => 'Portfolio Categories',
    ];

    $args = [
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'rewrite'           => ['slug' => 'portfolio-category'],
        'show_in_rest'      => false, // Disable Gutenberg for taxonomies
    ];

    register_taxonomy('portfolio_category', ['portfolio'], $args);
}
add_action('init', 'register_portfolio_taxonomy');

// Register Meta Boxes for Portfolio Post Type
function add_portfolio_meta_boxes() {
    add_meta_box(
        'portfolio_details_meta_box',        // Meta box ID
        'Portfolio Details',                 // Meta box title
        'render_portfolio_meta_box',         // Callback function to render the meta box fields
        'portfolio',                         // Post type (ensure it's set to 'portfolio')
        'normal',                            // Context ('normal' means in the normal place)
        'high'                               // Priority ('high' means at the top)
    );
}
add_action('add_meta_boxes', 'add_portfolio_meta_boxes');

// Render the Meta Box Fields for Portfolio
function render_portfolio_meta_box($post) {
    $start_date = get_post_meta($post->ID, '_portfolio_start_date', true);
    $end_date = get_post_meta($post->ID, '_portfolio_end_date', true);
    $technologies = get_post_meta($post->ID, '_portfolio_technologies', true);
    $live_link = get_post_meta($post->ID, '_portfolio_live_link', true);

    // Nonce field for security
    wp_nonce_field('portfolio_details_nonce_action', 'portfolio_nonce');
    ?>
    <p>
        <label for="portfolio_start_date">Project Starting Date:</label><br>
        <input type="date" id="portfolio_start_date" name="portfolio_start_date" value="<?php echo esc_attr($start_date); ?>" style="width:100%;">
    </p>
    <p>
        <label for="portfolio_end_date">Project End Date:</label><br>
        <input type="date" id="portfolio_end_date" name="portfolio_end_date" value="<?php echo esc_attr($end_date); ?>" style="width:100%;">
    </p>
    <p>
        <label for="portfolio_technologies">Used Technologies:</label><br>
        <textarea id="portfolio_technologies" name="portfolio_technologies" style="width:100%;" rows="3"><?php echo esc_textarea($technologies); ?></textarea>
    </p>
    <p>
        <label for="portfolio_live_link">Live Preview Link:</label><br>
        <input type="url" id="portfolio_live_link" name="portfolio_live_link" value="<?php echo esc_attr($live_link); ?>" style="width:100%;">
    </p>
    <?php
}

// Save Meta Box Data
function save_portfolio_meta($post_id) {
    // Avoid overwriting when auto-saving
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;

    // Verify nonce
    if (!isset($_POST['portfolio_nonce']) || !wp_verify_nonce($_POST['portfolio_nonce'], 'portfolio_details_nonce_action')) {
        return $post_id;
    }

    // Save project dates with validation
    if (isset($_POST['portfolio_start_date'])) {
        $start_date = sanitize_text_field($_POST['portfolio_start_date']);
        if (strtotime($start_date)) {
            update_post_meta($post_id, '_portfolio_start_date', $start_date);
        }
    }

    if (isset($_POST['portfolio_end_date'])) {
        $end_date = sanitize_text_field($_POST['portfolio_end_date']);
        if (strtotime($end_date)) {
            update_post_meta($post_id, '_portfolio_end_date', $end_date);
        }
    }

    // Save technologies
    if (isset($_POST['portfolio_technologies'])) {
        update_post_meta($post_id, '_portfolio_technologies', sanitize_textarea_field($_POST['portfolio_technologies']));
    }

    // Save live link
    if (isset($_POST['portfolio_live_link'])) {
        update_post_meta($post_id, '_portfolio_live_link', esc_url_raw($_POST['portfolio_live_link']));
    }

    return $post_id;
}
add_action('save_post', 'save_portfolio_meta');


// Flush rewrite rules on activation
register_activation_hook(__FILE__, 'portfolio_plugin_activation');
function portfolio_plugin_activation() {
    portfolio_register_post_type();
    register_portfolio_taxonomy();
    flush_rewrite_rules();
}
// Template loader for single portfolio and portfolio archive
function portfolio_plugin_template_loader($template) {
    if (is_singular('portfolio')) {
        $custom_template = plugin_dir_path(__FILE__) . 'templates/single-portfolio.php';
        if (file_exists($custom_template)) {
            return $custom_template;
        }
    }

    if (is_post_type_archive('portfolio')) {
        $archive_template = plugin_dir_path(__FILE__) . 'templates/archive-portfolio.php';
        if (file_exists($archive_template)) {
            return $archive_template;
        }
    }

    return $template;
}
add_filter('template_include', 'portfolio_plugin_template_loader');

?>