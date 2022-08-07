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

        // Subscriptions Plans
        $tables[] = [ 'subscription_plans', [
            [ 'name', 'VARCHAR', 128, 1 ],
            [ 'price', 'FLOAT', '', 1 ],
            [ 'interval', 'VARCHAR', 128, 1 ],
            [ 'quantity', 'INT', 13, 0 ],
        ], 'sp', 1 ];

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

        $db = new DB();
        $db->automate_tables( $tables );
    }

    function options(): void {
        $f = new FORM();
        $db = new DB();
        // API Fields
        $options_array = [ 'stripe_public_key', 'stripe_private_key', 'stripe_test_public_key', 'stripe_test_private_key', 'stripe_test' ];
        $ops = $db->get_options( $options_array );
        echo '<div class="row"';
        $f->option_params('stripe', 3 );
        echo '>';
        $attr = 'data-stripe';
        $pub = $ops['stripe_public_key'] ?? '';
        $pri = $ops['stripe_private_key'] ?? '';
        $pub_test = $ops['stripe_test_public_key'] ?? '';
        $pri_test = $ops['stripe_test_private_key'] ?? '';
        $test = $ops['stripe_test'] ?? '';
        $f->texts([
            [ 'stripe_public_key', 'Public Key', '', $pub ],
            [ 'stripe_private_key', 'Private Key', '', $pri ],
        ], $attr, 3 );
        echo '<div class="col-12 col-md-6"><div class="row">';
        $f->checkboxes( 'stripe_test', 'Test Mode', [1=>''], $test, $attr.' class="slide"', 0, 2 );
        $f->texts([
            [ 'stripe_test_public_key', 'Public Key - Test Mode', '', $pub_test ],
            [ 'stripe_test_private_key', 'Private Key - Test Mode', '', $pri_test ],
        ], $attr, 5);
        echo '</div></div>';
        $f->process_options('Save Stripe Options','store grad','','col-12 tac');
        echo '</div>';
    }

    function manage_subscription_plans( string $table_class = 'plain c r' ): void {
        $f = new FORM();
        $a = 'data-sp';
        ?>
        <table class="<?php echo $table_class; ?>">
            <thead>
            <tr>
                <td><?php E('Plan Name'); ?></td>
                <td><?php E('Pay Interval'); ?></td>
                <td><?php E('Price (USD)'); ?></td>
                <td><?php E('Max Quantity'); ?></td>
                <td><?php E('Options'); ?></td>
            </tr>
            </thead>
            <tbody>
            <?php
            $db = new DB();
            $sps = $db->select( 'subscription_plans' );
            if( !empty( $sps ) ) {
                foreach( $sps as $sp ){
                    echo '<tr>';
                    echo '<td>'.$sp['sp_name'].'</td>';
                    echo '<td>'.$sp['sp_interval'].'</td>';
                    echo '<td>'.$sp['sp_price'].'</td>';
                    echo '<td>'.$sp['sp_quantity'].'</td><td>';
                    $f->trash_html('subscription_plans','sp_id = \''.$sp['sp_id'].'\'','button','Delete','red b bsn l m0');
                    echo '</td></tr>';
                }
            }
            ?>
            </tbody>
            <tfoot>
            <tr <?php $f->process_params('subscription_plans','sp','sp_',2,2); ?>>
                <td><?php $f->text('name','','Ex: Platinum Plan','',$a); ?></td>
                <td><?php $f->select2('interval','','Choose...',['monthly'=>'Monthly','yearly'=>'Yearly'],'',$a,'',1); ?></td>
                <td><?php $f->text('price','','Ex: 250','',$a); ?></td>
                <td><?php $f->text('quantity','','Ex: 10','',$a); ?></td>
                <td><?php $f->process_html('Add','add_plan store grad'); ?></td>
            </tr>
            </tfoot>
        </table>
        <?php
    }

    function update_subscription_plan( int $user = 0, string $plan = '' ): void {

    }

    function checkout_form(): void {

    }

    /**
     * @param int $user_id User or Company ID to link to subscriptions database locally
     * @param int $default_quantity Default quantity to set a plan
     * @param bool $enable_plans Show or Hide Plans from the User
     * @param string $name Name of the Subscriber
     * @param string $email Email of the Subscriber
     * @return void
     */
    function subscription_form( int $user_id = 0, int $default_quantity = 0, bool $enable_plans = true, string $name = '', string $email = '', ): void {
        $db = new DB();
        // API Fields
        $options_array = [ 'stripe_public_key', 'stripe_private_key', 'stripe_test_public_key', 'stripe_test_private_key', 'stripe_test' ];
        $ops = $db->get_options( $options_array );
        $public_key = $ops['stripe_test'] ? ( $ops['stripe_test_public_key'] ?? '' ) : ( $ops['stripe_public_key'] ?? '' );
        if( !empty( $public_key ) ) {
        ?>
        <div class="subscription_wrap stripe" data-stripe-public-key="<?php echo $public_key; ?>">

            <div id="payment_response" class="dn"></div>

            <form id="subscription_form">

                <div class="row">
                    <?php
                    $f = new FORM();
                    $sps = $db->select( 'subscription_plans', '', '', 0, 0, '', 0, 'DESC', 'sp_quantity' );
                    $sub_plans = [];
                    $default_plan = '';
                    //echo $default_quantity;
                    foreach( $sps as $sp ) {
                        $sub_plans[ $sp['sp_id'] ] = $sp['sp_name'] . ' (' . $sp['sp_interval'] . ') ' . $sp['sp_price'] . ' USD Quantity - ' . $sp['sp_quantity'] ;
                        $default_plan = $default_quantity <= $sp['sp_quantity'] ? $sp['sp_id'] : $default_plan;
                    }
                    //echo $default_plan;
                    $show_plan = $enable_plans ? '' : 'disabled';
                    $f->select2('plan','Subscription Plan','Select a Plan...',$sub_plans,$default_plan,$show_plan,12,1);
                    $f->text('name','Full Name','Ex: Mohammed Ahmed',$name,'',6);
                    $f->text('email','Email','Ex: john_doe@website.com',$email,'',6);
                    ?>
                </div>

                <div class="form-group">
                    <label>CARD INFO</label>
                    <div id="card-element">
                        <!-- Stripe.js will create card input elements here -->
                    </div>
                </div>

                <!-- Form submit button -->
                <button id="submitBtn" class="btn btn-success">
                    <span class="spinner hidden" id="spinner"></span>
                    <span id="buttonText">Proceed</span>
                </button>
            </form>

            <div id="frmProcess" class="hidden">
                <span class="ring"></span> Processing...
            </div>

        </div>
        <?php
            get_scripts('stripe,stripe-checkout');
        } else {
            no_access('Stripe API keys are not saved!');
        }
    }
}