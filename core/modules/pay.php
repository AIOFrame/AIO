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
            skel( $charge );
        } catch (Twocheckout_Error $e) {
            skel( $e->getMessage() );
        }
    }

    function get_stripe_key(): string {
        $db = new DB();
        $os = $db->get_options(['stripe_public_key','stripe_private_key','stripe_test','stripe_test_public_key','stripe_test_private_key']);
        $public = $os['stripe_public_key'] ?? '';
        $private = $os['stripe_private_key'] ?? '';
        $test = $os['stripe_test'] ?? '';
        $test_public = $os['stripe_test_public_key'] ?? '';
        $test_private = $os['stripe_test_private_key'] ?? '';
        $stripe_key = !empty( $test ) && $test == 1 ? $test_private : $private;
        \Stripe\Stripe::setApiKey( $stripe_key );
        return $stripe_key;
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

class STRIPE {

    function __construct() {
        // Subscriptions
        $tables[] = [ 'subscriptions', [
            [ 'user', 'INT', 13, 0 ],
            [ 'plan', 'INT', 13, 0 ],
            [ 'method', 'VARCHAR', 256, 0 ],
            [ 'stripe_subscription_id', 'VARCHAR', 55, 1 ],
            [ 'stripe_customer_id', 'VARCHAR', 55, 1 ],
            [ 'stripe_payment_intent_id', 'VARCHAR', 55, 1 ],
            [ 'amount', 'FLOAT', '', 0 ],
            [ 'currency', 'VARCHAR', 4, 0 ],
            [ 'interval', 'VARCHAR', 10, 0 ],
            [ 'interval_count', 'TINYINT', 2, 0 ],
            [ 'email', 'VARCHAR', 256, 0 ],
            [ 'date', 'DATE', '', 0 ],
            [ 'start', 'DATE', '', 0 ],
            [ 'end', 'DATE', '', 0 ],
            [ 'status', 'INT', 2, 0 ]
        ], 'sub', 1 ];
    }

    function checkout_form(): void {

    }

    function subscription_form( int $subscription_id ): void {
        ?>
        <div class="subscription_wrap stripe">

            <div id="payment_response" class="dn"></div>

            <form id="subscription_form">

                <?php
                $f = new FORM();
                $f->text('plan','','','','class="dn"');
                $f->text('name','Full Name','Ex: Mohammed Ahmed');
                $f->text('email','Email','Ex: john_doe@website.com');
                ?>

                <div class="form-group">
                    <label>CARD INFO</label>
                    <div id="card-element">
                        <!-- Stripe.js will create card input elements here -->
                    </div>
                </div>

                <!-- Form submit button -->
                <button id="submitBtn" class="btn btn-success">
                    <div class="spinner hidden" id="spinner"></div>
                    <span id="buttonText">Proceed</span>
                </button>
            </form>

            div id="frmProcess" class="hidden">
            <span class="ring"></span> Processing...
        </div>

        </div>
        <?php
    }
}