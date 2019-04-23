$(document).ready( function(){

    $('.clear').on( 'click', function() {

        $.post( location.origin, { 'action': 'clear_log_viewer' }, function(r) {

            if( r = JSON.parse(r) ){

                notify( r[1] );
                $('.log').html('');
                /* if( r[0] === 1 ){
                    setTimeout(function(){ location.reload() },4000);
                } */

            }

        })

    })

});