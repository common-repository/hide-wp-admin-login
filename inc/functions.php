<?php

$wp_login_php = false;

if (!function_exists('hwal_user_trailingslashit')) {
	function hwal_user_trailingslashit( $string ) {
	    return hwal_use_trailing_slashes() ? trailingslashit( $string ) : untrailingslashit( $string );
	}
}

if (!function_exists('hwal_wp_template_loader')) {
	function hwal_wp_template_loader() {
	    global $pagenow;
	    $pagenow = 'index.php';
	    if ( ! defined( 'WP_USE_THEMES' ) ) {
	        define( 'WP_USE_THEMES', true );
	    }
	    wp();
	    require_once( ABSPATH . WPINC . '/template-loader.php' );
	    die;
	}
}

if (!function_exists('hwal_new_login_slug')) {
	function hwal_new_login_slug() {
	    if($slug = get_option('hwal_page')) {
	        return $slug;
	    }else if($slug = 'login') {
	        return $slug;
	    }
	}
}

if (!function_exists('hwal_new_redirect_slug')) {
	function hwal_new_redirect_slug() {
	    if($slug = get_option('hwal_redirect_admin')) {
	        return $slug;
	    }else if($slug = '404') {
	        return $slug;
	    }
	}
}

if (!function_exists('hwal_new_login_url')) {
	function hwal_new_login_url( $scheme = null ) {
	    $url = apply_filters('hide_wp_admin_login_home_url', home_url('/', $scheme));

	    if(get_option('permalink_structure')) {
	        return hwal_user_trailingslashit($url . hwal_new_login_slug());
	    }else{
	        return $url . '?' . hwal_new_login_slug();
	    }
	}
}

if (!function_exists('hwal_use_trailing_slashes')) {
	function hwal_use_trailing_slashes() {
	    return ('/' === substr( get_option('permalink_structure'), - 1, 1));
	}
}

if (!function_exists('hwal_new_redirect_url')) {
	function hwal_new_redirect_url( $scheme = null ) {
	    if( get_option( 'permalink_structure' ) ) {
	        return hwal_user_trailingslashit( home_url( '/', $scheme ) . hwal_new_redirect_slug() );
	    }else{
	        return home_url( '/', $scheme ) . '?' . hwal_new_redirect_slug();
	    }
	}
}

add_action('plugins_loaded', 'hwal_plugins_loaded', 9999 );
if (!function_exists('hwal_plugins_loaded')) {
	function hwal_plugins_loaded() {
	    global $pagenow, $wp_login_php;

	    $request = parse_url( rawurldecode( sanitize_url($_SERVER['REQUEST_URI']) ) );

	    if ( ( strpos( rawurldecode( $_SERVER['REQUEST_URI'] ), 'wp-login.php' ) !== false
	           || ( isset( $request['path'] ) && untrailingslashit( $request['path'] ) === site_url( 'wp-login', 'relative' ) ) )
	         && ! is_admin() ) {

	        $wp_login_php = true;

	        $_SERVER['REQUEST_URI'] = hwal_user_trailingslashit( '/' . str_repeat( '-/', 10 ) );

	        $pagenow = 'index.php';

	    } elseif ( ( isset( $request['path'] ) && untrailingslashit( $request['path'] ) === home_url( hwal_new_login_slug(), 'relative' ) )
	               || ( ! get_option( 'permalink_structure' )
	                    && isset( $_GET[ hwal_new_login_slug() ] )
	                    && empty( $_GET[ hwal_new_login_slug() ] ) ) ) {

	        $pagenow = 'wp-login.php';

	    } elseif ( ( strpos( rawurldecode( $_SERVER['REQUEST_URI'] ), 'wp-register.php' ) !== false
	                 || ( isset( $request['path'] ) && untrailingslashit( $request['path'] ) === site_url( 'wp-register', 'relative' ) ) )
	               && ! is_admin() ) {

	        $wp_login_php = true;

	        $_SERVER['REQUEST_URI'] = hwal_user_trailingslashit( '/' . str_repeat( '-/', 10 ) );

	        $pagenow = 'index.php';
	    }
	}
}

add_action('wp_loaded', 'hwal_wp_loaded');
if (!function_exists('hwal_wp_loaded')) {
	function hwal_wp_loaded() {
	    global $pagenow, $wp_login_php;

	    $request = parse_url( rawurldecode( sanitize_url($_SERVER['REQUEST_URI']) ) );

	    if ( ! ( isset( $_GET['action'] ) && $_GET['action'] === 'postpass' && isset( $_POST['post_password'] ) ) ) {
	        if ( is_admin() && ! is_user_logged_in() && ! defined( 'WP_CLI' ) && ! defined( 'DOING_AJAX' ) && ! defined( 'DOING_CRON' ) && $pagenow !== 'admin-post.php' && $request['path'] !== '/wp-admin/options.php' ) {
	            wp_safe_redirect( hwal_new_redirect_url() );
	            die();
	        }

	        if ( ! is_user_logged_in() && isset( $_GET['wc-ajax'] ) && $pagenow === 'profile.php' ) {
	            wp_safe_redirect( hwal_new_redirect_url() );
	            die();
	        }

	        if ( ! is_user_logged_in() && isset( $request['path'] ) && $request['path'] === '/wp-admin/options.php' ) {
	            header('Location: ' . hwal_new_redirect_url() );
	            die;
	        }

	        if ( $pagenow === 'wp-login.php' && isset( $request['path'] ) && $request['path'] !== hwal_user_trailingslashit( $request['path'] ) && get_option( 'permalink_structure' ) ) {
	            wp_safe_redirect( hwal_user_trailingslashit( hwal_new_login_url() )
	                              . ( ! empty( $_SERVER['QUERY_STRING'] ) ? '?' . $_SERVER['QUERY_STRING'] : '' ) );
	            die;
	        } elseif ( $wp_login_php ) {
	            if ( ( $referer = wp_get_referer() )
	                 && strpos( $referer, 'wp-activate.php' ) !== false
	                 && ( $referer = parse_url( $referer ) )
	                 && ! empty( $referer['query'] ) ) {

	                parse_str( $referer['query'], $referer );

	                @require_once WPINC . '/ms-functions.php';

	                if ( ! empty( $referer['key'] )
	                     && ( $result = wpmu_activate_signup( $referer['key'] ) )
	                     && is_wp_error( $result )
	                     && ( $result->get_error_code() === 'already_active'
	                          || $result->get_error_code() === 'blog_taken' ) ) {

	                    wp_safe_redirect( hwal_new_login_url()
	                                      . ( ! empty( $_SERVER['QUERY_STRING'] ) ? '?' . $_SERVER['QUERY_STRING'] : '' ) );

	                    die;
	                }
	            }

	            hwal_wp_template_loader();
	        } elseif ( $pagenow === 'wp-login.php' ) {
	            global $error, $interim_login, $action, $user_login;

	            $redirect_to = admin_url();

	            $requested_redirect_to = '';
	            if ( isset( $_REQUEST['redirect_to'] ) ) {
	                $requested_redirect_to = sanitize_url($_REQUEST['redirect_to']);
	            }

	            if ( is_user_logged_in() ) {
	                $user = wp_get_current_user();
	                if ( ! isset( $_REQUEST['action'] ) ) {
	                    $logged_in_redirect = apply_filters( 'whl_logged_in_redirect', $redirect_to, $requested_redirect_to, $user );
	                    wp_safe_redirect( $logged_in_redirect );
	                    die();
	                }
	            }
	            @require_once ABSPATH . 'wp-login.php';
	            die;
	        }
	    }
	}
}

add_filter( 'site_url', 'hwal_site_url', 10, 4 );
if (!function_exists('hwal_site_url')) {
	function hwal_site_url( $url, $path, $scheme, $blog_id ) {
	    return hwal_filter_wp_login_php( $url, $scheme );
	}
}

if (!function_exists('hwal_filter_wp_login_php')) {
	function hwal_filter_wp_login_php( $url, $scheme = null ) {
	    if ( strpos( $url, 'wp-login.php?action=postpass' ) !== false ) {
	        return $url;
	    }
	    if ( strpos( $url, 'wp-login.php' ) !== false && strpos( wp_get_referer(), 'wp-login.php' ) === false ) {
	        if ( is_ssl() ) {

	            $scheme = 'https';
	        }
	        $args = explode( '?', $url );
	        if ( isset( $args[1] ) ) {
	            parse_str( $args[1], $args );
	            if ( isset( $args['login'] ) ) {
	                $args['login'] = rawurlencode( $args['login'] );
	            }
	            $url = add_query_arg( $args, hwal_new_login_url( $scheme ) );
	        } else {
	            $url = hwal_new_login_url( $scheme );
	        }
	    }
	    return $url;
	}
}
