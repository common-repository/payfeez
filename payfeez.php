<?php
defined('ABSPATH') || die();

/**
 * Plugin Name: PayFeez
 * Description: Apply fees based on the WooCommerce payment gateway selected by the customer.
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: Killian Santos
 * Author URI: https://killian-santos.com/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: payfeez
 * Domain Path: /languages
 */

/**
 * Load translations
 */
function payfeez_load_textdomain()
{
    load_plugin_textdomain('payfeez', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('init', 'payfeez_load_textdomain');

/**
 * Check if WooCommerce is installed and activated
 */
function payfeez_is_woocommerce_installed()
{
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    return is_plugin_active('woocommerce/woocommerce.php');
}

if (payfeez_is_woocommerce_installed()) {
    require_once plugin_dir_path(__FILE__) . 'admin/admin-page.php'; // Include admin page with extension settings
    require_once plugin_dir_path(__FILE__) . 'includes/class-bacs-fee.php'; // Include BACS fee function
    require_once plugin_dir_path(__FILE__) . 'includes/class-paypal-fee.php'; // Include Paypal fee function
    require_once plugin_dir_path(__FILE__) . 'includes/class-stripe-fee.php'; // Include Stripe fee function
    require_once plugin_dir_path(__FILE__) . 'includes/class-stancer-fee.php'; // Include Stancer fee function
    require_once plugin_dir_path(__FILE__) . 'includes/class-checkout-refresh.php'; // Include checkout refresh on payment method change function
    require_once plugin_dir_path(__FILE__) . 'includes/class-required-min-cart-subtotal-amount.php'; // Include the function to block an order based on the amount in the WooCommerce shopping cart
} else {
    /**
     * Display an admin notice if WooCommerce is not installed or activated
     */
    function payfeez_woocommerce_not_detected()
    {
        echo '<div class="notice notice-error">';
        echo '<p>';
        echo 'PayFeez ';
        esc_html_e('requires', 'payfeez');
        echo ' <a href="https://woocommerce.com/" target="_blank">WooCommerce</a> ';
        esc_html_e('to be installed and active.', 'payfeez');
        echo '</p>';
        echo '</div>';
    }
    add_action('admin_notices', 'payfeez_woocommerce_not_detected');
}
