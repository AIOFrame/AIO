$(document).ready(function(){

    // Create wrap if doesn't exist
    if( !$('[data-alerts]').length ) {
        $('body').append('<div class="t r" data-alerts></div>');
    }

    // Load Alerts
    reload_alerts();
    //setInterval( reload_alerts, 60000 );

    // Close Alert
    $('body').on('click','.alert .close',function(){
        // Prepare for Removal
        $(this).parents('.alert').addClass('out');
        // Remove Alert
        setTimeout(function(){ $(this).parents('.alert').remove() }, 2000 );
    });

});

function alert( text, duration ) {
    notify( text, duration );
}

function notify( text, duration ) {
    // Process duration of notification
    duration = duration !== undefined && duration !== '' && duration > 0 ? duration * 1000 : 6000;

    // Create notification message
    let r = Math.random().toString(36).substring(7);
    let n = '<div class="alert in n_'+r+'"><div class="data"><div class="close"></div><div class="message">'+text+'</div></div><div class="time"></div></div>';
    let id = '.n_' + r;
    let ns = $('[data-alerts]');

    // Animate Timer
    let perc = 100;
    let timer = setInterval(function(){
        perc--;
        $( id + ' .time' ).css({ 'width': 'calc('+perc+'% - 20px)' });
    }, (duration + 400) / 100 );
    setTimeout(function(){ clearInterval(timer) },duration);

    // Add Notification
    ns.hasClass('b') ? ns.prepend(n) : ns.append(n);
    setTimeout(function(){ $(id).removeClass('in') },100);

    // Prepare for Removal
    setTimeout(function(){ $(id).addClass('out') },duration+1000);

    // Remove Notification
    setTimeout(function(){ $(id).remove() },duration+2000);
}

function reload_alerts(  ) {
    post( 'get_alerts', {}, 0, 0, '', 0, 'post_reload_alerts' );
}

function post_reload_alerts( e ) {
    console.log('load');
    console.log(e);
}