$(document).ready(function(){

    $('body').on('click','.notify .close',function(){
        // Prepare for Removal
        $(this).parents('.notify').addClass('out');
        // Remove Notification
        setTimeout(function(){ $(this).parents('.notify').remove() }, 2000 );
    });

})

function notify( text, duration ) {
    // Process duration of notification
    duration = duration !== undefined && duration !== '' && duration > 0 ? duration * 1000 : 6000;

    // Create notification message
    var r = Math.random().toString(36).substring(7);
    var n = '<div class="notify in n_'+r+'"><div class="data"><div class="close"></div><div class="message">'+text+'</div></div><div class="time"></div></div>'
    var id = '.n_' + r;
    let ns = $('.notices');

    // Animate Timer
    var perc = 100;
    var timer = setInterval(function(){
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