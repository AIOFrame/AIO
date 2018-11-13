<?php

class FUP {

    function file_uploader() {
        $cry = Crypto::initiate();
        ?>
        <link rel="stylesheet" href="<?php echo APPURL; ?>assets/styles/upload.css">
        <script src="<?php echo APPURL; ?>assets/scripts/upload.js"></script>
        <div id="file_uploader" class="file_modal" data-dir="<?php echo APPURL.'apps/'.APPDIR; ?>">
            <div class="files_head">
                <div class="close"></div>
                <input type="file" id="file_input">
                <label for="file_input" class="fup_icon browse_file"><span><?php __('Browse File'); ?></span></label>
            </div>
            <div class="files_body">
                <div class="uploaded_files">
                    <?php
                    $fs = select( 'storage', '*', 'file_scope = "'.$_SESSION['user_id'].'" OR file_scope = "0"', '40', 0, '', '', 'file_id', 'desc' );
                    if( !empty($fs) ){ foreach( $fs as $f ){
                        $bg = in_array($f['file_type'],['svg','jpg','png','jpeg']) ? 'style="background-image:url(\''.storage_url($f['file_url']).'\')"' : '';
                        echo '<div '.$bg.' class="fup_file" data-id="'.$cry->encrypt($f['file_id']).'" data-url="'.$f['file_url'].'">'.$f['file_name'].'</div>';
                    } } else {
                        echo '<div class="no_uploaded_files">'. _t('NO FILES FOUND!').'</div>';
                    } ?>
                </div>
                <div class="camera_view"></div>
            </div>
            <div class="files_actions">
                <div class="files_insert"></div>
            </div>
        </div>
        <div class="file_notify"></div>
        <?php
    }

}

/* TYPES OF UPLOAD

1. Unique per user file that gets stored in app (not root) > assets > images > user_id > file
2. Bulk saving directory that stores as app (not root) > assets > images > profile_pictures

HOW TO USE

HTML Attribute Structure
onclick - Triggers upload
data-path - Location where to upload file (Better encrypt to protect from inspect view and manipulation)
data-url - input where value should be updated with url of uploaded file
data-exts - allowed file upload formats
data-delete - Let the user delete files

<?php $cry = Crypto::initiate(); ?>
<button onclick="file_upload(this)" data-path="<?php $cry->enc('variations'); ?>" data-url="#image_url" data-id="#image_id" data-size="10240" data-exts="jpg,svg" data-nodelete>Upload</button>

*/