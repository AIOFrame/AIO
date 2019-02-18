<?php

function cc_avenue( $amount, $order_id, $address = [], $redirect_url = APPURL, $test = 0 ) {

    require_once COREPATH . 'core/components/pays/ccavenue/payment.php';
    require_once COREPATH . 'core/components/pays/ccavenue/util.php';
    //use Kishnio\CCAvenue\Payment as CCAvenueClient;

    $mid = get_option('cca_merchant');
    $cckey = get_option('cca_key');

    if (!empty($mid) && !empty($cckey)) {

        $ccavenue = new \Kishanio\CCAvenue\Payment($mid, $cckey, $redirect_url);

        if( isset( $_POST["encResponse"] ) ){

            $res = $ccavenue->response( $_POST["encResponse"] );

            var_dump( $res );

        } else {

            // set details
            $ccavenue->setAmount($amount);
            $ccavenue->setOrderId($order_id);

            isset($address['name']) ? $ccavenue->setBillingName($address['name']) : '';
            isset($address['address']) ? $ccavenue->setBillingAddress($address['address']) : '';
            isset($address['city']) ? $ccavenue->setBillingCity($address['city']) : '';
            isset($address['zip']) ? $ccavenue->setBillingZip($address['zip']) : '';
            isset($address['state']) ? $ccavenue->setBillingState($address['state']) : '';
            isset($address['country']) ? $ccavenue->setBillingCountry($address['country']) : '';
            isset($address['email']) ? $ccavenue->setBillingEmail($address['email']) : '';
            isset($address['phone']) ? $ccavenue->setBillingTel($address['phone']) : '';
            isset($address['notes']) ? $ccavenue->setBillingNotes($address['notes']) : '';

            // copy all the billing details to shipping details
            $ccavenue->billingSameAsShipping();

            // get encrypted data to be passed
            $data = $ccavenue->getEncryptedData();

            // merchant id to be passed along the param
            $merchant = $ccavenue->getMerchantId(); ?>

            <form method="post" name="redirect" action="http://www.ccavenue.com/shopzone/cc_details.jsp">
                <?php
                echo '<input type=hidden name=encRequest value="' . $data . '"">';
                echo '<input type=hidden name=Merchant_Id value="' . $merchant . '">';
                ?>
            </form>

            <script language='javascript'>document.redirect.submit();</script>

            <?php
        }

    }

}