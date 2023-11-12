$(document).ready(function(){

    $('body').on('click', '[data-step]', function () {
        $(this).parent().children().removeClass('on');
        $(this).addClass('on');
        let el = $(this).data('step');
        $(el).parent().children().addClass('dn');
        $(el).removeClass();
    })

    // Next Step
    .on('click', '.steps [data-next]', function () {
        $($(this).parents('.steps')).find('[data-step].on').next().click();
        step_controls_access( $(this) );
    })
    // Previous Step
    .on('click', '.steps [data-prev]', function () {
        $($(this).parents('.steps')).find('[data-step].on').prev().click();
        step_controls_access( $(this) );
    });

});

function step_controls_access( step_element ) {
    let se = step_element;

}

function goto_step( e, s ){
    $(e).find('.step:nth-child('+s+')').click();
}

function next_step( e ) {
    $(e).find('.on').next().click();
}

function prev_step( e ) {
    $(e).find('.on').prev().click();
}
