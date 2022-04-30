<?php

class OPTIONS {





    /**
     * Renders Maps Options
     * @return void
     */
    function map_options(): void {

    }

    /**
     * Renders Stripe API Options
     * @return void
     */
    function stripe_options(): void {
        $f = new FORM();
        $db = new DB();
        // Gateway Fields
        $gateway = [ 'stripe_public_key', 'stripe_private_key', 'stripe_test_public_key', 'stripe_test_private_key', 'stripe_test' ];
        $ops = $db->get_options( $gateway );
        echo '<div class="row"';
        $f->process_params('', 'stripe', '', 3, 0, [], 'Successfully Saved Settings');
        echo '>';
        $attr = 'data-stripe';
        $pub = $ops['stripe_public_key'] ?? '';
        $pri = $ops['stripe_private_key'] ?? '';
        $pub_test = $ops['stripe_test_public_key'] ?? '';
        $pri_test = $ops['stripe_test_private_key'] ?? '';
        $test = $ops['stripe_test'] ?? '';
        $f->texts([
            [ 'stripe_public_key', 'Stripe Public Key', '', $pub ],
            [ 'stripe_private_key', 'Stripe Private Key', '', $pri ],
            [ 'stripe_test_public_key', 'Stripe Public Key - Test Mode', '', $pub_test ],
            [ 'stripe_test_private_key', 'Stripe Private Key - Test Mode', '', $pri_test ],
        ], $attr, 3 );
        $f->checkboxes( 'stripe_test', 'Enable Test Code', [1=>''], $test, $attr.' class="slide"', 0, 4 );
        $f->process_html('Save Stripe Options','l r5 m0','','process_options_ajax','col-12 tac');
        echo '</div>';
    }

}