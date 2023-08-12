<?php
// TODO: Page Management System + Routes
// TODO: Menu Designer
// TODO: Content Editor / Website Elements Designer - Custom (Refer to VvvebJs, Novi)
// TODO: Template Manager

class CMS {

    public array $page_statuses = [ 1 => 'Live', 2 => 'Disabled', 3 => 'Draft', 4 => 'History' ];

    function __construct() {

    }

    function page_modal(): void {
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
        //$c->pre_modal( 'Page', 'f' );
        $f->pre_process( 'data-wrap', 'update_page_ajax', $r, 'p_', 2, 2 );
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
        //$c->post_modal();
    }

    function pages(): void {
        // TODO: Pages Table
        // TODO: Page Cards
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
        $saved = $db->insert( 'pages', prepare_keys( $p, 'page_' ), prepare_values( $p ) );
        if( $saved ) {
            if( !empty( $id ) ) {
                // Update History
                $update = $db->update( 'pages', [ 'page_status' ], [ 4 ], "page_id = {$id}" );
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