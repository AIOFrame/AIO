<?php
// TODO: Page Management System + Routes
// TODO: Menu Designer
// TODO: Content Editor / Website Elements Designer - Custom (Refer to VvvebJs, Novi)
// TODO: Template Manager

class CMS {

    public array $page_statuses = [ 1 => 'Publicly Visible', 2 => 'Disabled', 3 => 'Draft', 4 => 'History' ];

    function __construct() {

    }

    function page_form( string $modal_class = '', string $page_type = 'page' ): void {
        $c = new CODE();
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
        !empty( $modal_class ) ? $c->pre_modal( $page_type, $modal_class ) : '';
        $f->pre_process( 'data-wrap id="'.$page_type.'_form"', 'update_page_ajax', $r, 'p_', 2, 2, [ 'page_type' => strtolower( $page_type ) ] );
        _r();
        _c(8);
        $f->form( [ [ 't' => 'textarea', 'id' => 'content', 'n' => ucwords( $page_type ).' Content' ] ], '', $r );
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
        !empty( $modal_class ) ? $c->post_modal() : '';
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
            $c = new CODE();
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
                    $c->_pre('','acts').$f->_edit_html( '#'.$page_type.'_modal', $p, 'div', '', '', '', 'mat-ico', 'edit' ).$c->_post()
                ];
            }
            $c->table_view( $page_type, $table, $wrapper_class );
        }
    }

    function page_cards( string $page_type = 'page', string $wrapper_class = '', string|int $cols = 4 ): void {
        $pages = $this->_pages( $page_type );
        $status = $this->page_statuses;
        if( empty( $pages ) ) {
            no_content( 'No '.$page_type.' created yet!', '', $wrapper_class );
        } else {
            $c = new CODE();
            $f = new FORM();
            $cards = '';
            foreach( $pages as $p ) {
                $cards .= $c->_card( '4', 'br15', $p['page_title'], '', '/'.$p['page_url'], '', '', $status[ $p['page_status'] ] ?? '', '', [], [], '#'.$page_type.'_modal', $p, 'pages', "page_id = {$p['page_id']}" );
            }
            $c->grid_view( $page_type, $cards, $wrapper_class, $cols );
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
        return $d->select( [ 'pages', [ 'users', 'user_id', 'page_by' ] ], array_merge( prepare_values( $data, 'page_' ), [ 'user_name' ] ), 'page_type = \''.strtolower( $page_type ).'\'' );
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

    function get_menu(): array {
        // TODO: Returns Menu Array
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