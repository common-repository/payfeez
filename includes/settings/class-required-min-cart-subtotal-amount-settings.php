<?php

defined('ABSPATH') || die();

/**
 * Registers settings
 */
add_action('admin_init', 'payfeez_rmcsa');

function payfeez_rmcsa()
{
    register_setting(
        'payfeez_settings',           // Settings group.
        'payfeez_rmcsa',              // Setting name - RMCSA = Required Min Cart Subtotal Amount
        'payfeez_rmcsa_sanitize'      // Sanitize callback.
    );

    add_settings_section(
        'payfeez_rmcsa_section',             // Section ID
        __('Minimum order amount', 'payfeez'), // Title
        'payfeez_rmcsa_section_display',     // Callback
        'payfeez_settings_page'              // Page to display the section in
    );

    add_settings_field(
        'payfeez_rmcsa_activation',                    // Field ID
        __('Activate this feature', 'payfeez'),        // Title
        'payfeez_rmcsa_activation_field_display',      // Callback
        'payfeez_settings_page',                       // Page
        'payfeez_rmcsa_section',                       // Section
        array(
            'label_for' => 'payfeez_rmcsa_activation',  // Id for the input and label element.
            'description' => __('Check this box to activate the feature.', 'payfeez'),
        )
    );

    add_settings_field(
        'payfeez_rmcsa_amount',                        // Field ID
        __('Minimum amount', 'payfeez'),               // Title
        'payfeez_rmcsa_amount_field_display',          // Callback to actually display the field's markup
        'payfeez_settings_page',                       // Page
        'payfeez_rmcsa_section',                       // Section
        array(
            'label_for' => 'payfeez_rmcsa_amount',      // Id for the input and label element.
        )
    );
}


/**
 * Displays the content of the multiple settings section
 * 
 * @param  array  $args  Arguments passed to the add_settings_section() function call
 */
function payfeez_rmcsa_section_display($args)
{
    // Just var_dumping data here to help you visualize the array organization.
    // var_dump(get_option('payfeez_rmcsa'));
}


/**
 * Displays the checkbox field
 * 
 * @param  array  $args  Arguments passed to corresponding add_settings_field() call
 */
function payfeez_rmcsa_activation_field_display($args)
{
    $settings = get_option('payfeez_rmcsa');
    $checked = (bool) $settings['payfeez_rmcsa_activation'] ?: false;
?>
    <input id="<?php echo esc_attr($args['label_for']); ?>" type="checkbox" name="payfeez_rmcsa[payfeez_rmcsa_activation]" <?php checked($checked); ?>>
    <span><?php echo esc_html($args['description']); ?></span>
<?php
}


/**
 * Displays the text field
 * Note the `name` attribute of the input,refering now to an array of settings.
 * 
 * @param  array  $args  Arguments passed to corresponding add_settings_field() call
 */
function payfeez_rmcsa_amount_field_display($args)
{
    $settings = get_option('payfeez_rmcsa');
    $value = !empty($settings['payfeez_rmcsa_amount']) ? $settings['payfeez_rmcsa_amount'] : '';
?>
    <input id="<?php echo esc_attr($args['label_for']); ?>" class="regular-text" type="number" name="payfeez_rmcsa[payfeez_rmcsa_amount]" value="<?php echo esc_attr($value); ?>">
<?php
}


/**
 * Sanitize callback for our settings
 * We have to sanitize each of our field ourselves.
 * 
 * @param  array  $settings  An array of settings (due to the inputs' name attributes)
 */
function payfeez_rmcsa_sanitize($settings)
{
    // Sanitizes the fields
    $settings['payfeez_rmcsa_activation'] = isset($settings['payfeez_rmcsa_activation']);

    $settings['payfeez_rmcsa_amount']     = !empty($settings['payfeez_rmcsa_amount']) ? sanitize_text_field($settings['payfeez_rmcsa_amount']) : '';

    return $settings;
}
