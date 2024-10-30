<?php

add_filter('plugin_action_links_' . HIDE_WP_ADMIN_LOGIN_BASENAME, 'hwal_plugin_action_links');
if (!function_exists('hwal_plugin_action_links')) {
    function hwal_plugin_action_links( $links ) {
        array_unshift($links, '<a href="' . admin_url( 'options-general.php#hwal_settings' ) . '">' . __('Settings', 'hide-wp-admin-login') . '</a>');

        return $links;
    }
}

add_action('admin_menu', 'hide_wp_admin_login_menu_page');
if (!function_exists('hide_wp_admin_login_menu_page')) {
    function hide_wp_admin_login_menu_page() {
        $title = __('Hide WP Admin Login');

        add_options_page($title, $title, 'manage_options', 'hwal_settings', 'hwal_settings_page');
    }
}

if (!function_exists('hwal_settings_page')) {
    function hwal_settings_page() {
        _e('Hide WP Admin Login');
    }
}

add_action('admin_init', 'hwal_template_redirect');
if (!function_exists('hwal_template_redirect')) {
    function hwal_template_redirect() {
        if (!empty($_GET) && isset($_GET['page']) && 'hwal_settings' === $_GET['page']) {
            wp_redirect(admin_url('options-general.php#hwal_settings'));
            exit();
        }
    }
}

add_action('admin_init', 'hwal_admin_init');
if (!function_exists('hwal_admin_init')) {
    function hwal_admin_init() {
        add_settings_section(
            'hide-wp-admin-login-section',
            'Hide WP Admin Login',
            'hwal_section_desc',
            'general'
        );

        add_settings_field(
            'hwal_page',
            '<label for="hwal_page">' . __('Login URL', 'hide-wp-admin-login') . '</label>',
            'hwal_page_input',
            'general',
            'hide-wp-admin-login-section'
        );

        add_settings_field(
            'hwal_redirect_admin',
            '<label for="hwal_redirect_admin">' . __('Redirection URL', 'hide-wp-admin-login') . '</label>',
            'hwal_redirect_admin_input',
            'general',
            'hide-wp-admin-login-section'
        );

        register_setting('general', 'hwal_page', 'sanitize_title_with_dashes');
        register_setting('general', 'hwal_redirect_admin', 'sanitize_title_with_dashes');
    }
}

if (!function_exists('hwal_section_desc')) {
    function hwal_section_desc() {
        _e( '<div id="hwal_settings"></div>' );
    }
}

if (!function_exists('hwal_page_input')) {
    function hwal_page_input() {
        if(get_option('permalink_structure')) {
            _e( '<code>' . trailingslashit(home_url()) . '</code> <input id="hwal_page" type="text" name="hwal_page" value="' . esc_attr( hwal_new_login_slug() ) . '">' . (hwal_use_trailing_slashes() ? ' <code>/</code>' : '') );
        }else{
            _e( '<code>' . trailingslashit(home_url()) . '?</code> <input id="hwal_page" type="text" name="hwal_page" value="' . esc_attr( hwal_new_login_slug() ) . '">' );
        }
        _e( '<p class="description">' . __('Protect your website by changing the login URL and preventing access to the wp-login.php page and the wp-admin directory to non-connected people.', 'hide-wp-admin-login') . '</p>' );
    }
}

if (!function_exists('hwal_redirect_admin_input')) {
    function hwal_redirect_admin_input() {
        if(get_option('permalink_structure')) {
            _e( '<code>' . trailingslashit(home_url()) . '</code> <input id="hwal_redirect_admin" type="text" name="hwal_redirect_admin" value="' . esc_attr( hwal_new_redirect_slug() ) . '">' . (hwal_use_trailing_slashes() ? ' <code>/</code>' : '') );
        }else{
            _e( '<code>' . trailingslashit(home_url()) . '?</code> <input id="hwal_redirect_admin" type="text" name="hwal_redirect_admin" value="' . esc_attr( hwal_new_redirect_slug() ) . '">' );
        }

        _e( '<p class="description">' . __('Redirect URL when someone tries to access the wp-admin directory while not logged in.', 'hide-wp-admin-login') . '</p>' );
    }
}

add_action('admin_notices', 'hwal_admin_notices');
if (!function_exists('hwal_admin_notices')) {
    function hwal_admin_notices() {
        global $pagenow;

        if(!is_network_admin() && $pagenow === 'options-general.php' && isset($_GET['settings-updated']) && !isset($_GET['page'])) {

            _e( '<div class="updated notice is-dismissible"><p>' . sprintf( __('Your login page is now here: <strong><a href="%1$s">%2$s</a></strong>. Bookmark this page!', 'hide-wp-admin-login' ), hwal_new_login_url(), hwal_new_login_url()) . '</p></div>' );
        }
    }
}
