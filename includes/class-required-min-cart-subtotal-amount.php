<?php

defined('ABSPATH') || die();

add_action('woocommerce_check_cart_items', 'payfeez_check_if_rmcsa_activated');

/*
* Check if the module RMCSA is activated
*/
function payfeez_check_if_rmcsa_activated()
{
    // Get option payfeez_rmcsa
    $payfeez_rmcsa = get_option('payfeez_rmcsa');

    // Check if value of payfeez_rmcsa_activation = 1
    if (isset($payfeez_rmcsa['payfeez_rmcsa_activation']) && $payfeez_rmcsa['payfeez_rmcsa_activation'] == 1) {
        // Equal 1
        payfeez_required_min_cart_subtotal_amount();
    }

    // Not equal 1
    return;
}


/*
* Check if WooCommerce Cart is empty or not
*/
function payfeez_check_if_cart_empty()
{
    // Check if WooCommerce is activated
    if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        // Check if cart is empty
        if (WC()->cart->is_empty()) {
            // Cart is empty do nothing
            return;
        } else {
            // The cart is not empty: call the function to check the minimum amount
            payfeez_required_min_cart_subtotal_amount();
        }
    }
}


/*
* Run function RMCSA
*/
function payfeez_required_min_cart_subtotal_amount()
{
    // Get option payfeez_rmcsa and return value of payfeez_rmcsa_amount
    $payfeez_rmcsa_amount = get_option('payfeez_rmcsa')['payfeez_rmcsa_amount'];

    // Set minimum cart total amount
    $minimum_amount = intval($payfeez_rmcsa_amount);

    // Cart subtotal (before taxes and shipping charges)
    $cart_subtotal = WC()->cart->subtotal;

    // Add an error notice if cart subtotal is less than the minimum required
    if ($cart_subtotal < $minimum_amount) {

        // Display custom error message
        wc_add_notice(sprintf(
            /* translators: %s: Minimum order amount */
            __('<strong>A minimum amount of %s is required to validate your order.</strong>', 'payfeez'),
            wc_price($minimum_amount)
        ), 'error');
    }
}

