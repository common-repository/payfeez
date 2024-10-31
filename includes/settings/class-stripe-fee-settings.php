<?php

defined('ABSPATH') || die();

/**
 * Registers settings
 */
add_action('admin_init', 'payfeez_stripe');

/**
 * Check if stripe Gateway is activated
 */
function payfeez_check_stripe_gateway()
{
    // Get all active payment gateways
    $active_gateways = WC_Payment_Gateways::instance()->get_available_payment_gateways();

    // Check if the stripe gateway is active
    if (isset($active_gateways['stripe']) && $active_gateways['stripe']->enabled === 'yes') {
        // Execute the function payfeez_check_if_stripe_fee_activated
        payfeez_stripe();
    }
}

function payfeez_stripe()
{
    register_setting(
        'payfeez_stripe_settings',     
        'payfeez_stripe',     
        'payfeez_stripe_sanitize'
    );

    add_settings_section(
        'payfeez_stripe_section',          
        __('Payment fees for Stripe payments', 'payfeez'),
        'payfeez_stripe_section_display',  
        'payfeez_stripe_settings_page'             
    );

    add_settings_field(
        'payfeez_stripe_activation',                           
        __('Activate payment fees', 'payfeez'),          
        'payfeez_stripe_activation_field_display', 
        'payfeez_stripe_settings_page',                  
        'payfeez_stripe_section',                
        array(
            'label_for' => 'payfeez_stripe_activation',  
            'description' => __('Activate the calculation and application of payment fees', 'payfeez'),
        )
    );

    add_settings_field(
        'payfeez_stripe_fixed_fee',                             
        __('Fixed amount', 'payfeez'),            
        'payfeez_stripe_fixed_fee_field_display',   
        'payfeez_stripe_settings_page',                
        'payfeez_stripe_section',              
        array(
            'label_for' => 'payfeez_stripe_fixed_fee',
        )
    );

    add_settings_field(
        'payfeez_stripe_variable_fee',                             
        __('Variable amount (%)', 'payfeez'),            
        'payfeez_stripe_variable_fee_field_display',   
        'payfeez_stripe_settings_page',                
        'payfeez_stripe_section',              
        array(
            'label_for' => 'payfeez_stripe_variable_fee',
        )
    );
}

/**
 * Displays the content of the multiple settings section
 * 
 * @param  array  $args  Arguments passed to the add_settings_section() function call
 */
function payfeez_stripe_section_display($args)
{
    // Just var_dumping data here to help you visualize the array organization.
    // var_dump(get_option('payfeez_stripe'));
}

/**
 * Displays the checkbox field
 * 
 * @param  array  $args  Arguments passed to corresponding add_settings_field() call
 */
function payfeez_stripe_activation_field_display($args)
{
    $settings = get_option('payfeez_stripe');
    $checked = (bool) $settings['payfeez_stripe_activation'] ?: false;
    ?>
    <input id="<?php echo esc_attr($args['label_for']); ?>" type="checkbox" name="payfeez_stripe[payfeez_stripe_activation]" <?php checked($checked); ?>>
    <span><?php echo esc_html($args['description']); ?></span>
    <?php
}

/**
 * Displays the text field
 * Note the `name` attribute of the input, referring now to an array of settings.
 * 
 * @param  array  $args  Arguments passed to corresponding add_settings_field() call
 */
function payfeez_stripe_fixed_fee_field_display($args)
{
    $settings = get_option('payfeez_stripe');
    $value = !empty($settings['payfeez_stripe_fixed_fee']) ? $settings['payfeez_stripe_fixed_fee'] : '';
    ?>
    <input id="<?php echo esc_attr($args['label_for']); ?>" class="regular-text" type="number" min="0" max="5" step="any" name="payfeez_stripe[payfeez_stripe_fixed_fee]" value="<?php echo esc_attr($value); ?>">
    <?php
}

/**
 * Displays the text field
 * Note the `name` attribute of the input, referring now to an array of settings.
 * 
 * @param  array  $args  Arguments passed to corresponding add_settings_field() call
 */
function payfeez_stripe_variable_fee_field_display($args)
{
    $settings = get_option('payfeez_stripe');
    $value = !empty($settings['payfeez_stripe_variable_fee']) ? $settings['payfeez_stripe_variable_fee'] : '';
    ?>
    <input id="<?php echo esc_attr($args['label_for']); ?>" class="regular-text" type="number" min="0" max="10" step="any" name="payfeez_stripe[payfeez_stripe_variable_fee]" value="<?php echo esc_attr($value); ?>">
    <?php
}

/**
 * Sanitize callback for our settings
 * We have to sanitize each of our fields ourselves.
 * 
 * @param  array  $settings  An array of settings (due to the inputs' name attributes)
 */
function payfeez_stripe_sanitize($settings)
{
    // Sanitizes the fields
    $settings['payfeez_stripe_activation'] = isset($settings['payfeez_stripe_activation']);
    $settings['payfeez_stripe_fixed_fee'] = !empty($settings['payfeez_stripe_fixed_fee']) ? sanitize_text_field($settings['payfeez_stripe_fixed_fee']) : '';
    $settings['payfeez_stripe_variable_fee'] = !empty($settings['payfeez_stripe_variable_fee']) ? sanitize_text_field($settings['payfeez_stripe_variable_fee']) : '';

    return $settings;
}