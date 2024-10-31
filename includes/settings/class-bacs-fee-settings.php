<?php

defined('ABSPATH') || die();

/**
 * Registers settings
 */
add_action('admin_init', 'payfeez_bacs');

/**
 * Check if BACS Gateway is activated
 */
function payfeez_check_bacs_gateway()
{
    // Get all active payment gateways
    $active_gateways = WC_Payment_Gateways::instance()->get_available_payment_gateways();

    // Check if the BACS gateway is active
    if (isset($active_gateways['bacs']) && $active_gateways['bacs']->enabled === 'yes') {
        // Execute the function payfeez_check_if_bacs_fee_activated
        payfeez_bacs();
    }
}

function payfeez_bacs()
{
    register_setting(
        'payfeez_bacs_settings',
        'payfeez_bacs',
        'payfeez_bacs_sanitize'
    );

    add_settings_section(
        'payfeez_bacs_section',
        __('Payment fees for bank transfers', 'payfeez'),
        'payfeez_bacs_section_display',
        'payfeez_bacs_settings_page'
    );

    add_settings_field(
        'payfeez_bacs_activation',
        __('Activate payment fees', 'payfeez'),
        'payfeez_bacs_activation_field_display',
        'payfeez_bacs_settings_page',
        'payfeez_bacs_section',
        array(
            'label_for' => 'payfeez_bacs_activation',
            'description' => __('Activate the calculation and application of payment fees', 'payfeez'),
        )
    );

    add_settings_field(
        'payfeez_bacs_fixed_fee',
        __('Fixed amount', 'payfeez'),
        'payfeez_bacs_fixed_fee_field_display',
        'payfeez_bacs_settings_page',
        'payfeez_bacs_section',
        array(
            'label_for' => 'payfeez_bacs_fixed_fee',
        )
    );

    add_settings_field(
        'payfeez_bacs_variable_fee',
        __('Variable amount (%)', 'payfeez'),
        'payfeez_bacs_variable_fee_field_display',
        'payfeez_bacs_settings_page',
        'payfeez_bacs_section',
        array(
            'label_for' => 'payfeez_bacs_variable_fee',
        )
    );
}

/**
 * Displays the content of the multiple settings section
 * 
 * @param  array  $args  Arguments passed to the add_settings_section() function call
 */
function payfeez_bacs_section_display($args)
{
    // Just var_dumping data here to help you visualize the array organization.
    // var_dump(get_option('payfeez_bacs'));
}

/**
 * Displays the checkbox field
 * 
 * @param  array  $args  Arguments passed to corresponding add_settings_field() call
 */
function payfeez_bacs_activation_field_display($args)
{
    $settings = get_option('payfeez_bacs');
    $checked = (bool) $settings['payfeez_bacs_activation'] ?: false;
?>
    <input id="<?php echo esc_attr($args['label_for']); ?>" type="checkbox" name="payfeez_bacs[payfeez_bacs_activation]" <?php checked($checked); ?>>
    <span><?php echo esc_html($args['description']); ?></span>
<?php
}

/**
 * Displays the text field
 * Note the `name` attribute of the input, referring now to an array of settings.
 * 
 * @param  array  $args  Arguments passed to corresponding add_settings_field() call
 */
function payfeez_bacs_fixed_fee_field_display($args)
{
    $settings = get_option('payfeez_bacs');
    $value = !empty($settings['payfeez_bacs_fixed_fee']) ? $settings['payfeez_bacs_fixed_fee'] : '';
?>
    <input id="<?php echo esc_attr($args['label_for']); ?>" class="regular-text" type="number" min="0" max="5" step="any" name="payfeez_bacs[payfeez_bacs_fixed_fee]" value="<?php echo esc_attr($value); ?>">
<?php
}

/**
 * Displays the text field
 * Note the `name` attribute of the input, referring now to an array of settings.
 * 
 * @param  array  $args  Arguments passed to corresponding add_settings_field() call
 */
function payfeez_bacs_variable_fee_field_display($args)
{
    $settings = get_option('payfeez_bacs');
    $value = !empty($settings['payfeez_bacs_variable_fee']) ? $settings['payfeez_bacs_variable_fee'] : '';
?>
    <input id="<?php echo esc_attr($args['label_for']); ?>" class="regular-text" type="number" min="0" max="10" step="any" name="payfeez_bacs[payfeez_bacs_variable_fee]" value="<?php echo esc_attr($value); ?>">
<?php
}

/**
 * Sanitize callback for our settings
 * We have to sanitize each of our fields ourselves.
 * 
 * @param  array  $settings  An array of settings (due to the inputs' name attributes)
 */
function payfeez_bacs_sanitize($settings)
{
    // Sanitizes the fields
    $settings['payfeez_bacs_activation'] = isset($settings['payfeez_bacs_activation']);
    $settings['payfeez_bacs_fixed_fee'] = !empty($settings['payfeez_bacs_fixed_fee']) ? sanitize_text_field($settings['payfeez_bacs_fixed_fee']) : '';
    $settings['payfeez_bacs_variable_fee'] = !empty($settings['payfeez_bacs_variable_fee']) ? sanitize_text_field($settings['payfeez_bacs_variable_fee']) : '';

    return $settings;
}
