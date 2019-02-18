/* Place Universal Scripts Here */

var domain;

$(document).ready(function(){

    // MANIPULATOR
    // TODO: Change resetsrc and resetinput to reset
    $(document).on('click', '[data-action], [data-show], [data-on], [data-hide], [data-slide], [data-remove], [data-toggle], [data-resetsrc], [data-resetinput], [data-click], [data-href]', function (e) {
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
        $($(this).data('on')).toggleClass('on');
        $($(this).data('remove')).remove();
        $($(this).data('hide')).hide();
        $($(this).data('show')).show();
        $($(this).data('toggle')).toggle();
        $($(this).data('slide')).slideToggle();
        $($(this).data('resetsrc')).attr('src', '');
        $($(this).data('resetinput')).val('');
        $($(this).data('click')).click();
        if($($(this).data('show')).hasClass('modal') || $($(this).data('on')).hasClass('modal')){
            $('article').addClass('fade');
        }
        if($(this).data('href')){
            //elog($(e.target).data('prevent-default'));
            if( $(e.target).data('prevent-default') === undefined ){
                location.href = $(this).data('href');
            }
        }
    });

    // TABS & STEPS
    var tab = localStorage['tab'];
    if( tab !== undefined ){
        tab = JSON.parse(tab);
        if( tab.page === location.href ){
            setTimeout(function(){ $('[data-t="' + tab.tab + '"]').click(); },200);
        }
    }

    $('body').on('click', '.steps [data-t], .tab[data-t]', function () {
        $(this).parent().children().removeClass('on');
        $(this).addClass('on');
        $($(this).data('t')).parent().children().hide();
        $($(this).data('t')).show();
        if( $(this).parent().data('store-tab') !== undefined ){
            localStorage['tab'] = JSON.stringify({'page':location.href,'tab':$(this).data('t')})
        }
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

    // Prevent Default
    $('body').on('click','a .prevdef',function(e){
        //$(e).parents('a').preventDefault();
        console.log($(e).parents('a'));
    });

    // MODAL
    $('.modal .close').on('click',function(){
        $(this).parents('.modal').removeClass('on');
        $('article').removeClass('fade');
        reset_modal($(this));
    });

    // ALERT & NOTIFICATION
    $('#notification .close, #alert .close').on('click',function(){
        $(this).parents('#notification,#alert').removeClass('on');
    });

    if( typeof ClipboardJS === 'function' ){
        new ClipboardJS('.btn, button');
    }

    if( $.fn.select2 !== undefined ){
        $('select.easy, select.select2').select2({ width:'100%' });
    }

    // TODO: Make context menu dynamic
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
        $.post(location.origin,{'action':'set_language','lang':nl});
        $.post(location.origin,d,function(r){
            if( r = JSON.parse(r) ){
                $.each(r[0],function(i){
                    if( r[0][i] !== '' && r[1][i] !== '') {
                        $(":contains('" + r[0][i] + "')").filter(function () {
                            return $(this).children().length === 0;
                        }).html(r[1][i]);
                        elog('Replacing '+r[0][i]+'with '+r[1][i]);
                    }
                    //$('tbody').append('<tr><td>'++'</td><td>'+r[1][i]+'</td></tr>');
                });

                $('[data-lang]').removeClass('on');
                $(this).addClass('on');
            }
        });
    })

    .on('change','[data-languages]',function(){
        var nl = $(this).val();
        $.post(location.origin,{'action':'set_language','lang':nl},function(r){
            notify('Language Changed!');
            reload(3);
        });
    });

    // Fetch States

    $('body').on('change','select[data-states]',function(){

        var t = $($(this).data('states'));

        if( $(this).data('states') !== '' ){

            $.post( location.origin, { 'action':'states', 'id': $(this).val() }, function(r){

                if( r = JSON.parse( r ) ){

                    if( $.isArray( r ) ){

                        elog(r);

                        var o = '';
                        $.each( r, function(i,s){

                            o += '<option value="' + s + '">' + s + '</option>';

                        });

                        elog(o);
                        elog(t);

                        $(t).html(o);

                        if($(t).hasClass('select2')) {

                            $(t).select2('destroy').select2({ width:'100%' });

                        }
                    }

                }

            });

        }

    });

    // Markup '[data-m]','[data-mt]','[data-mr]','[data-mb]','[data-ml]','[data-p]','[data-pt]','[data-pr]','[data-pb]','[data-pl]'

    $.each($('[data-ml]'),function(a,b){ $(b).css({'margin-left':$(b).data('ml')}); });
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
function empty( e, d ) {
    d = d === undefined || d === '' ? '' : '[data-'+d+']';

    elog(d);

    if( $(e)[0] && $(e)[0].localName === 'div' ) {
        var r = [];
        $.each($(e).find('input'+d+',select'+d),function(a,b){
            if( b !== undefined && $(b).val() !== null && $(b).val() !== "" ){
                r.push(false);
            } else {
                elog( b );
                r.push(true);
            }
        });
        return $.inArray(true,r) !== -1 ? true : false;
    } else {
        if( $(e).val().length > 0 ){
            return false;
        } else {
            return true;
        }
    }
}

function sempty( e, d ) {
    d = d === undefined || d === '' ? '' : '[data-'+d+']';
    //elog(d);
    if( $(e)[0] && $(e)[0].localName === 'div' ){
        var r = [];
        $.each($(e).find('input'+d+',select'+d),function(a,b){
            if( b !== undefined && $(b).val() !== null && $(b).val() !== "" ){
                $(b).removeClass('empty');
                r.push(false);
            } else {
                $(b).addClass('empty');
                r.push(true);
            }
            //elog(b);
        });
        //elog(r);
        return $.inArray(true,r) !== -1 ? true : false;
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

        if( $(this).attr('type') === 'checkbox' || $(this).attr('type') === 'radio' ){

            v = $(this).is(':checked');

            if( v === true ){ v = 1; } else if( v === false ) { v = 0; }

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

function get_checkbox_values( e, string ) {
    var d = [];
    $('[name='+e+']').each(function(a,b){
        $(b).is(':checked') ? d.push($(b).val()) : '';
    });
    if( string ) {
        return d.join(', ');
    } else {
        return d;
    }
}

function clear_values( e, s ){
    $(e).find(":input[data-"+s+"]:not(:button)","select[data-"+s+"]","textarea[data-"+s+"]").each(function () {
        $(this).val("").trigger('chosen:updated');
    });
}

var appdebug = $('body').hasClass('debug') ? true : false;

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

    elog(d);

    $.post( domain, d, function(r){

        elog(r);

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

function edit_data( e, modal ) {

    var t = $(e).data('t') ? $($(e).data('t')) : $(modal);
    var data = $(e).data('data');

    $.each( $(modal).find('button,h1,h2,h3,h4,h5,h6,span,i,p'), function( a, b ){
        if( $(b).html().indexOf('Add') >= 0){
            $(b).html( $(b).html().replace('Add','Update') );
        }
    });

    elog(data); elog(t);

    $.each( data, function(i,d){
        if( i === 'id' ){
            t.addClass('on').data('id',d);
        } else {
            $('#'+i).val(d).change();
        }
    });
}

function reset_modal(e) {

    $.each( $(e).parents('.modal').find('button,h1,h2,h3,h4,h5,h6,span,i,p'), function( a, b ){
        if( $(b).html().indexOf('Update') >= 0){
            $(b).html( $(b).html().replace('Update','Add') );
        }
    });

    $(e).parents('.modal').data('id','');

    // TODO: If modal has class to empty then empty fields

}

function trash_data( q ) {
    var d = { 'action':'trash_data', 'query':q };
    elog(d);
    if( confirm('Are you sure to delete ?') ){
        $.post( location.origin, d, function(r){
            elog(r);
        })
    }
}

function elog( d ) {
    appdebug ? console.log( d ) : '';
}

// ALERTS

function alerted( $m ) {
    //alert( $m )
}

function notify( text, duration ) {
    duration = duration !== undefined && duration !== '' && duration > 0 ? duration * 1000 : 6000;
    $('#notification').addClass('on').children('div').not('.close').html( text );
    setTimeout(function(){
        $('#notification').removeClass('on');
    },duration);
}

function toggle_class_on( e ){
    $('body').on('click',e,function(){
        $(this).toggleClass('on');
    })
}

function slide_toggle( e, t ){
    $('body').on('click',e,function(){
        $(this).find(t).slideToggle();
    })
}

function reload( time_seconds ){

    var t = time_seconds !== undefined && time_seconds !== '' ? time_seconds * 1000 : 5000;
    setTimeout(function(){ location.reload() },t);

}