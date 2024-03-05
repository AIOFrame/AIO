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
        //skel( $options );
        //skel( $theme );
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

        //$c = new CODE();
        pre_html($class,$attrs,$pre_styles,$primary_color,$secondary_color,'icons,cards,modal,buttons,inputs,icons,tabs,steps,color,table,alerts,accordion',$styles,$scripts,$primary_font,$secondary_font,$icon_fonts);

    }

    /**
     * @param string|array $scripts
     * @return void
     */
    function post_html( string|array $scripts = [] ): void {
        $scripts = is_array( $scripts ) ? array_merge( $scripts, [ 'iro', 'scrollto', 'data', 'portal/portal' ] ) : $scripts.',iro,data,portal/portal';
        //$c = new CODE();
        post_html( $scripts );
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
        $a = new ACCESS();
        $c = Encrypt::initiate();
        $db = new DB();
        $ss = $db->select( 'sessions', '', 'session_uid = \''.get_user_id().'\'' );
        _r();
            !is_mobile() ? div('col-2') : '';
                _c(8);
                    pre_tabs('two');
                        tab( is_mobile() ? 'UI' : 'Appearance', 1, '#looks' );
                        tab( is_mobile() ? 'User' : 'User Details', 0, '#basic' );
                        tab( is_mobile() ? 'Pass' : 'Change Password', 0, '#pass' );
                        tab( ( is_mobile() ? 'Sessions' : 'Active Sessions' ) . ' ('.count($ss).')', 0, '#sess' );
                    post_tabs();

                    pre('','tab_data tab_data_box '.(is_mobile() ? 'p20' : 'p40'));

                        // UI / Appearance Tab Content
                        $f->option_params('id="looks"','data',2,2,'','theme,input_theme');
                        _r();
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
                        r_();
                        $f->process_options('Update Preferences','r5 xl mb0');
                        $f->post_process();

                        // User Details Tab Content
                        pre( 'basic', 'dn' );
                            $a->profile();
                        post();

                        // Password Tab Content
                        pre( 'pass', 'dn' );
                            $a->change();
                        post();

                        // Sessions Tab Content
                        pre('sess','dn');
                            $sessions = [
                                [ 'head' => [ 'OS', 'Start', 'Expiry', 'Browser' ] ],
                            ];
                            if( !empty( $ss ) ){
                                foreach( $ss as $s ){
                                    $id = !empty($s['session_id']) ? $c->encrypt($s['session_id']) : '';
                                    $sessions[] = [ 'body' => [ $s['session_os'], easy_date($s['session_time'],'d M, Y H:i:s'), easy_date($s['session_expiry'],'d M, Y H:i:s'), $s['session_client'] ] ];
                                }
                            }
                            table( $sessions, 'plain' );
                        post();
                    post();
                c_();
            !is_mobile() ? div('col-2') : '';
        r_();
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
        // TODO: Implement most params to be from user options

        if( $is_light ) {
            $logo = !empty( $options['logo_light'] ) ? 'style="background:url(\''.storage_url( $options['logo_light'] ).'\') no-repeat center / contain"' : '';
        } else {
            $logo = !empty( $options['logo_dark'] ) ? 'style="background:url(\''.storage_url( $options['logo_dark'] ).'\') no-repeat center / contain"' : '';
        }

        pre( '', '', 'header' );

            // Brand Panel
            pre( 'brand_panel' );
                $show_navigation ? div( 'nav_ico', ( str_contains( ICONS, 'Bootstrap' ) ? _el( 'i', 'bi bi-list menu' ) . _el( 'i', 'bi bi-x-lg close' ) : _div( 'mat-ico menu', 'menu' ) . _div( 'mat-ico close', 'close' ) ), 'menu' ) : '';
                a( APPURL . $logo_url, '', 'brand', '', $logo );
            post();

            // User Panel
            pre( 'user_panel' );

                // Show Alerts
                if( $show_alerts ) {
                    $alerts = $db->select( 'alerts', '', 'alert_user = "'.get_user_id().'" AND alert_seen = "0"' );
                    pre( 'alert', 'nav_ico', 'div', 'title="'.T('View Notifications').'"' );
                        div( 'mat-ico', 'notifications' );
                        el( 'span', '', !empty( $alerts ) ? count( $alerts ) : 0 );
                        pre( '', 'events drop' );
                            pre( '', 'n_events' );
                                if( !empty( $alerts )) {
                                    foreach( $alerts as $a ){
                                        div( 'n_event', $a['alert_msg'], '', 'data-type="'.$a['alert_type'].'"' );
                                    }
                                }
                            post();
                        post();
                    post();
                }

                // Show Regions
                if( !empty( $options['regions'] ) && isset( $c['features'] ) && ( in_array_any( [ 'regions', 'global' ], $c['features'] ) ) ) {
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
                    // TODO: Get Languages
                    pre( 'lang', 'nav_ico' );
                        div( 'mat-ico', 'translate' );
                        pre( '', 'drop' );
                            foreach( [] as $l => $n ){
                                $class = !empty( $_SESSION['lang'] ) ? $_SESSION['lang'] == $l ? ' on' : '' : '';
                                div( 'ln list '.$class, $n, '', 'data-lang="'.$l.'"' );
                            }
                        post();
                    post();
                }

                // Link to Front-end
                if( $link_to_front ) {
                    div( '', __a( APPURL, _div( 'nav_ico', _div( 'mat-ico', 'desktop_windows' ) ), '', T( 'Go to Frontend Website' ), 'target="_blank"' ), 'link_to_front' );
                }

                // Show User
                if( $show_user ) {
                    $user_name = $_SESSION['user']['name'] ?? 'Developer';
                    $user_data = [
                        [ 'b' => [ T('Name'), $user_name ] ],
                    ];
                    $user_pic = $_SESSION['user']['picture'] ?? '';
                    !empty( $_SESSION['user']['role'] ) ? $user_data[] = [ 'b' => [ T('Type'), $_SESSION['user']['type'] ] ] : '';
                    $code = $_SESSION['user']['phone_code'] ?? '';
                    $phone = $_SESSION['user']['phone'] ?? '';
                    $phone = $code . $phone;
                    !empty( $_SESSION['user']['gender'] ) ? $user_data[] = [ 'b' => [ T('Gender'), $_SESSION['user']['gender'] ] ] : '';
                    !empty( $_SESSION['user']['email'] ) ? $user_data[] = [ 'b' => [ T('Email'), $_SESSION['user']['email'] ] ] : '';
                    !empty( $phone ) ? $user_data[] = [ 'b' => [ T('Phone'), $phone ] ] : '';
                    !empty( $_SESSION['user']['dob'] ) ? $user_data[] = [ 'b' => [ T('Date of Birth'), easy_date( $_SESSION['user']['dob'] ) ] ] : '';
                    !empty( $_SESSION['user']['since'] ) ? $user_data[] = [ 'b' => [ T('Since'), easy_date( $_SESSION['user']['since'] ) ] ] : '';
                    //skel( $_SESSION );
                    pre( '', 'user_drop mr20' );
                        if( !is_mobile() && !is_tablet() ) {
                            div( 'user_pic', '', '', ( !empty( $user_pic ) ? 'style="background-image:url('. storage_url($user_pic) .')" class="bg"' : '' ) );
                            div( 'user_name', $user_name );
                            pre( '', 'user_details' );
                                div( 'pic', '', '', ( !empty( $user_pic ) ? 'style="background-image:url('. storage_url($user_pic) .')" class="bg"' : '' ) );
                                table($user_data,'user_data plain s mb10');
                                //h4( $user_name, 0, 'tac' );
                                //h5( $user_role, 0, 'tac' );
                                _r();
                                    div( 'col tal', __a( APPURL . $profile_url, T('My Profile'), 'r5 bsn s btn m0' ) );
                                    $logout_action = APPDEBUG ? 'logout_ajax' : $e->encrypt('logout_ajax');
                                    div( 'col tar', _b( 'red r5 bsn s m0', 'Logout', '', 'onclick="logout(\''.$logout_action.'\',\''.$logout_to.'\')"' ) );
                                r_();
                            post();
                        } else {
                            a( APPURL . $profile_url, '', 'user_pic', '', !empty( $user_pic ) ? 'style="background-image:url('. storage_url($user_pic) .')"' : '' );
                        }
                    post();
                }

            post();

        post( 'header' );
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
        pre( '', 'menu '.$class, 'aside' );
            _r();
                div( 'col-3', __a( APPURL . $root_url, _el( 'i', 'ico m mat-ico', 'home' ) ), '', 'data-intro' );
                pre( '', 'col-9' );
                    echo '<input type="search" placeholder="'.T('Search in Menu...').'">';
                post();
            r_();
            echo $content;
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
                        pre( '', 'set' );
                            div( 'title', T( $group_title ) );
                            pre( '', 'row', 'div', 'data-'.$data );
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
                                        div( $col, __a( APPURL . $url, _el( 'i', $ico_class, $icon ) . _div( 'title', T( $title ) ) ) );
                                    }
                                }
                            }
                            post();
                        post();
                    }
                }
            }
        post( 'aside' );
        div( 'credit', T('Copyright ©').' '.date('Y').' '.APPNAME );
    }

    /**
     * Renders PORTAL Options
     * @return void
     */
    function options(): void {
        $f = new FORM();
        $db = new DB();
        $portal_header_search_style = !empty( $ops['portal_header_search_style'] ) ? $ops['portal_header_search_style'] : '';
        $portal_header_search_show = !empty( $ops['portal_header_search_show'] ) ? $ops['portal_header_search_show'] : '';
        $portal_header_list_icon_text = !empty( $ops['portal_header_list_icon_text'] ) ? $ops['portal_header_list_icon_text'] : '';
        $portal_header_list_icon_class = !empty( $ops['portal_header_list_icon_class'] ) ? $ops['portal_header_list_icon_class'] : '';
        $portal_header_grid_icon_text = !empty( $ops['portal_header_grid_icon_text'] ) ? $ops['portal_header_grid_icon_text'] : '';
        $portal_header_grid_icon_class = !empty( $ops['portal_header_grid_icon_class'] ) ? $ops['portal_header_grid_icon_class'] : '';
        $dark = !empty( $ops['logo_dark'] ) ? $ops['logo_dark'] : '';
        $f->option_params_wrap( 'brand', 2, 2,  );
        $f->form([

        ], 'row' );
        //$f->text($r.'app_name','Web App / Site Name','Ex: AIO University...',$name,$attr,8);
        post();
    }

    public array $icon_options = [ 'menu' => 'menu', 'alerts' => 'notifications', 'languages' => 'language', 'frontend' => 'desktop_windows', 'user' => 'account_circle', 'logout' => 'logout', 'create' => 'add_circle', 'edit' => 'border_color', 'view' => 'file_open', 'delete' => 'delete', 'back' => 'keyboard_backspace', 'close' => 'close', 'save' => 'save', 'download' => 'download_for_offline' ];

    function icon_options(): void {
        $form[] = [ 'i' => 'portal_universal_icon_class', 'n' => 'Universal Icon Class', 'v' => 'mat-ico', 'c' => 12 ];
        $autoload = [ 'portal_universal_icon_class' ];
        foreach( $this->icon_options as $o => $v ) {
            $idt = 'port_ico_'.$o.'_text';
            $idc = 'port_ico_'.$o.'_class';
            $autoload[] = $idt;
            $autoload[] = $idc;
            $form[] = [ 'i' => $idt, 'n' => ucwords( $o ). ' Ico Text', 'v' => $v, 'c' => 2 ];
            $form[] = [ 'i' => $idc, 'n' => ucwords( $o ). ' Ico Class', 'v' => $v, 'c' => 2 ];
        }
        $o = new OPTIONS();
        $o->form( $form, 'row', 1, '', 'Successfully saved icon settings!', 2, 2, $autoload );
    }
}

/**
 * Renders HTMl for portal sub header / title bar
 * @param string $title Page title
 * @param string $back_url URL for back arrow if exist
 * @param string $list_view ID of the element shows table list view of content
 * @param string $grid_view ID of the element shows card / grid view of content
 * @param string $active_view Active view either list or grid
 * @param bool $show_search To show a search input
 * @param string|array $comp_or_actions String of comp path or array of actions
 * @param string $list_icon Content for list icon
 * @param string $grid_icon Content for grid icon
 * @return void
 */
function title_bar( string $title = '', string $back_url = '', string $list_view = '', string $grid_view = '', string $active_view = '', bool $show_search = true, string|array $comp_or_actions = [], string $list_icon = '', string $grid_icon = '' ): void {
    pre( '', 'header' );
        echo !empty( $back_url ) ? ( defined( 'ICONS' ) ? ( str_contains( ICONS, 'Material' ) ? __a( APPURL.$back_url, 'arrow_back', 'mat-ico back', 'Return' ) : ( str_contains( ICONS, 'Bootstrap' ) ? __a( APPURL . $back_url, _el( 'i', 'bi bi-arrow-90deg-left' ), 'back' ) : '' ) ) : '' ) : '';
        !empty( $title ) ? h1( $title, 1, 'title' ) : '';
        $show_search ? div( 'search_wrap', '<input type="search" class="header_search" placeholder="Search..." >' ) : '';

        !empty( $list_view ) || !empty( $grid_view ) ? pre( '', 'views' ) : '';
        if( !empty( $list_view ) ){
            pre( '', 'list_toggle'.($active_view == $list_view ? ' on' : ''), 'div', 'data-show="'.$list_view.'" data-off=".grid_toggle" data-on=".list_toggle" '.(!empty( $grid_view ) ? 'data-hide="'.$grid_view.'"' : '') );
            echo defined('ICONS') ? ( str_contains( ICONS, 'Material' ) ? _div( 'mat-ico', 'view_stream' ) : ( str_contains( ICONS, 'Bootstrap' ) ? _el( 'i', 'bi bi-list' ) : '' ) ) : '';
            post();
        }
        if( !empty( $grid_view ) ){
            pre( '', 'grid_toggle'.($active_view == $grid_view ? ' on' : ''), 'div', 'data-show="'.$grid_view.'" data-on=".grid_toggle" data-off=".list_toggle" '.(!empty( $list_view ) ? 'data-hide="'.$list_view.'"' : '') );
            echo defined('ICONS') ? ( str_contains( ICONS, 'Material' ) ? _div( 'mat-ico', 'grid_view' ) : ( str_contains( ICONS, 'Bootstrap' ) ? _el( 'i', 'bi bi-grid-3x2' ) : '' ) ) : '';
            post();
        }
        echo !empty( $list_view ) || !empty( $grid_view ) ? '</div>' : '';
        if( !empty( $actions ) || !empty( $comp_or_actions ) )
            is_array( $comp_or_actions ) ? div( 'actions' ) : get_comp( $comp_or_actions );
    post();
}

function back_url( string $url = '' ): void {
    a( APPURL.$url, 'arrow_back', 'mat-ico back', 'Go Back' );
    //echo '<a class="mat-ico back" href="'.APPURL . $url.'">arrow_back</a>';
}