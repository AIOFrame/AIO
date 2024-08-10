<?php

class PROJECTS {

    function __construct() {

    }

    function project_filters(): void {

    }

    function project_modal(): void {
        // TODO: Render Project Modal
    }

    function projects(): void {
        // TODO Render Projects Table
        // TODO Render Project Cards
    }

    function project(): void {

    }

    function project_overview(): void {

    }

    function __scope(): array {
        return [];
    }

    function scope(): void {

    }

    function __scope_form( string $class = '' ): array {
        return [];
    }

    function __project_form( string $class = '' ): array {
        $form = [

        ];
        return [];
    }

    function project_board(): void {

    }

    function project_timeline(): void {

    }

    function project_structure(): void {

    }

    function project_issues(): void {

    }

    function project_finances(): void {

    }

    function project_communication(): void {

    }

    function get_projects(): array {
        return [];
    }

    function get_project(): array {

        // Project
        //  Stages
        //      Tasks
        return [];
    }

    function get_project_overview(): array {
        return [];
    }

    function get_project_scope(): array {
        return [];
    }

    function get_project_board(): array {
        return [];
    }

    function get_project_timeline(): array {
        return [];
    }

    function get_project_structure(): array {
        return [];
    }

    function get_project_issues(): array {
        return [];
    }

    function get_project_finances(): array {
        return [];
    }

    function update_project(): array {
        return [];
    }

    function tasks(): void {
        // TODO: Tasks Table
        // TODO: Task Cards
        // TODO: Task Kanban
    }

    function get_tasks(): array {
        return [];
    }

    function get_task(): array {
        return [];
    }

    function update_task(): array {
        return [];
    }

    function options(): void {
        $o = new OPTIONS();
        $db = new DB();
        $os = $db->get_options('aio_project_feature_types,aio_project_categories');
        $form = [
            [ 'i' => 'aio_project_feature_types', 'n' => 'Project Feature Types (,)', 'p' => 'Ex: User Types, 3rd Party Modules...', 't' => 'textarea', 'max' => 1024, 'c' => 12, 'r' => 1, 'v' => $os['aio_project_feature_types'] ?? '' ],
            [ 'i' => 'aio_project_categories', 'n' => 'Project Categories (,)', 'p' => 'Ex: Information Technology, Construction...', 't' => 'textarea', 'max' => 1024, 'c' => 12, 'r' => 1, 'v' => $os['aio_project_categories'] ?? '' ],
        ];
        $o->form( $form, 'row' );
        $struct = [
            [ 'i' => 'n', 'n' => 'Feature Name', 'p' => 'Ex: Payment Gateway', 'c' => 6 ],
            [ 'i' => 'd', 'n' => 'Description', 'p' => 'Ex: Lets visitors make financial transactions', 'c' => 6 ],
            [ 'i' => 'i', 'n' => 'Icon', 'p' => 'Ex: home', 'c' => 3 ],
            [ 'i' => 'c', 'n' => 'Color', 'p' => 'Ex: #000000', 't' => 'color', 'c' => 3 ],
            [ 'i' => 't', 'n' => 'Category', 'p' => 'Select...', 'o' => $os['aio_project_feature_types'] ?? [], 'c' => 3 ],
            [ 'i' => 's', 'n' => 'Status', 'off' => 'Inactive', 'on' => 'Active', 't' => 'slide', 'c' => 3 ],
        ];
        $o->form( $struct, 'dynamic', 0, 'aio_project_features' );
        // TODO: Options dynamic table
        //data_table( 'project_feats', $struct, '', [ '' ] );
    }

}