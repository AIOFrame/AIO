<?php

class FUP {

    function file_uploader() {
        $cry = Crypto::initiate();
        get_style('upload');
        get_scripts(['jquery','upload']);
        // TODO: Translations as data attr
        ?>
        <div id="aio_up" class="file_modal" data-dir="<?php echo APPURL.'apps/'.APPDIR; ?>" data-action="<?php echo $cry->encrypt('file_process_ajax'); ?>">
            <div class="files_head">
                <h3><?php E('File Uploader'); ?></h3>
                <div class="acts">
                    <div class="expand"></div>
                    <div class="close"></div>
                </div>
                <input type="file" id="file_input">
            </div>
            <div class="files_body">
                <input type="search" placeholder="<?php E('Search'); ?>" class="search">
                <div class="uploaded_files">
                    <?php
                    $db = new DB();
                    $fs = $db ? ( !empty( $_SESSION ) && isset( $_SESSION['user_id'] ) ? $db->select( 'storage', '*', 'file_scope = "'.$_SESSION['user_id'].'" OR file_scope = "0"', '40', 0, '', '', 'file_id', 'DESC' ) : $db->select( 'storage', '*', 'file_scope = "0"', '40', 0, '', '', 'file_id', 'desc' ) ) : '';
                    if( !empty($fs) ){ foreach( $fs as $f ){
                        $bg = in_array($f['file_type'],['svg','jpg','png','jpeg']) ? 'style="background-image:url(\''.storage_url($f['file_url']).'\')"' : '';
                        $size = $f['file_size'] > 1024 ? number_format((float)($f['file_size'] / 1024), 2, '.', '') . ' MB' : $f['file_size'].' KB';
                        echo '<div '.$bg.' class="f '.$f['file_type'].'" data-id="'.$cry->encrypt($f['file_id']).'" data-url="'.$f['file_url'].'" data-delete="'.$f['file_delete'].'"><div class="name">'.$f['file_name'].'</div><div class="size">'.$size.'</div></div>';
                    } } else {
                        echo '<div class="no_uploaded_files"><span>'. T('NO FILES FOUND!').'</span></div>';
                    } ?>
                </div>
                <div class="camera_view"></div>
                <div class="drop_files"><span><?php E('Drop files to Upload!'); ?></span></div>
            </div>
            <div class="files_actions">
                <div class="fi i select"><?php E('Select'); ?></div>
                <label for="file_input" class="fb i browse"><?php E('Browse'); ?></label>
                <div class="disabled fd i trash"><?php E('Delete'); ?></div>
            </div>
            <div class="translations">
                <div class="extension_limit"><?php E('The file should be one of the extensions'); ?></div>
                <div class="size_limit"><?php E('Selected file exceeds file size limit!'); ?></div>
                <div class="file_select"><?php E('File Selected Successfully!'); ?></div>
                <div class="no_file_select"><?php E('NO FILE SELECTED! File Uploader Closed!!'); ?></div>
                <div class="remove_confirm"><?php E('Are you sure to remove attached file ?'); ?></div>
                <div class="upload_success"><?php E('File Uploaded Successfully!'); ?></div>
            </div>
        </div>
        <div class="file_notify"></div>
        <?php
    }

}

/* HOW TO USE

HTML Attribute Structure
onclick - Triggers upload
data-path - Location where to upload file (Better encrypt to protect from inspect view and manipulation)
data-url - input where value should be updated with url of uploaded file
data-exts - allowed file upload formats
data-delete - Let the user delete files

<?php $cry = Crypto::initiate(); ?>
<button onclick="file_upload(this)" data-path="<?php $cry->enc('variations'); ?>" data-url="#image_url" data-id="#image_id" data-size="10240" data-exts="jpg,svg" data-nodelete>Upload</button>

*/