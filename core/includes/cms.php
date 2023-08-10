<?php
// TODO: Page Management System + Routes
// TODO: Menu Designer
// TODO: Content Editor / Website Elements Designer - Custom (Refer to VvvebJs, Novi)
// TODO: Template Manager

class CMS {

    public array $page_statuses = [ 1 => 'Active', 2 => 'Inactive', 3 => 'Draft', 4 => 'History' ];

    function __construct() {

    }

    function page_modal(): void {
        $c = new CODE();
        $f = new FORM();
        $publish_fields = [
            [ 'id' => 'title', 'title' => 'Page Title' ],
            [ 'id' => 'url', 'title' => 'Hyperlink', 'p' => 'Ex: procedure-to-register', 'a' => 'data-no-space' ],
        ];
        $visibility_fields = [
            [ 't' => 'date', 'id' => 'birth', 'n' => 'Visible From', 'c' => 6 ],
            [ 't' => 'date', 'id' => 'expiry', 'n' => 'Visible Till', 'c' => 6 ],
            [ 'type' => 'select', 'id' => 'status', 'title' => 'Page Status' ]
        ];
        $seo_fields = [
            [ 't' => 'textarea', 'id' => 'desc', 'n' => 'Meta Description' ],
            [ 't' => 'textarea', 'id' => 'desc', 'n' => 'Meta Keywords' ],
            [ 'id' => 'author', 'n' => 'Meta Author' ],
        ];
        $r = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz"), 0, 8);
        //$c->pre_modal( 'Page', 'f' );
        $f->pre_process( 'data-wrap', 'update_page_ajax', $r, 'page', 2, 2 );
        _r();
        _c(8);
        $f->form( [ [ 't' => 'textarea', 'id' => 'content', 'n' => 'Page Content' ] ], '', $r );
        c_();
        _c(4);
        accordion( 'Identity Options', $f->_form( $publish_fields ), 'br15 w on' );
        accordion( 'Visibility Options', $f->_form( $visibility_fields, 'row', $r ), 'br15 w on' );
        accordion( 'SEO Options', $f->_form( $seo_fields, 'row', $r ), 'br15 w' );
        $f->process_trigger('Save Page','w r');
        c_();
        r_();
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

}