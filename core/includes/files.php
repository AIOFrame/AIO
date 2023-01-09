<?php

if( session_status() === PHP_SESSION_NONE ) {
    session_start();
}
// Allow from any origin
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    exit(0);
}

// This is the backend function that processes file upload

function file_process_ajax() {
    $cry = Encrypt::initiate();
    $db = new DB();
    //echo $cry->decrypt( $_POST['scope'] );
    //return;
    elog( json_encode( $_POST ) );

    // Sets the scope of uploaded file (If empty then the file is private)
    $scope = 0;
    if( isset( $_POST['scope'] ) && !is_numeric( $_POST['scope'] ) ) {
        $scope = $cry->decrypt( $_POST['scope'] );
    } else if( user_logged_in() ) {
        $scope = $_SESSION['user']['id'];
    }

    // Sets the file is deletable or not
    $delete = !empty($_POST['delete']) && $_POST['delete'] == 'true' ? 1 : 0;

    // Sets the path of the uploaded file (If empty then file is uploaded to user directory/y-m)
    $path = '';
    if( !empty( $_POST['path'] ) ) {
        $path = $cry->decrypt( $_POST['path'] );
    } else if( user_logged_in() ) {
        $path = '/'.$_SESSION['user']['id'].'/'.date('Y-m');
    }
    /* if( empty( $path ) ){
        echo json_encode( T('Location Accessibility Failure'), true );
        return;
    } */
    foreach( $_FILES as $file ){
        $fn = $file['name'];
        if ( !is_dir( APPPATH.'/storage/'.$path )) {
            mkdir( APPPATH.'/storage/'.$path, 0777, true);
        }
        $fe = pathinfo($fn, PATHINFO_EXTENSION);
        $fnc = explode('.',$fn); //[0].'_'.date('d_h_s').'.'.explode('.',$fn)[1];
        $fn = str_replace(' ','_',str_replace('.'.$fnc[count($fnc) - 1],'_'.date('y_h_s').'.'.$fnc[count($fnc) - 1],$fn));
        if( move_uploaded_file( $file['tmp_name'], APPPATH.'/storage/'.$path.'/'.$fn ) ) {
            $loc = '/storage'.$path.'/'.$fn;
            $fz = round( $file['size'] / 1024 );
            $vfn = ucwords( str_replace('.'.$fe, '', str_replace('-', ' ', str_replace('_', ' ', $file['name']))));
            global $db;
            if( $db ) {
                $uq = $db->insert('storage', ['file_name', 'file_url', 'file_scope', 'file_type', 'file_size', 'file_delete'], [$vfn, $loc, $scope, $fe, $fz, $delete]);
                if( $uq ){
                    $msg = array('success',T('File Uploaded Successfully'),$fn,$loc,$cry->encrypt($uq),$fe,$fz,storage_url($loc),$delete);
                    echo json_encode($msg, true);
                } else {
                    unlink( APPPATH.'/storage/'.$path.'/'.$fn );
                    $msg = array('error',T('File not uploaded'),T('Could not save record to storage database'),'','');
                    echo json_encode($msg, true);
                }
            } else {
                $msg = array('success',T('File Uploaded Successfully'),$fn,$loc,'',$fe,$fz,storage_url($loc),$delete);
                echo json_encode($msg, true);
            }
        } else {
            $msg = array('error',T('File not uploaded'),T('Could not upload file to storage server'),'','');
            echo json_encode($msg, true);
        }
    }
    die();
}

// This outputs an array of current user uploads to file uploader

function get_user_uploads( $off = 0 ) {
    /*global $db;
    if( $db ) {
        $uid = get_current_user_id();
        $iq = select( 'storage', '', 'file_scope = "'.$uid.'"', 20, $off, '', 0, 'ID' );
        //$iq = "SELECT * FROM storage WHERE file_scope = '$uid' ORDER BY ID DESC LIMIT 20 OFFSET $off";
        //if ($mq = mysqli_query($conn, $iq)) {
        if ( is_array( $iq ) ) {
            foreach( $iq as $i => $q ) {
                //return $q;
            }
            /*while ($row = mysqli_fetch_assoc( $mq ) ) {
                $fd[$row['ID']] = array($row['file_name'], $row['file_url'], $row['file_type'], $row['file_size'], substr(strrchr($row['file_url'], '.'), 1));
            }
            if (!empty($fd)) {
                return $fd;
            }
        }
    }*/
}

// Previous function as json encoded

function ajax_user_uploads(){
    // $off = $_POST['off'] ?? 0;
    // echo json_encode(get_user_uploads($off));
}

// Gets image url from id

function asset_id_to_url( $fid ): array {
    $db = new DB();
    $uid = get_current_user_id();
    //"SELECT * FROM storage WHERE ID = '$fid' AND file_scope = '$uid'";
    return $db->select( 'storage', '', 'file_id = "'.$fid.'" && file_scope = "'.$uid.'"' );
    /* if( $mq = mysqli_query( $conn, $iq ) ) {
        while( $row = mysqli_fetch_assoc( $mq ) ) {
            $fd = array( $row['file_name'], $row['file_url'], $row['file_type'], $row['file_size'], substr(strrchr($row['file_url'],'.'),1) );
            return json_encode($fd);
        }
    } */
}

/**
 * Renders visual download
 * @param string $urls URLs to render download
 * @param string $pre Class to wrap around each download
 */
function render_downloads( string $urls = '', string $pre = '' ) {
    if( !empty( $urls ) ) {
        $files = explode( '|', $urls );
        $pre = is_numeric( $pre ) ? 'col-12 col-lg-'.$pre : $pre;
        $db = new DB();
        foreach( $files as $f ) {
            $file = $db->select( 'storage', '', 'file_url = \''.$f.'\'', 1 );
            $ext = $file['file_type'] ?? '';
            $name = $file['file_name'] ?? '';
            $size = $file['file_size'] ?? 0;
            $size = $size > 1024 ? round( $file['file_size'] / 1024, 2 ).' Mb' : $file['file_size'].' Kb';
            echo '<div class="'.$pre.'">';
            echo '<a class="aio_df" href="'.storage_url($f).'" download>';
            echo '<i class="ico '.$ext.'"><span>'.$ext.'</span></i>';
            echo '<div class="name">'.$name.'</div>';
            echo '<div class="size">'.$size.'</div>';
            echo '</a></div>';
        }
    }
}

// Previous function as echoed

function get_image_data() {
    $fid = $_POST['id'];
    echo asset_id_to_url( $fid );
}

// Delete an uploaded document

function file_delete_ajax(): void {

    if( isset( $_POST['id'] ) && $_POST['id'] !== '' ) {

        $cry = Encrypt::initiate(); $db = new DB();

        $id = isset( $_POST['id'] ) && $_POST['id'] !== '' ? $cry->decrypt( $_POST['id'] ) : '';

        if( is_numeric( $id ) ){

            $user_id = get_user_id();

            $file = $db->select( 'storage', 'file_url', 'file_id = "'.$id.'" AND file_scope = "'.$user_id.'" AND file_delete = "1"', 1 );

            if( !empty( $file['file_url'] ) ) {

                $ac_file = unlink( APPPATH . $file['file_url'] );
                $db_file = $db->delete( 'storage', 'file_id = "'.$id.'" AND file_scope = "'.$user_id.'" AND file_delete = "1"' );

                $ac_file && $db_file ? ES('File Deleted') : EF('File NOT Deleted, Perhaps the file is restricted from deletion');

            } else {
                EF('File NOT Deleted, Perhaps the file is restricted from deletion');
            }

        } else {

            EF('Decrypting file failed, Please refresh and try again');

        }

    } else {

        EF('Delete request could not be processed. Please try again and if issue persists approach Support');

    }

}

function download_url( $urls, $class = 'file', $element = 'a' ) {
    $urls = explode( ',', $urls );
    if( is_array( $urls ) ) {
        foreach( $urls as $u ) {
            if( $element == 'a' ) {
                $data = 'href="'.storage_url( $u ).'"';
            } else {
                $data = storage_url( $u );
            }
            echo '<'.$element.' '.$data.' class="ico '.$class.'"></'.$element.'>';
        }
    }
}

/*function upload( $url_element, $text = 'Upload', $extensions = '', $deletable = 0, $path = '', $size_limit = '', $attr = '' ) {
    $cry = Encrypt::initiate();
    $extras = '';
    $extras .= !empty( $extensions ) ? 'data-exts="'.$extensions.'" ' : '';
    $extras .= $deletable ? 'data-delete ' : '';
    $extras .= !empty( $path ) ? 'data-path="'.$cry->encrypt($path).'" ' : '';
    $extras .= !empty( $size_limit ) ? 'data-size="'.$cry->encrypt($path).'" ' : '';
    echo '<button data-upload data-url="'.$url_element.'" '.$extras.' '.$attr.'>'.T($text);
    echo '</button>';
}*/