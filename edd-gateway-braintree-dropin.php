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

class EDD_Braintree_Dropin {

	private static $instance;

	private $merchant_id;
	private $merchant_account_id;
	private $public_key;
	private $private_key;

	/**
	 * Get object instance
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new EDD_Braintree_Dropin();
		}

		return self::$instance;
	}

	/**
	 * Construct
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		if ( ! $this->check_requirements() ) {
			return false;
		}

		add_filter( 'edd_payment_gateways', array( $this, 'register_gateway') );
		add_filter( 'edd_settings_gateways', array( $this, 'add_settings' ) );
		add_filter( 'edd_settings_sections_gateways', array( $this, 'settings_section' ) );

		$this->merchant_id         = edd_get_option( 'edd_braintree_dropin_merchant_id', '' );
		$this->merchant_account_id = edd_get_option( 'edd_braintree_dropin_merchant_account_id', '' );
		$this->public_key          = edd_get_option( 'edd_braintree_dropin_public_key', '' );
		$this->private_key         = edd_get_option( 'edd_braintree_dropin_private_key', '' );
	}

	/**
	 * Register "edd_braintree_dropin" gateway
	 *
	 * @param array $gateways
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function register_gateway( $gateways ) {

		$gateways['edd_braintree_dropin'] = array(
				'admin_label'    => 'Braintree Drop-In',
				'checkout_label' => __( 'Credit Card', EDD_BRAINTREE_DROPIN_DOMAIN )
			);

		return $gateways;
	}

	/**
	 * Add EDD Braintree Settings
	 *
	 * @param array $settings
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function add_settings( $settings ) {

		return array_merge( $settings, array(
				'braintree_dropin' => array(
					array(
						'id'   => 'edd_braintree_dropin_header',
						'name' => '<strong>' . __( 'Braintree Settings', EDD_BRAINTREE_DROPIN_DOMAIN ) . '</strong>',
						'desc' => __( 'Configure Braintree payment gateway', EDD_BRAINTREE_DROPIN_DOMAIN ),
						'type' => 'header'
					),
					array(
						'id'   => 'edd_braintree_dropin_merchant_id',
						'name' => __( 'Merchant ID', EDD_BRAINTREE_DROPIN_DOMAIN ),
						'desc' => __( 'Enter your Merchant ID.', EDD_BRAINTREE_DROPIN_DOMAIN ),
						'type' => 'text',
						'size' => 'regular',
					),
					array(
						'id'   => 'edd_braintree_dropin_merchant_account_id',
						'name' => __( 'Merchant Account ID', EDD_BRAINTREE_DROPIN_DOMAIN ),
						'desc' => __( 'Enter your Merchant Account ID.', EDD_BRAINTREE_DROPIN_DOMAIN ),
						'type' => 'text',
						'size' => 'regular',
					),
					array(
						'id'   => 'edd_braintree_dropin_public_key',
						'name' => __( 'Public Key', EDD_BRAINTREE_DROPIN_DOMAIN ),
						'desc' => __( 'Enter your Public Key.', EDD_BRAINTREE_DROPIN_DOMAIN ),
						'type' => 'text',
						'size' => 'regular',
					),
					array(
						'id'   => 'edd_braintree_dropin_private_key',
						'name' => __( 'Private Key', EDD_BRAINTREE_DROPIN_DOMAIN ),
						'desc' => __( 'Enter your Private Key.', EDD_BRAINTREE_DROPIN_DOMAIN ),
						'type' => 'text',
						'size' => 'regular',
					)
				)
			)
		);
	}

	/**
	 * Add EDD Settings Section
	 *
	 * @param array $sections
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function settings_section( $sections ) {
		$sections['braintree_dropin'] = __( 'Braintree Drop-In', EDD_BRAINTREE_DROPIN_DOMAIN );
		return $sections;
	}

	/**
	 * Check for required plugins and versions
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function check_requirements() {
		global $wp_version;

		if ( version_compare( $wp_version, '4.2', '<' ) ) {
			add_action( 'admin_notices', array( $this, 'wp_notice' ) );
			return false;
		}
		else if ( ! class_exists( 'Easy_Digital_Downloads' ) || version_compare( EDD_VERSION, '2.5', '<' ) ) {
			add_action( 'admin_notices', array( $this, 'edd_notice' ) );
			return false;
		}

		return true;
	}

	/**
	 * Braintree WP version notice
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function edd_notice() { ?>
		<div class="updated">
			<p><?php _e( '<strong>Notice:</strong> Easy Digital Downloads Braintree Drop-In requires Easy Digital Downloads 2.5 or higher', EDD_BRAINTREE_DROPIN_DOMAIN ); ?></p>
		</div>
	<?php
	}

	/**
	 * Braintree WP version notice
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function wp_notice() { ?>
		<div class="updated">
			<p><?php _e( '<strong>Notice:</strong> Easy Digital Downloads Braintree Drop-In requires Wordpress 4.2 or higher', EDD_BRAINTREE_DROPIN_DOMAIN ); ?></p>
		</div>
	<?php
	}
}

function edd_braintree_dropin_load_plugin() {
	EDD_Braintree_Dropin::get_instance();
}
add_action( 'plugins_loaded', 'edd_braintree_dropin_load_plugin' );
