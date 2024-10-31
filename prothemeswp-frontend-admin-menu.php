<?php
/*
Plugin Name: ProThemesWP Frontend Admin Menu
Plugin URI: https://wordpress.org/support/plugin/prothemeswp-frontend-admin-menu
Description: Displays the admin menu on the frontend of your website.
Author: ProThemesWP
Author URI: https://wordpress.org/support/users/prothemeswp
Text Domain: prothemeswp-frontend-admin-menu
Version: 1.0
Copyright: Copyright (c) 2019, ProThemesWP - info@prothemeswp.com
*/

@session_start();


//Add support and reviews links
add_filter( 'plugin_row_meta', function( $plugin_meta, $plugin_file, $plugin_data, $status ) {
		if( plugin_basename( __FILE__ ) == $plugin_file ) {
			$plugin_meta[] = '<a href="https://wordpress.org/support/plugin/' . dirname( plugin_basename( __FILE__ ) ) . '/">' . __( 'Support', 'prothemeswp-jquery-shortcode' ) . '</a>';
			$plugin_meta[] = '<a href="https://wordpress.org/support/plugin/' . dirname( plugin_basename( __FILE__ ) ) . '/reviews/">' . __( 'Reviews', 'prothemeswp-jquery-shortcode' ) . '</a>';
		}
		return $plugin_meta;	
}, 10, 4 );

//Start the output buffering
add_action( 'admin_head', 'prothemeswp_frontend_admin_menu_ob_start' );

if( !function_exists( 'prothemeswp_frontend_admin_menu_ob_start' ) ) {

	function prothemeswp_frontend_admin_menu_ob_start() {
		ob_start();
	};

}

//save the output buffer in the session
add_action( 'adminmenu', 'prothemeswp_frontend_admin_menu_get_html' );

function prothemeswp_frontend_admin_menu_get_html() {
	$html = ob_get_clean();
	echo $html;
	$start_of_menu = strpos( $html, '<div id="adminmenumain"');
	$html = substr($html, $start_of_menu);
	$html = str_replace('wp-menu-open', '', $html);
	$html = str_replace('wp-has-current-submenu', 'wp-not-current-submenu', $html);
	$html = preg_replace_callback('/href*\s*=\s*(["\'])(.*?)["\']/i', function($matches) {
		$url = $matches[2];
		if( strpos($url,'http')!==false ) {
			return $matches[0];
		}
		return "href={$matches[1]}" . admin_url( $matches[2] ) . "{$matches[1]}";
	}, $html);
	$_SESSION['prothemeswp-frontend-admin-menu-html'] = $html;
	$_SESSION['prothemeswp-frontend-admin-menu-user-id'] = get_current_user_id();
};

//Add "folded" css class on the frontend
add_filter( 'body_class', 'prothemeswp_frontend_admin_menu_folded_body_class' );

function prothemeswp_frontend_admin_menu_folded_body_class( $classes ) {
    return array_merge( $classes, array( 'folded' ) );
};

//If the user is still logged in, add the html, css and javascript for the menu
add_action( 'wp_footer', 'prothemeswp_frontend_admin_menu_scripts' );

function prothemeswp_frontend_admin_menu_scripts() {
	if(!empty($_REQUEST['customize_theme'])) {
		return;
	}
	if(empty($_SESSION['prothemeswp-frontend-admin-menu-html'])) {
		return;
	}
	$user_id = get_current_user_id();
	if($user_id != $_SESSION['prothemeswp-frontend-admin-menu-user-id']) {
		return;
	}
	echo $_SESSION['prothemeswp-frontend-admin-menu-html'];
	
	wp_enqueue_style('dashicons');
	wp_enqueue_style( 'admin-menu', admin_url( "css/admin-menu.min.css" ) );
	wp_enqueue_style( 'prothemeswp-frontend-admin-menu-frontend', plugins_url( 'prothemeswp-frontend-admin-menu-frontend.css', __FILE__ ) );
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'prothemeswp-frontend-admin-menu-frontend', plugins_url( 'prothemeswp-frontend-admin-menu-frontend.js', __FILE__ ), array( 'jquery' ) );
}