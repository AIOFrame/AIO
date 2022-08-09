<?php
require ROOTPATH . 'core/external/vendor/autoload.php';

class STRIPE {

    function __construct() {

        // Subscriptions Plans
        $tables[] = [ 'subscription_plans', [
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
            [ 'user', 'INT', 13, 0 ],
        ], 'stc', 1 ];

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
                <td><?php E('Options'); ?></td>
            </tr>
            </thead>
            <tbody>
            <?php
            $db = new DB();
            $sps = $db->select( 'subscription_plans' );
            if( !empty( $sps ) ) {
                foreach( $sps as $sp ){
                    $int = $intervals[ $sp['sp_interval'] ] ?? '';
                    echo '<tr>';
                    echo '<td>'.$sp['sp_name'].'</td>';
                    echo '<td>'.$int.'</td>';
                    echo '<td>'.$sp['sp_price'].'</td>';
                    echo '<td>'.$sp['sp_quantity'].'</td>';
                    echo '<td>'.$sp['sp_currency'].'</td><td>';
                    $f->trash_html('subscription_plans','sp_id = \''.$sp['sp_id'].'\'','button','Delete','red b bsn l m0');
                    echo '</td></tr>';
                }
            }
            ?>
            </tbody>
            <tfoot>
            <tr <?php $f->process_params('subscription_plans','sp','sp_',2,2); ?>>
                <td><?php $f->text('name','','Ex: Platinum Plan','',$a); ?></td>
                <td><?php $f->select2('interval','','Choose...',$intervals,'',$a,'',1); ?></td>
                <td><?php $f->text('price','','Ex: 250','',$a); ?></td>
                <td><?php $f->text('quantity','','Ex: 10','',$a); ?></td>
                <td><?php $f->select2('currency','','Choose...',['AED','AUD','USD','EUR','AED','INR'],'',$a); ?></td>
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
     * @param string $button_class Class of the Subscribe Button
     * @return void
     */
    function subscription_form( int $user_id = 0, int $default_quantity = 0, bool $enable_plans = true, string $name = '', string $email = '', string $button_class = 'l br5' ): void {
        skel($_POST);
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
        ?>
        <div class="subscription_wrap stripe" data-stripe-public-key="<?php echo $public_key; ?>">

            <div id="payment_response" class="dn"></div>

            <div id="subscription_form" <?php $f->process_params('','pay'); ?> >

                <div class="row">
                    <?php
                    $sps = $db->select( 'subscription_plans', '', '', 0, 0, '', 0, 'DESC', 'sp_quantity' );
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
                <button id="submit_button" onclick="init_subscription()" class="submit_button dn <?php echo $button_class; ?>">
                    <span class="spinner dn" id="spinner"></span>
                    <span id="buttonText"><?php E('Subscribe'); ?></span>
                </button>
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

        }
    }

    function update_subscription_product( int $sid = 0, string $name = '', float $price = 0, int $quantity = 1, string $interval = 'monthly', string $currency = 'USD' ): array {
        $db = new DB();

        $sid = $_POST['id'] ?? 0;
        $name = $_POST['name'] ?? $name;
        $price = $_POST['price'] ?? $price;
        $cents = round( $price * 100 );
        $quantity = $_POST['quantity'] ?? $quantity;
        $interval = $_POST['interval'] ?? $interval;
        $currency = $_POST['currency'] ?? $currency;

        // Fetch Existing Customer
        $product = $sid > 0 ? $db->select( 'subscription_plans', '', 'sp_id = \''.$sid.'\'', 1 ) : [];
        elog( $product );
        $product_data = [ 'name' => $name, 'price' => $price, 'quantity' => $quantity, 'interval' => $interval, 'currency' => $currency ];
        if( !empty( $product ) ) {
            $update = $db->update( 'subscription_plans', prepare_keys( $product_data, 'sp_' ), prepare_values( $product_data ), 'sp_id = \''.$sid.'\'' );
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
                    $api_error = $e->getMessage();
                }
            }
            return [ 'id' => $product['sp_stripe_product_id'] ];
        } else {
            $create = $db->insert( 'subscription_plans', prepare_keys( $product_data, 'sp_' ), prepare_values( $product_data ) );
            if( $create ) {
                try {
                    // Create price with subscription info and interval
                    $price = \Stripe\Price::create([
                        'unit_amount' => $cents,
                        'currency' => strtolower( $currency ),
                        'recurring' => ['interval' => $interval],
                        'product_data' => ['name' => $name],
                    ]);
                } catch (Exception $e) {
                    $api_error = $e->getMessage();
                }
            }
            return [ 'id' => $price->id ];
        }
    }

    function update_subscription( string $email = '', int $plan = 0 ): array {
        return [];
    }

}

function stripe_update_customer_ajax(): void {

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


        if( empty( $api_error ) && $customer ){

            $subscription_product = $stripe->update_subscription_product( $plan_id );
            $subscription_product_id = $subscription_product['id'] ?? 0;

            if( $subscription_product_id ){
                // Create a new subscription
                try {
                    $subscription = \Stripe\Subscription::create([
                        'customer' => $customer_id,
                        'items' => [[
                            'price' => $subscription_product_id,
                        ]],
                        'payment_behavior' => 'default_incomplete',
                        'expand' => [ 'latest_invoice.payment_intent' ],
                    ]);
                } catch( Exception $e ) {
                    $api_error = $e->getMessage();
                }

                if( empty( $api_error ) && $subscription ){
                    $output = [
                        'subscription_id' => $subscription->id,
                        'client_secret' => $subscription->latest_invoice->payment_intent->client_secret,
                        'customer_id' => $customer_id
                    ];
                    es(json_encode($output));
                }else{
                    ef($api_error);
                }
            } else {
                ef($api_error);
            }
        }else{
            ef($api_error);
        }
    } else {
        ef('Subscription ID cannot be empty! Please consult admin!');
    }
}

function register_stripe_payment_ajax(): void {
    $payment_intent = !empty($jsonObj->payment_intent)?$jsonObj->payment_intent:'';
    $subscription_id = !empty($jsonObj->subscription_id)?$jsonObj->subscription_id:'';
    $customer_id = !empty($jsonObj->customer_id)?$jsonObj->customer_id:'';
    $subscr_plan_id = !empty($jsonObj->subscr_plan_id)?$jsonObj->subscr_plan_id:'';

    // Fetch plan details from the database
    $sqlQ = "SELECT * FROM plans WHERE id=?";
    $stmt = $db->prepare($sqlQ);
    $stmt->bind_param("i", $db_id);
    $db_id = $subscr_plan_id;
    $stmt->execute();
    $result = $stmt->get_result();
    $planData = $result->fetch_assoc();

    $planName = $planData['name'];
    $planPrice = $planData['price'];
    $planInterval = $planData['interval'];

    // Retrieve customer info
    try {
        $customer = \Stripe\Customer::retrieve($customer_id);
    }catch(Exception $e) {
        $api_error = $e->getMessage();
    }

    // Check whether the charge was successful
    if(!empty($payment_intent) && $payment_intent->status == 'succeeded'){

        // Retrieve subscription info
        try {
            $subscriptionData = \Stripe\Subscription::retrieve($subscription_id);
        }catch(Exception $e) {
            $api_error = $e->getMessage();
        }

        $payment_intent_id = $payment_intent->id;
        $paidAmount = $payment_intent->amount;
        $paidAmount = ($paidAmount/100);
        $paidCurrency = $payment_intent->currency;
        $payment_status = $payment_intent->status;

        $created = date("Y-m-d H:i:s", $payment_intent->created);
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
        $sqlQ = "SELECT id FROM user_subscriptions WHERE stripe_payment_intent_id = ?";
        $stmt = $db->prepare($sqlQ);
        $stmt->bind_param("s", $db_txn_id);
        $db_txn_id = $payment_intent_id;
        $stmt->execute();
        $result = $stmt->get_result();
        $prevRow = $result->fetch_assoc();

        $payment_id = 0;
        if(!empty($prevRow)){
            $payment_id = $prevRow['id'];
        }else{
            // Insert transaction data into the database
            $sqlQ = "INSERT INTO user_subscriptions (user_id,plan_id,stripe_subscription_id,stripe_customer_id,stripe_payment_intent_id,paid_amount,paid_amount_currency,plan_interval,payer_email,created,plan_period_start,plan_period_end,status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $stmt = $db->prepare($sqlQ);
            $stmt->bind_param("iisssdsssssss", $db_user_id, $db_plan_id, $db_stripe_subscription_id, $db_stripe_customer_id, $db_stripe_payment_intent_id, $db_paid_amount, $db_paid_amount_currency, $db_plan_interval, $db_payer_email, $db_created, $db_plan_period_start, $db_plan_period_end, $db_status);
            $db_user_id = $userID;
            $db_plan_id = $subscr_plan_id;
            $db_stripe_subscription_id = $subscription_id;
            $db_stripe_customer_id = $customer_id;
            $db_stripe_payment_intent_id = $payment_intent_id;
            $db_paid_amount = $paidAmount;
            $db_paid_amount_currency = $paidCurrency;
            $db_plan_interval = $planInterval;
            $db_payer_email = $email;
            $db_created = $created;
            $db_plan_period_start = $current_period_start;
            $db_plan_period_end = $current_period_end;
            $db_status = $payment_status;
            $insert = $stmt->execute();

            if($insert){
                $payment_id = $stmt->insert_id;

                // Update subscription ID in users table
                $sqlQ = "UPDATE users SET subscription_id=? WHERE id=?";
                $stmt = $db->prepare($sqlQ);
                $stmt->bind_param("ii", $db_subscription_id, $db_user_id);
                $db_subscription_id = $payment_id;
                $db_user_id = $userID;
                $update = $stmt->execute();
            }
        }

        $output = [
            'payment_id' => base64_encode($payment_id)
        ];
        echo json_encode($output);
    } else {
        echo json_encode(['error' => 'Transaction has been failed!']);
    }
}