$(document).ready(function(){

    let page_path = location.pathname.replace( '/', '_' ).slice(1) + '_';
    $('[data-store]').each(function(){
        let title = $(this).data('store') !== '' ? $(this).data('store') + '_' : '_';
        let tab = localStorage[ page_path + title + 'tab'];
        if( tab !== undefined ){
            setTimeout(function(){ $('[data-t="' + tab + '"]').click(); },200);
        }
    });

    $('body').on('click', '.steps [data-t],.tabs [data-t]', function () {
        $(this).parent().children().removeClass('on');
        $(this).addClass('on');
        $($(this).data('t')).parent().children().hide();
        $($(this).data('t')).show();
        if( $(this).parent().data('store') !== undefined ){
            let title = $(this).parent().data('store') !== '' ? $(this).parent().data('store') + '_' : '_';
            localStorage[ page_path + title + 'tab' ] = $(this).data('t');
        }
        if( $(this).parent().data('save') !== undefined ){
            let title = $(this).parent().data('save') !== '' ? $(this).parent().data('save') + '_' : '_';
            localStorage[ page_path + title + 'tab' ] = $(this).data('t');
        }
    })
    // Next Step
    .on('click', '.tabs .next', function () {
        $($(this).parents('.tabs')).find('[data-t].on').next().click();
    })
    // Previous Step
    .on('click', '.tabs .prev', function () {
        $($(this).parents('.tabs')).find('[data-t].on').prev().click();
    });
});