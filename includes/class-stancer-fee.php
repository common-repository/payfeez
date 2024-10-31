<?php

defined('ABSPATH') || die();

add_action('woocommerce_cart_calculate_fees', 'payfeez_stancer_fee', 25);

// Add fee if "stancer" is the selected payment method
function payfeez_stancer_fee($cart)
{

    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }

    // Get option payfeez_rmcsa
    $payfeez_stancer_settings = get_option('payfeez_stancer');

    // Check if value of payfeez_stancer_activation = 1
    if (isset($payfeez_stancer_settings['payfeez_stancer_activation']) && $payfeez_stancer_settings['payfeez_stancer_activation'] == 1) {
        // Equal 1

        // if "stancer" is the selected payment method, we add the fee
        if ('stancer' == WC()->session->get('chosen_payment_method')) {

            $cart = WC()->cart;
            $cart_total = $cart->cart_contents_total + $cart->shipping_total; // Sum total of product price and shipping costs

            $payfeez_stancer_options = get_option('payfeez_stancer');

            $fixed_fee = isset($payfeez_stancer_options['payfeez_stancer_fixed_fee']) && $payfeez_stancer_options['payfeez_stancer_fixed_fee'] !== '' ? $payfeez_stancer_options['payfeez_stancer_fixed_fee'] : 0;
            $variable_fee = isset($payfeez_stancer_options['payfeez_stancer_variable_fee']) && $payfeez_stancer_options['payfeez_stancer_variable_fee'] !== '' ? $payfeez_stancer_options['payfeez_stancer_variable_fee'] / 100 * $cart_total : 0; // Calculate variable costs
            $fee = $fixed_fee + $variable_fee; // Sum fixed fee and variable fee

            global $payfeez_stancer_fee; // Déclarez la variable globale
            $payfeez_stancer_fee = $fee; // Assignez la valeur de $fee à la variable globale

            WC()->cart->add_fee(__('Payment fees', 'payfeez'), $fee); // Display the amount of the fee in the order summary
        }
        // Not equal 1
        return;
    }
}


add_filter('woocommerce_gateway_icon', 'payfeez_display_stancer_fee', 25, 2);
function payfeez_display_stancer_fee($icon_html, $gateway_id)
{

    global $payfeez_stancer_fee; // Get the global variable

    // Check if $payfeez_stancer_fee is 0 or empty
    if (empty($payfeez_stancer_fee) || $payfeez_stancer_fee == 0) {
        return $icon_html; // Do nothing and return the original HTML icon
    }

    if ('stancer' === $gateway_id) {
        return '<span class="custom-gateway-fee">+ ' . wc_price($payfeez_stancer_fee) . '</span>';
    }

    return $icon_html;
}
