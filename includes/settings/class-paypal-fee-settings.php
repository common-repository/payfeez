<?php

defined('ABSPATH') || die();

/**
 * Registers settings
 */
add_action('admin_init', 'payfeez_paypal');

/**
 * Check if paypal Gateway is activated
 */
function payfeez_check_paypal_gateway()
{
    // Get all active payment gateways
    $active_gateways = WC_Payment_Gateways::instance()->get_available_payment_gateways();

    // Check if the paypal gateway is active
    if (isset($active_gateways['paypal']) && $active_gateways['paypal']->enabled === 'yes') {
        // Execute the function payfeez_check_if_paypal_fee_activated
        payfeez_paypal();
    }
}

function payfeez_paypal()
{
    register_setting(
        'payfeez_paypal_settings',
        'payfeez_paypal',
        'payfeez_paypal_sanitize'
    );

    add_settings_section(
        'payfeez_paypal_section',
        __('Payment fees for PayPal payments', 'payfeez'),
        'payfeez_paypal_section_display',
        'payfeez_paypal_settings_page'
    );

    add_settings_field(
        'payfeez_paypal_activation',
        __('Activate payment fees', 'payfeez'),
        'payfeez_paypal_activation_field_display',
        'payfeez_paypal_settings_page',
        'payfeez_paypal_section',
        array(
            'label_for' => 'payfeez_paypal_activation',
            'description' => __('Activate the calculation and application of payment fees', 'payfeez'),
        )
    );

    add_settings_field(
        'payfeez_paypal_fixed_fee',
        __('Fixed amount', 'payfeez'),
        'payfeez_paypal_fixed_fee_field_display',
        'payfeez_paypal_settings_page',
        'payfeez_paypal_section',
        array(
            'label_for' => 'payfeez_paypal_fixed_fee',
        )
    );

    add_settings_field(
        'payfeez_paypal_variable_fee',
        __('Variable amount (%)', 'payfeez'),
        'payfeez_paypal_variable_fee_field_display',
        'payfeez_paypal_settings_page',
        'payfeez_paypal_section',
        array(
            'label_for' => 'payfeez_paypal_variable_fee',
        )
    );
}

/**
 * Displays the content of the multiple settings section
 * 
 * @param  array  $args  Arguments passed to the add_settings_section() function call
 */
function payfeez_paypal_section_display($args)
{
    // Just var_dumping data here to help you visualize the array organization.
    // var_dump(get_option('payfeez_paypal'));
}

/**
 * Displays the checkbox field
 * 
 * @param  array  $args  Arguments passed to corresponding add_settings_field() call
 */
function payfeez_paypal_activation_field_display($args)
{
    $settings = get_option('payfeez_paypal');
    $checked = (bool) $settings['payfeez_paypal_activation'] ?: false;
?>
    <input id="<?php echo esc_attr($args['label_for']); ?>" type="checkbox" name="payfeez_paypal[payfeez_paypal_activation]" <?php checked($checked); ?>>
    <span><?php echo esc_html($args['description']); ?></span>
<?php
}

/**
 * Displays the text field
 * Note the `name` attribute of the input, referring now to an array of settings.
 * 
 * @param  array  $args  Arguments passed to corresponding add_settings_field() call
 */
function payfeez_paypal_fixed_fee_field_display($args)
{
    $settings = get_option('payfeez_paypal');
    $value = !empty($settings['payfeez_paypal_fixed_fee']) ? $settings['payfeez_paypal_fixed_fee'] : '';
?>
    <input id="<?php echo esc_attr($args['label_for']); ?>" class="regular-text" type="number" min="0" max="5" step="any" name="payfeez_paypal[payfeez_paypal_fixed_fee]" value="<?php echo esc_attr($value); ?>">
<?php
}

/**
 * Displays the text field
 * Note the `name` attribute of the input, referring now to an array of settings.
 * 
 * @param  array  $args  Arguments passed to corresponding add_settings_field() call
 */
function payfeez_paypal_variable_fee_field_display($args)
{
    $settings = get_option('payfeez_paypal');
    $value = !empty($settings['payfeez_paypal_variable_fee']) ? $settings['payfeez_paypal_variable_fee'] : '';
?>
    <input id="<?php echo esc_attr($args['label_for']); ?>" class="regular-text" type="number" min="0" max="10" step="any" name="payfeez_paypal[payfeez_paypal_variable_fee]" value="<?php echo esc_attr($value); ?>">
<?php
}

/**
 * Sanitize callback for our settings
 * We have to sanitize each of our fields ourselves.
 * 
 * @param  array  $settings  An array of settings (due to the inputs' name attributes)
 */
function payfeez_paypal_sanitize($settings)
{
    // Sanitizes the fields
    $settings['payfeez_paypal_activation'] = isset($settings['payfeez_paypal_activation']);
    $settings['payfeez_paypal_fixed_fee'] = !empty($settings['payfeez_paypal_fixed_fee']) ? sanitize_text_field($settings['payfeez_paypal_fixed_fee']) : '';
    $settings['payfeez_paypal_variable_fee'] = !empty($settings['payfeez_paypal_variable_fee']) ? sanitize_text_field($settings['payfeez_paypal_variable_fee']) : '';

    return $settings;
}
