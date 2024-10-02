<?php
/**
 * EMS: Employee Management System
 */

class EMS {

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
        $ran = rand( 1, 99999 );
        $months = [ 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ];
        $employees = [
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
        global $options;
        $ico = $options['ico_class'] ?? 'mico';
        $present_ico = $options['attendance_present_ico'] ?? 'check_circle';
        $absent_ico = $options['attendance_absent_ico'] ?? 'do_disturb_on';
        $present_ico = __div( $ico . ' ico ' . $present_ico, $present_ico );
        $absent_ico = __div( $ico . ' ico ' . $absent_ico, $absent_ico );

        // View
        $r = __d( 'aio_attendance_view', 'aio_attendance_view' )
        . __d( 'aio_attendance_head' ) . __tab_heads( [ '.day_view_'.$ran => 'Day', '.week_view_'.$ran => 'Week', '.year_view_'.$ran => 'Year' ], 'material mb20', '', 1 ) . d__()
        . __d( 'aio_attendance_body' )

        // Day View
        . __d( 'day_view_'.$ran ) . __r();
            $present = $sick = $vacation = 0;
            foreach( $employees as $e ) {
                $e['a'] == 1 ? $present++ : '';
            };
            $absent = count( $employees ) - $present;
            $r .= __c( 3 ) . __div( 'widget stat', T('Attended') . __el('span','count green',$present) ) . c__()
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

}

function update_employee_ajax(): void {

}

function update_employee_contract_ajax(): void {

}

function update_expense(): void {

}