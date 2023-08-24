<div class="data">
    <?php
    foreach( $_POST as $k => $v ) {
        echo !is_array( $v ) ? '<input type="hidden" name="'.$k.'" value="'.$v.'">' : '<input type="hidden" name="'.$k.'[]" value=\''.serialize($v).'\'">';
    }
    ?>
    <div class="q">
        <div>Setup function files for logged in users</div>
        <div><input type="text" name="f_int" placeholder="Ex: contacts, invoices, payments, support etc."></div>
    </div>
    <div class="q">
        <div>Setup function files for non logged in users</div>
        <div><input type="text" name="f_ext" placeholder="Ex: login, register, support etc."></div>
    </div>
    <div class="q">
        <label for="feats">Select your Web App Features </label>
        <div class="row">
            <?php $feats = [
                'cms'=>'AIO Content Management System',
                'commerce'=>'AIO Commerce',
            ];
            $form->checkboxes( 'feats', '', $feats, '', 'data-one', 0, 6 );
            ?>
        </div>
    </div>
    <div>
        <div>Setup dynamic pages</div><br/>
        <div><input type="text" name="pages" data-dynamic='<?php echo json_encode([['text','page','Page'],['div','url'],['checkbox','script','Custom Script'],['checkbox','style','Custom Stylesheet']]); ?>'></div>
    </div>
</div>