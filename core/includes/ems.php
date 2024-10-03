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

    function contract_modal(): void {
        // TODO: Contract Modal
    }

    function get_contracts(): array {
        return [];
    }

    function get_contract(): array {
        return [];
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
        $r = __d( 'aio_attendance_view', 'aio_attendance_view' )
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
                $emp_card = __div( 'user', __image( ( $e['p'] ?? '' ), '', 'pic' ) . __div( '', $e['n'] ?? '' ) );
                $table[] = [ 'body' => [ $emp_card, '100%', $present_ico, $absent_ico, $present_ico, $present_ico, $present_ico ] ];
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
            $sub_data = [ ( $e['n'] ?? '' ), '95%' ];
            for( $x = 0; $x < 12; $x++ ) {
                $r = rand( 28, 100 );
                $sub_data[] = __div( 'leaves_stat', __div( 'per', $r . '%' ) . __div( 'progress', __div( 'bar ' . ( $r < 60 ? ( $r < 30 ? 'r' : 'o' ) : 'g' ), '', '', 'style="width:'.$r.'%"' ) ) . __div( 'days', '22/22 Days' ) );
            }
            $data[] = [ 'body' => $sub_data ];
        }
        return __get_style('ems/leaves') . __d( 'aio_leaves_view' )
            . __table( $data, 'r tac' )
        . d__();
    }

    function __contract_form(): void {
        $f = new FORM();
    }

    function departments( string $style = 'list', string $wrap_class = '', int $count = 12, int $page = 0 ): void {
        echo $this->__departments( $style, $wrap_class, $count );
    }

    function _departments( int $count = 12, int $page = 0, string $where = '' ): array {
        $d = new DB();
        return $d->select( 'aio_departments', [], $where, $count, $page );
    }

    function __departments( string $style = 'list', string $wrap_class = '', int $count = 12, int $page = 0, string $where = '' ): string {
        return $style == 'list' ? $this->__departments_list( $wrap_class, $count, $page ) : $this->__departments_cards( $wrap_class, $count, $page );
    }

    function __departments_list( string $class, int $count = 12, int $page = 0, string $where = '' ): string {
        $data = [
            [ 'head' => [ 'Name & Details', 'Color', 'Icon', 'Status', 'Actions' ] ],
        ];
        $ds = $this->_departments( $count, $page, $where );
        foreach( $ds as $d ) {
            $data[] = [ 'body' => [ $d['title'] . __div( 'fs op5', $d['desc'] ), $d['color'], $d['icon'], $d['status'], '' ] ];
        }
        return __table( $data, $class );
    }

    function __departments_cards( string $class, int $count = 12, int $page = 0, string $where = '' ): string {
        return '';
    }

    function department_form(): void {
        echo $this->__department_form();
    }

    function __department_form(): string {
        $f = new FORM();
        $form = [
            [ 'i' => 'name', 'n' => 'Department Name', 'p' => 'Ex: Accounting', 'c' => 8 ],
            [ 'i' => 'status', 'n' => 'Status', 't' => 'slide', 'off' => 'Inactive', 'on' => 'Active', 'v' => 1, 'c' => 4 ],
            [ 'i' => 'desc', 'n' => 'Description', 'p' => 'Ex: Handles the financial aspect of company', 'c' => 12 ],
            [ 'i' => 'color', 'n' => 'Color', 't' => 'color', 'v' => '#000000', 'c' => 6 ],
            [ 'i' => 'icon', 'n' => 'Icon', 'p' => 'Ex: home', 'c' => 6 ],
        ];
        return $f->__form( $form, 'row', 'dept' );
    }

    function _designations( int $count = 12, int $page = 0, string $where = '' ): array {
        $d = new DB();
        return $d->select( 'aio_designations', [], $where, $count, $page );
    }

    function designations(): void {
        echo $this->__designations();
    }

    function __designations(): string {
        $data = [
            [ 'head' => [ 'Name', 'Description', 'Seniority', 'Color', 'Department', 'Status', 'Actions' ] ],
        ];
        return __table( $data );
    }

    function designation_form(): void {
        echo $this->__designation_form();
    }

    function __designation_form(): string {
        $f = new FORM();
        $d = new DB();
        $ds = $d->select( 'aio_departments', 'dept_id,dept_name', 'dept_status = "1"' );
        $ds = !empty( $ds ) ? array_to_assoc( $ds, 'dept_id', 'dept_name' ) : [];
        $form = [
            [ 'i' => 'name', 'n' => 'Designation Name', 'p' => 'Ex: Design Director', 'c' => 8 ],
            [ 'i' => 'status', 'n' => 'Status', 't' => 'slide', 'off' => 'Inactive', 'on' => 'Active', 'v' => 1, 'c' => 4 ],
            [ 'i' => 'desc', 'n' => 'Description', 'p' => 'Ex: Responsible for design aspect of projects...', 'c' => 12 ],
            [ 'i' => 'dept', 'n' => 'Department', 't' => 'select2', 'p' => 'Select...', 'o' => $ds, 'c' => 4 ],
            [ 'i' => 'color', 'n' => 'Color', 't' => 'color', 'v' => '#000000', 'c' => 4 ],
            [ 'i' => 'icon', 'n' => 'Icon', 'p' => 'Ex: home', 'c' => 4 ],
        ];
        return $f->__form( $form, 'row', 'des' );
    }

}

function update_employee_ajax(): void {

}

function update_employee_contract_ajax(): void {

}

function update_expense(): void {

}