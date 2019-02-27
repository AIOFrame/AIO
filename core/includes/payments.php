<?php

function cc_avenue( $amount, $redirect_url = APPURL, $test = 0, $currency = 'AED', $order_id = '112233', $name = '', $address = '', $city = '', $state = '', $zip = '', $country = '', $tel = '', $email = '' ) {

    require_once COREPATH . 'core/components/pays/ccavenue/Crypto.php';

    $ccapis = ['cc_id','cc_access','cc_key','cc_test_id','cc_test_access','cc_test_key'];
    $cc = get_options($ccapis);
    $id = isset($cc['cc_id']) ? $cc['cc_id'] : '';
    $access = isset($cc['cc_access']) ? $cc['cc_access'] : '';
    $key = isset($cc['cc_key']) ? $cc['cc_key'] : '';
    $tid = isset($cc['cc_test_id']) ? $cc['cc_test_id'] : '';
    $taccess = isset($cc['cc_test_access']) ? $cc['cc_test_access'] : '';
    $tkey = isset($cc['cc_test_key']) ? $cc['cc_test_key'] : '';

    if( $test ) {
        $id = $tid;
        $access = $taccess;
        $key = $tkey;
    }

    if ( !empty($id) && !empty($key) ) {

        if( isset( $_POST["encResp"] ) ){

            $encResponse=$_POST["encResp"];			//This is the response sent by the CCAvenue Server
            $rcvdString=decrypt($encResponse,$key);		//Crypto Decryption used as per the specified working key.
            $order_status="";
            $decryptValues=explode('&', $rcvdString);
            $dataSize=sizeof($decryptValues);


            for($i = 0; $i < $dataSize; $i++) {
                $information=explode('=',$decryptValues[$i]);
                if($i==3)	$order_status=$information[1];
            }

            if($order_status==="Success") {
                return [1,T('Payment Successful')];
                //echo "<br>Thank you for shopping with us. Your credit card has been charged and your transaction is successful. We will be shipping your order to you soon.";
            } else if($order_status==="Aborted") {
                return [0,T('Payment Aborted')];
                //echo "<br>Thank you for shopping with us.We will keep you posted regarding the status of your order through e-mail";
            } else if($order_status==="Failure") {
                return [0,T('Payment Declined')];
                //echo "<br>Thank you for shopping with us.However,the transaction has been declined.";
            } else {
                return [0,T('Security Error, Illegal Access')];
                //echo "<br>Security Error. Illegal access detected";
            }
            /* echo "<br><br>";
            echo "<table cellspacing=4 cellpadding=4>";
            for($i = 0; $i < $dataSize; $i++) {
                $information=explode('=',$decryptValues[$i]);
                echo '<tr><td>'.$information[0].'</td><td>'.$information[1].'</td></tr>';
            }
            echo "</table><br>";
            echo "</center>"; */

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

            $encrypted_data=encrypt($merchant_data,$working_key);

            $post_url = 'https://secure.ccavenue.ae/transaction/transaction.do?command=initiateTransaction';

            ?>

            <form method="post" name="redirect" action="<?php echo $post_url; ?>">
                <?php
                echo '<input type=hidden name=encRequest value="' . $encrypted_data . '"">';
                echo '<input type=hidden name=access_code value="' . $access_code . '">';
                ?>
            </form>

<!--            <script language='javascript'>document.redirect.submit();</script>-->

            <?php
        }

    }

}