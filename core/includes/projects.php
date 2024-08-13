<?php

class PROJECTS {

    function __construct() {

    }

    function project_filters(): void {

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

    function client_form( string $class = '', string $data_attr = '', string $wrap_class = '', string $callback = '', string $redirect_url = '' ): void {
        $f = new FORM();
        $form = $this->__client_form_fields();
        $f->pre_process( '', 'process_project_ajax', $data_attr, '', [], 'Successfully saved project!', '', $callback, $redirect_url );
            $f->form( $form, $class, $data_attr, '', $wrap_class );
            $f->process_trigger( 'Save Project', '', '', '', '.tac' );
        $f->post_process();
    }

    function __client_form_fields(): array {
        $cs = get_countries();
        return [
            [ 't' => 'step', 'n' => 'Details', 'fields' => [
                [ 'i' => 'name', 'n' => 'Name', 'p' => 'Ex: Mercedes, Government of Dubai etc.', 'c' => 8 ],
                [ 'i' => 'status', 'n' => 'Status', 'off' => 'Inactive', 'on' => 'Active', 't' => 'slide', 'v' => 1, 'c' => 4 ],
                [ 'i' => 'logo', 'n' => 'Logo', 'p' => 'Upload', 't' => 'file', 's' => .2, 'e' => 'svg,png,jpg,jpeg,gif', 'c' => 4 ],
                [ 'i' => 'rid', 'n' => 'Registration No.', 'p' => 'Ex: 2541-2435-4455', 'c' => 4 ],
                [ 'i' => 'tno', 'n' => 'Tax Reg. No.', 'p' => 'Ex: 2541-2435-4455', 'c' => 4 ],
            ] ],
            [ 't' => 'step', 'n' => 'Address', 'fields' => [
                [ 'i' => 'address', 'n' => 'Address', 'p' => 'Ex: #1501, Rolls Royce Tower', 'c' => 8 ],
                [ 'i' => 'city', 'n' => 'City', 'p' => 'Ex: ', 'c' => 4 ],
                [ 'i' => 'state', 'n' => 'State', 'p' => 'Ex: Washington', 'c' => 4 ],
                [ 'i' => 'country', 'n' => 'Country', 'p' => 'Choose...', 't' => 'select2', 'o' => $cs, 'c' => 4 ],
                [ 'i' => 'zip', 'n' => 'Zip Code', 'p' => 'Ex: 12021', 'c' => 4 ],
                //[ 'i' => 'zone', 'n' => '', 'p' => '', 'c' => 1 ],
            ] ],
            [ 't' => 'step', 'n' => 'Communication', 'fields' => [
                [ 'i' => 'phone_code', 'i2' => 'phone', 'n' => 'Code', 'n2' => 'Phone', 'p2' => 'Ex: 1235 456 8574', 't' => 'phone', 'c' => 6 ],
                [ 'i' => 'fax', 'n' => 'Fax', 'p' => 'Ex: 1220 145 6585', 'c' => 6 ],
                [ 'i' => 'email', 'n' => 'Email', 'p' => 'Ex: www.website.com', 'c' => 6 ],
                [ 'i' => 'website', 'n' => 'Website', 'p' => 'Ex: support@website.com', 'c' => 6 ],
            ] ],
        ];
    }

    function project_form( string $class = '', string $data_attr = '', string $wrap_class = '', string $callback = '', string $redirect_url = '' ): void {
        $f = new FORM();
        $form = $this->__project_form_fields();
        $f->pre_process( '', 'process_project_ajax', $data_attr, '', [], 'Successfully saved project!', '', $callback, $redirect_url );
            $f->form( $form, $class, $data_attr, '', $wrap_class );
            $f->process_trigger( 'Save Project', '', '', '', '.tac' );
        $f->post_process();
    }

    function __project_form_fields(): array {
        $d = new DB();
        $clients = $d->select( 'clients', 'client_id,client_name', 'client_status = "\'1\'"' );
        $clients = array_to_assoc( $clients, 'client_id', 'client_name' );
        $cats = $d->get_option( 'aio_project_categories' );
        $cats = explode( ',', $cats );
        $details = [
            [ 'i' => 'name', 'n' => 'Project Title', 'p' => 'Ex: ABC Mobile App', 'c' => 9 ],
            [ 'i' => 'version', 'n' => 'Scope Version', 'p' => 'Ex: 1.2', 'v' => 1, 'c' => 3 ],
            [ 'i' => 'client', 'n' => 'Choose Client', 'p' => 'Choose...', 't' => 'select2', 'o' => $clients, 'c' => 9 ],
            [ 'i' => 'category', 'n' => 'Choose Category', 'p' => 'Choose...', 't' => 'select2', 'o' => $cats, 'c' => 3 ],
            [ 'i' => 'start', 'n' => 'Start Date', 't' => 'date', 'c' => 6 ],
            [ 'i' => 'expiry', 'n' => 'End Date', 't' => 'date', 'c' => 6 ],
            [ 'i' => 'lead', 'n' => 'Project Lead', 't' => 'select2', 'p' => 'Choose User...', 'o' => [], 'c' => 6 ],
            [ 'i' => 'sponsor', 'n' => 'Sponsor', 't' => 'select2', 'p' => 'Choose User...', 'o' => [], 'c' => 6 ],
        ];
        $feats = $d->get_option('aio_project_features');
        $feats = !empty( $feats ) ? unserialize( $feats ) : [];
        $feat_groups = array_group_by( $feats, 't' );
        $features = [];
        foreach( $feat_groups as $k => $v ) {
            $os = array_to_assoc( $v, 'n', 'n' );
            //skel( $os );
            $features[] = [ 'i' => 'features', 'n' => $k, 'p' => 'Choose...', 'o' => $os, 't' => 'select2', 'm' => 1, 'a' => 'data-array="features"', 'c' => 12 ];
        }
        //skel( $feat_groups );
        return [
            [ 't' => 'step', 'n' => 'Details', 'fields' => $details ],
            [ 't' => 'step', 'n' => 'Features', 'fields' => $features ],
            //[ 't' => 'step', 'n' => 'Scope', 'fields' => [] ],
            //[ 't' => 'step', 'n' => 'Financial', 'fields' => [] ],
        ];
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

    // Managing Tasks

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
            //[ 'i' => 'aio_project_feature_types', 'n' => 'Project Feature Types (,)', 'p' => 'Ex: User Types, 3rd Party Modules...', 't' => 'textarea', 'max' => 1024, 'c' => 12, 'r' => 1, 'v' => $os['aio_project_feature_types'] ?? '' ],
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
    }

}

function process_project_ajax(): void {
    elog( $_POST );
}