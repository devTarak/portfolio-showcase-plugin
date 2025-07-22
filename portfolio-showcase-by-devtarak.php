<?php
/**
 * Plugin Name: Portfolio Showcase by DevTarak
 * Plugin URI: https://github.com/devTarak/portfolio-showcase-plugin
 * Description: A simple and elegant portfolio showcase plugin with Elementor support.
 * Version: 1.0.0
 * Author: Tarak Rahman
 * Author URI: https://devtarak.github.io/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: portfolio-showcase-by-devtarak
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Include the custom post type and related functionality
require_once plugin_dir_path(__FILE__) . 'includes/portfolio-cpt.php';

// Enqueue styles and scripts
function portshby_enqueue_assets() {
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
add_action('wp_enqueue_scripts', 'portshby_enqueue_assets');

// Template overrides
function portshby_single_template_override($template) {
    if (is_singular('portfolio')) {
        $theme_template = locate_template('single-portfolio.php');
        return $theme_template ? $theme_template : plugin_dir_path(__FILE__) . 'templates/single-portfolio.php';
    }
    return $template;
}
add_filter('template_include', 'portshby_single_template_override');

function portshby_archive_template_override($template) {
    if (is_post_type_archive('portfolio')) {
        $theme_template = locate_template('archive-portfolio.php');
        return $theme_template ? $theme_template : plugin_dir_path(__FILE__) . 'templates/archive-portfolio.php';
    }
    return $template;
}
add_filter('template_include', 'portshby_archive_template_override');