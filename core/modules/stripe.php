<?php
require ROOTPATH . 'core/external/vendor/autoload.php';

class STRIPE {

    function __construct() {

        // Subscriptions Plans
        /*$tables[] = [ 'stripe_subscription_plans', [
            [ 'name', 'VARCHAR', 128, 1 ],
            [ 'price', 'FLOAT', '', 1 ],
            [ 'interval', 'VARCHAR', 128, 1 ],
            [ 'quantity', 'INT', 13, 1 ],
            [ 'currency', 'VARCHAR', 3, 1 ],
            [ 'stripe_product_id', 'VARCHAR', 55, 0 ],
        ], 'sp', 1 ];

        // Stripe Customers
        $tables[] = [ 'stripe_customers', [
            [ 'name', 'VARCHAR', 256, 1 ],
            [ 'email', 'VARCHAR', 256, 1 ],
            [ 'stripe_customer_id', 'VARCHAR', 55, 1 ],
            [ 'stripe_subscription_id', 'VARCHAR', 99, 0 ],
            [ 'stripe_secret_id', 'VARCHAR', 99, 0 ],
            [ 'user', 'INT', 13, 0 ],
        ], 'stc', 1 ];

        // Subscriptions
        $tables[] = [ 'stripe_subscriptions', [
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
            [ 'status', 'VARCHAR', 24, 0 ],
        ], 'sub', 1 ];

        $db = new DB();
        $db->automate_tables( $tables );*/
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
            [ 'stripe_public_key', 'Publishable Key', '', $pub ],
            [ 'stripe_private_key', 'Secret Key', '', $pri ],
        ], $attr, 3 );
        echo '<div class="col-12 col-md-6"><div class="row">';
        $f->checkboxes( 'stripe_test', 'Test Mode', [1=>''], $test, $attr.' class="slide"', 0, 2 );
        $f->texts([
            [ 'stripe_test_public_key', 'Publishable Key - Test Mode', '', $pub_test ],
            [ 'stripe_test_private_key', 'Secret Key - Test Mode', '', $pri_test ],
        ], $attr, 5);
        echo '</div></div>';
        $f->process_options('Save Stripe Options','store grad','','col-12 tac');
        echo '</div>';
    }

    function manage_subscription_plans( string $table_class = 'plain c r' ): void {
        $f = new FORM();
        $a = 'data-sp';
        $intervals = [ 'day'=>'Daily', 'week'=>'Weekly', 'month'=>'Monthly', 'year'=>'Yearly' ];
        ?>
        <table class="<?php echo $table_class; ?>">
            <thead>
            <tr>
                <td><?php E('Plan Name'); ?></td>
                <td><?php E('Recurring'); ?></td>
                <td><?php E('Price'); ?></td>
                <td><?php E('Max Quantity'); ?></td>
                <td><?php E('Currency'); ?></td>
                <td><?php E('Stripe ID'); ?></td>
                <td><?php E('Options'); ?></td>
            </tr>
            </thead>
            <tbody>
            <?php
            $db = new DB();
            $sps = $db->select( 'stripe_subscription_plans' );
            if( !empty( $sps ) ) {
                foreach( $sps as $sp ){
                    $int = $intervals[ $sp['sp_interval'] ] ?? '';
                    echo '<tr>';
                    echo '<td>'.$sp['sp_name'].'</td>';
                    echo '<td>'.$int.'</td>';
                    echo '<td>'.$sp['sp_price'].'</td>';
                    echo '<td>'.$sp['sp_quantity'].'</td>';
                    echo '<td>'.$sp['sp_currency'].'</td>';
                    echo '<td>'.$sp['sp_stripe_product_id'].'</td><td>';
                    $f->trash_html('stripe_subscription_plans','sp_id = \''.$sp['sp_id'].'\'','button','Delete','red b bsn l m0');
                    echo '</td></tr>';
                }
            }
            ?>
            </tbody>
            <tfoot>
            <tr <?php $f->process_params('','sp','',2,2); ?>>
                <td><?php $f->text('name','','Ex: Platinum Plan','',$a); ?></td>
                <td><?php $f->select2('interval','','Choose...',$intervals,'month',$a,'',1); ?></td>
                <td><?php $f->text('price','','Ex: 250','',$a); ?></td>
                <td><?php $f->text('quantity','','Ex: 10','',$a); ?></td>
                <td><?php $f->select2('currency','','Choose...',['AED','AUD','USD','EUR','AED','INR'],'USD',$a); ?></td>
                <td></td>
                <td><?php $f->process_html('Add','add_plan store grad','','update_subscription_product_ajax'); ?></td>
            </tr>
            </tfoot>
        </table>
        <?php
    }

    /**
     * Renders Subscription Form
     * @param int $user_id User or Company ID to link to subscriptions database locally
     * @param int $default_quantity Default quantity to set a plan
     * @param bool $enable_plans Show or Hide Plans from the User
     * @param string $name Name of the Subscriber
     * @param string $email Email of the Subscriber
     * @param string $button_class Class of the Subscribe Button
     * @return void
     */
    function subscription_form( int $user_id = 0, int $default_quantity = 0, bool $enable_plans = true, string $name = '', string $email = '', string $button_class = 'l br5' ): void {
        $db = new DB();
        // API Fields
        $options_array = [ 'stripe_public_key', 'stripe_private_key', 'stripe_test_public_key', 'stripe_test_private_key', 'stripe_test' ];
        $ops = $db->get_options( $options_array );
        $public_key = '';
        if( isset( $ops['stripe_test'] ) && $ops['stripe_test'] == 1 ) {
            $public_key = $ops['stripe_test_public_key'] ?? '';
        } else {
            $public_key = $ops['stripe_public_key'] ?? '';
        }
        if( !empty( $public_key ) ) {
            $f = new FORM();
            $e = Encrypt::initiate();
        ?>
        <div class="subscription_wrap stripe" data-stripe-public-key="<?php echo $public_key; ?>">

            <div id="payment_response" class="dn" data-action="<?php $e->enc('register_stripe_payment_ajax'); ?>"></div>

            <div id="subscription_form" <?php $f->process_params('','pay','',2,0,[],'','process_payment'); ?> >

                <div class="row">
                    <?php
                    $sps = $db->select( 'stripe_subscription_plans', '', '', 0, 0, '', 0, 'DESC', 'sp_quantity' );
                    $sub_plans = [];
                    $default_plan = '';
                    //echo $default_quantity;
                    foreach( $sps as $sp ) {
                        $sub_plans[ $sp['sp_id'] ] = $sp['sp_name'] . ' (' . $sp['sp_interval'] . ') ' . $sp['sp_price'] . ' USD Quantity - ' . $sp['sp_quantity'] ;
                        $default_plan = $default_quantity <= $sp['sp_quantity'] ? $sp['sp_id'] : $default_plan;
                    }
                    //echo $default_plan;
                    $attr = 'data-pay';
                    $show_plan = $enable_plans  ? $attr : 'disabled '. $attr;
                    $f->select2('plan','Subscription Plan','Select a Plan...',$sub_plans,$default_plan,$show_plan,12,1);
                    $f->text('name','Full Name','Ex: Mohammed Ahmed',$name,$attr,6);
                    $f->text('email','Email','Ex: john_doe@website.com',$email,$attr,6);
                    ?>
                </div>

                <div class="form-group">
                    <label><?php E('Enter Card Details'); ?></label>
                    <div id="card-element"></div>
                    <?php
                    $s_test = $db->get_option('stripe_test');
                    if( !empty( $s_test ) && $s_test == 1 ) {
                        echo '<div class="tac mt15"><div>Success Test Card: 4242424242424242</div><div>Failure Test Card: 4000000000009995</div></div>';
                    }
                    ?>
                </div>

                <!-- Form submit button -->
                <?php
                $f->process_html( 'Subscribe', $button_class . ' submit_button', '', 'update_stripe_subscription_ajax' );
                ?>
            </div>

            <div id="frmProcess" class="dn">
                <span class="ring"></span> Processing...
            </div>

        </div>
        <?php
            get_scripts('stripe,stripe-checkout');
        } else {
            no_access('Stripe API keys are not saved!');
        }
    }

    function update_customer( string $name = '', string $email = '', int $user = 0 ): array {
        if( !empty( $name ) && !empty( $email ) ) {

            $db = new DB();

            // Fetch Existing Customer
            $customer = $db->select( 'stripe_customers', '', 'stc_email = \''.$email.'\'', 1 );
            if( !empty( $customer ) ) {
                return [ 'id' => $customer['stc_stripe_customer_id'] ];
            } else {

                // Create Stripe Customer
                try {
                    $stripe_customer = \Stripe\Customer::create([
                        'name' => $name,
                        'email' => $email
                    ]);
                } catch( Exception $e ) {
                    elog( $e->getMessage() );
                    return [ $e->getMessage() ];
                }

                // Sync Stripe Customer to Local Database
                if( isset( $stripe_customer->id ) ) {
                    $customer_data = [ 'name' => $name, 'email' => $email, 'stripe_customer_id' => $stripe_customer->id ];
                    $user > 0 ? $customer_data['user'] = $user : '';
                    $db->insert( 'stripe_customers', prepare_keys( $customer_data, 'stc_' ), prepare_values( $customer_data ) );
                    return [ 'id' => $stripe_customer->id ];
                }
            }
            //
            $stripe = new STRIPE();
            $stripe->get_stripe_key();

            // Add customer to stripe

        } else {
            return [ 'Name and Email cannot be empty!' ];
        }
    }

    function update_subscription( string $customer_id, string $product_id, int $subscription_id = 0 ): array {
        $db = new DB();

        // Load Subscription
        $sub_query = !empty( $subscription_id ) ? 'stc_stripe_subscription_id = \''.$subscription_id.'\'' : 'stc_stripe_customer_id = \''.$customer_id.'\'';
        $sub = $db->select( 'stripe_customers', 'stc_stripe_subscription_id,stc_stripe_secret_id', $sub_query, 1 );

        $subscription_id = $sub['stc_stripe_subscription_id'] ?? $subscription_id;
        if( empty( $subscription_id ) ) {
            elog( 'Creating Subscription' );
            try {
                $subscription = \Stripe\Subscription::create([
                    'customer' => $customer_id,
                    'items' => [[
                        'price' => $product_id,
                    ]],
                    'payment_behavior' => 'default_incomplete',
                    'expand' => [ 'latest_invoice.payment_intent' ],
                ]);
                //$sid = $subscription->id ?? 0;
                //$secret = $subscription->latest_invoice->payment_intent->client_secret ?? 0;
            } catch( Exception $e ) {
                elog( $e->getMessage() );
                return [ 0, $e->getMessage() ];
            }
            $sid = $subscription->id ?? 0;
            $secret = $subscription->latest_invoice->payment_intent->client_secret ?? 0;
        } else {
            $sid = $sub['stc_stripe_subscription_id'];
            $secret = $sub['stc_stripe_secret_id'];
        }

        // Update Local Database
        $db->update( 'stripe_customers', [ 'stc_stripe_subscription_id', 'stc_stripe_secret_id' ], [ $sid, $secret ], 'stc_stripe_customer_id = \''.$customer_id.'\'' );

        return [ 'id' => $sid, 'secret' => $secret ];
    }

    function get_subscription_plan_from_quantity( int $quantity = 1 ): array {
        $db = new DB();
        $sps = $db->select( 'stripe_subscription_plans', '', '', 0, 0, '', 0, 'DESC', 'sp_quantity' );
        $sps = !empty( $sps ) ? array_by_key( $sps, 'sp_id' ) : [];
        $plan = '';
        foreach( $sps as $sp ) {
            $plan = $quantity <= $sp['sp_quantity'] ? $sp['sp_id'] : $plan;
        }
        return $sps[ $plan ] ?? [];
    }

}

function stripe_payment_intent_ajax(): void {
    $db = new DB();
    $stripe = $db->get_options(['stripe_public_key','stripe_private_key','stripe_test','stripe_test_public_key','stripe_test_private_key']);
    $s_private = $stripe['stripe_private_key'] ?? '';
    $s_test = $stripe['stripe_test'] ?? '';
    $st_private = $stripe['stripe_test_private_key'] ?? '';
    $stripe_key = !empty( $s_test ) && $s_test == 1 ? $st_private : $s_private;
    \Stripe\Stripe::setApiKey( $stripe_key );

    // Parse Data
    $e = Encrypt::initiate();
    $data = $e->decrypt_array( $_POST['data'] );
    elog( $stripe );
    elog( $data );

    header('Content-Type: application/json');

    try {
        // retrieve JSON from POST body
        $jsonStr = file_get_contents('php://input');
        $jsonObj = json_decode($jsonStr);

        // Create a PaymentIntent with amount and currency
        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
        ]);

        $output = [
            'id' => $paymentIntent->id,
            'clientSecret' => $paymentIntent->client_secret,
        ];
        echo json_encode($output);
    } catch (Error $e) {
        //http_response_code(500);
        elog( $e->getMessage() );
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function stripe_create_customer_ajax(): void {

    $api_error = '';

    $db = new DB();
    $stripe = $db->get_options(['stripe_public_key','stripe_private_key','stripe_test','stripe_test_public_key','stripe_test_private_key']);
    $s_private = $stripe['stripe_private_key'] ?? '';
    $s_test = $stripe['stripe_test'] ?? '';
    $st_private = $stripe['stripe_test_private_key'] ?? '';
    $stripe_key = !empty( $s_test ) && $s_test == 1 ? $st_private : $s_private;
    \Stripe\Stripe::setApiKey( $stripe_key );

    // Parse Data
    $e = Encrypt::initiate();
    $data = $e->decrypt_array( $_POST['data'] );

    $payment_intent_id = $_POST['payment_intent_id'] ?? '';
    $name = $data['name'] ?? '';
    $email = $data['email'] ?? '';

    // Add customer to stripe
    try {
        $customer = \Stripe\Customer::create(array(
            'name' => $name,
            'email' => $email
        ));
    } catch(Exception $e) {
        $api_error = $e->getMessage();
        elog( $e->getMessage() );
    }

    if( empty($api_error) && $customer){
        try {
            // Update PaymentIntent with the customer ID
            $paymentIntent = \Stripe\PaymentIntent::update($payment_intent_id, [
                'customer' => $customer->id
            ]);
        } catch (Exception $e) {
            $api_error = $e->getMessage();
            elog( $e->getMessage() );
            echo json_encode(['error' => $api_error]);
        }

        $output = [
            'id' => $payment_intent_id,
            'customer_id' => $customer->id
        ];
        echo json_encode($output);
    } else {
        //http_response_code(500);
        echo json_encode(['error' => $api_error]);
    }
}

function update_subscription_product_ajax( int|array $sid ): array {
    $db = new DB();

    $stripe = new STRIPE();
    $stripe->get_stripe_key();

    // Fetch Existing Subscription Plan
    if( is_numeric( $sid ) && $sid ) {
        $id = $sid;
        $product = $db->select( 'stripe_subscription_plans', '', 'sp_id = \''.$id.'\'', 1 );
        $name = $product['sp_name'] ?? '';
        $price = $product['sp_price'] ?? 0;
        $quantity = $product['sp_quantity'] ?? '';
        $interval = $product['sp_interval'] ?? '';
        $currency = $product['sp_currency'] ?? 'USD';
        elog( $product );
    } else {
        $id = $_POST['id'] ?? 0;
        $product = $id ? $db->select( 'stripe_subscription_plans', '', 'sp_id = \''.$id.'\'', 1 ) : [];
        $name = $_POST['name'] ?? '';
        $price = $_POST['price'] ?? 0;
        $quantity = $_POST['quantity'] ?? '';
        $interval = $_POST['interval'] ?? '';
        $currency = $_POST['currency'] ?? 'USD';
    }
    $cents = round( $price * 100 );
    $product_data = [ 'name' => $name, 'price' => $price, 'quantity' => $quantity, 'interval' => $interval, 'currency' => $currency ];
    elog( $product_data );

    // Update Subscription Product
    $product_id = 0;
    if( isset( $product['sp_stripe_product_id'] ) ) {
        $update = $db->update( 'stripe_subscription_plans', prepare_keys( $product_data, 'sp_' ), prepare_values( $product_data ), 'sp_id = \''.$id.'\'' );
        if( $update ) {
            try {
                // Update price with subscription info and interval
                $price = \Stripe\Price::update( $product['sp_stripe_product_id'], [
                    'unit_amount' => $cents,
                    'currency' => strtolower( $currency ),
                    'recurring' => ['interval' => $interval],
                    'product_data' => ['name' => $name],
                ]);
            } catch (Exception $e) {
                elog( $e->getMessage() );
                return [ 0, $e->getMessage() ];
            }
        }
        $product_id = $product['sp_stripe_product_id'];
    } else {
        try {
            // Create price with subscription info and interval
            $price = \Stripe\Price::create([
                'unit_amount' => $cents,
                'currency' => strtolower( $currency ),
                'recurring' => ['interval' => $interval],
                'product_data' => ['name' => $name],
            ]);
            $product_data['stripe_product_id'] = $price->id;
            $create = $db->insert( 'stripe_subscription_plans', prepare_keys( $product_data, 'sp_' ), prepare_values( $product_data ) );
        } catch (Exception $e) {
            elog( $e->getMessage() );
            return [ 0, $e->getMessage() ];
        }
        $product_id = $price->id;
    }

    if( is_array( $sid ) ) {
        !empty( $product_id ) ? es( 'Subscription Plan Successfully Created!' ) : ef( 'Failed to create subscription plan!' );
    }
    return [ 'id' => $product_id ];
}

function update_stripe_subscription_ajax(): void {
    $p = $_POST;
    $plan_id = $p['plan'] ?? 0;
    $name = $p['name'] ?? 0;
    $email = $p['email'] ?? 0;

    $stripe = new STRIPE();
    $stripe->get_stripe_key();

    // Add customer to stripe
    $customer = $stripe->update_customer( $name, $email );
    $customer_id = $customer['id'] ?? 0;

    // Fetch plan details from the database
    $db = new DB();
    if( $plan_id > 0 && !empty( $customer_id ) ) {

        if( $customer ){

            $subscription_product = update_subscription_product_ajax( $plan_id );
            $subscription_product_id = $subscription_product['id'] ?? 0;

            if( $subscription_product_id ){

                // Create a new subscription
                $subscription = $stripe->update_subscription( $customer_id, $subscription_product_id );
                $subscription_id = $subscription['id'] ?? 0;
                $subscription_secret = $subscription['secret'] ?? '';

                if( $subscription_id ){
                    $output = [
                        'subscription_id' => $subscription_id,
                        'client_secret' => $subscription_secret,
                        'customer_id' => $customer_id
                    ];
                    es(json_encode($output));
                } else {
                    ef( 'Subscription not Found in Database! Please consult admin!' );
                }
            } else {
                ef( 'Subscription Product ID not Found in Database! Please consult admin!' );
            }
        }else{
            ef( 'Customer not Found in Database! Please consult admin!' );
        }
    } else {
        ef('Subscription Plan cannot be empty! Please consult admin!');
    }
}

function register_stripe_payment_ajax(): void {

    $stripe = new STRIPE();
    $stripe->get_stripe_key();

    $p = $_POST;
    $db = new DB();
    elog( $p );
    $payment_intent = $p['payment_intent'];
    $subscription_id = $p['subscription_id'];
    $customer_id = $p['customer_id'];
    $plan_id = $p['plan_id'];

    // Fetch plan details from the database
    $product = $db->select( 'stripe_subscription_plans', '', 'sp_id = \''.$plan_id.'\'', 1 );

    // Retrieve customer info
    try {
        $customer = \Stripe\Customer::retrieve($customer_id);
    } catch( Exception $e ) {
        elog( $e->getMessage() );
    }

    // Check whether the charge was successful
    elog($payment_intent);
    if(!empty($payment_intent) && $payment_intent['status'] == 'succeeded'){

        // Retrieve subscription info
        try {
            $subscriptionData = \Stripe\Subscription::retrieve($subscription_id);
        }catch(Exception $e) {
            elog( $e->getMessage() );
        }

        $payment_intent_id = $payment_intent['id'];
        $paidAmount = $payment_intent['amount'];
        $paidAmount = ($paidAmount/100);
        $paidCurrency = $payment_intent['currency'];
        $payment_status = $payment_intent['status'];

        $created = date("Y-m-d H:i:s", $payment_intent['created']);
        $current_period_start = $current_period_end = '';
        if(!empty($subscriptionData)){
            $created = date("Y-m-d H:i:s", $subscriptionData->created);
            $current_period_start = date("Y-m-d H:i:s", $subscriptionData->current_period_start);
            $current_period_end = date("Y-m-d H:i:s", $subscriptionData->current_period_end);
        }

        $name = $email = '';
        if(!empty($customer)){
            $name = !empty($customer->name)?$customer->name:'';
            $email = !empty($customer->email)?$customer->email:'';
        }

        // Check if any transaction data exists already with the same TXN ID
        $exist = $db->select( 'stripe_subscriptions', '', 'sub_stripe_payment_intent_id = \''.$payment_intent_id.'\'', 1 );
        $db_txn_id = $payment_intent_id;

        $payment_id = 0;
        if( !empty( $exist ) ){
            $payment_id = $exist['sub_id'];
        } else {
            // Insert transaction data into the database
            $data = [
                'user' => 0,
                'plan' => $plan_id,
                'method' => 'card',
                'stripe_subscription_id' => $subscription_id,
                'stripe_customer_id' => $customer_id,
                'stripe_payment_intent_id' => $payment_intent_id,
                'amount' => $paidAmount,
                'currency' => $paidCurrency,
                'interval' => $product['sp_interval'],
                'interval_count' => 1,
                'email' => $email,
                'date' => $created,
                'start' => $current_period_start,
                'end' => $current_period_end,
                'status' => 1
            ];
            $record = $db->insert( 'stripe_subscriptions', prepare_keys( $data, 'sub_' ), prepare_values( $data ) );
        }
        $output = [
            'payment_id' => base64_encode($payment_id)
        ];
        echo json_encode($output);
    } else {
        echo json_encode(['error' => 'Transaction has been failed!']);
    }
}