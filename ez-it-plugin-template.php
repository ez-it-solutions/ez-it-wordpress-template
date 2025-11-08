<?php
/**
 * Plugin Name: Ez IT | Plugin Template
 * Plugin URI: https://www.Ez-IT-Solutions.com
 * Description: A modern WordPress plugin template with tabbed admin interface, dark/light themes, and advanced settings. Perfect starting point for Ez IT Solutions plugins.
 * Version: 1.0.0
 * Author: Ez IT Solutions | Chris Hultberg
 * Author URI: https://www.Ez-IT-Solutions.com
 * License: Proprietary
 * Text Domain: ez-it-template
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('EZIT_TEMPLATE_VERSION', '1.0.0');
define('EZIT_TEMPLATE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('EZIT_TEMPLATE_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Load required files
 */
require_once EZIT_TEMPLATE_PLUGIN_DIR . 'admin/class-admin-page-tabbed.php';

/**
 * Initialize the plugin
 */
add_action('plugins_loaded', function() {
    // Plugin initialization code here
});

/**
 * Register admin menu
 */
add_action('admin_menu', function() {
    add_menu_page(
        'Ez IT Template',
        'Ez IT Template',
        'manage_options',
        'ez-it-template',
        ['EzIT_Template_Admin_Page_Tabbed', 'render'],
        'dashicons-admin-generic',
        30
    );
});

/**
 * Enqueue admin styles and scripts
 */
add_action('admin_enqueue_scripts', function($hook) {
    if ($hook !== 'toplevel_page_ez-it-template') {
        return;
    }
    
    wp_enqueue_style(
        'ez-it-template-admin',
        EZIT_TEMPLATE_PLUGIN_URL . 'assets/css/admin-tabbed.css',
        [],
        EZIT_TEMPLATE_VERSION
    );
    
    wp_enqueue_script(
        'ez-it-template-admin',
        EZIT_TEMPLATE_PLUGIN_URL . 'assets/js/admin.js',
        ['jquery'],
        EZIT_TEMPLATE_VERSION,
        true
    );
    
    wp_localize_script('ez-it-template-admin', 'ezitTemplateAjax', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('ez_it_template_nonce')
    ]);
});

/**
 * Hide WordPress admin notices on plugin pages
 */
add_action('in_admin_header', function() {
    $screen = get_current_screen();
    if ($screen && $screen->id === 'toplevel_page_ez-it-template') {
        remove_all_actions('admin_notices');
        remove_all_actions('all_admin_notices');
    }
}, 1000);

/**
 * AJAX Theme Toggle Handler
 */
add_action('wp_ajax_ez_it_template_toggle_theme', function() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Insufficient permissions']);
    }
    
    check_ajax_referer('ez_it_template_nonce', 'nonce');
    
    $current_theme = get_option('ez_it_template_theme', 'dark');
    $new_theme = $current_theme === 'dark' ? 'light' : 'dark';
    
    update_option('ez_it_template_theme', $new_theme);
    
    wp_send_json_success([
        'theme' => $new_theme,
        'message' => 'Theme updated successfully'
    ]);
});

/**
 * Save Advanced Settings Handler
 */
add_action('admin_post_ez_it_template_save_settings', function() {
    if (!current_user_can('manage_options')) {
        wp_die('Insufficient permissions');
    }
    
    check_admin_referer('ez_it_template_save_settings');
    
    // Save settings
    update_option('ez_it_template_cache_enabled', isset($_POST['cache_enabled']) ? 1 : 0);
    update_option('ez_it_template_debug_mode', isset($_POST['debug_mode']) ? 1 : 0);
    update_option('ez_it_template_auto_cleanup', isset($_POST['auto_cleanup']) ? 1 : 0);
    
    $token_expiry = isset($_POST['token_expiry_hours']) ? intval($_POST['token_expiry_hours']) : 24;
    $token_expiry = max(1, min(168, $token_expiry)); // Clamp between 1-168 hours
    update_option('ez_it_template_token_expiry_hours', $token_expiry);
    
    wp_redirect(add_query_arg([
        'page' => 'ez-it-template',
        'tab' => 'settings',
        'ez_notice' => 'settings_saved'
    ], admin_url('admin.php')));
    exit;
});

/**
 * AJAX Tab Loading Handler
 */
add_action('wp_ajax_ez_it_template_load_tab', function () {
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Insufficient permissions']);
    }
    
    check_ajax_referer('ez_it_template_nonce', 'nonce');
    
    $tab = isset($_POST['tab']) ? sanitize_key($_POST['tab']) : 'dashboard';
    $theme = get_option('ez_it_template_theme', 'dark');
    
    ob_start();
    
    switch ($tab) {
        case 'dashboard':
            EzIT_Template_Admin_Page_Tabbed::render_dashboard_tab();
            break;
        case 'settings':
            EzIT_Template_Admin_Page_Tabbed::render_settings_tab($theme);
            break;
        case 'about':
            EzIT_Template_Admin_Page_Tabbed::render_about_tab();
            break;
        default:
            echo '<p>Tab not found.</p>';
    }
    
    $content = ob_get_clean();
    
    wp_send_json_success([
        'content' => $content,
        'tab' => $tab
    ]);
});

/**
 * Plugin activation
 */
register_activation_hook(__FILE__, function() {
    // Set default options
    add_option('ez_it_template_theme', 'dark');
    add_option('ez_it_template_cache_enabled', true);
    add_option('ez_it_template_debug_mode', false);
    add_option('ez_it_template_token_expiry_hours', 24);
    add_option('ez_it_template_auto_cleanup', true);
});

/**
 * Plugin deactivation
 */
register_deactivation_hook(__FILE__, function() {
    // Cleanup code here if needed
});
