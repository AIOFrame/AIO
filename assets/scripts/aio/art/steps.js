$(document).ready(function(){

    $('body').on('click', '[data-step]', function () {
        $(this).parent().children().removeClass('on');
        $(this).addClass('on');
        $($(this).data('step')).parent().children().hide();
        $($(this).data('step')).show();
    })

    // Next Step
    .on('click', '.steps .next', function () {
        $($(this).parents('.steps')).find('[data-t].on').next().click();
    })
    // Previous Step
    .on('click', '.steps .prev', function () {
        $($(this).parents('.steps')).find('[data-t].on').prev().click();
    });

});

function goto_step( e, s ){
    $(e).find('.step:nth-child('+s+')').click();
}

function next_step( e ) {
    $(e).find('.on').next().click();
}

function prev_step( e ) {
    $(e).find('.on').prev().click();
}