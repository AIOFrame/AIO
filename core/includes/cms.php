<?php
// TODO: Page Management System + Routes
// TODO: Menu Designer
// TODO: Content Editor / Website Elements Designer - Custom (Refer to VvvebJs, Novi)
// TODO: Template Manager

class CMS {

    public array $page_statuses = [ 1 => 'Active', 2 => 'Inactive', 3 => 'Draft', 4 => 'History' ];

    function __construct() {

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