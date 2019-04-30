<div id="aio_apis">
    <div class="tabs material">
        <div class="tab_heads">
            <div class="tab on" data-t="#aio_pays"><?php E('Payments'); ?></div>
            <div class="tab" data-t="#aio_mail"><?php E('E Mail'); ?></div>
            <div class="tab" data-t="#aio_sms"><?php E('SMS'); ?></div>
            <div class="tab" data-t="#aio_other"><?php E('Other'); ?></div>
        </div>
        <div class="tab_data p20">
            <div id="aio_pays">
                <div class="row mb20">
                    <div class="col-lg-6 col-6">
                        <label for="pay_way"><?php E('Preferred Payment Gateway'); ?></label>
                        <select id="pay_way" class="select2" data-placeholder="<?php E('Select Payment Gateway'); ?>">
                            <?php select_options(['cc_avenue'=>'CC Avenue']); ?>
                        </select>
                    </div>
                    <div class="col-lg-6 col-6">
                        <label for="pay_test"><?php E('Enable Payment Test Mode'); ?></label>
                        <div><input type="checkbox" id="pay_test" class="slide"></div>
                    </div>
                </div>
                <div id="cc_options" class="acc">
                    <div class="head" data-on="#cc_options"><?php E('CC Avenue APIs'); ?></div>
                    <div class="body">
                        <div class="row">
                            <?php in_texts([['cc_id','ID'],['cc_access','Access'],['cc_key','Key'],],'data-cc',4); ?>
                            <?php in_texts([['cc_test_id','Test ID'],['cc_test_access','Test Access'],['cc_test_key','Test Key']],'data-cc class="mb0"',4); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div id="aio_mail" class="dn">

            </div>
            <div id="aio_sms" class="dn">

            </div>
            <div id="aio_other" class="dn">

            </div>
        </div>
    </div>
    <div class="tar"><button class="blue r5 mb0"><?php E('Save APIs'); ?></button></div>
</div>