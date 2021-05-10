<?php

class PAY {

    function __construct() {
        require_once ROOTPATH . 'core/external/vendor/autoload.php';
    }

    function render_2checkout_api( string $pre = '' ) {
        $db = new DB();
        $f = new FORM();
        $id = '2checkout_merchant_id';
        $key = '2checkout_private_key';
        $db->save_post_options([$id,$key]);
        $f->text( $id, '2Checkout Merchant ID', 'Ex: 901248204', $db->get_option($id), '', $pre );
        $f->text( $key, '2Checkout Private Key', 'Ex: BE632CB0-BB29-11E3-AFB6-D99C28100996', $db->get_option($key), '', $pre );
    }

    function process_2checkout_payment() {
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

}