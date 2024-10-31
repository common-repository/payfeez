<?php

defined('ABSPATH') || die();

add_action('woocommerce_cart_calculate_fees', 'payfeez_bacs_fee', 25);

// Add fee if "bacs" is the selected payment method
function payfeez_bacs_fee($cart)
{

    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }

    // Get option payfeez_rmcsa
    $payfeez_bacs_settings = get_option('payfeez_bacs');

    // Check if value of payfeez_bacs_activation = 1
    if (isset($payfeez_bacs_settings['payfeez_bacs_activation']) && $payfeez_bacs_settings['payfeez_bacs_activation'] == 1) {
        // Equal 1

        // if "bacs" is the selected payment method, we add the fee
        if ('bacs' == WC()->session->get('chosen_payment_method')) {

            $cart = WC()->cart;
            $cart_total = $cart->cart_contents_total + $cart->shipping_total; // Sum total of product price and shipping costs

            $payfeez_bacs_options = get_option('payfeez_bacs');

            $fixed_fee = isset($payfeez_bacs_options['payfeez_bacs_fixed_fee']) && $payfeez_bacs_options['payfeez_bacs_fixed_fee'] !== '' ? $payfeez_bacs_options['payfeez_bacs_fixed_fee'] : 0;
            $variable_fee = isset($payfeez_bacs_options['payfeez_bacs_variable_fee']) && $payfeez_bacs_options['payfeez_bacs_variable_fee'] !== '' ? $payfeez_bacs_options['payfeez_bacs_variable_fee'] / 100 * $cart_total : 0; // Calculate variable costs
            $fee = $fixed_fee + $variable_fee; // Sum fixed fee and variable fee

            global $payfeez_bacs_fee; // Déclarez la variable globale
            $payfeez_bacs_fee = $fee; // Assignez la valeur de $fee à la variable globale

            WC()->cart->add_fee(__('Payment fees', 'payfeez'), $fee); // Display the amount of the fee in the order summary
        }
        // Not equal 1
        return;
    }
}


add_filter('woocommerce_gateway_icon', 'payfeez_display_bacs_fee', 25, 2);
function payfeez_display_bacs_fee($icon_html, $gateway_id)
{

    global $payfeez_bacs_fee; // Get the global variable

    // Check if $payfeez_bacs_fee is 0 or empty
    if (empty($payfeez_bacs_fee) || $payfeez_bacs_fee == 0) {
        return $icon_html; // Do nothing and return the original HTML icon
    }

    if ('bacs' === $gateway_id) {
        return '<span class="custom-gateway-fee">+ ' . wc_price($payfeez_bacs_fee) . '</span>';
    }

    return $icon_html;
}
