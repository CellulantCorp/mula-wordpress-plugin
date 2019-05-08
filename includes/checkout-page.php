<?php
    /**
     * Template Name: Mula Checkout
    */
    require dirname(__FILE__).'/../src/Config.php';
    get_header();
?>

<div id="mula-plugin-checkout-page">
    <form id="mula-checkout-form" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="POST">
        <input style="display: none;" type="hidden" name="action" value="handle_mula_checkout_request">
        <div>
            <label for="customerFirstName">First name</label>
            <input class="checkout-form-field" id="customerFirstName" name="customerFirstName" type="text" required/>
            <small id="customerFirstNameHelp" style="display: block;">provide your first name</small>
        </div>
        <div>
            <label for="customerLastName">Last name</label>
            <input class="checkout-form-field" id="customerLastName" name="customerLastName" type="text" required/>
            <small id="customerLastNameHelp" style="display: block;">provide your last name</small>
        </div>
        <div>
            <label for="MSISDN">Phone</label>
            <input class="checkout-form-field" id="MSISDN" name="MSISDN" type="tel" required/>
            <small id="MSISDNHelp" style="display: block;">include your country code, excluding the `+` or `brackets`</small>
        </div>
        <div>
            <label for="customerEmail">Email</label>
            <input class="checkout-form-field" id="customerEmail" name="customerEmail" type="email" required/>
            <small id="customerEmailHelp" style="display: block;">provide an active email, to receive invoices & receipts</small>
        </div>
        <?php if ( class_exists( 'WooCommerce' ) ) : ?>
            <div>
                <label for="amount">Amount</label>
                <input 
                    class="checkout-form-field" 
                    id="amount" 
                    value="<?php echo get_woocommerce_currency() .' '. WC()->cart->get_cart_contents_total(); ?>" 
                    name="amount" 
                    type="text" 
                    readonly/>
                <small id="amountHelp" style="display: block;">the amount and currency from the store</small>
            </div>
        <?php endif; ?>
        <br/>
        <div>
            <button data-checkout-type="<?php echo get_option(\Mula\Config::CHECKOUT_TYPE_SELECT_INPUT);?>" style="background-color: transparent; padding: 0; border: none; outline: none;" class="pay-with-mula-button"></button>
        </div>
    </form>
</div>

<?php get_footer(); ?>