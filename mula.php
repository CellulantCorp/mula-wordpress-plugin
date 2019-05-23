<?php

/**
 * Plugin Name: Mula
 * Plugin URI:  https://mula.africa
 * Description: A wordpress plugin for merchants to integrate Mula  on their online shops, offering their customers a pan-african variety of payment options.
 * Version:     1.0.0
 * Author:      Mula Team
 * Author URI:  https://mula.africa
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wporg
 * Domain Path: /languages
 */

date_default_timezone_set('UTC');
require dirname(__FILE__) . '/vendor/autoload.php';

use Mula\Mula;
use Mula\Config;
use Mula\Encrypt;

(new Mula())->init();

register_activation_hook(__FILE__, function () {
    $checkout_page_id = wp_insert_post([
        'post_title' => 'Mula Checkout',
        'post_name' => Config::CHECKOUT_PAGE_SLUG,
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_author' => 1
    ]);

    $success_redirect_page_id = wp_insert_post([
        'post_title' => 'Mula Success Redirect',
        'post_name' => Config::SUCCESS_REDIRECT_PAGE_SLUG,
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_author' => 1
    ]);

    $fail_redirect_page_id = wp_insert_post([
        'post_title' => 'Mula Fail Redirect',
        'post_name' => Config::FAILED_REDIRECT_PAGE_SLUG,
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_author' => 1
    ]);

    add_post_meta($checkout_page_id, '_wp_page_template', dirname(__FILE__) . '/includes/checkout-page.php');
    add_post_meta($fail_redirect_page_id, '_wp_page_template', dirname(__FILE__) . '/includes/fail-redirect-page.php');
    add_post_meta($success_redirect_page_id, '_wp_page_template', dirname(__FILE__) . '/includes/success-redirect-page.php');
});

register_deactivation_hook(__FILE__, function () {
    $pages_to_delete = ['Mula Checkout', 'Mula Success Redirect', 'Mula Fail Redirect'];
    
    foreach ($pages_to_delete as $key => $value) {
        $post_id = post_exists($value);
        if ( $post_id != 0 ) {
            wp_delete_post($post_id, true);
        }
    }
});

add_filter('page_template', function () {
    if ( is_page('mula-checkout') ) {
        return dirname(__FILE__) . '/includes/checkout-page.php';
    }
});

add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style(
        Config::CHECKOUT_PAGE_STYLE_HANDLE,
        plugins_url('public/css/mula-plugin.css', __FILE__),
        array(),
        '1.0.0',
        'all'
    );

    wp_enqueue_script(
        Config::CHECKOUT_PAGE_JS_SCRIPT,
        plugins_url('public/js/mula-plugin.js', __FILE__),
        ['jquery'],
        '1.0.0',
        true
    );

    wp_localize_script(
        Config::CHECKOUT_PAGE_JS_SCRIPT,
        Config::AJAX_OBJECT,
        array(
            "AJAX_URL" => admin_url( 'admin-ajax.php' )
        )
    );
});

add_action('wp_footer', function () {
    if ( is_page('mula-checkout') ) {
        echo '<script id="mula-checkout-library" type="text/javascript" src="'.Config::MULA_CHECKOUT_LIBRARY.'"></script>';
    }
});

add_action('admin_notices', function() {
    settings_errors();
});

add_action('admin_enqueue_scripts', function() {
    /**
     * @param string $handle
     * @param string $src = ''
     * @param array $deps = array()
     * @param string|bool|null $ver = false,
     * @param string $media = 'all'
     */
    wp_enqueue_style(
        Config::ADMIN_PAGE_STYLE_HANDLE,
        plugins_url('admin/css/mula-plugin.css', __FILE__ ),
        array(),
        '1.0.0',
        'all'
    );
});

add_action('admin_footer', function () {
    echo '<script src="'.Config::BABEL_CDN.'"></script>';
    echo '<script src="'.Config::REACT_CDN.'" crossorigin></script>';
    echo '<script src="'.Config::REACT_DOM_CDN.'" crossorigin></script>';
    echo '<script src="'.Config::MATERIAL_UI_CDN.'" crossorigin></script>';
    echo '<script type="text/babel" src="'.plugins_url('admin/js/get-started-stepper.js', __FILE__).'"></script>';
});

add_action('wp_ajax_handle_mula_checkout_request', 'process_mula_checkout_request');
add_action('wp_ajax_nopriv_handle_mula_checkout_request', 'process_mula_checkout_request');
function process_mula_checkout_request() {
    $MSISDN = $_POST['MSISDN'];
    $customerEmail = $_POST['customerEmail'];
    $customerLastName = $_POST['customerLastName'];
    $customerFirstName = $_POST['customerFirstName'];

    if ( class_exists( 'WooCommerce' ) ) {
        $currencyCode = strtoupper(get_woocommerce_currency());
        $requestAmount = WC()->cart->get_cart_contents_total();
    }

    $params = [
        "MSISDN" => $MSISDN,
        "accountNumber" => $MSISDN,
        "currencyCode" => $currencyCode,
        "requestAmount" => $requestAmount,
        "customerEmail" => $customerEmail,
        "customerLastName" => $customerLastName,
        "customerFirstName" => $customerFirstName,
        "paymentWebhookUrl" => get_bloginfo('url'),
        "requestDescription" => get_bloginfo('name'),
        "merchantTransactionID" => strtotime('now'),
        "serviceCode" => get_option(Config::SERVICE_CODE_TEXT_INPUT),
        "paymentWebhookUrl" => get_permalink(get_page_by_path(Config::WEB_HOOK_PAGE_SLUG)),
        "failRedirectUrl" => get_permalink(get_page_by_path(Config::FAILED_REDIRECT_PAGE_SLUG)),
        "successRedirectUrl" => get_permalink(get_page_by_path(Config::SUCCESS_REDIRECT_PAGE_SLUG)),
        "dueDate" => date("Y-m-d H:i:s", strtotime("+".get_option(Config::DUE_DATE_NUMBER_INPUT)." minutes"))
    ];

    $encrypted =  (new Encrypt())->encrypt($params);
    echo json_encode($encrypted);
    die();
}

?>
