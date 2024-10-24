<?php
/**
 * EMS: Employee Management System
 */

class EMS {

    public $employees = [
        [
            'n' => 'Mohammed',
            'd' => 'Design Director',
            'p' => 'https://placehold.it/50',
            'a' => 1,
        ],
        [
            'n' => 'Manfredi',
            'd' => 'Frontend Developer',
            'p' => 'https://placehold.it/50',
            'a' => 0,
        ],
        [
            'n' => 'Louis',
            'd' => 'Marketing Manager',
            'p' => 'https://placehold.it/50',
            'a' => 1,
        ],
        [
            'n' => 'Jerry',
            'd' => 'HR Manager',
            'p' => 'https://placehold.it/50',
            'a' => 1,
        ],
        [
            'n' => 'Ronald Lee',
            'd' => 'Accounts',
            'p' => 'https://placehold.it/50',
            'a' => 1,
        ]
    ];

    function __construct() {

    }

    function employee_filters(): void {

    }

    function employees(): void {
        // TODO: Employees List
        // TODO: Employee Cards
    }

    function employee(): void {
        // TODO: Employee Viewer
    }

    function employee_modal(): void {
        // TODO: Employee Modal
    }

    function __employees(): array {
        return [];
    }

    function __employee(): array {
        return [];
    }

    function contract_filters(): void {

    }

    function contracts(): void {
        // TODO: Contracts List
        // TODO: Contract Cards
    }

    function contract(): void {
        // TODO: Contract Viewer
    }

    function contract_form(): void {
        // TODO: Contract Form
    }

    function _contracts( int $count = 12, int $page = 0, string $where = '' ): array {
        return [];
    }

    function _contract( int $id ): array {
        return [];
    }

    // Contract Types

    function contract_types( string $edit_wrap = '', int $count = 12, int $page = 0, string $where = '' ): void {
        echo $this->__contract_types( $edit_wrap, $count, $page, $where );
    }

    function _contract_types( string $edit_wrap = '', int $count = 12, int $page = 0, string $where = '' ): array {
        $d = new DB();
        return $d->select( 'aio_contract_types', [], $where, $count, $page );
    }

    function __contract_types( string $edit_wrap = '', int $count = 12, int $page = 0, string $where = '' ): string {
        $cts = $this->_contract_types( $edit_wrap, $count, $page, $where );
        $r = '';
        $f = new FORM();
        if( !empty( $cts ) ) {
            foreach( $cts as $c ) {
                skel( $c );
            }
        }
        $r .= __d( 'aio_contract_type card br15 p30' )
        . __div( 'status t r g', 'Active' )
        . __r( 'aic' )
            . __c( 3 )
                . __h2( 'UAE - General Contract', 0 )
                . __div( 'desc mb20', 'Full Time Contract' )
            . c__()
            . __c( 6, 'df aic jcc' )
                . __mini_stat( 265, 'Work Days / Year', 'event', '', 'al p' )
                . __mini_stat( 42, 'Leaves / Year', 'free_cancellation', '', 'al r' )
                . __mini_stat( 40, 'Work Hours / Week', 'schedule', '', 'al o' )
            . c__()
            . __c( 3 )
                . __d( 'acts' )
                    . ( !empty( $edit_wrap ) ? $f->__edit_html( $edit_wrap ) : '' )
                    . $f->__trash_html( 'aio_contract_types', 'con_type_id = 1' )
                . d__()
            . c__()
        . r__() . d__();
        return $r;
    }

    function contract_type_form( bool $show_med_ins = true, bool $show_life_ins = false, bool $show_emp_ins = false ): void {
        echo $this->__contract_type_form( $show_med_ins, $show_life_ins, $show_emp_ins );
    }

    /**
     * @param bool $show_med_ins Show Medical Insurance Fields ?
     * @param bool $show_life_ins Show Life Insurance Fields ?
     * @param bool $show_emp_ins Show Employment Insurance Fields ?
     * @return string
     */
    function __contract_type_form( bool $show_med_ins = true, bool $show_life_ins = false, bool $show_emp_ins = false ): string {
        $f = new FORM();
        $i = [];
        if( $show_med_ins ) {
            $i[] = [ 't' => 'h4', 'n' => 'Medical Insurance' ];
            $i[] = [ 'i' => 'mi', 'n' => 'Provided ?', 't' => 'slide', 'off' => 'No', 'on' => 'Yes', 'c' => 3 ];
            $i[] = [ 'i' => 'mi_term', 'n' => 'Duration (m)', 't' => 'range', 'min' => 3, 'v' => 12, 'max' => 24, 'c' => 3 ];
            $i[] = [ 'i' => 'mi_cost', 'n' => 'Total Cost', 'p' => 'Ex: 2000', 'c' => 3 ];
            $i[] = [ 'i' => 'mi_alert', 'n' => 'Expiry Alerts', 't' => 'slide', 'c' => 3 ];
        }
        if( $show_life_ins ) {
            $i[] = [ 't' => 'h4', 'n' => 'Life Insurance' ];
            $i[] = [ 'i' => 'li', 'n' => 'Provided ?', 't' => 'slide', 'off' => 'No', 'on' => 'Yes', 'c' => 3 ];
            $i[] = [ 'i' => 'li_term', 'n' => 'Duration (m)', 't' => 'range', 'min' => 3, 'v' => 12, 'max' => 24, 'c' => 3 ];
            $i[] = [ 'i' => 'li_cost', 'n' => 'Total Cost', 'p' => 'Ex: 2000', 'c' => 3 ];
            $i[] = [ 'i' => 'li_alert', 'n' => 'Expiry Alerts', 't' => 'slide', 'c' => 3 ];
        }
        if( $show_emp_ins ) {
            $i[] = [ 't' => 'h4', 'n' => 'Loss of Employment Insurance' ];
            $i[] = [ 'i' => 'ei', 'n' => 'Provided ?', 't' => 'slide', 'off' => 'No', 'on' => 'Yes', 'c' => 3 ];
            $i[] = [ 'i' => 'ei_term', 'n' => 'Duration (m)', 't' => 'range', 'min' => 3, 'v' => 12, 'max' => 24, 'c' => 3 ];
            $i[] = [ 'i' => 'ei_cost', 'n' => 'Total Cost', 'p' => 'Ex: 2000', 'c' => 3 ];
            $i[] = [ 'i' => 'ei_alert', 'n' => 'Expiry Alerts', 't' => 'slide', 'c' => 3 ];
        }
        $days = [ T('Monday'), T('Tuesday'), T('Wednesday'), T('Thursday'), T('Friday'), T('Saturday'), T('Sunday') ];
        $form = [
            [ 'n' => 'Details', 't' => 'step', 'fields' => [
                [ 'i' => 'title', 'n' => 'Contract Name', 'p' => 'Ex: Full Time Contract', 'c' => 10 ],
                [ 'i' => 'renew_alert', 'n' => 'Expiry Alert', 't' => 'slide', 'c' => 2 ],
                [ 'i' => 'desc', 'n' => 'Description', 'p' => 'Ex: Contract specific to GCC...', 'c' => 12 ],
            ] ],
            [ 'n' => 'Days and Hours', 't' => 'step', 'fields' => [
                [ 'i' => 'title', 'n' => 'Working Days', 'd' => 'Check working days and uncheck holidays', 'p' => 'Select Days...', 't' => 'checkboxes', 'o' => $days, 'c' => 12, 'iw' => 'row', '_i' => 3 ],
            ] ],
            //[ 'n' => 'Work Hours', 't' => 'step', 'fields' => [

            //] ],
            [ 'n' => 'Visa', 't' => 'step', 'fields' => [
                [ 't' => 'h4', 'n' => 'Employment Visa' ],
                [ 'i' => 'visa_name', 'n' => 'Visa Name', 'p' => 'Ex: Employment Visa...', 'c' => 12 ],
                [ 'i' => 'visa', 'n' => 'Provided ?', 't' => 'slide', 'off' => 'No', 'on' => 'Yes', 'c' => 3 ],
                [ 'i' => 'visa_term', 'n' => 'Duration (m)', 't' => 'range', 'min' => 3, 'v' => 12, 'max' => 24, 'c' => 3 ],
                [ 'i' => 'visa_cost', 'n' => 'Total Cost', 'p' => 'Ex: 2000', 'c' => 3 ],
                [ 'i' => 'visa_alert', 'n' => 'Expiry Alert', 't' => 'slide', 'c' => 3 ]
            ] ],
            [ 'n' => 'Insurance', 't' => 'step', 'fields' => $i ],
            [ 'n' => 'Facilities', 't' => 'step', 'fields' => [
                [ 'f' => [
                    [ 'i' => 'name', 'n' => 'Title', 'p' => 'Ex: Transportation...', 'c' => 6 ],
                    [ 'i' => 'icon', 'n' => 'Icon', 'p' => 'Ex: car', 'c' => 3 ],
                    [ 'i' => 'color', 'n' => 'Color', 'p' => 'Ex: #000000', 'c' => 3, 't' => 'color' ],
                    [ 'i' => 'desc', 'n' => 'Facility Description', 'p' => 'Ex: A rental car will be provided...', 'c' => 12 ],
                ], 't' => 'dynamic', 'data' => 'terms' ]
            ] ],
            [ 'n' => 'Terms', 't' => 'step', 'fields' => [
                [ 'f' => [
                    [ 'i' => 'name', 'n' => 'Title', 'p' => 'Ex: Commitment...', 'c' => 6 ],
                    [ 'i' => 'icon', 'n' => 'Icon', 'p' => 'Ex: home', 'c' => 3 ],
                    [ 'i' => 'color', 'n' => 'Color', 'p' => 'Ex: #000000', 'c' => 3, 't' => 'color' ],
                    [ 'i' => 'desc', 'n' => 'Term Description', 'p' => 'Ex: Write terms in detail...', 'c' => 12 ],
                ], 't' => 'dynamic', 'data' => 'terms' ]
            ] ],
        ];
        return $f->__form( $form );
    }

    function organization(): void {
        // TODO: Renders organization management
    }

    function expense_filters(): void {

    }

    function expenses(): void {
        // TODO: Expenses List
        // TODO: Associate with projects if feature is active
    }

    function expense(): void {
        // TODO: Expense View
    }

    function expense_modal(): void {
        // TODO: Expense Modal
        // TODO: Associate with projects if feature is active
    }

    function options(): void {
        // TODO: Implement Employee Management Options
        // TODO: Implement EMS Regional Options
        $o = new OPTIONS();

        // Attendance Options
        // Working Days of Week
        // Week Starts on
    }

    function options_resources(): void {
        $o = new OPTIONS();
        $f = [
            [ '' ]
        ];
    }

    function options_contracts(): void {
        $o = new OPTIONS();
    }

    function options_ems(): void {
        $o = new OPTIONS();
    }

    function options_emails(): void {
        $o = new OPTIONS();
        // Emails to HR
            // Email on document expiry
            // Email on visa expiry
            // Email on eid expiry
            // Email on medical insurance expiry
        // Emails to Management
        // Emails to Team Leader
        // Emails to Employees
    }

    function attendance(): void {
        echo $this->__attendance();
    }

    function __attendance(): string {
        // Prerequisites
        $f = new FORM();
        $d = new DB();
        $ran = rand( 1, 99999 );
        $months = [ 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ];
        $employees = $this->employees;
        global $options;
        $ico = $options['ico_class'] ?? 'mico';
        $present_ico = $options['attendance_present_ico'] ?? 'check_circle';
        $absent_ico = $options['attendance_absent_ico'] ?? 'do_disturb_on';
        $present_ico = __div( $ico . ' ico ' . $present_ico, $present_ico );
        $absent_ico = __div( $ico . ' ico ' . $absent_ico, $absent_ico );

        // View
        $r = __get_styles( 'ems/ems' ) . __d( 'aio_attendance_view', 'aio_attendance_view' )
        . __d( 'aio_attendance_head' ) . __r() . __c( 6 ) . __tab_heads( [ '.day_view_'.$ran => 'Day', '.week_view_'.$ran => 'Week', '.year_view_'.$ran => 'Year' ], 'material mb20', '', 1 ) . c__() . __c( 6, 'tar' ) . $f->__date( 'date', '', '', date('Y-m-d'), '', 'bottom right' ) . c__() . d__()
        . __d( 'aio_attendance_body' )

        // Day View
        . __d( 'day_view_'.$ran ) . __r();
            $present = $sick = $vacation = 0;
            foreach( $employees as $e ) {
                $e['a'] == 1 ? $present++ : '';
            };
            $absent = count( $employees ) - $present;
            $r .= __c( 3 ) . __div( 'widget stat', T('Attending') . __el('span','count green',$present) ) . c__()
            . __c( 3 ) . __div( 'widget stat', T('Sick') . __el('span','count orange',$sick) ) . c__()
            . __c( 3 ) . __div( 'widget stat', T('On Vacation') . __el('span ','count pink',$vacation) ) . c__()
            . __c( 3 ) . __div( 'widget stat', T('Absent') . __el('span','count red',$absent) ) . c__();
            foreach( $employees as $e ) {
                $r .= __c( 3 ) . __card( 'employee_day_card br15', ($e['n'] ?? ''), '', ($e['d'] ?? ''), ($e['p'] ?? ''), 'pic', T( $e['a'] ? 'Present' : 'Absent' ), ( $e['a'] ? 'green' : 'red' ) . ' r' ) . c__();
            };
        $r .= r__() . d__()

        // Week View
        . __d( 'dn week_view_'.$ran );
            $week_start = strtotime('next Monday -1 week');
            $week_start = date('w', $week_start)==date('w') ? strtotime(date("Y-m-d",$week_start)." +7 days") : $week_start;
            $week_end = strtotime(date("Y-m-d",$week_start)." +6 days");
            $days_of_week = [];
            for( $x = 0; $x < 5; $x++ ) {
                $days_of_week[] = strtotime(date("Y-m-d",$week_start)." +$x days");
            }
            $table_heads = [ 'Name', '%' ];
            foreach( $days_of_week as $dw ) {
                $table_heads[] = date( 'd M', $dw ) . __div( '', date( 'D', $dw ) );
            }
            $table = [
                [ 'head' => $table_heads ],
            ];
            foreach( $employees as $e ){
                $table[] = [ 'body' => [ $this->__user_line( ( $e['n'] ?? '' ), ($e['p'] ?? ''), 'Director of Finance' ), '100%', $present_ico, $absent_ico, $present_ico, $present_ico, $present_ico ] ];
            }
            $table[] = [ 'foot' => [ count( $employees ) . ' ' . T('Employees'), '100%', '100%', '80%', '100%', '100%', '100%' ] ];
            $r .= __table( $table, 'r15 tac' )
        . d__()

        // Year View
        . __d( 'dn year_view_'.$ran ) . __r();
            foreach( $months as $m ) {
                $r .= __c( 3 ) . __card( 'br15 mb20', $m, '', '98%', '', '', '', '', [ [ 'Employees', 45 ], [ 'Working Days', 24 ], [ 'Total Days', 1080 ], [ 'Present Days', 990 ], [ 'Absent Days', 90 ] ], 's' ) . c__();
            }
        $r .= r__() . d__()

        . d__() . d__();
        return $r;
    }

    function leaves(): void {
        echo $this->__leaves();
    }

    function __leaves(): string {
        $employees = $this->employees;
        $data[] = [ 'head' => [ 'Employees', '%', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ] ];
        foreach( $employees as $e ) {
            $sub_data = [ $this->__user_line( $e['n'] ?? '', 'https://placehold.it/50', 'Director of Finance' ), '95%' ];
            for( $x = 0; $x < 12; $x++ ) {
                $r = rand( 28, 100 );
                $sub_data[] = __div( 'leaves_stat', __div( 'per', $r . '%' ) . __div( 'progress', __div( 'bar ' . ( $r < 60 ? ( $r < 30 ? 'r' : 'o' ) : 'g' ), '', '', 'style="width:'.$r.'%"' ) ) . __div( 'days', '22/22 Days' ) );
            }
            $data[] = [ 'body' => $sub_data ];
        }
        return __get_styles('ems/ems,ems/leaves') . __d( 'aio_leaves_view' )
            . __table( $data, 'r tac' )
        . d__();
    }

    function __contract_form(): void {
        $f = new FORM();
    }

    function departments( string $style = 'list', string $wrap_class = '', string $edit_wrap = '', int $count = 12, int $page = 0 ): void {
        echo $this->__departments( $style, $wrap_class, $edit_wrap, $count, $page );
    }

    function _departments( int $count = 12, int $page = 0, string $where = '' ): array {
        $d = new DB();
        $r = $d->select( 'aio_departments', '', $where, $count, $page );
        //skel( $r );
        return $r;
    }

    function __departments( string $style = 'list', string $wrap_class = '', string $edit_wrap = '', int $count = 12, int $page = 0, string $where = '' ): string {
        return $style == 'list' ? $this->__departments_list( $wrap_class, $edit_wrap, $count, $page ) : $this->__departments_cards( $wrap_class, $edit_wrap, $count, $page );
    }

    function __departments_list( string $class, string $edit_wrap = '', int $count = 12, int $page = 0, string $where = '' ): string {
        $data = [
            [ 'head' => [ 'Name & Details', 'Color', 'Icon', 'Status', 'Actions' ] ],
        ];
        $ds = $this->_departments( $count, $page, $where );
        $f = new FORM();
        foreach( $ds as $d ) {
            $edit = $f->__edit_html( $edit_wrap, $d );
            $delete = $f->__trash_html( 'aio_departments', 'dept_id = '.$d['dept_id'] );
            $data[] = [ 'body' => [ $d['dept_title'] . __div( 'fzs op5', $d['dept_desc'] ), __cb( $d['dept_color'] ), __ico( $d['dept_icon'] ), __status( $d['dept_status'] ), __div( 'acts', $edit . $delete ) ] ];
        }
        return __table( $data, $class );
    }

    function __departments_cards( string $class, string $edit_wrap = '', int $count = 12, int $page = 0, string $where = '' ): string {
        return '';
    }

    function department_form(): void {
        echo $this->__department_form();
    }

    function __department_form(): string {
        $f = new FORM();
        $form = [
            [ 'i' => 'title', 'n' => 'Department Name', 'p' => 'Ex: Accounting', 'c' => 8, 'r' => 1 ],
            [ 'i' => 'status', 'n' => 'Status', 't' => 'slide', 'off' => 'Inactive', 'on' => 'Active', 'v' => 1, 'c' => 4 ],
            [ 'i' => 'desc', 'n' => 'Description', 'p' => 'Ex: Handles the financial aspect of company', 'c' => 12 ],
            [ 'i' => 'color', 'n' => 'Color', 't' => 'color', 'v' => '#000000', 'c' => 6 ],
            [ 'i' => 'icon', 'n' => 'Icon', 'p' => 'Ex: home', 'c' => 6 ],
        ];
        return $f->__pre_process( '', 'aio_departments', 'dept', 'dept_', [], 'Successfully saved work department!' )
        . $f->__form( $form, 'row', 'dept' )
        . $f->__process_trigger( 'Save Department', 'mb0', '', '', '.tac' )
        . $f->__post_process();
    }

    function _designations( int $count = 12, int $page = 0, string $where = '' ): array {
        $d = new DB();
        return $d->select( 'aio_designations', [], $where, $count, $page );
    }

    function designations( string $style = 'list', string $wrap_class = '', string $edit_wrap = '', int $count = 12, int $page = 0, string $where = '' ): void {
        echo $style == 'list' ? $this->__designations_list( $wrap_class, $edit_wrap, $count, $page ) : $this->__designations_cards( $wrap_class, $edit_wrap, $count, $page );
    }

    function __designations_list( string $class, string $edit_wrap = '', int $count = 12, int $page = 0, string $where = '' ): string {
        $data = [
            [ 'head' => [ 'Name & Details', 'Color', 'Icon', 'Status', 'Actions' ] ],
        ];
        $ds = $this->_designations( $count, $page, $where );
        $f = new FORM();
        foreach( $ds as $d ) {
            $edit = $f->__edit_html( $edit_wrap, $d );
            $delete = $f->__trash_html( 'aio_designations', 'des_id = '.$d['des_id'] );
            $data[] = [ 'body' => [ 'l' => $d['des_title'] . __div( 'fzs op5', $d['des_desc'] ), 'c' => __cb( $d['des_color'] ), __ico( $d['des_icon'] ), __status( $d['des_status'] ), __div( 'acts', $edit . $delete ) ] ];
        }
        return __table( $data, $class );
    }

    function __designations_cards( string $class, string $edit_wrap = '', int $count = 12, int $page = 0, string $where = '' ): string {
        $ds = $this->_designations( $count, $page, $where );
        return '';
    }

    function designation_form(): void {
        echo $this->__designation_form();
    }

    function __designation_form(): string {
        $f = new FORM();
        $d = new DB();
        $ds = $d->select( 'aio_departments', 'dept_id,dept_title', 'dept_status = 1' );
        $ds = !empty( $ds ) ? array_to_assoc( $ds, 'dept_id', 'dept_title' ) : [];
        $form = [
            [ 'i' => 'title', 'n' => 'Designation Name', 'p' => 'Ex: Design Director', 'c' => 8, 'r' => 1 ],
            [ 'i' => 'status', 'n' => 'Status', 't' => 'slide', 'off' => 'Inactive', 'on' => 'Active', 'v' => 1, 'c' => 4 ],
            [ 'i' => 'desc', 'n' => 'Description', 'p' => 'Ex: Responsible for design aspect of projects...', 'c' => 12 ],
            [ 'i' => 'dept', 'n' => 'Department', 't' => 'select2', 'p' => 'Select...', 'o' => $ds, 'c' => 4, 'k' => 1 ],
            [ 'i' => 'color', 'n' => 'Color', 't' => 'color', 'v' => '#000000', 'c' => 4 ],
            [ 'i' => 'icon', 'n' => 'Icon', 'p' => 'Ex: home', 'c' => 4 ],
        ];
        return $f->__pre_process( '', 'aio_designations', 'design', 'des_', [], 'Successfully saved work designation!' )
        . $f->__form( $form, 'row', 'design' )
        . $f->__process_trigger( 'Save Designation', 'mb0', '', '', '.tac' )
        . $f->__post_process();
    }

    function user_line( string $name = '', string $picture = '', string $designation = '', string $url = '' ): void {
        echo $this->__user_line( $name, $picture, $designation, $url );
    }

    function __user_line( string $name = '', string $picture = '', string $designation = '', string $url = '' ): string {
        if( !empty( $url ) ) {
            return __a( APPURL . $url, __div( 'user_info', __image( $picture, '', 'pic' ) . __div( 'details', __div( 'name', $name ) . __div( 'design', $designation ) ) ), 'aio_user_info_line' );
        } else {
            return __div( 'aio_user_info_line', __div( 'user_info', __image( $picture, '', 'pic' ) . __div( 'details', __div( 'name', $name ) . __div( 'design', $designation ) ) ) );
        }
    }

}

function update_employee_ajax(): void {

}

function update_employee_contract_ajax(): void {

}

function update_expense(): void {

}