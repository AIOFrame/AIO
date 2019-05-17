<?php

function cc_avenue( $amount, $redirect_url = APPURL, $currency = 'AED', $order_id = '112233', $name = '', $address = '', $city = '', $state = '', $zip = '', $country = '', $tel = '', $email = '' ) {

    require_once COREPATH . 'core/components/pays/ccavenue/Crypto.php';

    $ccapis = ['pay_test','cc_auth','cc_test_id','cc_test_access','cc_test_key'];
    $cc = get_options($ccapis);

    $test = isset( $cc['pay_test'] ) && $cc['pay_test'] == 'true' ? 1 : 0;
    $site = str_replace( 'http://', '', str_replace( 'https://', '', str_replace( 'www.', '', APPURL ) ) );

    if( $test ) {
        $tid = isset($cc['cc_test_id']) ? $cc['cc_test_id'] : '';
        $taccess = isset($cc['cc_test_access']) ? $cc['cc_test_access'] : '';
        $tkey = isset($cc['cc_test_key']) ? $cc['cc_test_key'] : '';
        $id = $tid;
        $access = $taccess;
        $key = $tkey;
        $log = 'Test payment via CC Avenue, Test ID : '. $tid .', Test Access : '. $taccess . ', Test Key : '. $tkey . '.';
    } else if( isset( $cc['cc_auth'] ) && !empty( $cc['cc_auth'] ) ) {
        $cc_auths = explode( '|', $cc['cc_auth'] );
        if( is_array( $cc_auths ) ) {
            foreach( $cc_auths as $cc_auth ) {
                $cc_data = explode( ',', $cc_auth );
                if( is_array( $cc_data ) && $cc_data[0].'/' == $site ) {
                    $id = $cc_data[1];
                    $access = $cc_data[2];
                    $key = $cc_data[3];
                }
            }
        }
        $log = 'Payment via CC Avenue, ID : '.$id.', Access : '.$access.', Key : '.$key . '.';
    }
    elog( $log, 'payment', '', '', 'CC Avenue' );

    if ( !empty($id) && !empty($key) ) {

        if( isset( $_POST["encResp"] ) ){

            $encResponse=$_POST["encResp"];
            $rcvdString=decrypt($encResponse,$key);
            $odd = explode('&', $rcvdString);
            //skel($odd);
            foreach( $odd as $d ){
                $a = explode( '=', $d );
                $dd[$a[0]] = $a[1];
            }

            $data = [ 'amount' => $dd['mer_amount'], 'currency' => $dd['currency'], 'gate' => 'CC Avenue', 'way' => $dd['payment_mode'], 'at' => date('Y-m-d H:i:s'), 'by' => get_current_user_id(), 'status' => $dd['order_status'] ];
            $pay_record = insert( 'payments', prepare_keys( $data, 'pay_' ), prepare_values( $data ) );

            switch( $dd['order_status'] ) {
                case 'Success':
                    if( empty( $pay_record ) ){ add_option( 'pay_bkp', $data, get_current_user_id() ); }
                    return [$pay_record,T('Payment Successful')];
                    break;
                case 'Aborted':
                    return [0,T('Payment Aborted')];
                    break;
                case 'Failure':
                    return [0,T('Payment Declined')];
                    break;
                default:
                    return [0,T('Security Error, Illegal Access')];
                    break;
            }

        } else {

            // set details
            $amount = $test ? (string)$amount : $amount;

            $merchant_data='currency='.$currency.'&merchant_id='.$id.'&amount='.$amount.'&order_id='.$order_id.'&redirect_url='.$redirect_url.'&cancel_url='.$redirect_url.'&language=EN&';
            $working_key=$key;
            $access_code=$access;

            /* foreach ($_POST as $key => $value){
                $merchant_data.=$key.'='.$value.'&';
            } */

            $name !== '' ? $merchant_data.='billing_name='.$name.'&delivery_name='.$name.'&' : '';
            $address !== '' ? $merchant_data.='billing_address='.$address.'&delivery_address='.$address.'&' : '';
            $city !== '' ? $merchant_data.='billing_city='.$city.'&delivery_city='.$city.'&' : '';
            $state !== '' ? $merchant_data.='billing_state='.$state.'&delivery_state='.$state.'&' : '';
            $zip !== '' ? $merchant_data.='billing_zip='.$zip.'&delivery_zip='.$zip.'&' : '';
            $country !== '' ? $merchant_data.='billing_country='.$country.'&delivery_country='.$country.'&' : '';
            $tel !== '' ? $merchant_data.='billing_tel='.$tel.'&delivery_tel='.$tel.'&' : '';
            $email !== '' ? $merchant_data.='billing_email='.$email.'&delivery_email='.$email.'&' : '';

            /*isset($address['address']) ? $ccavenue->setBillingAddress($address['address']) : '';
            isset($address['city']) ? $ccavenue->setBillingCity($address['city']) : '';
            isset($address['zip']) ? $ccavenue->setBillingZip($address['zip']) : '';
            isset($address['state']) ? $ccavenue->setBillingState($address['state']) : '';
            isset($address['country']) ? $ccavenue->setBillingCountry($address['country']) : '';
            isset($address['email']) ? $ccavenue->setBillingEmail($address['email']) : '';
            isset($address['phone']) ? $ccavenue->setBillingTel($address['phone']) : '';
            isset($address['notes']) ? $ccavenue->setBillingNotes($address['notes']) : '';*/

            //echo $merchant_data;
            $merchant_data = substr( $merchant_data, 0 , -1 );

            $test ? skel( explode( '&', $merchant_data ) ) : '';

            $encrypted_data = encrypt($merchant_data, $working_key);

            $post_url = 'https://secure.ccavenue.ae/transaction/transaction.do?command=initiateTransaction';

            ?>

            <form method="post" name="redirect" action="<?php echo $post_url; ?>">
                <?php
                echo '<input type=hidden name=encRequest value="' . $encrypted_data . '"">';
                echo '<input type=hidden name=access_code value="' . $access_code . '">';
                ?>
            </form>

            <?php
        }

    } else {

        elog( 'Payment ID or Key missing', 'payment', '', '', 'CC Avenue' );
        notify( T('CC Avenue Payment ID or Key Missing from Options, Please consult developer') );

    }

}