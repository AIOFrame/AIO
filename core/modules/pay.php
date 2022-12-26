<?php
require ROOTPATH . 'core/external/vendor/autoload.php';

class PAY {

    function __construct() {
        require_once ROOTPATH . 'core/external/vendor/autoload.php';
    }

    function render_2checkout_api( string $pre = '' ): void {
        $db = new DB();
        $f = new FORM();
        $id = '2checkout_merchant_id';
        $key = '2checkout_private_key';
        $db->save_post_options([$id,$key]);
        $f->text( $id, '2Checkout Merchant ID', 'Ex: 901248204', $db->get_option($id), '', $pre );
        $f->text( $key, '2Checkout Private Key', 'Ex: BE632CB0-BB29-11E3-AFB6-D99C28100996', $db->get_option($key), '', $pre );
    }

    function process_2checkout_payment(): void {
        $db = new DB();
        $id = $db->get_option('2checkout_merchant_id');
        $key = $db->get_option('2checkout_private_key');
        Twocheckout::privateKey($key);
        Twocheckout::sellerId($id);
        Twocheckout::verifySSL(false);
        Twocheckout::format('json');
        try {
            $charge = Twocheckout_Charge::auth(array(
                "sellerId" => $id,
                "currency" => 'USD',
                "total" => '10.00',
                "demo" => true,
                "billingAddr" => array(
                    "name" => 'John Doe',
                    "addrLine1" => '104, Silver Tower',
                    "city" => 'Dubai',
                    "state" => 'DB',
                    "zipCode" => '120252',
                    "country" => 'UAE',
                    "email" => 'dumbuser@2co.com',
                    "phoneNumber" => '585872455'
                )
            ));
            // skel( $charge );
        } catch (Twocheckout_Error $e) {
            // skel( $e->getMessage() );
        }
    }

    function render_stripe_payment( string $pre_function, string $post_function, string $button_text = 'Proceed to Pay', string $button_wrap_class = 'tac' ): void {
        $db = new DB();
        $e = Encrypt::initiate();
        $stripe = $db->get_options(['stripe_test','stripe_public_key','stripe_test_public_key','stripe_private_key','stripe_test_private_key']);
        $public = $stripe['stripe_public_key'] ?? '';
        $private = $stripe['stripe_private_key'] ?? '';
        $test = $stripe['stripe_test'] ?? '';
        $test_public = $stripe['stripe_test_public_key'] ?? '';
        $test_private = $stripe['stripe_test_private_key'] ?? '';
        $key = $test == 1 ? $test_public : $public;
        $secret = $test == 1 ? $test_private : $private; /*data-secret="<?php echo $secret; ?>"*/
        if( !isset( $_GET['payment_intent'] ) ) { ?>
            <form id="payment-form" data-stripe="<?php echo $key; ?>" data-pre="<?php $e->enc($pre_function); ?>" data-post="<?php $e->enc($post_function); ?>">
                <div id="payment-element" class="mb20"></div>
                <div class="<?php echo $button_wrap_class; ?>">
                    <button class="pay" id="submit">
                        <span class="spinner loader hidden" id="spinner"></span>
                        <span id="button-text"><?php E($button_text); ?></span>
                    </button>
                </div>
            </form>
            <div id="paymentResponse" class="tac"></div>
            <?php
            $s_test = $db->get_option('stripe_test');
            if( !empty( $s_test ) && $s_test == 1 ) {
                echo '<div class="tac mt15"><div>Success Test Card: 4242 4242 4242 4242</div><div>Failure Test Card: 4000 0000 0000 9995</div></div>';
            }
        }
        get_scripts(['https://js.stripe.com/v3/','stripe-checkout']);
    }

    function stripe_options(): void {
        include_once( ROOTPATH . 'core/modules/stripe.php' );
        $stripe = new STRIPE();
        $stripe->options();
    }

    function get_stripe_subscriptions() {
        $key = $this->get_stripe_key();
        $stripe = new \Stripe\StripeClient($key);
        return $stripe->subscriptionItems->all();
    }

    function add_stripe_subscription( string $title, float $price ): void {

    }

    function remove_stripe_subscription( string $id ): void {

    }

}