<?php
/**
 * Plugin Name: WooCommerce Araxmex Shipping Request Integration by Noesis369  
 * Description: This plugin integrates the Aramex Shipping request API and creates a Shipping request when an order's status changes to processing.
 * Author: Siddharth Bhansali, Noesis369
 * Author URI: http://noesis369.com/
 * Version: 1.0
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

if ( ! class_exists( 'WC_Aramex_Integration' ) ) :

class WC_Aramex_Integration {

    /**
     * Construct the plugin.
     */
    public function __construct() {
        add_action( 'plugins_loaded', array( $this, 'init' ) );
    }

    /**
     * Initialize the plugin.
     */
    public function init() {
        // Checks if WooCommerce is installed.
        if ( class_exists( 'WC_Integration' ) ) {
            // Include our integration class.
            include_once 'includes/class-wc-integration-aramex-integration.php';

            // Register the integration.
            add_filter( 'woocommerce_integrations', array( $this, 'add_integration' ) );
        } else {
            // Add an admin notice if WooCommerce is not installed.
            add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
        }
    }

    /**
     * Add a new integration to WooCommerce.
     *
     * @param array $integrations Existing integrations.
     * @return array Modified integrations.
     */
    public function add_integration( $integrations ) {
        $integrations[] = 'WC_Integration_Aramex_Integration';
        return $integrations;
    }

    /**
     * Display an admin notice if WooCommerce is not installed.
     */
    public function woocommerce_missing_notice() {
        echo '<div class="error"><p>' . __( 'WooCommerce Aramex Integration requires WooCommerce to be installed and active.', 'woocommerce-aramex-integration' ) . '</p></div>';
    }

}

$WC_aramex_integration = new WC_Aramex_Integration( __FILE__ );

endif;
