<?php
// TODO: Page Management System + Routes
// TODO: Menu Designer
// TODO: Content Editor / Website Elements Designer - Custom (Refer to VvvebJs, Novi)
// TODO: Template Manager

class CMS {

    public array $page_statuses = [ 1 => 'Live', 2 => 'Disabled', 3 => 'Draft', 4 => 'History' ];

    function __construct() {

    }

    function page_form( bool $modal = true ): void {
        $c = new CODE();
        $f = new FORM();
        $statuses = $this->page_statuses;
        unset( $statuses[4] );
        $publish_fields = [
            [ 'id' => 'title', 'title' => 'Page Title', 'a' => 'required' ],
            [ 'id' => 'url', 'title' => 'URL Slug', 'p' => 'Ex: procedure-to-register', 'a' => 'data-no-space', 'c' => 12.1 ],
        ];
        $visibility_fields = [
            [ 't' => 'date', 'id' => 'birth', 'n' => 'Visible From', 'c' => 6 ],
            [ 't' => 'date', 'id' => 'expiry', 'n' => 'Visible Till', 'c' => 6 ],
            [ 'type' => 'select', 'id' => 'status', 'title' => 'Page Status', 'o' => $statuses, 'v' => 1, 'a' => 'required', 'k' => 1 ],
            [ 'id' => 'password', 'n' => 'Password', 'c' => 12.1 ],
        ];
        $seo_fields = [
        [ 't' => 'textarea', 'id' => 'meta_desc', 'n' => 'Meta Description' ],
            [ 't' => 'textarea', 'id' => 'meta_words', 'n' => 'Meta Keywords' ],
            [ 'id' => 'meta_author', 'n' => 'Meta Author', 'c' => 12.1 ],
        ];
        $r = $f->_random();
        $modal ? $c->pre_modal( 'Page', 'f' ) : '';
        $f->pre_process( 'data-wrap id="page_form"', 'update_page_ajax', $r, 'p_', 2, 2 );
        _r();
        _c(8);
        $f->form( [ [ 't' => 'textarea', 'id' => 'content', 'n' => 'Page Content' ] ], '', $r );
        c_();
        _c(4);
        accordion( 'Identity', $f->_form( $publish_fields, 'row', $r ), 'br15 w on' );
        accordion( 'Visibility', $f->_form( $visibility_fields, 'row', $r ), 'br15 w on' );
        accordion( 'SEO', $f->_form( $seo_fields, 'row', $r ), 'br15 w' );
        $f->process_trigger('Save Page','w r');
        c_();
        r_();
        $hidden_fields = [
            [ 'id' => 'date', 'a' => 'class="dn"', 'v' => date('Y-m-d H:i:s') ],
            [ 'id' => 'id', 'a' => 'class="dn"' ],
        ];
        $f->form( $hidden_fields, 'row', $r );
        $f->post_process();
        $modal ? $c->post_modal() : '';
    }

    function page_filters(): void {

    }

    function pages( string $type = 'table', string $wrapper_class = '' ): void {
        $d = new DB();
        $status = $this->page_statuses;
        $data = [ 'id', 'date', 'update', 'title', 'url', 'password', 'status', 'birth', 'expiry', 'by' ];
        $pages = $d->select( [ 'pages', [ 'users', 'user_id', 'page_by' ] ], array_merge( prepare_values( $data, 'page_' ), [ 'user_name' ] ) );
        if( empty( $pages ) ) {
            no_content( 'No pages created yet!', );
        } else {
            $c = new CODE();
            $f = new FORM();
            if( $type == 'table' ) {
                $table[] = [ 'head' => [ 'ID', 'Name', 'Date', 'Visibility', 'Status', 'User', 'Actions' ] ];
                foreach( $pages as $p ) {
                    $table[]['body'] = [
                        $p['page_id'],
                        $p['page_title'].'<div><small>'.$p['page_url'].'</small></div>',
                        easy_date($p['page_date']).'<div><small>'.T('Updated').': '.easy_date($p['page_update']).'</small></div>',
                        (!empty($p['page_birth'])?'<div><small>'.T('Visible from').': '.easy_date($p['page_birth']).'</small></div>':'').(!empty($p['page_expiry'])?'<div><small>'.T('Visible till').': '.easy_date($p['page_expiry']).'</small></div>':''),
                        $status[ $p['page_status'] ] ?? '',
                        $p['user_name'],
                        $f->_edit_html( '#page_modal', $p, 'div', '', '', '', 'mat-ico', 'edit' )
                    ];
                }
                $c->table( $table, $wrapper_class );
            } else if( $type == 'cards' ) {

            }
            // TODO: Page Cards
        }
    }

    function page_editor(): void {
        // TODO: JS Page Editor
        // TODO: JS Page Templates
    }

    function page(): void {
        // TODO: Page HTML
    }

    function get_pages(): array {
        // TODO: Returns Pages Array
        return [];
    }

    function get_page(): array {
        // TODO: Returns Page Content Array
        return [];
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