<?php

// global $options;
// skel( $options );

class OPTIONS {

    function brand_options(): void {
        $f = new FORM();
        $db = new DB();
        $brands = [ 'app_name', 'primary_color', 'secondary_color', 'logo_light', 'logo_dark', 'primary_color_dark', 'secondary_color_dark', 'fav' ];
        $ops = $db->get_options( $brands );
        echo '<div class="row"';
        $f->option_params( 'brand', 2, 2, $brands, [], [] );
        echo '>';
        $attr = 'data-brand';
        $ext = 'jpg,svg,png';
        $name = $ops['app_name'] ?? '';
        $c1 = $ops['primary_color'] ?? '';
        $c2 = $ops['secondary_color'] ?? '';
        $dc1 = $ops['primary_color_dark'] ?? '';
        $dc2 = $ops['secondary_color_dark'] ?? '';
        $light = $ops['logo_light'] ?? '';
        $dark = $ops['logo_dark'] ?? '';
        $fav = $ops['fav'] ?? '';
        $f->text('app_name','App Name','Ex: AIO University...',$name,$attr,8);
        $f->upload('fav','Fav Icon','Upload',$fav,0,0,'upload',$attr,'png',5,1,'',4);
        $f->upload('logo_light','Logo - Light Theme','Upload',$light,0,0,'upload',$attr,$ext,5,1,'',4);
        $f->color('primary_color','Primary Color','Ex: F1F1F1',$c1,$attr,4,'','#primary_color');
        $f->color('secondary_color','Secondary Color','Ex: A2A2A2',$c2,$attr,4,'','#secondary_color');
        $f->upload('logo_dark','Logo - Dark','Upload',$dark,0,0,'upload',$attr,$ext,5,1,'',4);
        $f->color('primary_color_dark','Primary Color - Dark','Ex: F1F1F1',$dc1,$attr,4,'','#primary_color_dark');
        $f->color('secondary_color_dark','Secondary Color - Dark','Ex: A2A2A2',$dc2,$attr,4,'','#secondary_color_dark');
        $f->process_options('Save Brand Options','store grad','','col-12 tac');
        echo '</div>';
    }

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
        $f->process_options('Save Stripe Options','store grad','','col-12 tac');
        echo '</div>';
    }

}