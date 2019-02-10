<?php
/**
 * WooCommerce Cart Notices
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Cart Notices to newer
 * versions in the future. If you wish to customize WooCommerce Cart Notices for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-cart-notices/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2019, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Cart_Notices;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_3_0 as Framework;

/**
 * Plugin lifecycle handler.
 *
 * @since 1.9.0
 *
 * @method \WC_Cart_Notices get_plugin()
 */
class Lifecycle extends Framework\Plugin\Lifecycle {


	/**
	 * Handles initial installation routine.
	 *
	 * @since 1.9.0
	 */
	protected function install() {
		global $wpdb;

		$wpdb->hide_errors();

		// initial install
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {

			if ( ! empty( $wpdb->charset ) ) {
				$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			}

			if ( ! empty( $wpdb->collate ) ) {
				$collate .= " COLLATE $wpdb->collate";
			}
		}

		$table = $wpdb->prefix . 'cart_notices';
		$sql =
			"CREATE TABLE IF NOT EXISTS $table (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			name varchar(100) NOT NULL,
			enabled boolean NOT NULL default false,
			type varchar(50) NOT NULL,
			message TEXT NOT NULL,
			action varchar(256) NOT NULL,
			action_url varchar(256) NOT NULL,
			data TEXT NOT NULL,
			date_added DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY  (id)
		) " . $collate;
		dbDelta( $sql );
	}


	/**
	 * Handles upgrades.
	 *
	 * @since 1.9.0
	 *
	 * @param string $installed_version the currently installed version
	 */
	protected function upgrade( $installed_version ) {

		if ( ! empty( $installed_version ) ) {

			$upgrades = array(
				'1.2.3' => 'update_to_1_2_3',
			);

			foreach ( $upgrades as $update_to_version => $update_script ) {

				if ( version_compare( $installed_version, $update_to_version, '<' ) ) {

					$this->$update_script();

					$this->get_plugin()->log( sprintf( 'Updated to version %s', $update_to_version ) );
				}
			}
		}
	}


	/**
	 * Updates to version 1.2.3
	 *
	 * @since 1.9.0
	 */
	private function update_to_1_2_3() {

		// old db version option name was removed in 1.2.3
		delete_option( 'wc_cart_notices_db_version' );
	}


}
