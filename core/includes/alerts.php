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

    /**
     * Returns an int of alerts that are not acted upon
     * @return int
     */
    function count(): int {
        $c = 0;
        $db = new DB();
        $count = $db->select( 'alerts', '', 'alert_user = \''.get_user_id().'\' && alert_seen = \'0\'', 0, 0, '', 1 );
        return !empty( $count ) && is_array( $count ) ? $count[0] : 0;
    }

    /**
     * Renders Alerts
     * @param string $alert_class Class for each alert
     * @param string $alerts_wrap_class Class for div wrapping around alerts
     */
    function alerts_html( string $alert_class = '', string $alerts_wrap_class = '' ){
        $db = new DB();
        $alerts = $db->select( 'alerts', '', 'alert_user = "'.get_user_id().'"' );
        if( is_array( $alerts ) && !empty( $alerts ) ){
            $alerts = replace_in_keys( $alerts, 'alert_' );
            $cry = Crypto::initiate();
            ?>
            <div data-aio-alerts class="alerts <?php echo $alerts_wrap_class; ?>" data-action="<?php $cry->enc('clear_alert_ajax'); ?>">
            <?php
            foreach( $alerts as $a ){
                $link = !empty( $a['link'] ) ? '<a class="title" href="'.APPURL.$a['link'].'">'.$a['name'].'</a><a class="note" href="'.APPURL.$a['link'].'">'.$a['note'].'</a>' : '<div class="title">'.$a['name'].'</div><div class="note">'.$a['note'].'</div>';
                $seen = $a['seen'] == 0 ? 'no' : 'yes';
                echo '<div class="alert '.$alert_class.' '.$seen.' '.$a['type'].'" data-type="'.$a['type'].'" data-id="'.$cry->encrypt($a['id']).'"><div class="clear" data-clear-alert></div><i class="ico '.$a['type'].'"></i><div class="info">'.$link.'</div></div>';
            } ?>
                <button data-clear-alerts class="clear_all" data-action="<?php $cry->enc('clear_alerts_ajax'); ?>"><?php E('Clear All'); ?></button>
            </div>
        <?php
        } else {
            echo '<div class="alerts"><div class="empty">'.T('You do not seem to have any alerts!').'</div></div>';
        }
    }

    /**
     * @param string $title Short title for the notification Ex: Approved, Email Sent, Successfully added
     * @param string $note Notification alert content
     * @param string $type Type of notification, used for styling purpose Ex: done, error, alert
     * @param string $link Action link URL for user to redirect to upon click of alert
     * @param int $user User ID of alert receiver
     * @param int $from Sender user ID (optional)
     * @return bool
     */
    function create( string $title, string $note = '', string $type = 'done', string $link = '', int $user = 0, int $from = 0 ): bool {
        $user = $user == 0 ? get_user_id() : $user;
        $from = !empty( $from ) ? $from : 0;
        $keys = [ 'from', 'user', 'name', 'note', 'type', 'link', 'seen', 'time' ];
        $values = [ $from, $user, $title, $note, $type, $link, 0, date("Y-m-d H:i:s")];
        $db = new DB();
        return $db->insert( 'alerts', prepare_values($keys,'alert_'), $values );
    }
}

//function alert_seen( $alert_id = '' ) {
//    $aid = isset( $_POST['id'] ) && !empty( $_POST['id'] ) ? $_POST['id'] : $alert_id;
//    $c = Crypto::initiate();
//    if( $id = $c->decrypt($aid) ){
//        echo ALERTS::alert_seen( $id ) ? 1 : 0;
//    }
//}

function clear_alert_ajax() {
    if( isset( $_POST['id'] ) ) {
        $cry = Crypto::initiate();
        $id = $cry->decrypt( $_POST['id'] );
        if( is_numeric( $id ) ) {
            $db = new DB();
            //$upd = $db->update('alerts',['alert_seen'],[1],'alert_id = \''.$id.'\'');
            $del = $db->delete( 'alerts', 'alert_id = \''.$id.'\'' );
            echo $del ? json_encode( [ 1, $_POST['id'] ] ) : json_encode( [ 0, '0' ] );
        }
    }
}

function clear_alerts_ajax() {
    $db = new DB();
    $del = $db->delete( 'alerts', 'alert_user = \''.get_user_id().'\'' );
    echo $del ? json_encode([1]) : json_encode([0]);
}

function get_alerts_ajax() {
    $r = [];
    if( user_logged_in() ) {
        $db = new DB();
        $as = $db->select( 'alerts', '', 'alert_user = \''.get_user_id().'\'' );
        $r = is_array( $as ) ? $as : [ 1, 'test' ];
    }
    echo json_encode( $r );
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