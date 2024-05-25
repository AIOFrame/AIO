<?php

class FUP {

    function file_uploader(): void {
        global $options;
        $cry = Encrypt::initiate();
        get_style('upload');
        get_scripts(['jquery','upload']);
        // TODO: Translations as data attr
        pre( 'aio_up', 'file_modal', 'div', 'data-dir="'.APPURL.'apps/'.APPDIR.'" data-action="'.( APPDEBUG ? 'file_process_ajax' : $cry->encrypt('file_process_ajax') ).'" data-delete-action="'.$cry->encrypt('file_delete_ajax').'"' );
            pre( '', 'files_head' );
                h3( 'File Uploader' );
                pre( '', 'info' );
                    div( 'sizes', __div( ( $options['icon_class'] ?? 'mico' ) . ' ico ' . ( $options['ico_file_size'] ?? '' ), ( $options['ico_file_size'] ??  'sd_card' ) ) . __div( 'title', __div( '', T('Max File Size') ) . __div( '', __el( 'span', 'size' ) . __el( 'span', 'measure', 'Mb' ) ) ) );
                    div( 'types', __div( ( $options['icon_class'] ?? 'mico' ) . ' ico ' . ( $options['ico_file_type'] ?? '' ), ( $options['ico_file_type'] ?? 'description' ) ) . __div( 'title', T('File Types') . __div( 'types' ) ) );
                    div( 'max', __div( ( $options['icon_class'] ?? 'mico' ) . ' ico ' . ( $options['ico_file_limit'] ?? '' ), $options['ico_file_limit'] ?? 'file_copy' ) . __div( 'title', T('Files Limit') . __div( 'qty', 2 ) ) );
                post();
                pre( '', 'acts' );
                    div( ( $options['icon_class'] ?? 'mico' ) . ' expand ' . ( $options['ico_file_expand'] ?? '' ), $options['ico_file_expand'] ?? 'open_in_full' );
                    div( ( $options['icon_class'] ?? 'mico' ) . ' contract ' . ( $options['ico_file_contract'] ?? '' ), $options['ico_file_contract'] ?? 'close_fullscreen' );
                    div( ( $options['icon_class'] ?? 'mico' ) . ' close ' . ( $options['ico_close'] ?? '' ), $options['ico_close'] ?? 'close' );
                    /* if( is_array( ICONS ) )
                        in_array( 'Bootstrap', ICONS ) ? el( 'i', 'bi bi-arrows-angle-expand expand' ) . el( 'i', 'bi bi-x-lg close' ) : div( 'mat-ico expand' ) . div( 'mat-ico close', 'close' );
                    else
                        str_contains( ICONS, 'Bootstrap' ) ? el( 'i', 'bi bi-arrows-angle-expand expand' ) . el( 'i', 'bi bi-x-lg close' ) : div( 'mat-ico expand' ) . div( 'mat-ico close', 'close' ); */
                post();
                echo '<input type="file" id="file_input">';
            post();
            pre( '', 'files_body' );
                pre( '', 'search_wrap' );
                    echo '<input type="search" placeholder="'.T('Search in files...').'" class="search">';
                post();
                pre( '', 'uploaded_files' );
                    $db = new DB();
                    $fs = $db ? ( !empty( $_SESSION ) && isset( $_SESSION['user']['id'] ) ? $db->select( 'storage', '*', 'file_scope = "'.$_SESSION['user']['id'].'" OR file_scope = "0"', '40', 0, '', '', 'DESC', 'file_id' ) : $db->select( 'storage', '*', 'file_scope = "0"', '40', 0, '', '', 'DESC', 'file_id' ) ) : '';
                    if( !empty($fs) ){ foreach( $fs as $f ){
                        $bg = in_array($f['file_type'],['svg','jpg','png','jpeg']) ? 'style="background-image:url(\''.storage_url($f['file_url']).'\')"' : '';
                        $size = $f['file_size'] > 1024 ? number_format((float)($f['file_size'] / 1024), 2, '.', '') . ' MB' : $f['file_size'].' KB';
                        echo '<div '.$bg.' class="f '.$f['file_type'].'" data-id="'.$cry->encrypt($f['file_id']).'" data-url="'.$f['file_url'].'" data-delete="'.$f['file_delete'].'"><div class="name">'.$f['file_name'].'</div><div class="size">'.$size.'</div></div>';
                    } } else {
                        div( 'no_uploaded_files', __el( 'span', '', T('NO FILES FOUND!') ) );
                    }
                post();
                div( 'camera_view' );
                div( 'drop_files', __el( 'span', '', T('Drop files to Upload!') ) );
            post();
            div( 'files_actions', __el( 'label', 'fi i select', T('Choose') ) . __el( 'label', 'fb i browse', 'Browse', '', 'for="file_input"', 1 ) . __el( 'label', 'disabled fd i trash', T('Delete') ) );
            div( 'translations',
                __div( 'extension_limit', T('The file should be one of the extensions') ) .
                __div( 'size_limit', T('Selected file size exceeds file size limit of ') ) .
                __div( 'file_select', T('File Selected Successfully!') ) .
                __div( 'no_file_select', T('NO FILE SELECTED! File Uploader Closed!!') ) .
                __div( 'remove_confirm', T('Are you sure to remove attached file ?') ) .
                __div( 'upload_success', T('File Uploaded Successfully!') )
            );
        post();
        div( 'file_notify' );
    }
}

/* HOW TO USE

HTML Attribute Structure
onclick - Triggers upload
data-path - Location where to upload file (Better encrypt to protect from inspect view and manipulation)
data-url - input where value should be updated with url of uploaded file
data-exts - allowed file upload formats
data-delete - Let the user delete files

<?php $cry = Encrypt::initiate(); ?>
<button onclick="file_upload(this)" data-path="<?php $cry->enc('variations'); ?>" data-url="#image_url" data-id="#image_id" data-size="10240" data-exts="jpg,svg" data-nodelete>Upload</button>

*/