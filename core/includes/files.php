<?php

// This is the backend function that processes file upload

function file_process() {

    $cry = Crypto::initiate();
    //echo $cry->decrypt( $_POST['scope'] );
    //return;

    // Sets the scope of uploaded file (If empty then the file is private)
    $scope = !empty($_POST['scope']) && $cry->decrypt( $_POST['scope'] ) == 'false' ? 0 : get_current_user_id();

    // Sets the file is deletable or not
    $delete = !empty($_POST['delete']) && $_POST['delete'] == 'true' ? 1 : 0;

    // Sets the path of the uploaded file (If empty then file is uploaded to user directory/y-m)
    $path = !empty($_POST['path']) ? $cry->decrypt($_POST['path']) : get_current_user_id().'/'.date('Y-m');
    if( empty($path) ){ echo json_encode('Location Accessibility Failure', false); return; }

    foreach( $_FILES as $file ){
        $fn = $file['name'];
        if ( !is_dir( APPPATH.'/storage/'.$path )) {
            mkdir( APPPATH.'/storage/'.$path, 0777, true);
        }
        $fe = pathinfo($fn, PATHINFO_EXTENSION);
        $fnc = explode('.',$fn); //[0].'_'.date('d_h_s').'.'.explode('.',$fn)[1];
        $fn = str_replace('.'.$fnc[count($fnc) - 1],'_'.date('y_h_s').'.'.$fnc[count($fnc) - 1],$fn);
        if( move_uploaded_file( $file['tmp_name'], APPPATH.'/storage/'.$path.'/'.$fn ) ) {
            $loc = '/storage/'.$path.'/'.$fn;
            $fz = round( $file['size'] / 1024 );
            $vfn = ucwords( str_replace('.'.$fe, '', str_replace('-', ' ', str_replace('_', ' ', $file['name']))));
            $uq = insert( 'storage', array( 'file_name', 'file_url', 'file_scope', 'file_type', 'file_size', 'file_delete' ), array( $vfn, $loc, $scope, $fe, $fz, $delete ) );
            if( $uq ){
                $msg = array('success','File Uploaded Successfully',$fn,$loc,$cry->encrypt($uq),$fe,$fz,storage_url($loc),$delete);
                echo json_encode($msg, true);
            } else {
                unlink( APPPATH.'/storage/'.$path.'/'.$fn );
                $msg = array('error','File not uploaded','Could not save record to storage database','','');
                echo json_encode($msg, true);
            }
        } else {
            $msg = array('error','File not uploaded','Could not upload file to storage server','','');
            echo json_encode($msg, true);
        }
    }
    die();
}

// This outputs an array of current user uploads to file uploader

function get_user_uploads( $off = 0 ) {
    global $conn;
    $uid = get_current_user_id();
    $iq = "SELECT * FROM storage WHERE file_scope = '$uid' ORDER BY ID DESC LIMIT 20 OFFSET $off";
    if( $mq = mysqli_query( $conn, $iq ) ) {
        while( $row = mysqli_fetch_assoc( $mq ) ) {
            $fd[$row['ID']] = array( $row['file_name'], $row['file_url'], $row['file_type'], $row['file_size'], substr(strrchr($row['file_url'],'.'),1) );
        }
        if( !empty( $fd ) ){
            return $fd;
        }
    }
}

// Previous function as json encoded

function ajax_user_uploads(){
    $off = $_POST['off'] ?? 0;
    echo json_encode(get_user_uploads($off));
}

// Gets image url from id

function image_data( $fid ) {
    global $conn;
    $uid = get_current_user_id();
    $iq = "SELECT * FROM storage WHERE ID = '$fid' AND file_scope = '$uid'";
    if( $mq = mysqli_query( $conn, $iq ) ) {
        while( $row = mysqli_fetch_assoc( $mq ) ) {
            $fd = array( $row['file_name'], $row['file_url'], $row['file_type'], $row['file_size'], substr(strrchr($row['file_url'],'.'),1) );
            return json_encode($fd);
        }
    }
}

// Previous function as echoed

function get_image_data() {
    $fid = $_POST['id'];
    echo image_data( $fid );
}

// Delete an uploaded document

function file_delete() {

    if( isset( $_POST['id'] ) && $_POST['id'] !== '' ) {

        $cry = Crypto::initiate();

        $id = isset( $_POST['id'] ) && $_POST['id'] !== '' ? $cry->decrypt( $_POST['id'] ) : '';

        if( is_numeric( $id ) ){

            $user_id = get_current_user_id();

            $file = select( 'storage', 'file_url', 'file_id = "'.$id.'" AND file_scope = "'.$user_id.'" AND file_delete = "1"', 1 );

            if( !empty( $file['file_url'] ) ) {

                elog(APPPATH . $file['file_url']);
                $ac_file = unlink( APPPATH . $file['file_url'] );
                $db_file = delete( 'storage', 'file_id = "'.$id.'" AND file_scope = "'.$user_id.'" AND file_delete = "1"' );

                if( $ac_file && $db_file ) {

                    echo json_encode( [ 1, T('File Deleted') ] );

                } else {

                    echo json_encode( [ 0, T('File NOT Deleted, Perhaps the file is restricted from deletion') ] );

                }

            } else {

                echo json_encode( [ 0, T('File NOT Deleted, Perhaps the file is restricted from deletion') ] );

            }

        } else {

            echo json_encode( [ 0, T('Decrypting file failed, Please refresh and try again') ] );

        }

    } else {

        echo json_encode( [ 0, T('Delete request could not be processed. Please try again and if issue persists approach Support') ] );

    }

}