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
        $f->pre_process( '', 'clients', $data_attr, 'client_', [], 'Successfully saved client!', '', $callback, $redirect_url );
            $f->form( $form, $class, $data_attr, '', $wrap_class );
            $f->process_trigger( 'Save Project', '', '', '', '.tac' );
        $f->post_process();
    }

    function __client_form_fields(): array {
        $cs = get_countries('iso2');
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
                [ 'i' => 'city', 'n' => 'City / Region', 'p' => 'Ex: Downtown', 'c' => 4 ],
                [ 'i' => 'state', 'n' => 'State', 'p' => 'Ex: Dubai', 'c' => 4 ],
                [ 'i' => 'country', 'n' => 'Country', 'p' => 'Choose...', 't' => 'select2', 'o' => $cs, 'k' => 1, 'c' => 4 ],
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

    function project_form( string $data_attr = '', string $lead_user_type = '', string $sponsor_user_type = '', string $wrap_class = '', string $callback = '', string $redirect_url = '' ): void {
        $f = new FORM();
        $form = $this->__project_form_fields( $lead_user_type, $sponsor_user_type );
        $f->pre_process( '', 'process_project_ajax', $data_attr, '', [], 'Successfully saved project!', '', $callback, $redirect_url );
            $f->form( $form, 'row', $data_attr, '', $wrap_class );
            $f->process_trigger( 'Save Project', '', '', '', '.tac' );
        $f->post_process();
    }

    function __project_form_fields( string $lead_user_type = '', string $sponsor_user_type = '' ): array {
        $d = new DB();
        // Prepare Clients
        $clients = $d->select( 'clients', 'client_id,client_name', 'client_status = \'1\'' );
        $clients = array_to_assoc( $clients, 'client_id', 'client_name' );
        // Prepare Users
        $users = $d->select( 'user', 'user_id,user_name', 'user_status = \'1\' && ( user_type = \''.$lead_user_type.'\' || user_type = \''.$sponsor_user_type.'\' )' );
        $users = array_group_by( $users, 'user_type' );
        $leads = !empty( $users ) ? array_to_assoc( $users[ $lead_user_type ], 'user_id', 'user_name' ) : [];
        $sponsors = !empty( $users ) ? array_to_assoc( $users[ $sponsor_user_type ], 'user_id', 'user_name' ) : [];
        //$ = array_to_assoc( $clients, 'client_id', 'client_name' );
        $os = $d->get_options( 'aio_project_categories,aio_project_scope_statuses' );
        $cats = explode( ',', ( $os['aio_project_categories'] ?? '' ) );
        $statuses = explode( ',', ( $os['aio_project_scope_statuses'] ?? '' ) );
        $intro = [
            [ 'i' => 'name', 'n' => 'Project Title', 'p' => 'Ex: ABC Mobile App', 'c' => 6, 'v' => 'fake_name{{ Project}}' ],
            [ 'i' => 'banner', 'n' => 'Banner', 'p' => 'Upload', 't' => 'file', 's' => .2, 'e' => 'svg,png,jpg,jpeg,gif', 'c' => 3 ],
            [ 'i' => 'category', 'n' => 'Choose Category', 'p' => 'Choose...', 't' => 'select2', 'o' => $cats, 'c' => 3, 'k' => 1 ],
            [ 'i' => 'intro', 'n' => 'Introduction', 'p' => 'Ex: Mobile App for so and so...', 't' => 'textarea', 'c' => 12 ],
            [ 'i' => 'objectives', 'n' => 'Objectives', 'p' => 'Ex: To let users navigate...', 't' => 'textarea', 'c' => 12 ],
        ];
        $specifics = [
            [ 'i' => 'client', 'n' => 'Choose existing Client', 'p' => 'Choose...', 't' => 'select2', 'o' => $clients, 'c' => 6, 'k' => 1 ],
            [ 'i' => 'client_name', 'n' => 'or Client Name', 'p' => 'Ex: Monarch Exports LLC', 'c' => 3 ],
            [ 'i' => 'client_logo', 'n' => 'Client Logo', 'p' => 'Upload', 't' => 'file', 's' => .2, 'e' => 'svg,png,jpg,jpeg,gif', 'c' => 3 ],
            [ 'i' => 'access', 'n' => 'Password', 'p' => 'Ex: ********', 't' => 'password', 'c' => 6 ],
            //[ 'i' => 'version', 'n' => 'Scope Version', 'p' => 'Ex: 1.2', 'v' => 1, 'c' => 6 ],
            [ 'i' => 'start', 'n' => 'Start Date', 't' => 'date', 'c' => 3 ],
            [ 'i' => 'expiry', 'n' => 'End Date', 't' => 'date', 'c' => 3 ],
            [ 'i' => 'lead', 'n' => 'Project Lead', 't' => 'select2', 'p' => 'Choose User...', 'o' => $leads, 'c' => 6, 'k' => 1 ],
            [ 'i' => 'sponsor', 'n' => 'Sponsor', 't' => 'select2', 'p' => 'Choose User...', 'o' => $sponsors, 'c' => 6, 'k' => 1 ],
        ];
        $feats = $d->get_option('aio_project_features');
        $feats = !empty( $feats ) ? unserialize( $feats ) : [];
        $feat_groups = array_group_by( $feats, 't' );
        $features = [];
        foreach( $feat_groups as $k => $v ) {
            $os = array_to_assoc( $v, 'n', 'n' );
            $features[] = [ 'i' => 'features', 'n' => $k, 'p' => 'Choose...', 'o' => $os, 't' => 'select2', 'm' => 1, 'a' => 'data-array="features"', 'c' => 12 ];
        }
        $scope = [
            [ 'i' => 'o', 'n' => 'Order', 'v' => 1, 'c' => 1 ],
            [ 'i' => 'n', 'n' => 'Title', 'p' => 'Ex: Feature One', 'c' => 5 ],
            //[ 'i' => 't', 'n' => 'Est. Hours', 'p' => 'Ex: 4', 'c' => 2 ],
            //[ 'i' => 'sd', 'n' => 'Start Date', 't' => 'date', 'c' => 2 ],
            //[ 'i' => 'ed', 'n' => 'End Date', 't' => 'date', 'c' => 2 ],
            [ 'i' => 'ds', 'n' => 'Description', 'p' => 'Ex: This feature enables...', 'c' => 6 ],
            //[ 'i' => 'p', 'n' => 'Priority', 'p' => 'Choose...', 't' => 'select2', 'o' => $statuses, 'c' => 2 ],
            //[ 'i' => 's', 'n' => 'Status', 'off' => '', 'on' => '', 't' => 'slide', 'v' => 1, 'c' => 2 ],
        ];
        $finances = [
            [ 'i' => 'd', 'n' => 'Description', 'p' => 'Ex: Project Code Handover...', 'c' => 3 ],
            [ 'i' => 't', 'n' => 'Amount', 'p' => 'Ex: 21000...', 'c' => 3 ],
            [ 'i' => 'dt', 'n' => 'Date', 't' => 'date', 'c' => 3 ],
            [ 'i' => 's', 'n' => 'Status', 't' => 'slide', 'off' => 'Unpaid', 'on' => 'Paid', 'c' => 3 ],
        ];
        return [
            [ 't' => 'step', 'n' => 'Intro', 'fields' => $intro ],
            [ 't' => 'step', 'n' => 'Specifics', 'fields' => $specifics ],
            [ 't' => 'step', 'n' => 'Features', 'fields' => $features ],
            [ 't' => 'step', 'n' => 'Scope', 'fields' => [ [ 'f' => $scope, 't' => 'dynamic', 'data' => 'scope' ] ], 'group' => 'scope' ],
            [ 't' => 'step', 'n' => 'Financial', 'fields' => [ [ 'f' => $finances, 't' => 'dynamic', 'data' => 'finances' ] ], 'group' => 'finances'  ],
        ];
    }

    function __projects(): array {
        return [];
    }

    // Project HTML

    function project( int $id ): void {
        echo $this->__project( $id );
    }

    function __project(): string {

        // Project
        //  Stages
        //      Tasks
        return '';
    }

    function project_overview( int $id ): void {
        echo $this->__project_overview( $id );
    }

    function __project_overview( int $id, string $card_class = 'card br15 p20' ): string {
        global $options;
        $ico_class = $options['icon_class'] ?? '';
        $r = __r();
            $r .= __c(8);
                $r .= __d( 'card p0 bsn nf', 'project_overview' );
                    // Progress Header
                    $r .= __d( 'overview_head', '', 'style="background:url(\'https://placehold.it/1000\')"' );
                        $r .= __div( 'status right green bg', 'Active' );
                        $r .= __image( 'https://placehold.it/50', '', 'logo l' );
                        $r .= __h1( 'Adidas', 0 );
                    $r .= d__();
                    // Progress Intro
                    $r .= __d( 'project_intro' );
                        $r .= __h2( 'Website and Mobile App Design', 0, 'subtle' );
                        $r .= __div( 'desc', 'Nulla faucibus malesuada. In placerat feugiat eros ac tempor. Integer euismod massa sapien, et consequat enim laoreet et. Nulla sit amet nisi dapibus, gravida turpis sit amet, accumsan nisl. Fusce vel semper risus. Morbi congue eros sagittis, sodales turpis venenatis, iaculis dui. Proin ac purus sed nibh dapibus neque. scelerisque sed quis ante. Suspendisse potenti.' );
                    $r .= d__();
                    // Progress Stats
                    $r .= __d( 'project_stats' );
                        $r .= __div( 'start', __div( 'k', T('Commence Date') ) . __div( 'v', '15 Aug, 2024' ) );
                        $r .= __div( 'cat', __div( 'k', T('Category') ) . __div( 'v', 'Software' ) );
                        $r .= __div( 'cat', __div( 'k', T('Location') ) . __div( 'v', 'United Arab Emirates' ) );
                        $r .= __div( 'cat', __div( 'k', T('Completion ETA') ) . __div( 'v', '25 Sep, 2024' ) );
                    $r .= d__();
                    // Progress Objectives

                    // Project Stakeholders
                    $r .= __r( 'project_stakes mb20' );
                        $r .= __c( 6 );
                            // Project Lead
                            $r .= __d( 'project_user_set df aic' );
                                $r .= __div( 'pic', '', '', 'style="background: url(\'https://placehold.it/50\')"' );
                                $r .= __div( 'details', __div( 'name', 'Mohammed Ahmed' ) . __div( 'title', 'Project Lead' ) );
                            $r .= d__();
                        $r .= c__();
                        $r .= __c( 6 );
                            // Project Sponsor
                            $r .= __d( 'project_user_set df aic' );
                                $r .= __div( 'pic', '', '', 'style="background: url(\'https://placehold.it/50\')"' );
                                $r .= __div( 'details', __div( 'name', 'John Doe' ) . __div( 'title', 'Project Sponsor' ) );
                            $r .= d__();
                        $r .= c__();
                    $r .= r__();
                    // Progress Overview
                    $r .= __d( 'project_info_set' );
                        $r .= __div( 'top df aic jsb', __div( 'l', 'Progress' ) . __div( 'r', '75%' ) );
                        $r .= __div( 'progress', __div( 'blue', '', '', 'style="width: 75%"' ) );
                        $r .= __div( 'base df aic jsb', __div( 'l', '191 Tasks' ) . __div( 'r', '252 Tasks' ) );
                    $r .= d__();
                    // Timeline Overview
                    $r .= __d( 'project_info_set' );
                        $r .= __div( 'top df aic jsb', __div( 'l', 'Delivery' ) . __div( 'r', '15 of 25 days' ) );
                        $r .= __div( 'progress', __div( 'green', '', '', 'style="width: 75%"' ) );
                        $r .= __div( 'base df aic jsb', __div( 'l', '25th Oct, 2024' ) . __div( 'r', '20th Nov, 2024' ) );
                    $r .= d__();
                    // Financial Overview
                    $r .= __d( 'project_info_set' );
                        $r .= __div( 'top df aic jsb', __div( 'l', 'Financial' ) . __div( 'r', '2 of 4 cheques' ) );
                        $r .= __div( 'progress', __div( 'red', '', '', 'style="width: 50%"' ) );
                        $r .= __div( 'base df aic jsb', __div( 'l', '12,000 AED' ) . __div( 'r', '27,500 AED' ) );
                    $r .= d__();
                $r .= d__();
            $r .= c__();
            $r .= __c(4);
                // Loop Features
                $r .= __d( $card_class . ' project_features' );
                    $r .= __h3( 'Third Party Modules' );
                    $r .= __d( 'features' );
                        // Loop Feature Sets
                        $r .= __d( 'feature_set df aic' );
                            $r .= __div( 'icon', __i( $ico_class . ' mico ico', 'home' ), '', 'style="background:forestgreen"' );
                            $r .= __div( 'details', __h4( 'Maps' ) . __h5( 'Integrate Google Maps' ) );
                        $r .= d__();
                    $r .= d__();
                $r .= d__();
            $r .= c__();
        $r .= r__();
        return $r;
    }

    function project_scope( int $id ): void {
        echo $this->__project_scope( $id );
    }

    function __project_scope( int $id ): string {
        $f = new FORM();
        $statuses = [ 1 => 'Complete', 2 => 'Progress', 3 => 'Pending' ];
        $priorities = [ 1 => 'Urgent', 2 => 'High', 3 => 'Medium', 4 => 'Low' ];
        $users = [ 1 => 'Shaikh', 2 => 'Afnan' ];
        $editable = 1 > 2;
        $r = __d( 'project_scope' );
            // Loop Scope Stage
            $r .= __d( 'scope_set' );
                $r .= __h2( 'Stage 1 - Mockups', 0, 'subtle' );
                $scope_set[] = [ 'thead' => [ '', T('Order'), T('Description'), T('Time'), T('Start'), T('End'), T('Priority'), T('Users'), T('Status') ] ];
                // Loop Scope Data
                    $a = 'disabled';
                    $scope_set[] = [ 'tbody' => [
                        __div( 'view', __div( 'mico s', 'check_circle' ) ) . ( $editable ? __div( 'edit', $f->__checkboxes( 'check', '', [ 'done' => '' ], 0, $a ) ) : '' ),
                        __div( 'view', __div( '', '1.1' ) ) . ( $editable ? __div( 'edit', $f->__text( 'order', '', 'Ex: 1.2', '1.1', $a ) ) : '' ),
                        __div( 'view', __div( 'name', 'Landing Page Mockup' ) . __div( 'desc', 'A page to welcome the user on initial visit' ) ) . ( $editable ? __div( 'edit', $f->__text( 'name', '', 'Ex: Landing Page Mockup...', 'Landing Page Mockup', $a ) . $f->__text( 'desc', '', 'Ex: A page to welcome the user on initial visit...', 'A page to welcome the user on initial visit', $a ) ) : '' ),
                        __div( 'view', __div( '', '4 Days' ) ) . ( $editable ? __div( 'edit', $f->__text( 'duration', '', 'Ex: 4', '4', $a ) . $f->__text( 'duration_unit', '', 'Ex: Hours', 'Days', $a ) ) : '' ),
                        __div( 'view', __div( 's', '15 Aug, 2024' ) ) . ( $editable ? __div( 'edit', $f->__date( 'start', '', '', '2024-08-15', $a, 'bottom center' ) ) : '' ),
                        __div( 'view', __div( 'e', '20 Sep, 2024' ) ) . ( $editable ? __div( 'edit', $f->__date( 'end', '', '', '2024-09-20', $a, 'bottom center' ) ) : '' ),
                        __div( 'view', __div( 'p h', 'High' ) ) . ( $editable ? __div( 'edit', $f->__select( 'priority', '', '', $priorities, 2, $a, '', 1 ) ) : '' ),
                        __div( 'view', __div( 'us', 'Shaikh' ) ) . ( $editable ? __div( 'edit', $f->__select( 'users', '', '', $users, 2, $a, '', 1 ) ) : '' ),
                        __div( 'view', __div( 'status g', 'Done' ) ) . ( $editable ? __div( 'edit', $f->__select( 'status', '', '', $statuses, 2, $a, '', 1 ) ) : '' ),
                    ] ];
                    $scope_set[] = [ 'tbody' => [
                        __div( 'view', __div( 'mico s', 'check_circle' ) ) . ( $editable ? __div( 'edit', $f->__checkboxes( 'check', '', [ 'done' => '' ], 0, $a ) ) : '' ),
                        __div( 'view', __div( '', '1.2' ) ) . ( $editable ? __div( 'edit', $f->__text( 'order', '', 'Ex: 1.2', '1.1', $a ) ) : '' ),
                        __div( 'view', __div( 'name', 'About Page Mockup' ) . __div( 'desc', 'A page to welcome the user on initial visit' ) ) . ( $editable ? __div( 'edit', $f->__text( 'name', '', 'Ex: Landing Page Mockup...', 'Landing Page Mockup', $a ) . $f->__text( 'desc', '', 'Ex: A page to welcome the user on initial visit...', 'A page to welcome the user on initial visit', $a ) ) : '' ),
                        __div( 'view', __div( '', '1 Days' ) ) . ( $editable ? __div( 'edit', $f->__text( 'duration', '', 'Ex: 4', '4', $a ) . $f->__text( 'duration_unit', '', 'Ex: Hours', 'Days', $a ) ) : '' ),
                        __div( 'view', __div( 's', '15 Aug, 2024' ) ) . ( $editable ? __div( 'edit', $f->__date( 'start', '', '', '2024-08-15', $a, 'bottom center' ) ) : '' ),
                        __div( 'view', __div( 'e', '20 Sep, 2024' ) ) . ( $editable ? __div( 'edit', $f->__date( 'end', '', '', '2024-09-20', $a, 'bottom center' ) ) : '' ),
                        __div( 'view', __div( 'p h', 'High' ) ) . ( $editable ? __div( 'edit', $f->__select( 'priority', '', '', $priorities, 2, $a, '', 1 ) ) : '' ),
                        __div( 'view', __div( 'us', 'Shaikh' ) ) . ( $editable ? __div( 'edit', $f->__select( 'users', '', '', $users, 2, $a, '', 1 ) ) : '' ),
                        __div( 'view', __div( 'status o', 'Progress' ) ) . ( $editable ? __div( 'edit', $f->__select( 'status', '', '', $statuses, 2, $a, '', 1 ) ) : '' ),
                    ] ];
                    $scope_set[] = [ 'tbody' => [
                        __div( 'view', __div( 'mico s', 'check_circle' ) ) . ( $editable ? __div( 'edit', $f->__checkboxes( 'check', '', [ 'done' => '' ], 0, $a ) ) : '' ),
                        __div( 'view', __div( '', '1.3' ) ) . ( $editable ? __div( 'edit', $f->__text( 'order', '', 'Ex: 1.2', '1.1', $a ) ) : '' ),
                        __div( 'view', __div( 'name', 'Contact Page Mockup' ) . __div( 'desc', '' ) ) . ( $editable ? __div( 'edit', $f->__text( 'name', '', 'Ex: Landing Page Mockup...', 'Landing Page Mockup', $a ) . $f->__text( 'desc', '', 'Ex: A page to welcome the user on initial visit...', 'A page to welcome the user on initial visit', $a ) ) : '' ),
                        __div( 'view', __div( '', '2 Days' ) ) . ( $editable ? __div( 'edit', $f->__text( 'duration', '', 'Ex: 4', '4', $a ) . $f->__text( 'duration_unit', '', 'Ex: Hours', 'Days', $a ) ) : '' ),
                        __div( 'view', __div( 's', '15 Aug, 2024' ) ) . ( $editable ? __div( 'edit', $f->__date( 'start', '', '', '2024-08-15', $a, 'bottom center' ) ) : '' ),
                        __div( 'view', __div( 'e', '20 Sep, 2024' ) ) . ( $editable ? __div( 'edit', $f->__date( 'end', '', '', '2024-09-20', $a, 'bottom center' ) ) : '' ),
                        __div( 'view', __div( 'p h', 'High' ) ) . ( $editable ? __div( 'edit', $f->__select( 'priority', '', '', $priorities, 2, $a, '', 1 ) ) : '' ),
                        __div( 'view', __div( 'us', 'Shaikh' ) ) . ( $editable ? __div( 'edit', $f->__select( 'users', '', '', $users, 2, $a, '', 1 ) ) : '' ),
                        __div( 'view', __div( 'status o', 'Progress' ) ) . ( $editable ? __div( 'edit', $f->__select( 'status', '', '', $statuses, 2, $a, '', 1 ) ) : '' ),
                    ] ];
                    $scope_set[] = [ 'tfoot' => [
                        '',
                        '',
                        '1 of 2 Done',
                        '6 Days',
                        '01 Aug, 2024',
                        '20 Sep, 2024',
                        '',
                        '',
                        '',
                    ] ];
                $r .= __table( $scope_set, 'project_scope_data' );
            $r .= d__();
        $r .= d__();
        return $r;
    }

    function project_finances( int $id ): void {
        echo $this->__project_finances( $id );
    }

    function __project_finances( int $id, string $card_class = 'card p20 br15' ): string {
        $r = __d( 'project_finance' );
            $r .= __r();
                $r .= __c( 6 );
                    $r .= __div( $card_class, __el( 'canvas', '', '', 'planned_expenses' ) );
                $r .= c__();
                $r .= __c( 6 );
                    $r .= __div( $card_class, __el( 'canvas', '', '', 'project_invoices' ) );
                $r .= c__();
            $r .= r__();
            $table[] = [ 'thead' => [ T('Description'), T('Amount'), T('%'), T('Dated'), T('Status') ] ];
            // Loop Finances
            $table[] = [ 'tbody' => [
                'Initial Payment',
                '25,000 AED',
                '25%',
                '20 Aug, 2024',
                __div( 'status g', 'Paid' )
            ] ];
            $table[] = [ 'tbody' => array(
                'Project Ready - Demo',
                '25,000 AED',
                '25%',
                '10 Sep, 2024',
                __div( 'status r', 'Pending' )
            )];
            $table[] = [ 'tbody' => array(
                'Code Handover',
                '50,000 AED',
                '50%',
                '15 Sep, 2024',
                __div( 'status r', 'Pending' )
            )];
            $r .= __table( $table, 'project_finances' );
        $r .= d__();
        return $r;
    }

    function project_structure( int $id ): void {
        echo $this->__project_structure( $id );
    }

    function __project_structure( int $id, string $card_class = 'card br15 p20' ): string {
        global $options;
        $down = __ico( ( $options['ico_dropdown'] ?? 'expand_more' ), 'sl' );
        $remove = __ico( ( $options['ico_remove'] ?? 'remove_circle_outline' ), 'm' );
        $trash = __ico( ( $options['ico_delete'] ?? 'delete' ), 'sl' );
        $check = __ico( ( $options['ico_check'] ?? 'check_circle' ), 'xs' );
        $uncheck = __ico( ( $options['ico_uncheck'] ?? 'radio_button_unchecked' ), 'xs' ); // __div( $ico . ' ico xs ' . $uncheck, $uncheck );
        $r = __d( 'project_structure' );
            // Loop Structure
            $common_attr = 'data-cont';
            $encoding = 'utf8_unicode_ci';
            $r .= __div( 'df aic jsb', __div( '', __h2( 'Contacts', 0, 'subtle' ) ) . __div( 'df aic g2', __div ( '', 'cont_' ) . $down ) );
            $table[] = [ 'thead' => [ 'ID', 'Name', 'Type', 'Attr', 'Required', 'Max', 'Null', 'Col', 'Actions' ] ];
                // Loop Structure Fields
                $table[] = [ 'tbody' => [ 'name', 'Contact Name', 'Text', '', $check, 256, $uncheck, 6, $remove ] ];
            $r .= __table( $table, 'project_structure' );
            $r .= __div( 'df aic jsb', __div( 'left df g2', __div( 'attr', $common_attr ) . __div( 'encoding', $encoding ) ) . $trash );
        $r .= d__();
        return $r;
    }

    function project_issues( int $id ): void {
        echo $this->__project_issues( $id );
    }

    function __project_issues( int $id, string $card_class = 'card br15' ): string {
        global $options;
        $down = __ico( $options['ico_download'] ?? 'download_for_offline' );
        $image = __ico( $options['ico_image'] ?? 'image' );
        $attach = __ico( $options['ico_attachment'] ?? 'attachment' );
        $url = __ico( $options['ico_url'] ?? 'link' );
        $dropdown = __ico( $options['ico_dropdown'] ?? 'expand_more', 'sm' );
        $edit = __ico( $options['ico_edit'] ?? 'edit' );
        $trash = __ico( $options['ico_delete'] ?? 'delete' );
        $issue_statuses = [ 'pending' => 'Pending', 'progress' => 'In Progress', 'verify' => 'To Verify', 'done' => 'Completed' ];
        $issue_head = __div( 'df aic', __h4( 'Frontend', 0, 'title' ) . __div( 'status red r', 'Pending' ) );
        $issue_body = __div( 'df aic jsb', __div( 'issue', 'Unable to view user profile in the interface designated for profile area' ) . $dropdown );
        $issue_foot = __div( 'df aic jsb', __div( 'acts', $down . $image . $attach . $url ) . __div( 'acts', $edit . $trash ) );
        $issue = __div( $card_class . ' issue_card p0', __div( $card_class . ' upper nf p0', __div( 'top', $issue_head ) . __div( 'body', $issue_body ) ) . __div( 'foot', $issue_foot ) );
        $r = __d( 'project_issues' );
            // Loop Statuses
            foreach ( $issue_statuses as $status => $status_label ) {
                $r .= __d( $status ) . __h2( $status_label, 1, 'status_title' );
                    $r .= __d( 'issues_list' );
                        // Loop Issues
                        $r .= $issue;
                    $r .= d__();
                $r .= d__();
            }
        $r .= d__();
        return $r;
    }

    function project_board( int $id ): void {
        echo $this->__project_board( $id );
    }

    function __project_board( int $id ): string {
        return '';
    }

    function project_timeline( int $id ): void {
        echo $this->__project_timeline( $id );
    }

    function __project_timeline( int $id ): string {
        return '';
    }

    // Project JSON

    function _project_overview( int $id ): array {
        return [];
    }

    function _project_scope( int $id ): array {
        return [];
    }

    function _project_finances( int $id ): array {
        return [];
    }

    function _project_structure( int $id ): array {
        return [];
    }

    function _project_issues( int $id ): array {
        return [];
    }

    function _project_board( int $id ): array {
        return [];
    }

    function _project_timeline( int $id ): array {
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

    function __tasks(): array {
        return [];
    }

    function __task(): array {
        return [];
    }

    function update_task(): array {
        return [];
    }

    function options(): void {
        $o = new OPTIONS();
        $db = new DB();
        $os = $db->get_options('aio_project_feature_types,aio_project_scope_statuses,aio_project_categories');
        $form = [
            //[ 'i' => 'aio_project_feature_types', 'n' => 'Project Feature Types (,)', 'p' => 'Ex: User Types, 3rd Party Modules...', 't' => 'textarea', 'max' => 1024, 'c' => 12, 'r' => 1, 'v' => $os['aio_project_feature_types'] ?? '' ],
            [ 'i' => 'aio_project_scope_statuses', 'n' => 'Project Scope Statuses (,)', 'p' => 'Ex: Critical, High, Normal...', 't' => 'textarea', 'max' => 1024, 'c' => 12, 'r' => 1, 'v' => $os['aio_project_scope_statuses'] ?? '' ],
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