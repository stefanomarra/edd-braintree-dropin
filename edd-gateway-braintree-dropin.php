<?php
/*
Plugin Name: Easy Digital Downloads - Braintree Drop-In
Description: Accept payments in EDD using Braintree Drop-In UI
Author: Stefano Marra
Author URI: https://www.stefanomarra.com
Version: 1.0.0
Text Domain: edd-braintree-dropin
Domain Path: languages

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// Exit if accessed directly
if ( !defined('ABSPATH') ) exit;

/**
  * Define some variables
  */
define('EDD_BRAINTREE_DROPIN_DOMAIN', 'edd-braintree-dropin');

/**
 * Actions
 */
add_action( 'admin_init', 'edd_braintree_dropin_check_requirements' );




/**
 * Check for required plugins and versions
 *
 * @since 1.0.0
 * @return bool
 */
function edd_braintree_dropin_check_requirements() {
	global $wp_version;

	if ( version_compare( $wp_version, '4.2', '<' ) ) {
		add_action( 'admin_notices', 'edd_braintree_dropin_wp_notice' );
		return false;
	}
	else if ( ! class_exists( 'Easy_Digital_Downloads' ) || version_compare( EDD_VERSION, '2.5', '<' ) ) {
		add_action( 'admin_notices', 'edd_braintree_dropin_edd_notice' );
		return false;
	}
}

/**
 * Braintree WP version notice
 *
 * @since 1.0.0
 * @return void
 */
function edd_braintree_dropin_edd_notice() { ?>
	<div class="updated">
		<p><?php _e( '<strong>Notice:</strong> Easy Digital Downloads Braintree Drop-In requires Easy Digital Downloads 2.5 or higher', EDD_BRAINTREE_DROPIN_DOMAIN ); ?></p>
	</div>
<?php
}

/**
 * Braintree WP version notice
 *
 * @since 1.0.0
 * @return void
 */
function edd_braintree_dropin_wp_notice() { ?>
	<div class="updated">
		<p><?php _e( '<strong>Notice:</strong> Easy Digital Downloads Braintree Drop-In requires Wordpress 4.2 or higher', EDD_BRAINTREE_DROPIN_DOMAIN ); ?></p>
	</div>
<?php
}