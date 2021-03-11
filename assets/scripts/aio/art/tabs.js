$(document).ready(function(){

    var pagepath = location.href.replace(location.origin+'/','').replace('/','_');
    var tab = localStorage[ pagepath + '_tab'];
    if( tab !== undefined ){
        setTimeout(function(){ $('[data-t="' + tab + '"]').click(); },200);
    }

    $('body').on('click', '.steps [data-t],.tabs [data-t]', function () {
        $(this).parent().children().removeClass('on');
        $(this).addClass('on');
        $($(this).data('t')).parent().children().hide();
        $($(this).data('t')).show();
        if( $(this).parent().data('store-tab') !== undefined ){
            localStorage[ pagepath + '_tab' ] = $(this).data('t');
        }
    });
})