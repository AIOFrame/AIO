$(document).ready(function(){

    let page_path = location.href.replace(location.origin+'/','').replace('/','_');
    let tab = localStorage[ page_path + '_tab'];
    if( tab !== undefined ){
        setTimeout(function(){ $('[data-t="' + tab + '"]').click(); },200);
    }

    $('body').on('click', '.steps [data-t],.tabs [data-t]', function () {
        $(this).parent().children().removeClass('on');
        $(this).addClass('on');
        $($(this).data('t')).parent().children().hide();
        $($(this).data('t')).show();
        if( $(this).parent().data('store') !== undefined || $(this).parent().data('save') !== undefined ){
            localStorage[ page_path + '_tab' ] = $(this).data('t');
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