<?php
/**
 * Plugin Name: Portfolio Showcase
 * Plugin URI: https://github.com/devTarak/portfolio-showcase-plugin
 * Description: A simple and elegant portfolio showcase plugin with Elementor support.
 * Version: 1.0.0
 * Author: Tarak Rahman
 * Author URI: https://devtarak.github.io/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: portfolio-showcase
 * Domain Path: /languages
 */

// Include the custom post type and related functionality
require_once plugin_dir_path(__FILE__) . 'includes/portfolio-cpt.php';

// You can add other plugin-wide hooks or functions here if needed
// Enqueue styles and scripts
function portfolio_showcase_enqueue_assets() {
    wp_enqueue_style(
        'portfolio-style',
        plugin_dir_url(__FILE__) . 'assets/css/portfolio.css',
        [],
        '1.0.0'
    );

    wp_enqueue_script(
        'portfolio-script',
        plugin_dir_url(__FILE__) . 'assets/js/portfolio.js',
        ['jquery'],
        '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'portfolio_showcase_enqueue_assets');
function portfolio_single_template_override($template) {
    if (is_singular('portfolio')) {
        $theme_template = locate_template('single-portfolio.php');
        if ($theme_template) {
            return $theme_template;
        } else {
            return plugin_dir_path(__FILE__) . 'templates/single-portfolio.php';
        }
    }
    return $template;
}
add_filter('template_include', 'portfolio_single_template_override');
function portfolio_archive_template_override($template) {
    if (is_post_type_archive('portfolio')) {
        $theme_template = locate_template('archive-portfolio.php');
        if ($theme_template) {
            return $theme_template;
        } else {
            return plugin_dir_path(__FILE__) . 'templates/archive-portfolio.php';
        }
    }
    return $template;
}
