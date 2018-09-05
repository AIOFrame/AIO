<?php

class FUP {

    function file_uploader() {
        ?>
        <link rel="stylesheet" href="<?php echo APPURL; ?>assets/styles/upload.css">
        <script src="<?php echo APPURL; ?>assets/scripts/upload.js"></script>
        <div id="file_uploader" class="file_modal" data-dir="<?php echo APPURL.'apps/'.APPDIR; ?>">
            <div class="files_head"><input type="file" id="file_input" accept="image/*"><label for="file_input" class="fup_icon browse_file"><span>Browse file...</span></label>
            </div>
            <div class="files_body">
                <div class="uploaded_files">
                    <?php
                    $fs = select( 'storage', '*', 'file_scope = "'.$_SESSION['user_id'].'" OR file_scope = "0"' );
                    if( !empty($fs) ){ foreach( $fs as $f ){
                        $bg = in_array($f['file_type'],['svg','jpg','png','jpeg']) ? 'style="background:url(\''.storage_url($f['file_url']).'\') no-repeat 10px 50% / 25px"' : '';
                        echo '<div '.$bg.' class="fup_file" data-id="'.$f['file_id'].'" data-url="'.$f['file_url'].'">'.$f['file_name'].'</div>';
                    } } ?>
                </div>
                <div class="camera_view"></div>
            </div>
            <div class="files_actions">
                <div class="files_insert"></div>
                <div class="files_delete"></div>
            </div>
        </div>
        <?php
    }

}

/* TYPES OF UPLOAD

1. Unique per user file that gets stored in app (not root) > assets > images > user_id > file
2. Bulk saving directory that stores as app (not root) > assets > images > profile_pictures

HOW TO USE

HTML Structure (Encrypting Path so advanced users wont manipulate location or permissions, other fields wont matter as url and file id are public)
<?php $cry = Crypto::initiate(); ?>
<button onclick="file_upload(this)" data-path="<?php $cry->enc('variations'); ?>" data-url="#image_url" data-id="#image_id" data-size="10240" data-exts="jpg,svg">Upload</button>

*/