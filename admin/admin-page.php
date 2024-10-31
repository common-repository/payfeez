<?php
defined('ABSPATH') || die();

/**
 * Adds a PayFeez submenu to WooCommerce.
 */
function payfeez_woocommerce_add_submenu_page()
{
    // Add a new submenu under WooCommerce, providing various details such as page title, menu title, capability, menu slug, and callback function
    add_submenu_page(
        'woocommerce',                        // Parent menu slug
        'PayFeez',                             // Page title (not translatable)
        'PayFeez',                             // Menu title (not translatable)
        'manage_options',                      // Capability required to see this menu item
        'payfeez',                             // Menu slug
        'payfeez_settings_page_content'        // Callback function to display the content of the options page
    );
}

add_action('admin_menu', 'payfeez_woocommerce_add_submenu_page');

// Include required files for the settings pages.
require_once plugin_dir_path(__DIR__) . 'includes/settings/class-bacs-fee-settings.php';
require_once plugin_dir_path(__DIR__) . 'includes/settings/class-paypal-fee-settings.php';
require_once plugin_dir_path(__DIR__) . 'includes/settings/class-stripe-fee-settings.php';
require_once plugin_dir_path(__DIR__) . 'includes/settings/class-stancer-fee-settings.php';
require_once plugin_dir_path(__DIR__) . 'includes/settings/class-required-min-cart-subtotal-amount-settings.php';

/**
 * Displays the settings page content.
 */
function payfeez_settings_page_content()
{
    // Sanitize and retrieve the current tab from the URL, default to null if not set.
    $tab = filter_input(INPUT_GET, 'tab', FILTER_SANITIZE_STRING);
    $default_tab = null;
    $tab = $tab ?: $default_tab;
?>

    <div class="wrap" style="max-width:800px;">
        <!-- Display the settings page title. -->
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

        <!-- Navigation tabs for different payment gateway settings. -->
        <nav class="nav-tab-wrapper">
            <a href="?page=payfeez" class="nav-tab <?php if ($tab === null) : ?>nav-tab-active<?php endif; ?>">
                <?php esc_html_e('Settings', 'payfeez'); ?>
            </a>
            <?php
            // Get all available payment gateways.
            $gateways = WC_Payment_Gateways::instance()->get_available_payment_gateways();

            // Display a tab for each supported payment gateway if it's available.
            if (isset($gateways['bacs'])) {
                printf(
                    '<a href="?page=payfeez&tab=bacs" class="nav-tab%s">%s</a>',
                    $tab === 'bacs' ? ' nav-tab-active' : '',
                    esc_html__('Direct bank transfer', 'payfeez')
                );
            }

            if (isset($gateways['ppcp-gateway'])) {
                printf(
                    '<a href="?page=payfeez&tab=paypal" class="nav-tab%s">PayPal</a>',
                    $tab === 'paypal' ? ' nav-tab-active' : ''
                );
            }

            if (isset($gateways['stripe'])) {
                printf(
                    '<a href="?page=payfeez&tab=stripe" class="nav-tab%s">Stripe</a>',
                    $tab === 'stripe' ? ' nav-tab-active' : ''
                );
            }

            if (isset($gateways['stancer'])) {
                printf(
                    '<a href="?page=payfeez&tab=stancer" class="nav-tab%s">Stancer</a>',
                    $tab === 'stancer' ? ' nav-tab-active' : ''
                );
            }
            ?>
        </nav>

        <!-- Content of the selected tab. -->
        <div class="tab-content">
            <?php
            // Display the settings form for the selected payment gateway.
            switch ($tab):
                case 'bacs':
            ?>
                    <form action="options.php" method="post">
                        <?php settings_fields('payfeez_bacs_settings'); ?>
                        <?php do_settings_sections('payfeez_bacs_settings_page'); ?>
                        <?php submit_button(); ?>
                    </form>
                <?php
                    break;
                case 'paypal':
                ?>
                    <form action="options.php" method="post">
                        <?php settings_fields('payfeez_paypal_settings'); ?>
                        <?php do_settings_sections('payfeez_paypal_settings_page'); ?>
                        <?php submit_button(); ?>
                    </form>
                <?php
                    break;
                case 'stripe':
                ?>
                    <form action="options.php" method="post">
                        <?php settings_fields('payfeez_stripe_settings'); ?>
                        <?php do_settings_sections('payfeez_stripe_settings_page'); ?>
                        <?php submit_button(); ?>
                    </form>
                <?php
                    break;
                case 'stancer':
                ?>
                    <form action="options.php" method="post">
                        <?php settings_fields('payfeez_stancer_settings'); ?>
                        <?php do_settings_sections('payfeez_stancer_settings_page'); ?>
                        <?php submit_button(); ?>
                    </form>
                <?php
                    break;
                default:
                ?>
                    <form action="options.php" method="post">
                        <?php settings_fields('payfeez_settings'); ?>
                        <?php do_settings_sections('payfeez_settings_page'); ?>
                        <?php submit_button(); ?>
                    </form>
            <?php
                    break;
            endswitch;
            ?>
        </div>
    </div>
<?php
}
?>