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

    function render_stripe_payment( float $amount, string $currency, string $email, string $name, string $process_payment_response_backend, string $process_payment_response_frontend, string $button_text = 'Proceed to Pay', string $button_wrap_class = 'tac' ): void {
        $db = new DB();
        $e = Encrypt::initiate();
        global $options;
        //skel($options);
        $stripe = $db->get_options(['stripe_test','stripe_public_key','stripe_test_public_key','stripe_private_key','stripe_test_private_key']);
        $public = $stripe['stripe_public_key'] ?? '';
        $test = $stripe['stripe_test'] ?? '';
        $test_public = $stripe['stripe_test_public_key'] ?? '';
        $key = $test == 1 ? $test_public : $public;
        $data = ['amount'=>$amount,'currency'=>$currency,'email'=>$email,'name'=>$name];
        ?>
        <script>
            let stripe_key = '<?php echo $key; ?>';
            let stripe_data = '<?php $e->enc_array($data); ?>';
            let payment_intent_action = '<?php $e->enc('stripe_payment_intent_ajax'); ?>';
            let create_customer_action = '<?php $e->enc('stripe_create_customer_ajax'); ?>';
            let process_payment_response_backend = '<?php $e->enc($process_payment_response_backend); ?>';
            let process_payment_response_frontend = '<?php echo $process_payment_response_frontend; ?>';
        </script>
        <?php
        echo '<div id="stripe_payment_wrapper"><div id="payment_response" class="tac"></div>';
        if( !isset( $_GET['payment_intent'] ) ) { ?>
            <form id="stripe_form">
                <div id="checkout_inputs" class="mb20"></div>
                <div class="<?php echo $button_wrap_class; ?>">
                    <button class="pay" id="stripe_pay_btn">
                        <span class="spinner loader hidden" id="spinner"></span>
                        <span id="stripe_pay_btn_text"><?php E($button_text); ?></span>
                    </button>
                </div>
            </form>
            <div hidden>
                <label for="stripe_input_sample" id="stripe_label_sample">Sample</label>
                <input type="text" id="stripe_input_sample">
            </div>
            <script>
                let appearance = {};
                document.addEventListener("DOMContentLoaded", function(e) {
                    let label = getComputedStyle( document.getElementById('stripe_label_sample') );
                    let input = getComputedStyle( document.getElementById('stripe_input_sample') );
                    //console.log(input);
                    appearance = {
                        theme: 'stripe',
                        rules: {
                            '.Label': {
                                color: label.color,
                                fontWeight: label.fontWeight,
                                fontFamily: label.fontFamily,
                                fontSize: label.fontSize,
                                padding: label.padding,
                                margin: label.margin
                            },
                            '.Input': {
                                color: input.color,
                                backgroundColor: input.backgroundColor,
                                fontWeight: input.fontWeight,
                                fontFamily: input.fontFamily,
                                fontSize: input.fontSize,
                                padding: input.padding,
                                border: input.border,
                                borderRadius: input.borderRadius,
                                boxShadow: input.boxShadow,
                                margin: input.margin
                            },
                            '.Input:focus': {
                                borderTop: '<?php echo $options['input_border_top'] ?? '1'; ?>px solid <?php echo $options['input_border_color_active_light'] ?? '#000'; ?>',
                                borderRight: '<?php echo $options['input_border_right'] ?? '1'; ?>px solid <?php echo $options['input_border_color_active_light'] ?? '#000'; ?>',
                                borderBottom: '<?php echo $options['input_border_bottom'] ?? '1'; ?>px solid <?php echo $options['input_border_color_active_light'] ?? '#000'; ?>',
                                borderLeft: '<?php echo $options['input_border_left'] ?? '1'; ?>px solid <?php echo $options['input_border_color_active_light'] ?? '#000'; ?>',
                                boxShadow: input.boxShadow
                            }
                        }
                    };
                    //console.log(appearance);
                })

            </script>
            <?php
            $s_test = $db->get_option('stripe_test');
            if( !empty( $s_test ) && $s_test == 1 ) {
                echo '<div class="tac mt15"><div>Success Test Card: 4242 4242 4242 4242</div><div>Failure Test Card: 4000 0000 0000 9995</div></div>';
            }
        }
        get_scripts(['https://js.stripe.com/v3/','stripe-checkout']);
        echo '</div>';
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