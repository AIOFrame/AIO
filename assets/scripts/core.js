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
        if($($(this).data('show')).hasClass('modal') && $($(this).data('show')).data('fade') === undefined ){
            $('article').addClass('fade');
        }
        if($($(this).data('on')).hasClass('modal') && $($(this).data('on')).data('fade') === undefined ){
            $('article').addClass('fade');
        }
        if($(this).data('href')){
            //elog($(e.target).data('prevent-default'));
            if( $(e.target).data('prevent-default') === undefined ){
                location.href = $(this).data('href');
            }
        }
    })

    .on('change','[data-check]',function(){

        var v = $(this).is(':checked') ? 'true' : 'false';

        $($(this).data('check')).val(v);

    });

    // Scroll Save

    restore_scroll();

    store_scroll();

    // TABS & STEPS
    var pagepath = location.href.replace(location.origin+'/','').replace('/','_');
    var tab = localStorage[ pagepath + '_tab'];
    if( tab !== undefined ){
        setTimeout(function(){ $('[data-t="' + tab + '"]').click(); },200);
    }

    $('body').on('click', '.steps [data-t], .tab[data-t]', function () {
        $(this).parent().children().removeClass('on');
        $(this).addClass('on');
        $($(this).data('t')).parent().children().hide();
        $($(this).data('t')).show();
        if( $(this).parent().data('store-tab') !== undefined ){
            localStorage[ pagepath + '_tab' ] = $(this).data('t');
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
        //elog($(e).parents('a'));
    });

    // MODAL
    $('.modal .close').on('click',function(){
        $(this).parents('.modal').removeClass('on');
        var removeFade = true;
        $('.modal').each(function(a,b){
            if( $(b).hasClass('on') ) {
                removeFade = false;
            }
        });
        if( removeFade ) { $('article').removeClass('fade'); }
        reset_modal($(this));
    });

    // ALERT & NOTIFICATION
    $('#notification .close, #alert .close').on('click',function(){
        $(this).parents('#notification,#alert').removeClass('on');
    });

    if( typeof ClipboardJS === 'function' ){
        var clipboard = new ClipboardJS('[data-clipboard-target],[data-clipboard-text]');

        clipboard.on('success', function(e) {
            notify('Copied!',1);
        });

        clipboard.on('error', function(e) {
            elog('Action:', e.action);
            elog('Trigger:', e.trigger);
        });
    }

    //$(body).on('click',['data-paste'],function(){
        //elog( paste( $(this).data('paste') ) );
    //});

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
            //elog(e);
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
        /* $.post(location.origin,d,function(r){
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
        }); */
        location.reload();
    })

    .on('change','[data-languages]',function(){
        var nl = $(this).val();
        $.post(location.origin,{'action':'set_language','lang':nl},function(r){
            notify('Language Changed!');
            reload(3);
        });
    });

    // FILES UI



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

    // Number Formatter

    $('body').on('keyup','input.fn',function(){
        var a = format_number($(this).val());
        $(this).val(a);
    });

    $('.fn:not(input)').each(function(i,e){
        var a = format_number($(e).html());
        $(this).html(a);
    });

    // $('input.fn').each(function(i,e){
    //     var a = format_number($(e).val());
    //     $(this).val(a);
    // });

    $("input[data-no-space]").on({
        keydown: function(e) {
            if (e.which === 32)
                return false;
        },
        change: function() {
            this.value = this.value.replace(/\s/g,'');
        }
    });

});

function format_number(a){
    /*var selection = window.getSelection().toString();
    if ( selection !== '' ) {
        return;
    }
    if ( $.inArray( a.keyCode, [38,40,37,39] ) !== -1 ) {
        return;
    }
    var a = a.toString().replace(/[\D\s\._\-]+/g, "");
    //var a = a.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
    a = a ? parseInt( a, 10 ) : 0; */
    return a.toLocaleString();
}

function fn( a ) {
    return format_number( a );
}

function unformat_number(a) {
    return parseFloat( a.replace(/[($)\s\._,\-]+/g, '') );
}

function ufn( a ){
    return unformat_number( a );
}

function store_scroll() {
    var scrollTimer;
    $('[data-save-scroll]').on( 'scroll', function(e){

        clearTimeout(scrollTimer);
        scrollTimer = setTimeout(function() {

            var id = $($(e)[0].target).attr('id');
            if (id !== undefined) {

                localStorage[id + '_scroll'] = $($(e)[0].target).scrollTop();

            }

        }, 250);

    });
}

function restore_scroll() {
    $('[data-save-scroll]').each(function(a,b){

        if( $(b).attr('id') !== undefined ) {

            var scroll_pos = localStorage[ $(b).attr('id') + '_scroll' ];
            $(b).scrollTop(scroll_pos);

        }

    })
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

// FIELD FUNCTIONS
function empty( e, d ) {
    d = d === undefined || d === '' ? '' : '[data-'+d+']';

    if( $(e)[0] && ( $(e)[0].localName === 'div' || $(e)[0].localName === 'tr' ) ) {
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

    var result;

    if( $(e)[0] && ( $(e)[0].localName === 'div' || $(e)[0].localName === 'tr' ) ){
        var r = [];
        $.each($(e).find('input'+d+',select'+d),function(a,b){
            if( b !== undefined && $(b).val() !== null && $(b).val() !== "" ){
                $(b).removeClass('empty');
                r.push(false);
            } else {
                $(b).addClass('empty');
                r.push(true);
                if( $(e).data('empty-notify') !== undefined && $(e).data('empty-notify') !== '' ) {
                    notify( $(e).data('empty-notify') );
                }
            }
            //elog(b);
        });
        //elog(r);
        result = $.inArray(true,r) !== -1 ? true : false;
    } else {
        if( $(e).val() !== null && $(e).val() !== "" ){
            $(e).removeClass('empty');
            result = false;
        } else {
            $(e).addClass('empty');
            result = true;
        }
        if( result && $(e).data('empty-notify') !== undefined && $(e).data('empty-notify') !== '' ) {
            notify( $(e).data('empty-notify') );
        }
    }
    return result;
}

function clear(e){
    $(e).val("");
}

function valid_email( id ) {
    var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
    return !pattern.test( $(id).val() );
}

function svalid_email( id ) {
    var v = valid_email( id );
    !v ? $(id).removeClass('empty') : $(id).addClass('empty');
    if( v && $(id).data('valid-email-notify') !== undefined && $(id).data('valid-email-notify') !== '' ) {
        notify( $(id).data('valid-email-notify') );
    }
    return v;
}

function slength( e, l ){
    var r = $(e).val().length >= l;
    r ? $(e).removeClass('empty') : $(e).addClass('empty');
    if( !r && $(e).data('length-notify') !== undefined && $(e).data('length-notify') !== '' ) {
        notify( $(e).data('length-notify') );
    }
    return !r;
}

function get_values( e, s, pre ) {

    pre = pre !== undefined && pre !== '' ? pre + '_' : '';

    var data = {};

    $(e).find(":input[data-"+s+"]:not(:button)","select[data-"+s+"]","textarea[data-"+s+"]").each(function () {

        var v;

        if($(this).hasClass('fn')){ v = ufn( $(this).val() ) } else { v = $(this).val() }

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

    elog(data);

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

    $(e).attr('disabled',true);

    var p = $(e).parents('[data-t]');
    var title = $(p).data('title');
    var pre = $(p).data('pre');
    if( $(p).data('sempty') !== '' && $(p).data('sempty') !== undefined ) {
        if( sempty( p, $(p).data('sempty') ) ) {
            $(e).attr('disabled',false);
            return;
        }
    }
    var d = get_values( p, pre, pre );
    d.action = $(e).data('action') !== undefined && $(e).data('action') !== '' ? $(e).data('action') : 'process_data';
    d.target = $(p).data('t');
    d.pre = pre;

    var types = Array('id','by','action','h','d','dt');

    $.each(types,function(x,a){
        if( $(p).data(a) !== undefined && $(p).data(a) !== '' ){
            d[a] = $(p).data(a);
        }
    });

    //elog(d);

    $.post( domain, d, function(r){

        //elog(r);

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
                    //files_ui();
                }
            }
        }

        $(e).attr('disabled',false);
    });
}

function edit_data( e, modal, on ) {

    var t = $(e).data('t') ? $($(e).data('t')) : $(modal);
    var data = $(e).data('data');

    if( on !== undefined ) {
        $(t).addClass('on');
    }

    $('article').addClass('fade');

    $.each( $(modal).find('button,h1,h2,h3,h4,h5,h6,span,i,p'), function( a, b ){
        if( $(b).html().indexOf('Add') >= 0){
            $(b).html( $(b).html().replace('Add','Update') );
        }
    });

    //elog(data); elog(t);

    $.each( data, function(i,d){
        if( i === 'id' ){
            t.addClass('on').data('id',d);
        } else {
            $('#'+i).val(d).change();
        }
    });

    /* if( typeof files_ui === 'function' ){
        files_ui();
    }

    if( typeof file_ui === 'function' ){
        file_ui();
    } */
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

function slide_toggle( e, t ){
    $('body').on('click',e,function(){
        $(this).find(t).slideToggle();
    })
}

function reload( time_seconds ){

    var t = time_seconds !== undefined && time_seconds !== '' ? time_seconds * 1000 : 5000;
    setTimeout(function(){ location.reload() },t);

}

function post( action, data, notif, reload ) {

    var d = $.extend({}, {'action':action}, data);

    $.post( location.origin, d, function(r) {

        elog(r);

        try {

            var r = JSON.parse( r );

            if( notif !== '' ) {

                notify( r[1], notif );

            }

            if( r[0] === 1 && reload !== undefined ) {

                setTimeout(function(){ location.reload() }, reload * 1000 );

            }

            elog(r);

            return r[0] === 1;

        }
        catch( e ) { elog(e); }

    })

}

/* async function paste( e ) {
    var t = navigator.clipboard.readText();
    $(e).val(t);
} */

// TODO: Search filter to filter repeating elements for specific text
function search_filter( repeater, targets, c ) {

}

function page_warning( $message ) {
    console.log( '%c' + $message, 'width:100%;background:#fff;border-radius:6px;box-shadow:0 0 15px rgba(0,0,0,.2);font-size:22px;padding:40px;text-align:center;color:#000;font-family:"Lato",sans-serif;font-weight:400;margin:20px;' );
}