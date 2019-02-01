<?php

class ALERTS {

    function __construct() {
    }

    public static function get( $limit = 20, $offset = 0, $unseen = 0, $days = '', $type = '' ) {
        $days = empty( $days ) ? '30' : $days;
        $q = 'al_time > DATE_SUB(NOW(), INTERVAL '.$days.' DAY)';
        $q .= $unseen ? ' AND al_seen = 0': '';
        $alerts = select( 'alerts', '*', $q, $limit, $offset );
        return $alerts;
    }

    public static function count( $array = [] ){
        $c = 0;
        if( is_array( $array ) && !empty( $array ) ){
            foreach( $array as $a ){
                isset( $a['seen'] ) && !$a['seen'] ? $c++ : '';
                isset( $a['al_seen'] ) && !$a['al_seen'] ? $c++ : '';
            }
        }
        return $c;
    }

    public static function html( $array = [] ){
        $alerts = replace_in_keys( $array, 'al_' );
        if( is_array( $array ) && !empty( $array ) ){
            $cry = Crypto::initiate();
            foreach( $alerts as $a ){
                if( !empty( $a['link'] ) ){
                    echo '<a href="'.APPURL.$a['link'].'" class="alert '.$a['type'].'" data-type="'.$a['type'].'"><div  data-id="'.$cry->encrypt($a['id']).'" class="close"></div><div class="title">'.$a['name'].'</div><div class="note">'.$a['note'].'</div></a>';
                } else {
                    echo '<div class="alert '.$a['type'].'" data-type="'.$a['type'].'"><div data-id="'.$cry->encrypt($a['id']).'" class="close"></div><div class="title">'.$a['name'].'</div><div class="note">'.$a['note'].'</div></div>';
                }
            }
        }
    }

    public static function create( $title, $note = '', $user = '', $type = 'done', $link = '', $from = '' ) {
        $from = !empty($from) ? $from : get_current_user_id();
        $user = !empty($user) ? $user : get_current_user_id();
        $keys = [ 'from', 'user', 'name', 'note', 'type', 'link', 'seen', 'time' ];
        $values = [ $from, $user, $title, $note, $type, $link, 0, date("Y-m-d H:i:s")];
        $a = insert( 'alerts', prepare_values($keys,'al_'), $values );
        return $a ? true : false;
    }

    public static function alert_seen( $alert_id ) {
        return update( 'alerts', 'al_seen', 1, 'al_id = "'.$alert_id.'"' );
    }
}

function alert_seen( $alert_id = '' ) {
    $aid = isset( $_POST['id'] ) && !empty( $_POST['id'] ) ? $_POST['id'] : $alert_id;
    $c = Crypto::initiate();
    if( $id = $c->decrypt($aid) ){
        echo ALERTS::alert_seen( $id ) ? 1 : 0;
    }
}

/*

GUIDE FOR ACTIONS

How to use:

$alerts = new ALERTS();
$alerts->create_alert( 'New Employee Joined', 'New Employee "Jackie Chan" has joined', 1, 'visit', 'employees/5015', 0 );

Using Parameters Types and Actions:

1. 'visit', 'employees/5112'
   Will make alert click visit the action url joined with domain name

2. 'notify', ''
   Will just notify the user and click wont take anywhere

3. '

Feel free to add more ideas...

*/