<?php

if( !defined( 'COREPATH' ) ) { exit(); }

function template_exists() {
    if( isset( $_GET['a'] ) ) {
        if( file_exists( APPPATH . '/templates/'.$_GET['a'].'.php' ) ) {
            return $_GET['a'];
        } else if( file_exists( APPPATH . '/access/'.$_GET['a'].'.php' ) ) {
            return $_GET['a'];
        } else {
            return false;
        }
    } else if( isset( $_GET['admin'] ) ) {
        if( file_exists( APPPATH . '/admin/'.$_GET['admin'].'.php' ) ) {
            return $_GET['admin'];
        } else {
            return false;
        }
    } else {
        return 'home';
    }
}

function get_title() {
    if( !empty( PAGE ) ) {
        $p = PAGE == 'ROOT' ? 'Welcome' : PAGE;
        echo ucwords( str_replace('-',' ', str_replace('_',' ', $p )) ) . ' - ' . APPNAME;
    } else {
        echo APPNAME;
    }
}

function get_page_title() {
    if( template_exists() ) {
        echo ucwords( str_replace('-',' ',str_replace('_',' ',PAGE )) );
    } else {
        echo APPNAME;
    }
}

function get_assets_url(){
    return APPURL.'apps/'. APPDIR.'/assets/';
}

function get_menu_order() {
    return unserialize(get_option('menu_order'));
    /*if( $mq = mysqli_query( $conn, $mo ) ) {
        while( $row = mysqli_fetch_assoc( $mq ) ) {
            return unserialize( $row['option_value'] );
        }
    }*/
}

function get_pages() { ?>
    <nav>
    <?php echo is_mobile() ? '<div data-hide="aside" class="hider"></div>' : '';
    $ul = get_user_level();
    $menus = unserialize( get_option('menu_order') );
    if( !empty( $menus ) ) {
        foreach( $menus as $group ) { ?>
        <div class="rel">
<!--            <h3>--><?php //echo $group['group']; ?><!--</h3>-->
            <ul>
                <?php foreach( $group['menus'] as $menus ) {
                    if(!empty($menus['access'])){
                        if( in_array( $ul, $menus['access'] ) ){
                            ?>
                            <li><a href="<?php echo APPURL . $menus['url']; ?>" title="<?php echo $menus['name']; ?>"><i class="ico <?php echo $menus['icon']; ?>"></i><?php echo $menus['name']; ?></a></li>
                            <?php
                        }
                    }
                } ?>
            </ul>
        </div>
    <?php } } else { ?>
    <div class="general_menu">
    <h3 class="ui">General</h3>
    <?php foreach( glob( APPPATH . '/templates/*.php' ) as $file ) {
        echo '<li><a href="'.APPURL.'/'.str_replace( '.php', '', str_replace( APPPATH . '/templates/', '', $file ) ).'">'.ucfirst( str_replace( '.php', '', str_replace( APPPATH . '/templates/', '', $file ) ) ).'</a></li>';
    } ?>
    </div>
    <div class="admin_menu">
    <h3 class="ui">Admin Options</h3>
    <?php foreach( glob( APPPATH . '/admin/*.php' ) as $file ) {
        echo '<li><a href="'.APPURL.'/admin/'.str_replace( '.php', '', str_replace( APPPATH . '/admin/', '', $file ) ).'">'.ucfirst( str_replace( '.php', '', str_replace( APPPATH . '/admin/', '', $file ) ) ).'</a></li>';
    } ?>
    </div>
    <?php } ?>
    </nav>
<?php }

function all_pages() {
    foreach( glob( APPPATH . '/templates/*.php' ) as $file ) {
        //echo str_replace( '.php', '', str_replace( COREPATH.'apps/'.UINAME.'/templates/', '', $file ) );
        $links[] = array("name" => str_replace( '.php', '', str_replace( APPPATH . '/templates/', '', $file ) ), "link" => APPURL.'/'.str_replace( '.php', '', str_replace( APPPATH . '/templates/', '', $file ) ), "type" => "user_menu" );
        //$links[] = ;
    }
    foreach( glob( APPPATH . '/admin/*.php' ) as $file ) {
        $links[] = array("name" => str_replace( '.php', '', str_replace( APPPATH . '/admin/', '', $file ) ), "link" => APPURL.'/admin/'.str_replace( '.php', '', str_replace( APPPATH . '/admin/', '', $file ) ), "type" => "admin_menu" );
    }
    return $links;
    //
        //echo '<div class="drag_menu" data-link="'.APPURL.'/admin/'.str_replace( '.php', '', str_replace( COREPATH.'apps/'.UINAME.'/admin/', '', $file ) ).'">'.ucfirst( str_replace( '.php', '', str_replace( COREPATH.'apps/'.UINAME.'/admin/', '', $file ) ) ).'</div>';
    //}
}

function get_template_pages() {
    foreach( glob( APPPATH . '/templates/*.php' ) as $file ) {
        $links[] = array("name" => str_replace( '.php', '', str_replace( APPPATH . '/pages/', '', $file ) ), "slug" => str_replace( APPPATH . '/pages/', '', $file ), "link" => APPURL.'/'.str_replace( '.php', '', str_replace( APPPATH . '/pages/', '', $file ) ), "type" => "user_menu" );
    }
    return $links;
}

function get_admin_pages() {
    foreach( glob( APPPATH . '/admin/*.php' ) as $file ) {
        $links[] = array("name" => str_replace( '.php', '', str_replace( APPPATH . '/admin/', '', $file ) ), "slug" => str_replace( APPPATH . '/admin/', '', $file ), "link" => APPURL.'/admin/'.str_replace( '.php', '', str_replace( APPPATH . '/admin/', '', $file ) ), "type" => "admin_menu" );
    }
    return $links;
}

function get_access_pages() {
    foreach( glob( APPPATH . '/access/*.php' ) as $file ) {
        $links[] = array("name" => str_replace( '.php', '', str_replace( APPPATH . '/access/', '', $file ) ), "slug" => str_replace( APPPATH . '/access/', '', $file ), "link" => APPURL.'/access/'.str_replace( '.php', '', str_replace( APPPATH . '/access/', '', $file ) ), "type" => "access_pages" );
    }
    return $links;
}

function get_default_styles() {
    foreach( glob( COREPATH.'assets/styles/*.min.css' ) as $f ) {
        echo '<link rel="stylesheet" href="'.APPURL.'/'.str_replace( COREPATH, '', $f ).'">';
    }
    if( is_desktop() ){
        foreach( glob( COREPATH.'assets/styles/desktop/*.min.css' ) as $f ) {
            echo '<link rel="stylesheet" href="'.APPURL.'/'.str_replace( COREPATH, '', $f ).'">';
        }
    }
    if( is_tablet() ){
        foreach( glob( COREPATH.'assets/tablet/styles/*.min.css' ) as $f ) {
            echo '<link rel="stylesheet" href="'.APPURL.'/'.str_replace( COREPATH, '', $f ).'">';
        }
    }
    if( is_mobile() ){
        foreach( glob( COREPATH.'assets/mobile/styles/*.min.css' ) as $f ) {
            echo '<link rel="stylesheet" href="'.APPURL.'/'.str_replace( COREPATH, '', $f ).'">';
        }
    }
}

function default_styles() {
    foreach( glob( COREPATH.'assets/styles/*.min.css' ) as $f ) {
        $ds['default'][] = str_replace( COREPATH, '', $f );
    }
    if( is_desktop() ){
        foreach( glob( COREPATH.'assets/styles/desktop/*.min.css' ) as $f ) {
            $ds['desktop'][] = str_replace( COREPATH, '', $f );
        }
    }
    if( is_tablet() ){
        foreach( glob( COREPATH.'assets/tablet/styles/*.min.css' ) as $f ) {
            $ds['tablet'][] = str_replace( COREPATH, '', $f );
        }
    }
    if( is_mobile() ){
        foreach( glob( COREPATH.'assets/mobile/styles/*.min.css' ) as $f ) {
            $ds['mobile'][] = str_replace( COREPATH, '', $f );
        }
    }
    return $ds;
}

function get_theme_styles() {
    echo '<link rel="stylesheet" href="'.APPURL.'apps/'.APPDIR.'/styles/reset/reset.min.css">';

    foreach( glob( COREPATH.'apps/'.APPDIR.'/styles/*.min.css' ) as $f ) {
        echo '<link rel="stylesheet" href="'.APPURL.'/'.str_replace( COREPATH, '', $f ).'">';
    }
    if( is_desktop() ){
        foreach( glob( COREPATH.'apps/'.APPDIR.'/styles/desktop/*.min.css' ) as $f ) {
            echo '<link rel="stylesheet" href="'.APPURL.'/'.str_replace( COREPATH, '', $f ).'">';
        }
    }
    if( is_tablet() ){
        foreach( glob( COREPATH.'apps/'.APPDIR.'/tablet/styles/*.min.css' ) as $f ) {
            echo '<link rel="stylesheet" href="'.APPURL.'/'.str_replace( COREPATH, '', $f ).'">';
        }
    }
    if( is_mobile() ){
        foreach( glob( COREPATH.'apps/'.APPDIR.'/mobile/styles/*.min.css' ) as $f ) {
            echo '<link rel="stylesheet" href="'.APPURL.'/'.str_replace( COREPATH, '', $f ).'">';
        }
    }
}

function get_style( $f ) {
    $fl = COREPATH . 'assets/styles/'. $f;
    if( file_exists( $fl . '.min.css' ) ){
        echo '<link rel="stylesheet" href="'. APPURL .'assets/styles/'. $f . '.min.css">';
    } else if( file_exists( $fl . '.css' ) ){
        echo '<link rel="stylesheet" href="'. APPURL .'assets/styles/'. $f . '.css">';
    }
}

function get_app_style( $f ) {
    $fl = APPPATH . '/assets/styles/' . $f;
    $cfl = COREPATH . '/assets/styles/' . $f;
    if( file_exists( $fl . '.min.css' ) ){
        echo '<link rel="stylesheet" href="'. APPURL .'apps/'. APPDIR .'/assets/styles/'. $f . '.min.css">';
    } else if( file_exists( $fl . '.css' ) ){
        echo '<link rel="stylesheet" href="'. APPURL .'apps/'. APPDIR .'/assets/styles/'. $f . '.css">';
    } else if( file_exists( $cfl . '.min.css' ) ) {
        echo '<link rel="stylesheet" href="'. APPURL .'/assets/styles/'. $f . '.min.css">';
    } else if( file_exists( $cfl . '.css' ) ){
        echo '<link rel="stylesheet" href="'. APPURL .'/assets/styles/'. $f . '.css">';
    }
}

function get_app_script( $f ) {
    $fl = APPPATH . '/assets/scripts/' . $f;
    $cfl = COREPATH . '/assets/scripts/' . $f;
    if( file_exists( $fl . '.min.js' ) ){
        echo '<script src="'. APPURL .'apps/'. APPDIR .'/assets/scripts/'. $f . '.min.js"></script>';
    } else if( file_exists( $fl . '.js' ) ){
        echo '<script src="'. APPURL .'apps/'. APPDIR .'/assets/scripts/'. $f . '.js"></script>';
    } else if( file_exists( $cfl . '.min.js' ) ){
        echo '<script src="'. APPURL .'assets/scripts/'. $f . '.min.js"></script>';
    } else if( file_exists( $cfl . '.js' ) ){
        echo '<script src="'. APPURL .'assets/scripts/'. $f . '.js"></script>';
    }
}

function bg_asset( $f ) {
    $supported_types = ['gif', 'jpg', 'jpeg', 'png', 'bmp', 'svg'];
    foreach( glob( APPPATH . '/assets/images/*' ) as $af ){
        $ft = pathinfo( $af, PATHINFO_EXTENSION );
        foreach( $supported_types as $type ){
            if( $ft == $type && strpos( $af, $f ) !== false ){
                echo 'background-image:url('.str_replace( COREPATH, APPURL, $af).');';
            }
        }
    }
}

function reset_styles() {
    if( file_exists( APPPATH . '/assets/styles/reset.min.css' ) ){
        echo '<link rel="stylesheet" href="' . APPURL . 'apps/' . APPDIR . '/assets/styles/reset.min.css'.'">';
    } else if( file_exists( COREPATH.'assets/styles/reset.min.css' ) ) {
        echo '<link rel="stylesheet" href="' . APPURL . 'assets/styles/reset.min.css'.'">';
    } else if( file_exists( COREPATH.'assets/styles/reset.css' ) ) {
        echo '<link rel="stylesheet" href="' . APPURL . 'assets/styles/reset.css'.'">';
    }
}

function get_styles( $ar = "" ) {
    if( !empty( $ar ) ){
        foreach( $ar as $f ){
            get_app_style( $f );
        }
    }
}

function get_scripts( $ar = "" ) {
    if( !empty( $ar ) ){
        foreach( $ar as $f ){
            get_app_script( $f );
        }
    }
}

function theme_styles() {
    foreach( glob( COREPATH . 'apps/' . APPDIR . '/styles/*.min.css' ) as $f ) {
        $ts[] = str_replace( COREPATH, '', $f );
    }
    return $ts;
}

function get_default_scripts() {
    foreach( glob( COREPATH.'assets/scripts/*.min.js' ) as $f ) {
        echo '<script src="'.APPURL.'/'.str_replace( COREPATH, '', $f ).'"></script>';
    }
    if( is_desktop() ){
        foreach( glob( COREPATH.'assets/scripts/desktop/*.min.js' ) as $f ) {
            echo '<script src="'.APPURL.'/'.str_replace( COREPATH, '', $f ).'"></script>';
        }
    }
    if( is_tablet() ){
        foreach( glob( COREPATH.'assets/tablet/scripts/*.min.js' ) as $f ) {
            echo '<script src="'.APPURL.'/'.str_replace( COREPATH, '', $f ).'"></script>';
        }
    }
    if( is_mobile() ){
        foreach( glob( COREPATH.'assets/mobile/scripts/*.min.js' ) as $f ) {
            echo '<script src="'.APPURL.'/'.str_replace( COREPATH, '', $f ).'"></script>';
        }
    }

}

function default_scripts() {
    foreach( glob( COREPATH.'assets/scripts/*.min.js' ) as $f ) {
        $ds[] = str_replace( COREPATH, '', $f );
    }
    if( is_desktop() ){
        foreach( glob( COREPATH.'assets/scripts/desktop/*.min.js' ) as $f ) {
            $ds['desktop'][] = str_replace( COREPATH, '', $f );
        }
    }
    if( is_tablet() ){
        foreach( glob( COREPATH.'assets/tablet/scripts/*.min.js' ) as $f ) {
            $ds['tablet'][] = str_replace( COREPATH, '', $f );
        }
    }
    if( is_mobile() ){
        foreach( glob( COREPATH.'assets/mobile/scripts/*.min.js' ) as $f ) {
            $ds['mobile'][] = str_replace( COREPATH, '', $f );
        }
    }
    return $ds;
}

function get_theme_scripts() {
    foreach( glob( COREPATH.'apps/'.APPDIR.'/scripts/*.min.js' ) as $f ) {
        echo '<script src="'.APPURL.'/'.str_replace( COREPATH, '', $f ).'"></script>';
    }
    if( is_desktop() ){
        foreach( glob( COREPATH.'apps/'.APPDIR.'/scripts/desktop/*.min.js' ) as $f ) {
            echo '<script src="'.APPURL.'/'.str_replace( COREPATH, '', $f ).'"></script>';
        }
    }
    if( is_tablet() ){
        foreach( glob( COREPATH.'apps/'.APPDIR.'/tablet/scripts/*.min.js' ) as $f ) {
            echo '<script src="'.APPURL.'/'.str_replace( COREPATH, '', $f ).'"></script>';
        }
    }
    if( is_mobile() ){
        foreach( glob( COREPATH.'apps/'.APPDIR.'/mobile/scripts/*.min.js' ) as $f ) {
            echo '<script src="'.APPURL.'/'.str_replace( COREPATH, '', $f ).'"></script>';
        }
    }
}

function theme_scripts() {
    foreach( array_reverse( glob( COREPATH.'apps/'.APPDIR.'/scripts/*.min.js' )) as $f ) {
        $ts[] = str_replace( COREPATH, '', $f );
    }
    return $ts;
}

function header_files() {
    $ghdss = get_option( 'dss' );
    if( $ghdss ) {
        $hdss = array_key_exists( 'header', unserialize( $ghdss ) ) ? unserialize( $ghdss )['header'] : Array();
    } else {
        $hdss = Array();
    }
    echo get_option('external_header');
    $t = template_exists().'_page_options';
    if(!empty($t)){
        $to = get_option($t);
        if(!empty($to)) {
            $hfs = array_key_exists( 'header', unserialize( $to ) ) ? unserialize( $to )['header'] : Array();
            $tss = array_merge( $hdss, $hfs );
            if (!empty($tss)) {
                foreach ($tss as $hf) {
                    if ($hf[0] == 'style') {
                        echo '<link rel="stylesheet" href="' . APPURL . '/apps/' . APPDIR . '/styles/' . str_replace(COREPATH, '', $hf[1]) . '">';
                        if( is_mobile() ){
                            if( glob( COREPATH.'apps/'.APPDIR.'/mobile/styles/' . str_replace(COREPATH, '', $hf[1]) ) ){
                                echo '<link rel="stylesheet" href="' . APPURL . '/apps/' . APPDIR . '/mobile/styles/' . str_replace(COREPATH, '', $hf[1]) . '">';
                            }
                        } else if( is_tablet() ){
                            if( glob( COREPATH.'apps/'.APPDIR.'/tablet/styles/' . str_replace(COREPATH, '', $hf[1]) ) ){
                                echo '<link rel="stylesheet" href="' . APPURL . '/apps/' . APPDIR . '/tablet/styles/' . str_replace(COREPATH, '', $hf[1]) . '">';
                            }
                        }
                    } else if ($hf[0] == 'script') {
                        if( is_mobile() ){
                            if( glob( COREPATH.'apps/'.APPDIR.'/mobile/scripts/' . str_replace(COREPATH, '', $hf[1]) ) ){
                                echo '<script src="' . APPURL . '/apps/' . APPDIR . '/mobile/scripts/' . str_replace(COREPATH, '', $hf[1]) . '"></script>';
                            } else {
                                echo '<script src="' . APPURL . '/apps/' . APPDIR . '/scripts/' . str_replace(COREPATH, '', $hf[1]) . '"></script>';
                            }
                        } else if( is_tablet() ){
                            if( glob( COREPATH.'apps/'.APPDIR.'/tablet/scripts/' . str_replace(COREPATH, '', $hf[1]) ) ){
                                echo '<script src="' . APPURL . '/apps/' . APPDIR . '/tablet/scripts/' . str_replace(COREPATH, '', $hf[1]) . '"></script>';
                            } else {
                                echo '<script src="' . APPURL . '/apps/' . APPDIR . '/scripts/' . str_replace(COREPATH, '', $hf[1]) . '"></script>';
                            }
                        } else {
                            echo '<script src="' . APPURL . '/apps/' . APPDIR . '/scripts/' . str_replace(COREPATH, '', $hf[1]) . '"></script>';
                        }
                    }
                }
            }
        } else {
            get_default_styles();
            get_theme_styles();
        }
    }
    if( is_mobile() ){
        if( glob( COREPATH.'apps/'.APPDIR.'/mobile/styles/' . PAGE . '.min.css' ) ){
            echo '<link rel="stylesheet" href="' . APPURL . '/apps/' . APPDIR . '/mobile/styles/' . PAGE . '.min.css' . '">';
        }
    } else if( is_tablet() ){
        if( glob( COREPATH.'apps/'.APPDIR.'/tablet/styles/' . PAGE . '.min.css' ) ){
            echo '<link rel="stylesheet" href="' . APPURL . '/apps/' . APPDIR . '/mobile/styles/' . PAGE . '.min.css' . '">';
        }
    } else {
        if( glob( COREPATH.'apps/'.APPDIR.'/styles/' . PAGE . '.min.css' ) ) {
            echo '<link rel="stylesheet" href="' . APPURL . '/apps/' . APPDIR . '/styles/' . PAGE . '.min.css' . '">';
        }
    }
}

function footer_files() {
    $ghdss = get_option( 'dss' );
    if( $ghdss ) {
        $hdss = array_key_exists( 'footer', unserialize( $ghdss ) ) ? unserialize( $ghdss )['footer'] : Array();
    } else {
        $hdss = Array();
    }
    echo get_option('external_footer');
    $t = template_exists().'_page_options';
    if(!empty($t)){
        $to = get_option($t);
        if( !empty( $to ) ){
            $ffs = array_key_exists( 'footer', unserialize( $to ) ) ? unserialize( $to )['footer'] : Array();
            $tss = array_merge( $hdss, $ffs );
            if(!empty($tss)){
                foreach ($tss as $ff) {
                    if( is_mobile() ){
                        if( glob( COREPATH.'apps/'.APPDIR.'/mobile/scripts/' . str_replace(COREPATH, '', $ff[1]) ) ){
                            echo '<script src="' . APPURL . '/apps/' . APPDIR . '/mobile/scripts/' . str_replace(COREPATH, '', $ff[1]) . '"></script>';
                        } else {
                            echo '<script src="' . APPURL . '/apps/' . APPDIR . '/scripts/' . str_replace(COREPATH, '', $ff[1]) . '"></script>';
                        }
                    } else if( is_tablet() ){
                        if( glob( COREPATH.'apps/'.APPDIR.'/tablet/scripts/' . str_replace(COREPATH, '', $ff[1]) ) ){
                            echo '<script src="' . APPURL . '/apps/' . APPDIR . '/tablet/scripts/' . str_replace(COREPATH, '', $ff[1]) . '"></script>';
                        } else {
                            echo '<script src="' . APPURL . '/apps/' . APPDIR . '/scripts/' . str_replace(COREPATH, '', $ff[1]) . '"></script>';
                        }
                    } else {
                        echo '<script src="' . APPURL . '/apps/' . APPDIR . '/scripts/' . str_replace(COREPATH, '', $ff[1]) . '"></script>';
                    }
                }
            }
        } else {
            echo '<script src="'.APPURL.'/apps/'.APPDIR.'/scripts/jquery.min.js"></script>';
            echo '<script src="'.APPURL.'/apps/'.APPDIR.'/scripts/jquery-ui.min.js"></script>';
            echo '<script src="'.APPURL.'/apps/'.APPDIR.'/scripts/scripts.min.js"></script>';
            echo '<script src="'.APPURL.'/apps/'.APPDIR.'/scripts/admin.min.js"></script>';
        }
    }
    if( is_mobile() ){
        if( glob( COREPATH.'apps/'.APPDIR.'/mobile/scripts/' . PAGE . '.min.js' ) ){
            echo '<script src="' . APPURL . '/apps/' . APPDIR . '/mobile/scripts/' . PAGE . '.min.js' . '"></script>';
        } /* else {
            echo '<script src="' . APPURL . '/apps/' . UINAME . '/scripts/' . PAGE . '.min.js' . '"></script>';
        } */
    } else if( is_tablet() ){
        if( glob( COREPATH.'apps/'.APPDIR.'/tablet/scripts/' . PAGE . '.min.js' ) ){
            echo '<script src="' . APPURL . '/apps/' . APPDIR . '/tablet/scripts/' . PAGE . '.min.js' . '"></script>';
        } /* else {
            echo '<script src="' . APPURL . '/apps/' . UINAME . '/scripts/' . PAGE . '.min.js' . '"></script>';
        } */
    } else {
        if( glob( COREPATH.'apps/'.APPDIR.'/scripts/' . PAGE . '.min.js' ) ){
            echo '<script src="' . APPURL . '/apps/' . APPDIR . '/scripts/' . PAGE . '.min.js' . '"></script>';
        }
    }
}

// This fetches the page banner
function get_page_head( $s = '2' ) {
    $banner = get_option('page_banner');
    if( $banner == 'yes' ){
        $banner_img = get_option('pages_head_banner');
        if( !empty( $banner_img ) ) {
            ?>
            <div class="banner_header" style="background-image:url(<?php echo $banner_img; ?>)">
                <h1><?php get_page_title(); ?></h1>
            </div>
        <?php } else { ?>
            <div class="material_header" data-style="<?php echo $s; ?>" data-material="true">
                <h1><?php get_page_title(); ?></h1>
            </div>
        <?php }
    }
}

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
                $msg = array('success','File Uploaded Successfully',$fn,$loc,$uq,$fe,$fz);
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

// Detects is page is requested via mobile
function is_mobile() {
    if ( empty($_SERVER['HTTP_USER_AGENT']) ) {
        return false;
    } elseif (
        strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false ) {
            return 'true';
    } elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') == false ) {
        return true;
    } elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== false ) {
        return false;
    } else {
        return false;
    }
}

// Detects if the page is requested via Tablet
function is_tablet() {
    if ( empty($_SERVER['HTTP_USER_AGENT']) ) {
        return false;
    } elseif (
        strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false ) {
            return false;
    } elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') == false ) {
        return false;
    } elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== false ) {
        return true;
    } else {
        return false;
    }
}

// Detects if the page is requested via Desktop
function is_desktop() {
    if( !is_mobile() && !is_tablet() ){
        return true;
    } else {
        return false;
    }
}

// Skelton for Arrays and Objects
function skel( $s ){
    if( !empty( $s ) ){
        echo '<pre style="font-size:15px">';
        print_r( $s );
        echo '</pre>';
    } else {
        echo 'Its Empty';
    }
}

// Skelton for Arrays and Objects In Error Log
function log_skel( $s ){
    if( !empty( $s ) ){
        error_log(print_r($s, true));
    } else {
        error_log( 'Its Empty');
    }
}

// Loads header html in template
function get_header() {
    if( is_mobile() ){
        if( glob( APPPATH . '/mobile/header.php' ) ){
            include( APPPATH . '/mobile/header.php' );
        } else {
            include( APPPATH . '/header.php' );
        }
    } else if( is_tablet() ){
        if( glob( APPPATH . '/tablet/header.php' ) ){
            include( APPPATH . '/tablet/header.php' );
        } else {
            include( APPPATH . '/header.php' );
        }
    } else {
        include( APPPATH . '/header.php' );
    }
}

// Loads footer html in template
function get_footer() {
    if( is_mobile() ){
        if( glob( APPPATH . '/mobile/footer.php' ) ){
            include( APPPATH . '/mobile/footer.php' );
        } else {
            include( APPPATH . '/footer.php' );
        }
    } else if( is_tablet() ){
        if( glob( APPPATH . '/tablet/footer.php' ) ){
            include( APPPATH . '/tablet/footer.php' );
        } else {
            include( APPPATH . '/footer.php' );
        }
    } else {
        include( APPPATH . '/footer.php' );
    }
}

// Gets any file from components folder
function get_comp( $n ){
    $ns = explode('/',str_replace('.php','',$n));
    $x = 1;
    $fl = APPPATH . '/components/';
    foreach($ns as $n){
        if($x == count($ns)){
            $fl .= $n . '.php';
        } else {
            $fl .= $n . '/';
        }
        $x++;
    }
    file_exists( $fl ) ? include( $fl ) : '';
}

function get_page( $n ){
    $ns = explode('/',$n);
    $x = 1;
    $fl = APPPATH . '/pages/';
    foreach($ns as $n){
        if($x == count($ns)){
            $fl .= $n . '.php';
        } else {
            $fl .= $n . '/';
        }
        $x++;
    }
    file_exists( $fl ) ? include( $fl ) : '';
}

// This was supposed to built htaccess, yet in progress
function build_routes() {
    $segments = 7;
    $pre = 'Options +FollowSymLinks -MultiViews
RewriteEngine On
RewriteBase /

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
';

}

function set_title( $title ){
    echo '<script>$(document).ready(function() { document.title = "' . $title . ' - ' . APPNAME . '"; });</script>';
}

function is_assoc( $a ) {
    return array_keys( $a ) !== range(0, count( $a ) - 1);
}

function select_options( $options = '', $selected = '' ) {
    $d = $options;
    $s = $selected;
    if( is_array($d) ){
        if (is_assoc($d)) {
            foreach ($d as $k => $t) {
                echo '<option value="' . $k . '" ' . ($k == $s ? "selected" : "") . '>' . $t . '</option>';
            }
        } else {
            foreach ($d as $t) {
                echo '<option value="' . $t . '" ' . ($t == $s ? "selected" : "") . '>' . $t . '</option>';
            }

        }
    } else if( is_numeric( $d ) ){
        for($x=0;$x<=$d;$x++){
            echo '<option value="' . $x . '" ' . ($x == $s ? "selected" : "") . '>' . $x . '</option>';
        }
    }
}

/**
 * @param array $d : Array of Options to print Can be assosiative array or one dimensional array
 * @param array $s : Options to check
 * @param string $tp : Type Checkbox or radio box
 * @param string $attrs : any attributes to be added to options Ex: name, on_click function , ..
 * @param string $before : opening tag for a container to wrap element in Ex:<div class"col-3">
 * @param string $after : closing tag of input container Ex: </div>
 */
function check_options( $d, $s = array(), $tp = 'checkbox', $attrs="", $before="", $after="" ) {
    if( is_assoc( $d )) {
        foreach( $d as $k => $t ){
            echo $before. '<label for="cb_'.$k.'" ><input '. $attrs.'  id="cb_'.$k.'" type="'.$tp.'" value="'.$k.'" '. (in_array($k, $s)? "checked" : "") .'>'.$t.'</label>'.$after;
        }
    } else {
        foreach( $d as $t ){
            echo $before. '<label for="cb_'.str_replace(' ','_',$t).'" ><input' . $attrs . 'id="cb_'.str_replace(' ','_',$t).'" type="'.$tp.'" value="'.$t.'" '. (in_array($t, $t)? "checked" : "") .'>'.$t.'</label>'.$after;
        }
    }
}

/***
 * @param array $array
 * @param $key
 * @return array|null
 * Pass an array with a key, to make the whole array grouped into one key.
 */

function array_group_by(array $array, $key) {
    if (!is_string($key) && !is_int($key) && !is_float($key) && !is_callable($key) ) {
        trigger_error('array_group_by(): The key should be a string, an integer, or a callback', E_USER_ERROR);
        return null;
    }
    $func = (!is_string($key) && is_callable($key) ? $key : null);
    $_key = $key;

    $grouped = [];
    foreach ($array as $value) {
        $key = null;

        if (is_callable($func)) {
            $key = call_user_func($func, $value);
        } elseif (is_object($value) && isset($value->{$_key})) {
            $key = $value->{$_key};
        } elseif (isset($value[$_key])) {
            $key = $value[$_key];
        }

        if ($key === null) {
            continue;
        }

        $grouped[$key][] = $value;
    }
    if (func_num_args() > 2) {
        $args = func_get_args();

        foreach ($grouped as $key => $value) {
            $params = array_merge([ $value ], array_slice($args, 2, func_num_args()));
            $grouped[$key] = call_user_func_array('array_group_by', $params);
        }
    }
    return $grouped;
}

function array_row_to_key( array $array, $key ){
    if( !is_assoc( $array ) && !empty( $array ) ){
        $new_array = [];
        foreach( $array as $a ){
            $new_array[ $a[ $key ] ] = $a;
        }
        return $new_array;
    }
}

function page_is( $p ) {
    return is_array( $p ) ? in_array(PAGEPATH,$p) ? true : false : PAGEPATH == $p ? true : false;
}

function page_of( $p ) {
    $pages = explode( '/', PAGEPATH );
    return in_array( $p, $pages ) ? true : false;
}

function render_menu( $array, $prefix = '' ) {
    $pre = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on" ? "https://" : "http://";
    $curl = rtrim( $pre.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], '/' );
    if( is_array( $array ) ){
        echo '<ul>';
        foreach( $array as $l => $t ){
            $sls = '';
            $link = rtrim( APPURL.$prefix.$t[0], '/' );
            if( $curl == $link ) { $c = 'on'; $title = $t[1]; } else { $c = ''; }
            if( !empty( $t[2] ) && is_array( $t[2] ) ){
                $sls .= '<ul>';
                foreach( $t[2] as $sl => $st ){
                    $link = APPURL.$prefix.$st[0];
                    if( $curl == $link ) { $sc = 'class="on"'; $c = 'on'; $title = $st[1].' - '.$t[1]; } else { $sc = ''; }
                    $sls .= '<li><a href="'.$link.'" '.$sc.'>'.$st[1].'</a></li>';
                }
                $sls .= '</ul>';
            }

            $s = !empty($sls) ? 'sub' : '';
            echo '<li class="'.$s.' '.$c.'"><a href="'.APPURL.$prefix.$t[0].'" class="'.$c.'">'.$t[1].'</a>'.$sls.'</li>';
        }
        echo '</ul>';
        !empty( $title ) ? define( 'PAGET', $title ) : '';
    }
}

function page_title_by_menu() {
    echo defined('PAGET') && !empty( PAGET ) ? PAGET : '';
}

// File Uploader
function media_upload() {
    include_once( COREPATH . 'core/components/media.php' );
}

function file_upload() {
    include_once( COREPATH . 'core/components/file_uploader.php' );
    $file = new FUP();
    return $file->file_uploader();
}

function icon_picker() {
    include_once( COREPATH.'core/components/icons.php' );
}

function access_permitter() {
    include_once( COREPATH . 'core/components/file_access.php' );
}

function get_currencies(){
    return include( COREPATH . 'core/components/data/currencies.php' );
}

function render_maps() {
    include_once( COREPATH . 'core/components/google_maps.php' );
    $gmaps = new GMaps();
    $gmaps->google_maps();
}

function send_sms( $number, $message, $gateway, $key = '', $secret = '' ) {
    include_once( COREPATH . 'core/components/sms.php' );
    $sms = new SMS;
    return $sms->send_sms( $number, $message, $gateway, $key, $secret );
}

function email( $to, $to_name ,$subject, $content, $from , $from_name, $cc = '' ){
    include_once( COREPATH . 'core/components/mailer.php' );
    $mailer = new Mailer;
//    return $mailer->send_email( $to, $subject, $content, $from, $cc );
    return $mailer->mandrill_send( $to,$to_name, $subject, $content, $from,$from_name, $cc );
}

function barcode( $text, $width = '100', $height = '36', $textShow = 'false', $bgColor = '#ffffff', $lineColor = '#000000', $textAlign = 'center', $textPosition = 'bottom', $textMargin = '0', $format = 'CODE128' ) {
    include_once( COREPATH . 'core/components/barcode.php' );
    $brcd = new BRCD;
    return $brcd->generate( $text, $width, $height, $bgColor, $lineColor, $textShow, $textAlign, $textPosition, $textMargin, $format );
}

function get_vue( $type = '' ) {
    if( !empty( $type ) ) {
        echo '<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>';
    } else {
        echo '<script src="https://cdn.jsdelivr.net/npm/vue.js"></script>';
    }
}

function say( $string = '' ) {
    echo !empty( $string ) ? $string : '';
}

function years_from_date( $date ){
    $then = date('Ymd', strtotime( $date ));
    $diff = date('Ymd') - $then;
    return substr($diff, 0, -4);
}

function easy_date( $date, $format = 'd M, Y' ) {
    $date = date_create( $date );
    return date_format( $date, $format );
}

function get_countries( $country = "" ){
    $countries = include( COREPATH . 'core/components/data/countries.php');
    if ($country == '' || !isset($country)) {
        return $countries;
    } else {
        if( is_numeric( $country ) ){
            $ckeys = array_keys( $countries );
            if( isset( $countries[$ckeys[$country]] ) && !empty($countries[$ckeys[$country]]) )
                echo $countries[$ckeys[$country]];
            else
                return 'errorrrr';
        } else {
            $position =  array_key_exists($country, $countries) ;
            if ($position)
                return $countries[$country];
            else
                return "error";
        }
    }
}

function storage_url( $url ) {
    return !empty( $url ) ? APPURL . 'apps/' . APPDIR . $url : '';
}

function months( $assoc = true ) {
    if( $assoc ){
        return [ '1' => 'January', '2' => 'February', '3' => 'March', '4' => 'April', '5' => 'May', '6' => 'June', '7' => 'July', '8' => 'August', '9' => 'September', '10' => 'October', '11' => 'November', '12' => 'December' ];
    } else {
        return [ 'January' => 'January', 'February' => 'February', 'March' => 'March', 'April' => 'April', 'May' => 'May', 'June' => 'June', 'July' => 'July', 'August' => 'August', 'September' => 'September', 'October' => 'October', 'November' => 'November', 'December' => 'December' ];
    }
}

function years( $from, $to, $desc = true ) {
    if( is_int( $from ) && is_int( $to ) && $from < $to ){
        $years = [];
        if( $desc ) {
            for( $x = $from; $x <= $to; $x++ ){
                $years[] = $x;
            }
        } else {
            for( $x = $to; $x >= $from; $x-- ){
                $years[] = $x;
            }
        }
        return $years;
    }
}