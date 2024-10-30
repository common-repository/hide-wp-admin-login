<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   Hide WP Admin Login
 * @author    AppAspect
 * @link      https://www.appaspect.com/
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'hwal_page' );
delete_option( 'hwal_redirect_admin' );

flush_rewrite_rules();

//info: optimize table
$GLOBALS['wpdb']->query( "OPTIMIZE TABLE `" . $GLOBALS['wpdb']->prefix . "options`" );
