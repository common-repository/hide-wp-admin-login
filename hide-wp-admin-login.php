<?php
/**
 * Plugin Name: Hide WP Admin Login
 * Plugin URI: https://appaspectshop.com/
 * Description: Hide WordPress Admin Site URL
 * Version: 1.0.0
 * Author: AppAspect
 * Author URI: https://www.appaspect.com/
 * Text Domain:  hide-wp-admin-login
 * Domain Path:  /languages
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

define( 'HIDE_WP_ADMIN_LOGIN_VERSION', '1.0.0' );
define( 'HIDE_WP_ADMIN_LOGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'HIDE_WP_ADMIN_LOGIN_DIR', plugin_dir_path( __FILE__ ) );

// Check plugin requirements
if ( version_compare(PHP_VERSION, '7.1', '<') ) {
    if (!function_exists('hide_wp_admin_login_disable_plugin')) {
        /**
         * Disable plugin
         *
         * @return void
         */
        function hide_wp_admin_login_disable_plugin(){
            if (current_user_can('activate_plugins') && is_plugin_active(HIDE_WP_ADMIN_LOGIN_BASENAME)) {
                deactivate_plugins(__FILE__);
                if(isset($_GET['activate'])) {
                    unset($_GET['activate']);
                }
            }
        }
    }

    if (!function_exists('hide_wp_admin_login_show_error')) {
        /**
         * Show error
         *
         * @return void
         */
        function hide_wp_admin_login_show_error(){

            _e( '<div class="error"><p><strong>Hide WP Admin Login</strong> needs at least PHP 7.1 version, please update before installing the plugin.</p></div>' );
        }
    }

    // add actions
    add_action('admin_init', 'hide_wp_admin_login_disable_plugin');
    add_action('admin_notices', 'hide_wp_admin_login_show_error');

    // do not load anything more
    return;
}

require_once HIDE_WP_ADMIN_LOGIN_DIR . 'admin/admin.php';
require_once HIDE_WP_ADMIN_LOGIN_DIR . 'inc/functions.php';
