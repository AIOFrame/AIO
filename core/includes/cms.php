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
            'icon' => 'text',
            'name' => 'Text',
            'desc' => 'A simple text widget',
            'form' => [
                [ 't' => 'textarea', 'i' => 'text', 'l' => 'Text Content', 'c' => 12 ]
            ],
            'html' => '<div>{{text}}</div>'
        ],
        // Icon
        // Divider + text
        // Info Box
        // Tabs
        // Image
        // Gallery
        // Accordion
        // Slider
        // Heading
        // Button
        // Video Player
        // Audio Player
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

    function page_form( string $modal_class = '', string $page_type = 'page', bool $content_builder = false ): void {
        $f = new FORM();
        $statuses = $this->page_statuses;
        unset( $statuses[4] );
        $publish_fields = [
            [ 'id' => 'title', 'title' => ucwords( $page_type ).' Title', 'a' => 'required' ],
            [ 'id' => 'url', 'title' => 'URL Slug', 'p' => 'Ex: procedure-to-register', 'a' => 'data-no-space', 'c' => 12.1 ],
        ];
        $visibility_fields = [
            [ 't' => 'date', 'id' => 'birth', 'n' => 'Visible From', 'c' => 6 ],
            [ 't' => 'date', 'id' => 'expiry', 'n' => 'Visible Till', 'c' => 6 ],
            [ 'type' => 'select', 'id' => 'status', 'title' => ucwords( $page_type ).' Status', 'o' => $statuses, 'v' => 1, 'a' => 'required', 'k' => 1 ],
            [ 'id' => 'password', 'n' => 'Password', 'c' => 12.1 ],
        ];
        $seo_fields = [
        [ 't' => 'textarea', 'id' => 'meta_desc', 'n' => 'Meta Description' ],
            [ 't' => 'textarea', 'id' => 'meta_words', 'n' => 'Meta Keywords' ],
            [ 'id' => 'meta_author', 'n' => 'Meta Author', 'c' => 12.1 ],
        ];
        $r = $f->_random();
        !empty( $modal_class ) ? pre_modal( $page_type, $modal_class ) : '';
        $f->pre_process( 'data-wrap id="'.$page_type.'_form"', 'update_page_ajax', $r, 'p_', 2, 2, [ 'page_type' => strtolower( $page_type ) ] );
        _r();
            _c(8);
                if( $content_builder ) {
                    $f->content_builder('content',ucwords( $page_type ).' Content','','','data-'.$r);
                } else {
                    $f->richtext('content',ucwords( $page_type ).' Content','','data-'.$r);
                }
            c_();
        _c(4);
        accordion( 'Identity', $f->_form( $publish_fields, 'row', $r ), 'br15 w on' );
        accordion( 'Visibility', $f->_form( $visibility_fields, 'row', $r ), 'br15 w on' );
        accordion( 'SEO', $f->_form( $seo_fields, 'row', $r ), 'br15 w' );
        $f->process_trigger('Save '.$page_type,'w r');
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

    function pages_list( string $page_type = 'page', string $wrapper_class = '' ): void {
        $pages = $this->_pages( $page_type );
        $status = $this->page_statuses;
        if( empty( $pages ) ) {
            no_content( 'No '.$page_type.' created yet!', '', $wrapper_class );
        } else {
            $f = new FORM();
            $table[] = [ 'head' => [ 'ID', 'Name', 'Date', 'Visibility', 'Status', 'User', 'Actions' ] ];
            foreach( $pages as $p ) {
                $table[]['body'] = [
                    $p['page_id'],
                    $p['page_title'].'<div><small>/'.$p['page_url'].'</small></div>',
                    easy_date($p['page_date']).'<div><small>'.T('Updated').': '.easy_date($p['page_update']).'</small></div>',
                    (!empty($p['page_birth'])?'<div><small>'.T('Visible from').': '.easy_date($p['page_birth']).'</small></div>':'').(!empty($p['page_expiry'])?'<div><small>'.T('Visible till').': '.easy_date($p['page_expiry']).'</small></div>':''),
                    $status[ $p['page_status'] ] ?? '',
                    $p['user_name'],
                    _pre('','acts').$f->_edit_html( '#'.$page_type.'_modal', $p, 'div', '', '', '', 'mat-ico', 'edit' )._post()
                ];
            }
            table_view( $page_type, $table, $wrapper_class );
        }
    }

    function page_cards( string $page_type = 'page', string $wrapper_class = '', string|int $cols = 4 ): void {
        $pages = $this->_pages( $page_type );
        $status = $this->page_statuses;
        if( empty( $pages ) ) {
            no_content( 'No '.$page_type.' created yet!', '', $wrapper_class );
        } else {
            $cards = [];
            foreach( $pages as $p ) {
                $cards[] = _card( 'br15', $p['page_title'], '', '/'.$p['page_url'], '', '', $status[ $p['page_status'] ] ?? '', '', [], [], '#'.$page_type.'_modal', $p, 'pages', "page_id = {$p['page_id']}" );
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
            echo $page['page_content'];
        } else if( $show_no_content ) {
            no_content( 'Page not found!' );
        }
    }

    function _pages( string $page_type = 'page' ): array {
        $d = new DB();
        $data = [ 'id', 'date', 'update', 'title', 'url', 'password', 'status', 'birth', 'expiry', 'by' ];
        return $d->select( [ 'pages', [ 'users', 'user_id', 'page_by' ] ], array_merge( prepare_values( $data, 'page_' ), [ 'user_name' ] ), 'page_type = \''.strtolower( $page_type ).'\' && page_status != \'4\'' );
    }

    function _page( string|int $id_url ): array {
        $return = [];
        if( !empty( $id_url ) ) {
            $db = new DB();
            $page = is_numeric( $id_url ) ? $db->select( 'pages', '', "page_id = {$id_url}", 1 ) : $db->select( 'pages', '', "page_url= '{$id_url}'", 1 );
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

    function content_builder( string|array $id, string $label = '', string $placeholder = '', string|null $value = '', string $attrs = '', string|float|int $pre = '', string $post = '' ): void {
        $f = new FORM();
        $f->content_builder( $id, $label, $placeholder, $value, $attrs, $pre, $post );
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
        $f->pre_process( 'data-wrap id="static_widget_form"', 'widgets', 'widget', 'widget_', 2, 2 );
        _r();
        $f->text('name','Widget Name','Ex: Social Widget','','data-widget required',4);
        $f->text('desc','Description','Ex: Displays a social platform sharing widget','','data-widget',4);
        $icon_title = defined( 'ICONS' ) ? ( str_contains( ICONS, 'Material' ) ? 'Material Icon' : ( str_contains( ICONS, 'bootstrap' ) ? 'Bootstrap Icon' : 'Widget Icon' ) ) : 'Widget Icon';
        $f->text('icon',$icon_title,'Ex: lightbulb','','data-widget required',2);
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
                    'icon' => $cw['widget_icon'],
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
                $icon = defined( 'ICONS' ) && !empty( $p['widget_icon'] ) ? ( str_contains( ICONS, 'Material' ) ? _div('mat-ico xxl',$p['widget_icon']) : ( str_contains( ICONS, 'bootstrap' ) ? _el('i','b bi-'.$p['widget_icon']) : $p['widget_icon'] ) ) : '-';
                $cards[] = _card( 'br15', $p['widget_name'], '', $p['widget_desc'], _div('pic',$icon), '', $p['widget_status'] == 1 ? 'Active' : 'Inactive', $p['widget_status'] == 1 ? 'green' : 'grey', [], [], $modal_identity, $p, 'widgets', "widget_id = {$p['widget_id']}" );
            }
            grid_view( 'widget_cards', $cards, $wrapper_class, $cols );
        }
    }

}

function update_menu_ajax(): void {

}

function update_page_ajax(): void {
    $p = replace_in_keys( $_POST, 'p_', '' );
    if( !empty( $p['title'] ) ) {
        unset( $p['pre'] );
        unset( $p['t'] );
        $id = $p['id'] ?? 0;
        unset( $p['id'] );
        $p['content'] = htmlspecialchars( $p['content'] );
        $p['by'] = get_user_id();
        $p['update'] = date('Y-m-d H:i:s');
        $p['url'] = !empty( $p['url'] ) ? $p['url'] : strtolower( str_replace( ' ', '-', $p['title'] ) );
        $p['date'] = $p['date'] ?? date('Y-m-d H:i:s');
        $db = new DB();

        // Check if page exists with same slug
        $exist = $db->select( 'pages', 'page_id', "page_url = {$p['url']}" );
        if( $exist ) {
            ef('Page with same url exist! Please change page title and url!!');
            return;
        }

        $saved = $db->insert( 'pages', prepare_keys( $p, 'page_' ), prepare_values( $p ) );
        if( $saved ) {
            if( !empty( $id ) ) {
                // Update History
                $update = $db->update( 'pages', [ 'page_status', 'page_parent' ], [ 4, $saved ], "page_id = {$id}" );
                if( $update ) {
                    es('Successfully updated page!');
                } else {
                    ef('Failed to update page, Please consult administrator!');
                }
            } else {
                es('Successfully saved new page!');
            }
        } else {
            ef('Failed to store page, please consult administrator!');
        }
        //skel( $p );
    } else {
        ef('Failed due to page title not set!');
    }
}