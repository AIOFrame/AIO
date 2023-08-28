<?php

class PORTAL {

    /**
     * Renders Admin Portal
     * @param string $class Class for <body> tag
     * @param string $attrs Attributes for <body> tag
     * @param string $primary_color Primary color for theme (without #)
     * @param string $secondary_color Secondary color for theme (without #)
     * @param string|array $styles Styles to be linked
     * @param string|array $scripts Scripts to be added
     * @param string|array $primary_font Array of primary font and weights Ex: [ 'Lato', '300, 400' ]
     * @param string|array $secondary_font Array of secondary font and weights Ex: [ 'Cairo', '300, 400' ]
     * @param string|array $icon_fonts Icon Fonts Ex: 'MaterialIcons' or [ 'MaterialIcons', 'BootstrapIcons' ]
     * @return void
     */
    function pre_html( string $class = '', string $attrs = '', string $primary_color = '00A99D', string $secondary_color = '', string|array $styles = [], string|array $scripts = [], string|array $primary_font = [], string|array $secondary_font = [], string|array $icon_fonts = '' ): void {

        // Appearance
        global $options;
        $theme = $options['default_theme'] ?? '';
        $theme = $options['theme'] ?? $theme;
        global $dark_mode;
        if( str_contains( $theme, 'dark' ) ) {
            $class .= $theme . ' d';
            $dark_mode = 1;
        } else {
            $class .= $theme . ' l';
            $dark_mode = 0;
        }

        // Prepare Pre Styles
        $pre_styles = ['bootstrap/css/bootstrap-grid','select2','air-datepicker'];

        // Prepare Post Styles
        !empty( $styles ) ? ( is_array( $styles ) ? array_merge( $styles, [ 'portal/portal', 'portal/ui/'.$theme ] ) : $styles .= ',portal/portal,portal/ui/'.$theme ) : $styles = ['portal/portal','portal/ui/',$theme];

        // Prepare Scripts
        !empty( $scripts ) ? ( is_array( $scripts ) ? $scripts[] = 'jquery' : $scripts .= ',jquery' ) : '';

        $c = new CODE();
        $c->pre_html($class,$attrs,$pre_styles,$primary_color,$secondary_color,'icons,cards,modal,buttons,inputs,icons,tabs,steps,color,table,alerts,accordion',$styles,$scripts,$primary_font,$secondary_font,$icon_fonts);

    }

    /**
     * @param string|array $scripts
     * @return void
     */
    function post_html( string|array $scripts = [] ): void {
        $scripts = is_array( $scripts ) ? array_merge( $scripts, [ 'iro', 'scrollto', 'data', 'portal/portal' ] ) : $scripts.',iro,data,portal/portal';
        $c = new CODE();
        $c->post_html( $scripts );
    }

    /**
     * Renders Admin Portal Login HTML
     * @param string $login_redirect_url URL to redirect after login
     * @param string $attrs Attributes to add to the login wrapper
     * @param string $primary_color
     * @param string $secondary_color
     * @param string|array $pre_styles Pre styles to add
     * @param string|array $styles Internal styles to add
     * @param string|array $scripts Scripts to add
     * @param string|array $primary_font Array of primary font and weights Ex: [ 'Lato', '300, 400' ]
     * @param string|array $secondary_font Array of secondary font and weights Ex: [ 'Cairo', '300, 400' ]
     * @param string|array $icon_fonts Icon Fonts Ex: 'MaterialIcons' or [ 'MaterialIcons', 'BootstrapIcons' ]
     * @return void
     */
    function auth_page( string $login_redirect_url = '', string $attrs = '', string $primary_color = '', string $secondary_color = '', string|array $pre_styles = [], string|array $styles = [], string|array $scripts = [], string|array $primary_font = [], string|array $secondary_font = [], string|array $icon_fonts = 'MaterialIcons' ): void {

        $styles = is_array( $styles ) ? array_merge( $styles, [ 'portal/login', 'login' ] ) : $styles . ',portal/login,login';
        $scripts = is_array( $scripts ) ? array_merge( $scripts, [ 'aio', 'portal/login', 'login' ] ) : $scripts . ',aio,portal/login,login';

        $c = new CODE();
        $c->auth_page( $login_redirect_url, $attrs, $pre_styles, $primary_color, $secondary_color, $styles, $scripts, $primary_font, $secondary_font, $icon_fonts );
    }

    /**
     * Renders User Profile HTML
     * @return void
     */
    function user_profile(): void {
        // TODO: Based on if ecom in features, implement my orders tab
        // TODO: Based on if ecom in features, implement my addresses tab
        // TODO: Based on if crm or ems in features, implement my expenses tab
        // TODO: Based on if ems in features, implement my employee details tab
        $f = new FORM();
        $c = Encrypt::initiate();
        $db = new DB();
        $user = $db->select( 'users', '', 'user_id = \''.get_user_id().'\'', 1 );
        $ss = $db->select( 'sessions', '', 'session_uid = \''.get_user_id().'\'' );
        ?>
        <div class="row">
            <?php echo !is_mobile() ? '<div class="col-2"></div>' : ''; ?>
            <div class="col-12 col-lg-8">
                <div class="tabs two">
                    <div class="tab_heads" data-store>
                        <div class="tab on" data-tab="#looks"><?php is_mobile() ? E('UI') : E('Appearance'); ?></div>
                        <div class="tab" data-tab="#basic"><?php is_mobile() ? E('User') : E('User Details'); ?></div>
                        <div class="tab" data-tab="#pass"><?php is_mobile() ? E('Pass') : E('Change Password'); ?></div>
                        <div class="tab" data-tab="#sess"><?php is_mobile() ? E('Sessions') : E('Active Sessions'); ?><?php echo ' ('.count($ss).')'; ?></div>
                    </div>
                    <div class="tab_data tab_data_box <?php echo is_mobile() ? 'p20' : 'p40'; ?>">
                        <?php $f->option_params('id="looks"','data',2,2,'','theme,input_theme'); ?>
                            <div class="row">
                                <?php
                                $uis = [ 'default' => 'Default - Light' ];
                                $ui_list = scandir( ROOTPATH . 'assets/styles/portal/ui' );
                                foreach( $ui_list as $ui ) {
                                    if( str_contains( $ui, '.scss' ) ) {
                                        $s = str_replace( '.scss', '', $ui );
                                        $uis[ $s ] = ucwords( str_replace( '-', ' ', $s ) );
                                    }
                                }
                                $f->select( 'theme', 'Dashboard Style', 'Select Theme...', $uis, '', 'data-data class="select2"', 6, 1 );
                                $f->select( 'input_theme', 'Input Style', 'Select Theme...', [], '', 'data-data class="select2"', 6, 1 );
                                //skel( $uis );
                                //$f->texts([['']])
                                ?>
                            </div>
                        <?php
                        $f->process_options('Update Preferences','r5 xl mb0');
                        $f->post_process();
                        ?>

                        <?php $f->pre_process('id="basic" class="dn"','update_profile_ajax','user','user_',3,3,[],'Successfully updated user details!'); ?>
                        <div class="row">
                            <?php
                            $f->texts([['login','User Login','',$user['user_login']],['since','User Since','',easy_date($user['user_since'])]],'disabled','6');
                            $f->texts([['name','Full Name','Ex: John Doe',$user['user_name']]],'required data-user','6');
                            $f->input('email','email','E Mail','Ex: john@company.com',$user['user_email'],'data-help',6);
                            $f->upload('picture','Upload Picture','Upload',$user['user_picture'],0,0,'upload','data-user','svg,jpg,png',10,1,'',4);
                            //$f->process_html('Update Profile','r5 xl mb0','','update_profile_ajax','.col-12 tar');
                            ?>
                        </div>
                        <?php
                        $f->process_trigger('Update Profile','r5 xl mb0');
                        $f->post_process();
                        ?>

                        <?php $f->pre_process('id="pass" class="dn"','change_password_ajax','ps','',3,3,[],'Successfully updated user password!'); ?>
                            <div class="row">
                                <?php
                                $min_string = T('Minimum Characters');
                                $f->inputs('password',[['pass_old','Old Password'],['pass','New Password','','','data-length-notify="Password minimum length is 8 Characters"']],'data-ps minlength="8" data-minlength="'.$min_string.'" data-help required',6);
                                ?>
                            </div>
                        <?php
                        $f->process_trigger('Change Password','r5 xl mb0');
                        $f->post_process();
                        ?>

                        <div id="sess" class="dn">
                            <table class="">
                                <thead>
                                <tr>
                                    <th><?php E('OS'); ?></th>
                                    <th><?php E('Start'); ?></th>
                                    <th><?php E('Expiry'); ?></th>
                                    <th><?php E('Browser'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if( !empty( $ss ) ){
                                    foreach( $ss as $s ){
                                        $id = !empty($s['session_id']) ? $c->encrypt($s['session_id']) : '';

                                        ?>
                                        <tr class="tac">
                                            <td><?php echo $s['session_os']; ?></td>
                                            <td><?php echo easy_date($s['session_time'],'d M, Y H:i:s'); ?></td>
                                            <td><?php echo easy_date($s['session_expiry'],'d M, Y H:i:s'); ?></td>
                                            <td><?php echo $s['session_client']; ?></td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                            <div class="tar">
                                <?php
                                //$f->process_html('Logout All Sessions','r5 l','','clear_sessions_ajax');
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo !is_mobile() ? '<div class="col-2"></div>' : ''; ?>
        </div>
        <?php
        file_upload();
    }

    /**
     * Renders Admin Portal Header HTML
     * @param bool $show_navigation Show Navigation
     * @param string $logo_url URL link for the Logo
     * @param bool $show_alerts Show Alerts Icons and Dropdown
     * @param bool $show_languages Show Languages Toggle / Dropdown
     * @param bool $link_to_front Shows a hyperlink to front-end
     * @param bool $show_user Show User Dropdown
     * @param string $profile_url Profile URL for user icon
     * @param string $logout_to URL to direct users to after logout
     * @return void
     */
    function render_header( bool $show_navigation = false, string $logo_url = 'admin', bool $show_alerts = false, bool $show_languages = false, bool $link_to_front = false, bool $show_user = false, string $profile_url = '', string $logout_to = '' ): void {
        $db = new DB();
        $e = Encrypt::initiate();
        global $options;
        global $is_light;
        //$c = json_decode( CONFIG, 1 );
        $c = CONFIG;

        if( $is_light ) {
            $logo = !empty( $options['logo_light'] ) ? 'style="background:url(\''.storage_url( $options['logo_light'] ).'\') no-repeat center / contain"' : '';
        } else {
            $logo = !empty( $options['logo_dark'] ) ? 'style="background:url(\''.storage_url( $options['logo_dark'] ).'\') no-repeat center / contain"' : '';
        }
        ?>
        <header>
            <div id="brand_panel">
                <?php echo $show_navigation ? '<div id="menu" class="nav_ico"><div class="mat-ico menu">menu</div><div class="mat-ico close">close</div></div>' : ''; ?>
                <a href="<?php echo APPURL.$logo_url ?>" class="brand" <?php echo $logo; ?>></a>
                <?php if( is_mobile() || is_tablet() ){?>

                <?php } ?>
            </div>
            <div id="user_panel">
                <?php

                // Show Alerts
                if( $show_alerts ) {
                    $alerts = $db->select( 'alerts', '', 'alert_user = "'.get_user_id().'" AND alert_seen = "0"' );
                    echo '<div id="alert" class="nav_ico" title="View Notifications"><div class="mat-ico">notifications</div><span>';
                    echo !empty( $alerts ) ? count( $alerts ) : 0;
                    echo '</span><div class="events drop"><div class="n_events">';
                    if( !empty( $alerts )) {
                        foreach( $alerts as $a ){
                            echo '<div class="n_event" data-type="'.$a['alert_type'].'">'.$a['alert_msg'].'</div>';
                        }
                    }
                    echo '</div></div></div>';
                }

                // Show Regions
                if( !empty( $options['regions'] ) && isset( $c['features'] ) && ( in_array( 'regions', $c['features'] ) || in_array( 'global', $c['features'] ) ) ) {
                    $countries = get_countries('iso2','flag name');
                    //$regions = $db->get_options(['regions','primary_region']);
                    $set_countries = array_map( 'trim', explode( ',', $options['regions'] ) );
                    $my_region = $db->get_option('region',get_user_id());
                    $cr = !empty( $my_region ) ? $my_region : ( !empty( $options['primary_region'] ) ? $options['primary_region'] : $set_countries[ 0 ] );
                    $live = isset( $countries[$cr] ) ? explode( ' ', $countries[$cr] ) : [];
                    if( !empty( $set_countries ) ) {
                        $rico = !empty( $cr ) ? '<div class="reg-ico">'.$live[0].'</div>' : '<div class="mat-ico">map</div>';
                        echo '<div id="region" class="nav_ico" title="Change Region">'.$rico.'<div class="drop" data-action="'. ( APPDEBUG ? 'set_region_ajax' : $e->encrypt('set_region_ajax') ) .'">';
                        foreach( $set_countries as $r ){
                            $t = $countries[$r] ?? '';
                            if( !empty( $cr ) && $cr == $r  ) {
                                echo '<div class="ln list on">'.$t.'</div>';
                            } else {
                                echo '<div class="ln list" data-set-region="'.$r.'">'.$t.'</div>';
                            }
                        }
                        echo '</div></div>';
                    }
                    //skel( $regions );
                }

                // Show Languages
                if( $show_languages && in_array( 'languages', $c ) ) {
                    echo '<div id="lang" class="nav_ico" title="Change Language"><div class="mat-ico">translate</div><div class="drop">';
                    // TODO: Get Languages
                    foreach( [] as $l => $n ){
                        $class = !empty( $_SESSION['lang'] ) ? $_SESSION['lang'] == $l ? ' on' : '' : '';
                        echo '<div class="ln list'.$class.'" data-lang="'.$l.'">'.$n.'</div>';
                    }
                    echo '</div></div>';
                }

                // Link to Front-end
                if( $link_to_front ) {
                    echo '<div id="link_to_front"><a target="_blank" href="'.APPURL.'"><div class="nav_ico"><div class="mat-ico">desktop_windows</div></div></a></div>';
                }

                // Show User
                if( $show_user ) {
                    $user_pic = $_SESSION['user']['picture'] ?? '';
                    $user_name = $_SESSION['user']['name'] ?? 'Developer';
                    $user_role = $_SESSION['user']['role'] ?? $_SESSION['user']['type'];
                    ?>
                    <div class="user_drop mr20">
                        <?php if( !is_mobile() && !is_tablet() ) { ?>
                        <div class="user_pic" <?php echo !empty( $user_pic ) ? 'style="background-image:url('. storage_url($user_pic) .')" class="bg"' : ''; ?>></div>
                        <div class="user_name"><?php echo $user_name; ?></div>
                        <div class="user_details">
                            <div class="pic" <?php echo !empty( $user_pic ) ? 'style="background-image:url('. storage_url($user_pic) .')" class="bg"' : ''; ?>></div>
                            <h4 class="name"><?php echo $user_name; ?></h4>
                            <h4 class="role"><?php echo $user_role; ?></h4>
                            <div class="row">
                                <div class="col tal"><a href="<?php echo APPURL.$profile_url; ?>" class="r5 bsn s btn m0"><?php E('My Profile'); ?></a></div>
                                <div class="col tar"><button class="red r5 bsn s m0" onclick="logout(<?php echo '\''.$e->encrypt('logout_ajax').'\',\''.$logout_to.'\''; ?>)"><?php E('Log out'); ?></button></div>
                            </div>
                        </div>
                        <?php } else { ?>
                            <a href="<?php echo APPURL.$profile_url; ?>" class="user_pic" <?php echo !empty( $user_pic ) ? 'style="background-image:url('. storage_url($user_pic) .')" class="bg"' : ''; ?>></a>
                        <?php } ?>
                    </div>
                <?php } ?>
<!--                    <div id="log_off" class="nav_ico" onclick="log_off()"></div>-->
            </div>
        </header>
        <?php
    }

    /**
     * Renders Side Menu
     * @param array $menus Array set of [ 'title' => 'Contacts', 'url' => 'contacts', 'icon' => 'users', 'perm' => 'view_contacts', 'type' => 'admin', 'col' => 4, 'group' => 'Contacts', 'data' => 'contacts', 'menu' => [] ];
     * @param string $root_url URL of root Dashboard
     * @param string $content Any HTML content to be shown after Search Bar
     * @param string $class Class for the wrapper aside element
     * @param bool $gradient_icons Gradient Icons
     * @return void
     */
    function render_menu( array $menus = [], string $root_url = 'portal', string $content = '', string $class = '', bool $gradient_icons = false ): void {
        //$menus = !empty( $menus ) ? array_group_by( $menus, 'group' ) : [];
        ?>
        <aside class="menu <?php echo $class; ?>">
            <div class="row">
                <div class="col-2" data-intro>
                    <a href="<?php echo APPURL.$root_url; ?>" class="home_link">
                        <i class="ico l mat-ico">home</i>
                    </a>
                </div>
                <div class="col-10">
                    <input type="search" placeholder="<?php E('Search in Menu...'); ?>">
                </div>
            </div>
            <?php echo $content;
            if( !empty( $menus ) ) {
                $user_type = isset( $_SESSION['user']['type'] ) && !empty( $_SESSION['user']['type'] ) ? $_SESSION['user']['type'] : '';
                foreach( $menus as $menu_set ) {
                    //skel( $menu_set );
                    $group_title = $menu_set['group'] ?? '';
                    $data = $menu_set['data'] ?? str_replace( ' ', '_', strtolower( $group_title ) );
                    $menu_list = $menu_set['menu'] ?? [];
                    $group_restricted = [];
                    if( !empty( $menu_set['user_can'] ) ) {
                        $user_can_in_group = explode( ',', $menu_set['user_can'] );
                        foreach( $user_can_in_group as $ucg ) {
                            if( !user_can( $ucg ) ) {
                                $group_restricted[] = 1;
                            }
                        }
                    }
                    if( !empty( $menu_set['type'] ) ) {
                        $user_types_set = explode( ',', $menu_set['type'] );
                        if( empty( $user_type ) || !in_array( $user_type, $user_types_set ) ) {
                            $group_restricted[] = 1;
                        }
                    }
                    if( empty( $group_restricted ) ) {
                        //skel( $menu_set );
                    ?>
                    <div class="set">
                        <div class="title"><?php E( $group_title ); ?></div>
                        <div class="row" <?php echo 'data-'.$data; ?>>
                            <?php
                            if( !empty( $menu_list ) ) {
                                foreach( $menu_list as $menu ) {
                                    $col = isset( $menu['col'] ) ? 'item col-'.$menu['col'] : 'item col-4';
                                    $user_can = $menu['user_can'] ?? '';
                                    $url = $menu['url'] ?? '';
                                    $icon = $menu['icon'] ?? '';
                                    $title = $menu['title'] ?? '';
                                    $restricted = [];
                                    if( !empty( $perm ) ) {
                                        $user_can = explode( ',', $user_can );
                                        foreach( $user_can as $uc ) {
                                            if( !user_can( $uc ) ) {
                                                $restricted[] = 1;
                                            }
                                        }
                                    }
                                    if( isset( $menu['type'] ) ) {
                                        $user_types = explode( ',', $menu['type'] );
                                        if( !in_array( $user_type, $user_types ) ) {
                                            $restricted[] = 1;
                                        }
                                    }
                                    if( empty( $restricted ) ) {
                                        $ico_class = $gradient_icons ? 'mat-ico l grad-bg' : 'mat-ico l';
                                    ?>
                                    <div class="<?php echo $col; ?>">
                                        <a href="<?php echo APPURL.$url; ?>">
                                            <?php echo '<i class="'.$ico_class.'">'.$icon.'</i>' ?>
                                            <div class="title"><?php E( $title ); ?></div>
                                        </a>
                                    </div>
                                    <?php
                                    }
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                    }
                }
            }
            ?>
        </aside>
        <div class="credit"><?php echo T('Copyright ©').' '.date('Y').' '.APPNAME; ?></div>
        <?php
    }

    /**
     * Renders HTMl for portal sub header / title bar
     * @param string $title Page title
     * @param string $back_url URL for back arrow if exist
     * @param string $list_view ID of the element shows table list view of content
     * @param string $grid_view ID of the element shows card / grid view of content
     * @param string $active_view
     * @param array $actions
     * @return void
     */
    function title_bar( string $title = '', string $back_url = '', string $list_view = '', string $grid_view = '', string $active_view = '', array $actions = [], ): void {
        echo '<div class="header">';
        echo !empty( $back_url ) ? ( defined( 'ICONS' ) ? ( str_contains( ICONS, 'Material' ) ? '<a class="mat-ico back" href="'.APPURL . $back_url.'">arrow_back</a>' : ( str_contains( ICONS, 'Bootstrap' ) ? '<a class="back" href="'.APPURL . $back_url.'"><i class="bi bi-arrow-90deg-left"></i></a>' : '' ) ) : '' ) : '';
        echo !empty( $title ) ? '<h1 class="title">'.T($title).'</h1>' : '';
        echo !empty( $list_view ) || !empty( $grid_view ) ? '<div class="views">' : '';
        if( !empty( $list_view ) ){
            echo '<div class="list_toggle'.($active_view == $list_view ? ' on' : '').'" data-show="'.$list_view.'" data-off=".grid_toggle" data-on=".list_toggle" '.(!empty( $grid_view ) ? 'data-hide="'.$grid_view.'"' : '').'>';
            echo defined('ICONS') ? ( str_contains( ICONS, 'Material' ) ? '<div class="mat-ico">list</div>' : ( str_contains( ICONS, 'Bootstrap' ) ? '<i class="bi bi-list"></i>' : '' ) ) : '';
            echo '</div>';
        }
        if( !empty( $grid_view ) ){
            echo '<div class="grid_toggle'.($active_view == $grid_view ? ' on' : '').'" data-show="'.$grid_view.'" data-on=".grid_toggle" data-off=".list_toggle" '.(!empty( $list_view ) ? 'data-hide="'.$list_view.'"' : '').'>';
            echo defined('ICONS') ? ( str_contains( ICONS, 'Material' ) ? '<div class="mat-ico">grid_view</div>' : ( str_contains( ICONS, 'Bootstrap' ) ? '<i class="bi bi-grid-3x2"></i>' : '' ) ) : '';
            echo '</div>';
        }
        echo !empty( $list_view ) || !empty( $grid_view ) ? '</div>' : '';
        echo '<div class="actions">';
        echo '</div></div>';
    }
}

function back_url( string $url = '' ): void {
    echo '<a class="mat-ico back" href="'.APPURL . $url.'">arrow_back</a>';
}