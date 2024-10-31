<?php

defined('ABSPATH') || die();

/**
 * Registers settings
 */
add_action('admin_init', 'payfeez_stancer');

/**
 * Check if stancer Gateway is activated
 */
function payfeez_check_stancer_gateway()
{
    // Get all active payment gateways
    $active_gateways = WC_Payment_Gateways::instance()->get_available_payment_gateways();

    // Check if the stancer gateway is active
    if (isset($active_gateways['stancer']) && $active_gateways['stancer']->enabled === 'yes') {
        // Execute the function payfeez_check_if_stancer_fee_activated
        payfeez_stancer();
    }
}

function payfeez_stancer()
{
    register_setting(
        'payfeez_stancer_settings',
        'payfeez_stancer',
        'payfeez_stancer_sanitize'
    );

    add_settings_section(
        'payfeez_stancer_section',
        __('Payment fees for Stancer payments', 'payfeez'),
        'payfeez_stancer_section_display',
        'payfeez_stancer_settings_page'
    );

    add_settings_field(
        'payfeez_stancer_activation',
        __('Activate payment fees', 'payfeez'),
        'payfeez_stancer_activation_field_display',
        'payfeez_stancer_settings_page',
        'payfeez_stancer_section',
        array(
            'label_for' => 'payfeez_stancer_activation',
            'description' => __('Activate the calculation and application of payment fees', 'payfeez'),
        )
    );

    add_settings_field(
        'payfeez_stancer_fixed_fee',
        __('Fixed amount', 'payfeez'),
        'payfeez_stancer_fixed_fee_field_display',
        'payfeez_stancer_settings_page',
        'payfeez_stancer_section',
        array(
            'label_for' => 'payfeez_stancer_fixed_fee',
        )
    );

    add_settings_field(
        'payfeez_stancer_variable_fee',
        __('Variable amount (%)', 'payfeez'),
        'payfeez_stancer_variable_fee_field_display',
        'payfeez_stancer_settings_page',
        'payfeez_stancer_section',
        array(
            'label_for' => 'payfeez_stancer_variable_fee',
        )
    );
}

/**
 * Displays the content of the multiple settings section
 * 
 * @param  array  $args  Arguments passed to the add_settings_section() function call
 */
function payfeez_stancer_section_display($args)
{
    // Just var_dumping data here to help you visualize the array organization.
    // var_dump(get_option('payfeez_stancer'));
}

/**
 * Displays the checkbox field
 * 
 * @param  array  $args  Arguments passed to corresponding add_settings_field() call
 */
function payfeez_stancer_activation_field_display($args)
{
    $settings = get_option('payfeez_stancer');
    $checked = (bool) $settings['payfeez_stancer_activation'] ?: false;
?>
    <input id="<?php echo esc_attr($args['label_for']); ?>" type="checkbox" name="payfeez_stancer[payfeez_stancer_activation]" <?php checked($checked); ?>>
    <span><?php echo esc_html($args['description']); ?></span>
<?php
}

/**
 * Displays the text field
 * Note the `name` attribute of the input, referring now to an array of settings.
 * 
 * @param  array  $args  Arguments passed to corresponding add_settings_field() call
 */
function payfeez_stancer_fixed_fee_field_display($args)
{
    $settings = get_option('payfeez_stancer');
    $value = !empty($settings['payfeez_stancer_fixed_fee']) ? $settings['payfeez_stancer_fixed_fee'] : '';
?>
    <input id="<?php echo esc_attr($args['label_for']); ?>" class="regular-text" type="number" min="0" max="5" step="any" name="payfeez_stancer[payfeez_stancer_fixed_fee]" value="<?php echo esc_attr($value); ?>">
<?php
}

/**
 * Displays the text field
 * Note the `name` attribute of the input, referring now to an array of settings.
 * 
 * @param  array  $args  Arguments passed to corresponding add_settings_field() call
 */
function payfeez_stancer_variable_fee_field_display($args)
{
    $settings = get_option('payfeez_stancer');
    $value = !empty($settings['payfeez_stancer_variable_fee']) ? $settings['payfeez_stancer_variable_fee'] : '';
?>
    <input id="<?php echo esc_attr($args['label_for']); ?>" class="regular-text" type="number" min="0" max="10" step="any" name="payfeez_stancer[payfeez_stancer_variable_fee]" value="<?php echo esc_attr($value); ?>">
<?php
}

/**
 * Sanitize callback for our settings
 * We have to sanitize each of our fields ourselves.
 * 
 * @param  array  $settings  An array of settings (due to the inputs' name attributes)
 */
function payfeez_stancer_sanitize($settings)
{
    // Sanitizes the fields
    $settings['payfeez_stancer_activation'] = isset($settings['payfeez_stancer_activation']);
    $settings['payfeez_stancer_fixed_fee'] = !empty($settings['payfeez_stancer_fixed_fee']) ? sanitize_text_field($settings['payfeez_stancer_fixed_fee']) : '';
    $settings['payfeez_stancer_variable_fee'] = !empty($settings['payfeez_stancer_variable_fee']) ? sanitize_text_field($settings['payfeez_stancer_variable_fee']) : '';

    return $settings;
}
