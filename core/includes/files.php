<?php

// This is the backend function that processes file upload

function file_process() {

    $cry = Crypto::initiate();
    //echo $cry->decrypt( $_POST['scope'] );
    //return;

    // Sets the scope of uploaded file (If empty then the file is private)
    $scope = !empty($_POST['scope']) && $cry->decrypt( $_POST['scope'] ) == 'false' ? 0 : get_current_user_id();

    // Sets the path of the uploaded file (If empty then file is uploaded to user directory/y-m)
    $path = !empty($_POST['path']) ? $cry->decrypt($_POST['path']) : get_current_user_id().'/'.date('Y-m');
    if( empty($path) ){ echo json_encode('Location Accessibility Failure', false); return; }

    foreach( $_FILES as $file ){
        $fn = $file['name'];
        if ( !is_dir( APPPATH.'/storage/'.$path )) {
            mkdir( APPPATH.'/storage/'.$path, 0777, true);
        }
        $fe = pathinfo($fn, PATHINFO_EXTENSION);
        $fn = explode('.',$fn)[0].'_'.date('d_h_s').'.'.explode('.',$fn)[1];
        if( move_uploaded_file( $file['tmp_name'], APPPATH.'/storage/'.$path.'/'.$fn ) ) {
            $loc = '/storage/'.$path.'/'.$fn;
            $fz = round( $file['size'] / 1024 );
            $vfn = ucwords( str_replace('.'.$fe, '', str_replace('-', ' ', str_replace('_', ' ', $file['name']))));
            $uq = insert( 'storage', array( 'file_name', 'file_url', 'file_scope', 'file_type', 'file_size' ), array( $vfn, $loc, $scope, $fe, $fz ) );
            if( $uq ){
                $msg = array('success','File Uploaded Successfully',$fn,storage_url($loc),$cry->encrypt($uq),$fe,$fz);
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

function truncate_upload() {
    $fid = $_POST['id'];
    $uid = get_current_user_id();
    $id = json_decode( image_data( $fid ), true );
    //$ri = "DELETE FROM storage WHERE ";
    $ri = delete( 'storage', 'ID = "'.$fid.'" AND file_scope = "'.$uid.'"' );
    if( $ri && unlink( COREPATH.$id[1] ) ) {
        echo json_encode( array( 'success', 'File Deleted', $id[0].' was deleted and erased from records successfully' ), true );
    } else {
        echo json_encode( array( 'error', 'File NOT Deleted', 'File could not be deleted, it may not exist' ), true );
    }
}