<?php

defined('ABSPATH') || die();

add_action('woocommerce_checkout_init', 'payfeez_checkout_refresh');
function payfeez_checkout_refresh()
{
    wc_enqueue_js("jQuery( function( $ ){
        $( 'form.checkout' ).on( 'change', 'input[name^=\"payment_method\"]', function(){
            $( 'body' ).trigger( 'update_checkout' );
        });
    });");
}
