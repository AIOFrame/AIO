<?php global $ui_params;
$sets = [ 'sms' => 'SMS', 'email' => 'E Mail', 'payments' => 'Payments', 'other' => 'Other' ];

$apis = ['pay_way','pay_test','cc_id','cc_access','cc_key','cc_test_id','cc_test_access','cc_test_key','cc_test','mailer','send_grid_key'];
//skel($_POST);
save_post_options( $apis );
$o = get_options( $apis );
//skel($o);
?>
<div id="aio_apis">
    <div class="tabs material">
        <div class="tab_heads">
            <?php $x = 0; foreach( $ui_params as $p ) {
                $c = $x == 0 ? 'l on' : '';
                echo '<div class="tab '.$c.'" data-t="#'.$p.'">'.T($sets[$p]).'</div>';
                $x++;
            } ?>
        </div>
        <div class="tab_data pt20">
            <?php if( in_array( 'payments', $ui_params ) ) { ?>
            <form method="post"  id="payments">
                <div class="row">
                    <div class="col-lg-6 col-6">
                        <label for="pay_way"><?php E('Preferred Payment Gateway'); ?></label>
                        <select id="pay_way" name="pay_way" class="select2" data-placeholder="<?php E('Select Payment Gateway'); ?>">
                            <?php select_options(['cc_avenue'=>'CC Avenue','paypal'=>'Paypal','stripe'=>'Stripe','2checkout'=>'2 Checkout','authorize'=>'Authorize.Net','skrill'=>'Skrill','amazon'=>'Amazon Payments','payfort'=>'Payfort','telr'=>'Telr','instamojo'=>'Instamojo','WePay'=>'We Pay','paytm'=>'Pay TM','alipay'=>'Ali Pay','wechat'=>'We Chat Pay / Ten Pay','unionpay'=>'Union Pay'],$o['pay_way'] ?? ''); ?>
                        </select>
                    </div>
                    <div class="col-lg-6 col-6">
                        <?php render_input('toggle','pay_test',T('Enable Payment Test Mode'),'',$o['pay_test'] ?? ''); ?>
<!--                            <input type="checkbox" id="pay_test" name="pay_test" class="slide" value="1"<?php //echo isset($o['pay_test']) ? 'checked' : ''; ?>-->
                    </div>
                </div>
                <div id="cc_options" class="acc <?php echo isset($o['pay_way']) && $o['pay_way'] == 'cc_avenue' ? 'on' : ''; ?>">
                    <div class="head" data-on="#cc_options"><?php E('CC Avenue APIs'); ?></div>
                    <div class="body">
                        <div class="row">
                            <?php in_texts([['cc_id','ID','',$o['cc_id'] ?? ''],['cc_access','Access','',$o['cc_access'] ?? ''],['cc_key','Key','',$o['cc_key'] ?? ''],],'data-cc',4); ?>
                            <?php in_texts([['cc_test_id','Test ID','',$o['cc_test_id'] ?? ''],['cc_test_access','Test Access','',$o['cc_test_access'] ?? ''],['cc_test_key','Test Key','',$o['cc_test_key'] ?? '']],'data-cc',4); ?>
                            <div class="col-12">Test Info - Card : 5123456789012346, Exp Month : May (05), Exp Year : 2021, CCV : 123</div>
                        </div>
                    </div>
                </div>
                <div class="tar"><button class="blue r5 mb0"><?php E('Save Payment APIs'); ?></button></div>
            </form>
            <?php }
            if( in_array( 'payments', $ui_params ) ) { ?>
            <form method="post"  id="email" class="dn">
                <div class="row">
                    <div class="col-lg-6 col-6">
                        <label for="mailer"><?php E('Preferred E Mail Marketer'); ?></label>
                        <select id="mailer" name="mailer" class="select2" data-placeholder="<?php E('Select E Mail Marketer'); ?>">
                            <?php select_options(['sendinblue'=>'Send in Blue','sendgrid'=>'SendGrid','getresponse'=>'GetResponse','sendx'=>'SendX','mailchimp'=>'Mail Chimp','mandrill'=>'Mandrill','smtp'=>'SMTP'],$o['mailer'] ?? ''); ?>
                        </select>
                    </div>
                </div>
                <div id="sendgrid" class="acc <?php echo isset($o['mailer']) && $o['mailer'] == 'sendgrid' ? 'on' : ''; ?>">
                    <div class="head" data-on="#sendgrid"><?php E('SendGrid APIs'); ?></div>
                    <div class="body">
                        <div class="row">
                            <?php in_texts([['send_grid_key','SendGrid Key','',$o['send_grid_key'] ?? '']],'data-cc',6); ?>
                        </div>
                    </div>
                </div>
                <div class="tar"><button class="blue r5 mb0"><?php E('Save Emails APIs'); ?></button></div>
            </form>
            <?php }
            if( in_array( 'sms', $ui_params ) ) { ?>
            <form method="post" id="sms" class="dn">
                <div class="tar"><button class="blue r5 mb0"><?php E('Save SMS APIs'); ?></button></div>
            </form>
            <?php }
            if( in_array( 'payments', $ui_params ) ) { ?>
            <form method="post" id="other" class="dn">
                <div class="tar"><button class="blue r5 mb0"><?php E('Save APIs'); ?></button></div>
            </form>
            <?php } ?>
        </div>
    </div>
</div>