/* Place Universal Scripts Here */

var domain;

$(document).ready(function(){

    // MANIPULATOR
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

    // TABS & STEPS
    $('body').on('click', '.steps [data-t], .tab[data-t]', function () {
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
    $('.steps .next').on('click', function () {
        $($(this).parents('.steps')).find('[data-t].on').next().click();
    });
    // Previous Step
    $('.steps .prev').on('click', function () {
        $($(this).parents('.steps')).find('[data-t].on').prev().click();
    });

    // MODAL
    $('.modal .close').on('click',function(){
        $(this).parents('.modal').hide();
        $('article').removeClass('fade');
    });

    // ALERT & NOTIFICATION
    $('#notification .close, #alert .close').on('click',function(){
        $(this).parents('#notification,#alert').removeClass('on');
    });

    if( typeof ClipboardJS === 'function' ){
        new ClipboardJS('.btn, button');
    }

    var cm = $('.context-menu');
    if( cm.length > 0 ){
        $(cm).on('click',function(){
            $(this).hide();
        });
        document.addEventListener( "contextmenu", function(e) {
            var x = e.clientX; var y = e.clientY;
            //console.log(e);
            e.preventDefault();
            $(cm).css({'left':x,'top':y,'display':'block'});
        });
    }

    // CHANGE LANGUAGE
    $('body').on('click','[data-lang]',function(){
        var cl = $('[data-lang].on').data('lang');
        var nl = $(this).data('lang');
        var d = {'action':'get_translations','languages':[cl,nl],'method':'json'};
        //console.log(d);
        $.post(location.origin,{'action':'set_language','lang':nl});
        $.post(location.origin,d,function(r){
            if( r = JSON.parse(r) ){
                //console.log(r);
                $.each(r[0],function(i){
                    if( r[0][i] !== '' && r[1][i] !== '') {
                        $(":contains('" + r[0][i] + "')").filter(function () {
                            return $(this).children().length === 0;
                        }).html(r[1][i]);
                        console.log('Replacing '+r[0][i]+'with '+r[1][i]);
                    }
                    //$('tbody').append('<tr><td>'++'</td><td>'+r[1][i]+'</td></tr>');
                });

                $('[data-lang]').removeClass('on');
                $(this).addClass('on');
            }
        });
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

// FIELD FUNCTIONS
function empty( e ) {
    if( $(e)[0] && $(e)[0].localName === 'div' ) {
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
    if( $(e)[0] && $(e)[0].localName === 'div' ){
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

function get_values( e, s, pre ) {
    pre = pre !== undefined && pre !== '' ? pre + '_' : '';
    var data = {};
    $(e).find(":input[data-"+s+"]:not(:button)","select[data-"+s+"]","textarea[data-"+s+"]").each(function () {
        var v;
        if($(this).hasClass('nf')){ v = unformat_number( $(this).val() ) } else { v = $(this).val() }
        if($(this).attr('type') === 'checkbox' || $(this).attr('type') === 'radio'){ v = $(this).is(':checked'); }

        if( v === true ){
            v = 1;
        }

        if( $(this).data('key') !== undefined ){
            data[pre + $(this).data('key')] = v;
        } else if( $(this).attr('id') !== undefined ){
            data[pre + $(this).attr('id')] = v;
        } else {
            data[pre + $(this).attr('class')] = v;
        }
    });
    return data;
}

function clear_values( e, s ){
    $(e).find(":input[data-"+s+"]:not(:button)","select[data-"+s+"]","textarea[data-"+s+"]").each(function () {
        $(this).val("").trigger('chosen:updated');
    });
}

function process_data( e ){
    var p = $(e).parents('[data-t]');
    var title = $(p).data('title');
    var pre = $(p).data('pre');
    var d = get_values( p, pre, pre );
    d.action = 'process_data';
    d.target = $(p).data('t');
    d.pre = pre;
    var types = Array('id','by','action','h','d','dt');
    $.each(types,function(x,a){
        if( $(p).data(a) !== undefined && $(p).data(a) !== '' ){
            d[a] = $(p).data(a);
        }
    });
    if( $('body').hasClass('debug') ){
        console.log(d);
    }
    $.post( domain, d, function(r){
        if( $('body').hasClass('debug') ){
            console.log(r);
        }
        if( r = JSON.parse(r) ){
            if(p.data('notify') !== undefined && p.data('notify') > 0){
                notify(title+' '+r[1]);
            }
            if(r[0] === 1){
                if(p.data('reload') !== undefined && p.data('reload') > 0){
                    setTimeout(function(){ location.reload() },p.data('reload') * 1000)
                }
                if(p.data('reset') !== undefined && p.data('reset') !== ''){
                    $('[data-'+p.data('reset')+']').val('');
                }
            }
        }
    });
}

// ALERTS

function alert(  ) {

}

function notify( text, duration ) {
    duration = duration !== undefined && duration !== '' && duration > 0 ? duration * 1000 : 6000;
    $('#notification').addClass('on').children('div').not('.close').html( text );
    setTimeout(function(){
        $('#notification').removeClass('on');
    },duration);
}