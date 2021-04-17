$(document).ready(function(){

    let b = $('body');

    $(b).on('click', '[data-t]', function() {
        $(this).parent().children().removeClass('on');
        $(this).addClass('on');
        $($(this).data('t')).parent().children().removeClass('on');
        $($(this).data('t')).addClass('on');
    });

})