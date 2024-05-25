<?php
// TODO: Page Management System + Routes
// TODO: Menu Designer
// TODO: Content Editor / Website Elements Designer - Custom (Refer to VvvebJs, Novi)
// TODO: Template Manager

class CMS {

    public array $page_statuses = [ 1 => 'Publicly Visible', 2 => 'Disabled', 3 => 'Draft', 4 => 'History' ];

    public array $widgets = [
        // Text Block
        'text' => [
            'name' => 'Text Widget',
            'desc' => 'A simple text widget',
            'ico' => 'text_snippet',
            'form' => [
                [ 't' => 'textarea', 'i' => 'text', 'l' => 'Text Content', 'c' => 12 ]
            ],
            'html' => '<div class="text_widget">{{text}}</div>'
        ],
        // Icon
        'icon' => [
            'name' => 'Icon Widget',
            'desc' => 'A simple icon widget',
            'ico' => 'add_reaction',
            'form' => [
                [ 't' => 'upload', 'i' => 'icon', 'l' => 'Upload Icon', 'c' => 4 ],
                [ 'i' => 'text', 'l' => 'Text', 'c' => 4 ],
                [ 't' => 'select', 'i' => 'type', 'l' => 'Icon Font', 'c' => 4, 'o' => [ 'mat-ico ' => 'Material Icons', 'bi bi-' => 'Bootstrap Icons', 'fa fa-' => 'Font Awesome' ], 'k' => 1 ],
            ],
            'html' => '<div class="{{icon}} {{type}}">{{icon}}</div>'
        ],
        // Divider + text
        // Info Box
        // Tabs
        // Image
        'image' => [
            'name' => 'Image Widget',
            'desc' => 'A simple image widget',
            'ico' => 'add_photo_alternate',
            'form' => [
                [ 't' => 'upload', 'i' => 'image', 'l' => 'Upload Image', 'c' => 2 ],
                [ 't' => 'text', 'i' => 'height', 'l' => 'Height', 'c' => 2 ],
                [ 't' => 'text', 'i' => 'width', 'l' => 'Width', 'c' => 2 ],
                [ 't' => 'text', 'i' => 'title', 'l' => 'Image Title', 'c' => 6 ],
            ],
            'html' => '<img src="{{image}}" alt="{{title}}" style="height:{{height}};width:{{width}}">'
        ],
        // Gallery
        // Accordion
        'accordion'=> [
            'name' => 'Accordion',
            'desc'=> 'A simple Accordion Widget',
            'ico' => 'calendar_view_day',
            'form'=> [
                [ 't' => 'text', 'i' => 'title', 'l' => 'Accordion Title', 'c'=> 12],
                [ 't' => 'textarea', 'i' => 'text', 'l' => 'Content', 'c'=> 12],
            ],
            'html' => '<div class="accordion"><div class="accordion_head">{{title}}</div><div class="accordion_body">{{content}}</div></div>',
        ],
        // Slider
        'slider'=> [
            'name' => 'Slider',
            'desc'=> 'A simple Slider Widget',
            'ico' => 'slideshow',
            'form'=> [
                [ 't' => 'files', 'i' => 'images', 'l' => 'Slider Images', 'c'=> 12 ],
                [ 't' => 'number', 'i' => 'view', 'l' => 'Slides per view', 'c' => 3 ],
                [ 't' => 'number', 'i' => 'scroll', 'l' => 'Slides per scroll', 'c' => 3 ],
                [ 't' => 'slide', 'i' => 'arrows', 'l' => 'Show Arrows', 'c' => 3 ],
                [ 't' => 'slide', 'i' => 'dots', 'l' => 'Show Dots', 'c' => 3 ]
            ],
            'html' => '<div class="image_slider" data-slider data-view="{{view}}" data-scroll="{{scroll}}" data-arrows="{{arrows}}" data-dots="{{dots}}">{{images}}</div>'
        ],
        // Heading
        'heading'=> [
            'name' => 'Heading',
            'desc'=> 'A simple Heading Widget',
            'ico' => 'text_fields',
            'form'=> [
                [ 't' => 'text', 'i' => 'text', 'l' => 'Heading Text', 'c'=> 6 ],
                [ 't' => 'select', 'i' => 'size', 'l' => 'Header Size', 'o' => [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ], 'c'=> 3 ],
                [ 't' => 'text', 'i' => 'family', 'l' => 'Font Family', 'c'=> 3 ],
            ],
            'html' => '<{{size}} style="font-family:{{family};">{{text}}</{{size}}>'
        ],
        // Button
        'button'=> [
            'name' => 'Button',
            'desc'=> 'A simple Button Widget',
            'ico' => 'smart_button',
            'form'=> [
                [ 't' => 'text', 'i' => 'title', 'l' => 'Button Name', 'c'=> 6],
                [ 't' => 'text', 'i' => 'class', 'l' => 'Button Class', 'c'=> 6],
            ],
            'html' => '<button class="{{class}}">{{title}}</button>'
        ],
        // Video Player
        'video_player'=> [
            'name' => 'Video Player',
            'desc'=> 'A simple Video Player Widget',
            'ico' => 'video_call',
            'form'=> [
                [ 't' => 'text', 'i' => 'title', 'l' => 'Video Title', 'c'=> 6 ],
                [ 't' => 'upload', 'i' => 'video', 'l' => 'Upload Video', 'c'=> 6 ],
                [ 't' => 'text', 'i' => 'height', 'l' => 'Video  Height', 'c' => 6 ],
                [ 't' => 'text', 'i' => 'width', 'l' => 'Video Width', 'c' => 6 ],
            ],
        'html' => '<video width="{{width}}" height="{{height}}" src="{{video}}" title="{{title}}"></video>'],
        // YouTube Video Player
        'youtube_video'=> [
            'name' => 'Youtube Video',
            'desc'=> 'Youtube Video player Widget',
            'ico' => 'ondemand_video',
            'form'=> [
                [ 't' => 'text', 'i' => 'url', 'l' => 'Video URL', 'p' => '', 'c'=> 12 ],
                [ 't' => 'text', 'i' => 'height', 'l' => 'Video  Height', 'c' => 6 ],
                [ 't' => 'text', 'i' => 'width', 'l' => 'Video Width', 'c' => 6 ],
            ],
            'html' => '<video width="{{width}}" height="{{height}}" src="{{video}}" title="{{title}}"></video>'],
        // Audio Player
        'audio_player'=> [
            'name' => 'Audio Player',
            'desc'=> 'A simple Audio Player Widget',
            'ico' => 'music_note',
            'form'=> [
                [ 't' => 'text', 'i' => 'title', 'l' => 'Audio Title', 'c'=> 6],
                [ 't' => 'upload', 'i' => 'audio', 'l' => 'Upload Audio', 'c'=> 6],
            ],
            'html' => '<audio controls><source src="{{audio}}"></audio>'
        ],
        // Google Maps
        // Page Gallery
        // Product Gallery
        // Blog Gallery
        // Pricing
        // Table
        // Payment Button
        // Progress Bar
        // Custom Widgets
    ];

    function __construct() {

    }

    /**
     * @param string $modal_class s = small, m = medium, l = large, xl, f = full, l20, l40, l50, l60, l80, r20, r40...
     * @param string $content_type Ex: Page, Blog, News
     * @param bool $content_builder Render Content Builder or show simple Rich Text editor
     * @param string $widget_picker_class
     * @return void
     */
    function page_form( string $modal_class = '', string $content_type = 'page', bool $content_builder = false, string $widget_picker_class = 'b80' ): void {
        $f = new FORM();
        $statuses = $this->page_statuses;
        unset( $statuses[4] );
        $publish_fields = [
            [ 'id' => 'title', 'title' => ucwords( $content_type ).' Title', 'a' => 'required' ],
            [ 'id' => 'url', 'title' => 'URL Slug', 'p' => 'Ex: procedure-to-register', 'a' => 'data-no-space', 'c' => 12.1 ],
        ];
        $visibility_fields = [
            [ 't' => 'date', 'id' => 'birth', 'n' => 'Visible From', 'c' => 6 ],
            [ 't' => 'date', 'id' => 'expiry', 'n' => 'Visible Till', 'c' => 6 ],
            [ 'type' => 'select', 'id' => 'status', 'title' => ucwords( $content_type ).' Status', 'o' => $statuses, 'v' => 1, 'a' => 'required', 'k' => 1 ],
            [ 'id' => 'password', 'n' => 'Password', 'p' => 'Ex: **********', 'c' => 12.1 ],
        ];
        $seo_fields = [
            [ 't' => 'textarea', 'id' => 'meta_desc', 'n' => 'Meta Description', 'm' => 512 ],
            [ 't' => 'textarea', 'id' => 'meta_words', 'p' => '', 'n' => 'Keywords', 'm' => 512 ],
            [ 'id' => 'author', 'n' => 'Author', 'p' => 'Ex: Shaikh', 'm' => 256 ],
            [ 't' => 'slide', 'id' => 'follow', 'n' => 'Visible to Search Engines', 'off' => 'No', 'on' => 'Yes', 'v' => 1, 'c' => 12.1 ],
        ];
        $r = $f->_random();
        !empty( $modal_class ) ? pre_modal( $content_type, $modal_class ) : '';
        $f->pre_process( 'data-wrap id="'.$content_type.'_form"', 'update_content_ajax', $r, 'p_', [ 'content_type' => strtolower( $content_type ) ] );
        _r();
            _c(4);
                accordion( 'Identity', $f->_form( $publish_fields, 'row', $r ), 'br15 w on' );
                accordion( 'SEO', $f->_form( $seo_fields, 'row', $r ), 'br15 w on' );
                accordion( 'Visibility', $f->_form( $visibility_fields, 'row', $r ), 'br15 w' );
                $f->process_trigger('Save '.$content_type,'w r');
            c_();
            _c(8);
                if( $content_builder ) {
                    $f->content_builder('content',ucwords( $content_type ).' Content','','','data-'.$r);
                } else {
                    $f->richtext('content',ucwords( $content_type ).' Content','','data-'.$r);
                }
            c_();
        r_();
        $hidden_fields = [
            [ 'id' => 'date', 'a' => 'class="dn"', 'v' => date('Y-m-d H:i:s') ],
            [ 'id' => 'id', 'a' => 'class="dn"' ],
        ];
        $f->form( $hidden_fields, 'row', $r );
        $f->post_process();
        !empty( $modal_class ) ? post_modal() : '';
    }

    function page_filters(): void {

    }

    function pages( string $page_type = 'page', string $content_style = 'table', string $wrapper_class = '', string|int $cols = 4 ): void {
        if( in_array( $content_style, [ 'tables', 'table', 'list' ] ) ) {
            $this->pages_list( $page_type, $wrapper_class );
        } else if( in_array( $content_style, [ 'cards', 'card' ] ) ) {
            $this->page_cards( $page_type, $wrapper_class, $cols );
        }
    }

    function pages_list( string $page_type = 'page', bool $show_user = false, string $wrapper_class = '' ): void {
        $pages = $this->_pages( $page_type, $show_user );
        $status = $this->page_statuses;
        global $options;
        if( empty( $pages ) ) {
            no_content( 'No '.$page_type.' created yet!', '', '', '', $wrapper_class );
        } else {
            $f = new FORM();
            $table[] = [ 'head' => [ 'ID', 'Name', 'Date', 'Visibility', 'Status', 'User', 'Actions' ] ];
            foreach( $pages as $p ) {
                $table[]['body'] = [
                    $p['content_id'],
                    $p['content_title']. __div('', __el('small','',$p['content_url']) ),
                    easy_date($p['content_date']).__div('', __el('small','',T('Updated').': '.easy_date($p['content_update'])) ),
                    ( !empty($p['content_birth']) ? __div('', __el('small','',T('Visible from').': '.easy_date($p['content_birth'])) ) : '' ) . ( !empty($p['content_expiry']) ? __div('', __el('small','',T('Visible till').': '.easy_date($p['content_expiry'])) ) : '' ),
                    $status[ $p['content_status'] ] ?? '',
                    $show_user ? $p['user_name'] : '',
                    __pre('','acts').$f->__edit_html( '#'.$page_type.'_modal', $p, 'div', '', '', '', ( $options['icon_class'] ?? 'mico' ), ( $options['ico_edit'] ?? 'edit' ) )._post()
                ];
            }
            table_view( $page_type, $table, $wrapper_class );
        }
    }

    function page_cards( string $page_type = 'page', bool $show_user = false, string $wrapper_class = '', string|int $cols = 4 ): void {
        $pages = $this->_pages( $page_type, $show_user );
        $status = $this->page_statuses;
        if( empty( $pages ) ) {
            no_content( 'No '.$page_type.' created yet!', '', $wrapper_class );
        } else {
            $cards = [];
            foreach( $pages as $p ) {
                $cards[] = _card( 'br15', $p['content_title'], '', '/'.$p['content_url'], '', '', $status[ $p['content_status'] ] ?? '', '', [], '', [], '', '#'.$page_type.'_modal', $p, 'pages', "content_id = {$p['page_id']}" );
            }
            grid_view( $page_type, $cards, $wrapper_class, $cols );
        }
    }

    function page_editor(): void {
        // TODO: JS Page Editor
        // TODO: JS Page Templates
    }

    /**
     * Renders page by url or id
     * @param string|int $id_url ID or URL of the page
     * @param bool $show_no_content Show no content graphic if page not found
     * @return void
     */
    function page( string|int $id_url, bool $show_no_content = false ): void {
        $page = $this->_page( $id_url );
        if( $page ) {
            echo $page['content_content'];
        } else if( $show_no_content ) {
            no_content( 'Page not found!' );
        }
    }

    function _pages( string $page_type = 'page', bool $show_user = false ): array {
        $d = new DB();
        $data = [ 'id', 'date', 'update', 'title', 'content', 'url', 'password', 'status', 'birth', 'expiry', 'by' ];
        if( $show_user ) {
            $pages = $d->select( [ 'content', [ 'users', 'user_id', 'content_by' ] ], array_merge( prepare_values( $data, 'content_' ), [ 'user_name' ] ), 'content_type = \''.strtolower( $page_type ).'\' && content_status != \'4\'' );
        } else {
            $pages = $d->select( 'content', prepare_values( $data, 'content_' ), 'content_type = \''.strtolower( $page_type ).'\' && content_status != \'4\'' );
        }
        return $pages;
    }

    function _page( string|int $id_url ): array {
        $return = [];
        if( !empty( $id_url ) ) {
            $db = new DB();
            $page = is_numeric( $id_url ) ? $db->select( 'content', '', "content_id = {$id_url}", 1 ) : $db->select( 'content', '', "content_url= '{$id_url}'", 1 );
            if( $page ) {
                $return = $page;
            }
        }
        return $return;
    }

    function categories(): void {
        //$db = new DB();
        //$categories = $db->select( 'page_terms' );
    }

    function get_categories(): array {
        // TODO: Return categories array
        return [];
    }

    function options(): void {
        $form = [
            [ 't' => 'text' ],
        ];
        $f = new FORM();
        //$f->option_params_wrap( '', '', $auto_load, $unique, $encrypt, $success, $callback, $confirm );
            //$f->form( $form, $type );
            //$f->process_trigger( 'Save Options', 'store grad', '', '', '.col-12 tac' );
        //post();
        // TODO: Implement CMS Options
    }

    function menu_builder(): void {
        // TODO: Renders Menu Builder with JS
    }

    function menus(): void {
        // TODO: Renders Menu HTML
    }

    function get_menu( int|string $id_name = '' ): array {
        // TODO: Returns Menu Array
        return [];
    }

    function content_builder( string|array $id, string $label = '', string $placeholder = '', string|null $value = '', string $attrs = '', string|float|int $pre = '', int|float $height = 400, string $post = '' ): void {
        $f = new FORM();
        $r = rand(9,9999);
        //$f->content_builder( $id, $label, $placeholder, $value, $attrs, $pre, $post );
        $f->pre( $pre );
            div( 'aio_cb_viewer', '', 'view_' . $r );
            pre( '', 'd', 'div', 'style="display:non"' );
                $f->textarea( $id, '', $placeholder, $value, $attrs . ' data-aio_cb_code="'.$r.'"' );
            post();
            pre( '', '', 'div', 'style="height:'.$height.'px" data-aio_cb_field="'.$r.'"' );
                get_comp('content_builder');
            post();
        $f->post( $pre, $post );
        // TODO: Content Builder Header / Footer
        // TODO: Content Builder Rows, Grids and Widgets
        // TODO: Content Builder Widget Picker
        // TODO: Content Builder Drag, Drop, Delete Widgets
    }

    /**
     * Renders a form to create static content widget
     * @param string $title Title of the widget
     * @param string $modal_class Size class of the modal if needed as popup
     * @return void
     */
    function static_widget_form( string $title = 'Widget Builder', string $modal_class = '' ): void {
        $f = new FORM();
        !empty( $modal_class ) ? pre_modal( $title, $modal_class ) : '';
        $r = $f->_random();
        $f->pre_process( 'data-wrap id="static_widget_form"', 'widgets', 'widget', 'widget_' );
        _r();
        $f->text('name','Widget Name','Ex: Social Widget','','data-widget required',4);
        $f->text('desc','Description','Ex: Displays a social platform sharing widget','','data-widget',4);
        //$icon_title = defined( 'ICONS' ) ? ( str_contains( ICONS, 'Material' ) ? 'Material Icon' : ( str_contains( ICONS, 'bootstrap' ) ? 'Bootstrap Icon' : 'Widget Icon' ) ) : 'Widget Icon';
        //$f->text('icon',$icon_title,'Ex: lightbulb','','data-widget required',2);
        $f->upload('image','Image','Browse','',0,0,'upload','data-widget','jpg,png,svg,jpeg,webp',.3,1,'',2);
        $f->slide('status','Status','','',1,'m','data-widget',2);
        r_();
        pre_tabs('widget_tabs material mb20');
            tab('Widget Fields',1);
            tab('HTML Code');
            tab('Styles');
            tab('Scripts');
        post_tabs();
        pre('widget_data');
            pre('widget_fields_data');
                $f->form_builder('form','Widget Fields','','data-widget','',400);
            post();
            pre('html_code_data','dn');
                div('widget_tags','','widget_tags');
                $f->code('html','HTML Code','','data-widget');
            post();
            pre('styles_data','dn');
                _r();
                    $f->code('ui_front','Frontend CSS Styling','','data-widget',6);
                    $f->code('ui_back','Backend CSS Styling','','data-widget',6);
                r_();
            post();
            pre('scripts_data','dn');
                _r();
                    $f->code('ux_front','Frontend JS Scripts','','data-widget',6);
                    $f->code('ux_back','Backend JS Scripts','','data-widget',6);
                r_();
            post();
        post();
        pre('','tac');
            $f->process_trigger('Save Widget','grad mb0');
        post();
        $f->post_process();
        !empty( $modal_class ) ? post_modal() : '';
        file_upload();
    }

    /**
     * OBSOLETE - TO DELETE
     * @param string $name
     * @param string $desc
     * @param string $image
     * @param array $form_fields
     * @param string $html
     * @param string $ui_front
     * @param string $ui_back
     * @param string $ux_front
     * @param string $ux_back
     * @return array
     */
    function static_widget( string $name, string $desc = '', string $image = '', array $form_fields = [], string $html = '', string $ui_front = '', string $ui_back = '', string $ux_front = '', string $ux_back = '' ): array {
        if( empty( $form_fields ) || empty( $html ) ) {
            return [ 0, T('Missing widget form fields or html code!') ];
        }
        $d = new DB();
        $widget_data = [
            'name' => $name,
            'desc' => $desc,
            'image' => $image,
            'form' => $form_fields,
            'html' => $html,
            'ui_front' => $ui_front,
            'ui_back' => $ui_back,
            'ux_front' => $ux_front,
            'ux_back' => $ux_back
        ];
        $add = $d->insert( 'widgets', prepare_keys( $widget_data, 'widget_' ), prepare_values( $widget_data ) );
        return $add ? [ $add, T('Successfully added widget!') ] : [ 0, T('Failed to register widget!') ];
    }

    /**
     * Fetches an available widget
     * @param string|int $identity ID or Name of the widget
     * @return void
     */
    function get_widget( string|int $identity ): void {
        $d = new DB();
        $w = is_numeric( $identity ) ? $d->select( 'widgets', '', 'widget_id = \''.$identity.'\'', 1 ) : $d->select( 'widgets', '', 'widget_name = \''.$identity.'\'', 1 );

    }

    function _widgets(): array {
        $widgets = $this->widgets;
        $db = new DB();
        // PreBuilt Widgets
        // TODO: Create following default widgets
        $custom_widgets = $db->select( 'widgets', '', 'widget_status = \'1\'' );
        if( !empty( $custom_widgets ) ) {
            foreach( $custom_widgets as $cw ) {
                $widgets[ strtolower( str_replace(' ','_',$cw['widget_name'] ) ) ] = [
                    'image' => !empty( $cw['widget_image'] ) ? storage_url( $cw['widget_image'] ) : '',
                    'name' => $cw['widget_name'],
                    'desc' => $cw['widget_desc'],
                    'form' => $cw['widget_form'],
                    'html' => $cw['widget_html'],
                    'ui' => $cw['widget_ui_back'],
                    'ux' => $cw['widget_ux_back'],
                ];
            }
        }
        return $widgets;
    }


    function widgets( string $wrapper_class = '', int $cols = 4, string $modal_identity = '' ): void {
        $db = new DB();
        $widgets = $db->select( 'widgets' );
        if( empty( $widgets ) ) {
            no_content( 'No widgets created yet!' );
        } else {
            //$f = new FORM();
            $cards = [];
            foreach( $widgets as $p ) {
                $image = !empty( $p['widget_image'] ) ? storage_url( $p['widget_image'] ) : '';
                //$icon = defined( 'ICONS' ) && !empty( $p['widget_icon'] ) ? ( str_contains( ICONS, 'Material' ) ? __div('mat-ico xxl',$p['widget_icon']) : ( str_contains( ICONS, 'bootstrap' ) ? __el('i','b bi-'.$p['widget_icon']) : $p['widget_icon'] ) ) : '-';
                $cards[] = _card( 'br15', $p['widget_name'], '', $p['widget_desc'], __div('tac',_img($image,'','widget_image',$p['widget_name'],$p['widget_name'],'style="height: 100px"')), '', $p['widget_status'] == 1 ? 'Active' : 'Inactive', $p['widget_status'] == 1 ? 'green' : 'grey', [], [], $modal_identity, $p, 'widgets', "widget_id = {$p['widget_id']}" );
            }
            grid_view( 'widget_cards', $cards, $wrapper_class, $cols );
        }
    }

}

function update_menu_ajax(): void {

}

function update_content_ajax(): void {
    $p = replace_in_keys( $_POST, 'p_', '' );
    if( !empty( $p['title'] ) ) {
        unset( $p['pre'] );
        unset( $p['t'] );
        $id = $p['id'] ?? 0;
        unset( $p['id'] );
        //$p['content'] = htmlspecialchars( $p['content'] );
        $p['by'] = get_user_id();
        $p['update'] = date('Y-m-d H:i:s');
        $p['url'] = !empty( $p['url'] ) ? $p['url'] : strtolower( str_replace( ' ', '-', $p['title'] ) );
        $p['date'] = $p['date'] ?? date('Y-m-d H:i:s');
        $db = new DB();

        // Check if page exists with same slug
        $exist = $db->select( 'content', 'content_id', "content_url = {$p['url']}" );
        if( $exist ) {
            ef('Content with same url exist! Please change title and url!!');
            return;
        }

        $saved = $db->insert( 'content', prepare_keys( $p, 'content_' ), prepare_values( $p ) );
        if( $saved ) {
            if( !empty( $id ) ) {
                // Update History
                $update = $db->update( 'content', [ 'content_status', 'content_parent' ], [ 4, $saved ], "content_id = {$id}" );
                if( $update ) {
                    es('Successfully updated content!');
                } else {
                    ef('Failed to update content, Please consult administrator!');
                }
            } else {
                es('Successfully saved new page!');
            }
        } else {
            ef('Failed to store content, please consult administrator!');
        }
        //skel( $p );
    } else {
        ef('Failed due to content title not set!');
    }
}