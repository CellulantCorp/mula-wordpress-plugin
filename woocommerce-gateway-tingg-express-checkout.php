<?php
/**
 * Plugin Name: WooCommerce Mula Checkout Payment Gateway
 * Plugin URI: https://woocommerce.com/products/woocommerce-gateway-mula-express-checkout/
 * Description: A payment gateway for Mula Express Checkout
 * Version: 1.0.0
 * Author: Mula
 * Author URI: https://woocommerce.com
 * Copyright: © 2018 WooCommerce / Mula.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: woocommerce-gateway-mula-express-checkout
 * Domain Path: /languages
 * WC tested up to: 3.6
 * WC requires at least: 2.6
 */

/**
 * Copyright (c) 2019 Mula, Inc.
 *
 * The name of the Mula may not be used to endorse or promote products derived from this
 * software without specific prior written permission. THIS SOFTWARE IS PROVIDED ``AS IS'' AND
 * WITHOUT ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, WITHOUT LIMITATION, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * Include plugin configurations
 */
require_once plugin_dir_path( __FILE__ ) . 'config.php';

/*
 * This action hook registers our PHP class as a WooCommerce payment gateway
 */
add_filter('woocommerce_payment_gateways', 'add_mula_gateway_class');
function add_mula_gateway_class($gateways)
{
    $gateways[] = 'WC_Gateway_Mula'; // your class name is here
    return $gateways;
}

/*
 * The class itself, please note that it is inside plugins_loaded action hook
 */
add_action('plugins_loaded', 'init_mula_gateway_class');
function init_mula_gateway_class() 
{
 
    class WC_Gateway_Mula extends WC_Payment_Gateway 
    {
		/**
		 * Encrypts the clear text payload
		 * @param array $payload
		 */
		private function encrypt($payload) 
		{
			$encrypted_payload = openssl_encrypt(
				json_encode($payload, true), 
				'AES-256-CBC', 
				hash('sha256', $this->secret_key), 
				0, 
				substr(hash('sha256', $this->iv_key), 0, 16)
			);

			return base64_encode($encrypted_payload);
		}

        /**
         * Class constructor, more about it in Step 3
        */
        public function __construct()
        {
			$this->icon = '';
			$this->has_fields = true;
			$this->method_title = 'Mula';
			$this->id = 'mula_express_checkout';
			$this->method_description = 'Mula allows you to make and collect payment in 33+ countries in Africa from a single integration';

			// gateways can support subscriptions, refunds, saved payment methods
			$this->supports = array(
				'products'
			);

			// Method with all the options fields
			$this->init_form_fields();

			// Load the settings.
			$this->init_settings();
			$this->title = $this->get_option('title');
			$this->enabled = $this->get_option('enabled');
			$this->description = $this->get_option('description');
			$this->testmode = 'yes' === $this->get_option('testmode');
			$this->payment_period = $this->get_option('payment_period');

			$this->checkout_url = $this->testmode ? MULA_CHECKOUT_TEST_URL : MULA_CHECKOUT_LIVE_URL;
			$this->iv_key = $this->testmode ? $this->get_option('test_iv_key') : $this->get_option('iv_key');
			$this->secret_key = $this->testmode ? $this->get_option('test_secret_key') : $this->get_option('secret_key');
			$this->access_key = $this->testmode ? $this->get_option('test_access_key') : $this->get_option('access_key');
			$this->service_code = $this->testmode ? $this->get_option('test_service_code') : $this->get_option('service_code');

			// action hook to saves the settings
			add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));

			// custom JavaScript to obtain a token
			add_action('wp_enqueue_scripts', array($this, 'payment_scripts'));

			//payment gateway webhook
			add_action('woocommerce_api_mula_payment_webhook', array($this, 'webhook'));
		}
 
		/**
 		 * Plugin options, we deal with it in Step 3 too
 		 */
        public function init_form_fields()
        {
			$this->form_fields = array(
				'enabled' => array(
					'title'       => 'Enable/Disable',
					'label'       => 'Enable Mula Express Checkout',
					'type'        => 'checkbox',
					'default'     => 'no'
				),
				'title' => array(
					'title'       => 'Title',
					'type'        => 'text',
					'description' => 'This controls the title which the user sees during checkout.',
					'default'     => 'Mula',
					'desc_tip'    => true,
				),
				'description' => array(
					'title'       => 'Description',
					'type'        => 'textarea',
					'description' => 'This controls the description which the user sees during checkout.',
					'default'     => 'Pay with banks, mobile money, and cards throughout Africa with Mula express checkout.',
				),
				'payment_period' => array(
					'title' => 'Payment period',
					'type' => 'number',
					'description' => 'This sets the amount of time in minutes before a checkout request on an order expires',
					'default' => '1440'
				),
				'testmode' => array(
					'title'       => 'Test mode',
					'label'       => 'Enable Test Mode',
					'type'        => 'checkbox',
					'description' => 'Place the payment gateway in test mode using test API keys.',
					'default'     => 'yes',
					'desc_tip'    => true,
				),
				'test_iv_key' => array(
					'title'       => 'Test IV Key',
					'type'        => 'text'
				),
				'test_secret_key' => array(
					'title'       => 'Test Secret Key',
					'type'        => 'text',
				),
				'test_access_key' => array(
					'title'       => 'Test Access Key',
					'type'        => 'text',
				),
				'test_service_code' => array(
					'title' => 'Test Service Code',
					'type' => 'text'
				),
				'iv_key' => array(
					'title' => 'Live IV Key',
					'type' => 'text'
				),
				'secret_key' => array(
					'title' => 'Live Secret Key',
					'type' => 'text'
				),
				'access_key' => array(
					'title' => 'Live Access Key',
					'type' => 'text',
				),
				'service_code' => array(
					'title' => 'Live Service Code',
					'type' => 'text'
				),
			);
		}
		 
		/*
		 * Custom JavaScript
		 */
		public function payment_scripts() 
		{
			// we need JavaScript to process a token only on cart/checkout pages
			if (!is_cart() && !is_checkout() && !isset($_GET['pay_for_order'])) {
				return;
			}
		
			// if our payment gateway is disabled, we do not have to enqueue JS too
			if ($this->enabled === 'no') {
				return;
			}
		}
 
		/*
 		 * Fields validation, more in Step 5
		 */
        public function validate_fields()
        {
			$billing_country = $_POST['billing_country'];

			$supported_countries = array_map(function ($value) {
				return $value['countryCode'];
			}, MULA_SUPPORTED_COUNTRIES);

			if (!in_array($billing_country, $supported_countries)) {
				wc_add_notice('<strong>Billing Country</strong> is not supported by Mula.', 'error');
				return false;
			}

			return true;
		}
 
		/*
		 * Processing the payment here
		 */
        public function process_payment( $order_id )
        {
			global $woocommerce;

			// we need it to get any order details
			$order = wc_get_order( $order_id );

			// checkout tranasction description
			$order_excerpt = array_reduce($order->get_items(), function($carry, $item) {
				$format = '%d x %s, ';

				$quantity = $item->get_quantity();

				$product = $item->get_product();
    			$product_name = $product->get_name();
				return $carry .= sprintf($format, $quantity, $product_name);
			});

			// filter to get selected country
			$order_country = array_filter(MULA_SUPPORTED_COUNTRIES, function($item) use($order) {
				return $item['countryCode'] == $order->get_billing_country();
			});

			// array with parameters for API interaction
			$payload = array(
				"accountNumber" => $order->get_id(),
				"requestAmount" => $order->get_total(),
				"MSISDN" => $order->get_billing_phone(),
				"customerEmail" => $order->get_billing_email(),
				"customerLastName" => $order->get_billing_last_name(),
				"customerFirstName" => $order->get_billing_first_name(),
				
				//computed properties
				"serviceCode" => $this->service_code,
				"merchantTransactionID" => strtotime('now'),
				"requestDescription" => rtrim(trim($order_excerpt), ','),
				"currencyCode" => $order_country[array_keys($order_country)[0]]['currencyCode'],
				"dueDate" => date("Y-m-d H:i:s", strtotime("+" . $this->payment_period . " minutes")),

				// webhooks
				"failRedirectUrl" => get_permalink(get_page_by_path('shop')),
				"successRedirectUrl" => $order->get_checkout_order_received_url(),
				"paymentWebhookUrl" => get_site_url() . '/wc-api/mula_payment_webhook',
			);

			$checkout_payment_url = sprintf(
				$this->checkout_url . "?params=%s&accessKey=%s&countryCode=%s",
				$this->encrypt($payload), 
				$this->access_key, 
				$order->get_billing_country()
			);

			//clear the cart
			$woocommerce->cart->empty_cart();
			
			// redirct to Mula checkout express
			return array('result' => 'success', 'redirect' => $checkout_payment_url);
		}
 
		/*
		 * In case you need a webhook, like PayPal IPN etc
		 */
        public function webhook()
        {
			$callback_json_payload = file_get_contents('php://input');
			$payload = json_decode($callback_json_payload, true);
			$order = wc_get_order($payload['accountNumber']);
            
            // successful payment
            if ($payload["requestStatusCode"] == 178) {
                $order->payment_complete();
    			$order->reduce_order_stock();
    			
    			$note =  sprintf("Order #%s has been fully paid", strval($payload['accountNumber']));
    			$order->add_order_note( $note );
    			
    			$response = array(
    		        "statusCode" => 183,
    		        "statusDescription" => "Payment accepted",
    		        "receiptNumber" =>  $payload['accountNumber'],
    		        "checkoutRequestID" => $payload["checkoutRequestID"],
    		        "merchantTransactionID" => $payload["merchantTransactionID"]
    		    );
    		    
    		    echo json_encode($response, true);
            }

            exit();
			// update_option('webhook_debug', $response);
		}
 	}
}
