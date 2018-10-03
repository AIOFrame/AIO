/* Place Universal Scripts Here */

var domain;

// MANIPULATOR
$(document).ready(function(){
    $(document).on('click', '[data-action], [data-show], [data-hide], [data-slide], [data-remove],[data-toggle],[data-resetsrc],[data-resetinput]', function () {
        if ($(this).data('togglescroll') === true) {
            scroll_lock();
        }
        if ($(this).data('blur') === true) {
            blurred();
        }
        if ($(this).data('targettoggleclass')) {
            $($(this).data('hide')).toggleClass($(this).data('targettoggleclass'));
            $($(this).data('action')).toggleClass($(this).data('targettoggleclass'));
            $($(this).data('show')).toggleClass($(this).data('targettoggleclass'));
        }
        $($(this).data('remove')).remove();
        $($(this).data('hide')).hide();
        $($(this).data('show')).show();
        $($(this).data('toggle')).toggle();
        $($(this).data('slide')).slideToggle();
        $($(this).data('resetsrc')).attr('src', '');
        $($(this).data('resetinput')).val('');
        if($($(this).data('show')).hasClass('modal')){
            $('article').addClass('fade');
        }
    });
});

// STEPS
$(document).ready(function () {
    // Tabs
    $('body').on('click', '.steps [data-t]', function () {
        $(this).parent().children().removeClass('on');
        $(this).addClass('on');
        $($(this).data('t')).parent().children().hide();
        $($(this).data('t')).show();
    });
    $('body').on('click', '[data-step]', function () {
        $(this).parent().children().removeClass('on');
        $(this).addClass('on');
        $($(this).data('step')).parent().children().hide();
        $($(this).data('step')).show();
    });
    // Next Step
    $('.steps_content .next').on('click', function () {
        $($(this).parents('.steps_content')).prev('.step_heads').find('.step_head.on').next().click();
    });
    // Previous Step
    $('.steps_content .prev').on('click', function () {
        $($(this).parents('.steps_content')).prev('.step_heads').find('.step_head.on').prev().click();
    });
});

// TABS
$(document).ready(function () {
    // Tabs
    $('body').on('click', '.tab[data-t]', function () {
        $(this).parent().children().removeClass('on');
        $(this).addClass('on');
        $( $(this).data('t') ).parent().children().hide();
        $( $(this).data('t') ).show();
    })
});

function goto_step( e, s ){
    $(e).find('.step_head:nth-child('+s+')').click();
}

function next_step( e ) {
    $(e).find('.on').next().click();
}

function prev_step( e ) {
    $(e).find('.on').prev().click();
}

// FIELD FUNCTIONS
function empty( e ) {
    if( $(e)[0].localName === 'div' ) {
        $.each($(e).find('input,select'),function(a,b){
            if( b !== undefined && $(b).val() !== null && $(b).val() !== "" ){
                $r = false;
            } else {
                $r = true;
            }
        });
        return $r;
    } else {
        if( $(e).val().length > 0 ){
            return false;
        } else {
            return true;
        }
    }
}

function sempty( e ) {
    if( $(e)[0].localName === 'div' ){
        $.each($(e).find('input,select'),function(a,b){
            if( b !== undefined && $(b).val() !== null && $(b).val() !== "" ){
                $(b).removeClass('empty');
                $r = false;
            } else {
                $(b).addClass('empty');
                $r = true;
            }
        });
        return $r;
    } else {
        if( $(e).val() !== null && $(e).val() !== "" ){
            $(e).removeClass('empty');
            return false;
        } else {
            $(e).addClass('empty');
            return true;
        }
    }
}

function clear(e){
    $(e).val("");
}

function get_values( e, s ) {
    var data = {};
    $(e).find(":input[data-"+s+"]:not(:button)","select[data-"+s+"]","textarea[data-"+s+"]").each(function () {
        var v;
        if($(this).hasClass('nf')){ v = unformat_number( $(this).val() ) } else { v = $(this).val() }
        if($(this).attr('type') === 'checkbox' || $(this).attr('type') === 'radio'){ v = $(this).is(':checked'); }
        if( $(this).data('key') !== undefined ){
            data[$(this).data('key')] = v;
        } else if( $(this).attr('id') !== undefined ){
            data[$(this).attr('id')] = v;
        } else {
            data[$(this).attr('class')] = v;
        }
    });
    return data;
}

function clear_values( e, s ){
    $(e).find(":input[data-"+s+"]:not(:button)","select[data-"+s+"]","textarea[data-"+s+"]").each(function () {
        $(this).val("").trigger('chosen:updated');
    });
}