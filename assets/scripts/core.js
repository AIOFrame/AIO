var domain;
let b = $('body');
$(document).ready(function(){
    let b = $('body');
    // MANIPULATOR
    // TODO: Change resetsrc and resetinput to reset
    $(document).on('click', '[data-action], [data-show], [data-on], [data-off], [data-hide], [data-slide], [data-remove], [data-toggle], [data-resetsrc], [data-resetinput], [data-click], [data-href],[data-force-on]', function (e) {
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
        $($(this).data('off')).removeClass('on');
        $($(this).data('force-on')).addClass('on');
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

    .on('click','[data-dark]',function(){
        $(this).toggleClass('on');
        $(b).toggleClass('d');
        localStorage.setItem('dark_mode',$(b).hasClass('d'));
    })

    .on('change','[data-check]',function(){
        var v = $(this).is(':checked') ? '1' : '0';
        $($(this).data('check')).val(v);
    })

    .on('click','.aio_dynamics .add',function(){
        var dyn = $(this).parents('.aio_dynamics').prev('input').data('dyn');
        $(this).parents('.aio_dynamics').find('.fields').append(dyn);
    })

    .on('click','.aio_dynamics .trash',function(){
        $(this).parents('.field_set').remove();
    })

    .on('change keyup','.aio_dynamics input',function () {
        var d = [];
        $.each( $(this).parents('.aio_dynamics').find('.field_set'), function(a,b){
            d.push( get_values( b ) );
        });
        elog(d);
        $(this).parents('.aio_dynamics').prev('input').val( JSON.stringify( d ) );
    });

    // Color Picker
    $(b).on('click','[data-color-picker]',function(){
        $('.color_picker_wrap').addClass('on');
        init_color_picker($(this),$(this).val());
    })

    .on('click','.color_picker_wrap .close',function(){
        $('.color_picker_wrap').removeClass('on');
    })

    .on('keyup','.color_picker_wrap input',function(){
        if( $(this).val().length > 6 ) {
            var cp = $('.color-picker');
            $(cp).html('');
            var colorPicker = iro.ColorPicker('.color-picker', {'color': $(this).val()});
            $($(cp).data('value')).val($(this).val());
        }
    })

    // Load Dark Mode
    var dark = localStorage.getItem('dark_mode');
    dark === 'true' ? b.addClass('d') : b.removeClass('d');

    if( $('[data-color-picker]').length > 0 ) {
        $('<div class="color_picker_wrap"><div class="close"></div><div class="color-picker"></div><div class="controls"><input type="text" value="#ffffff" class="code"></div></div>').appendTo(b);
    }

    // Scroll Save

    restore_scroll();

    store_scroll();

    // TABS & STEPS
    var pagepath = location.href.replace(location.origin+'/','').replace('/','_');
    var tab = localStorage[ pagepath + '_tab'];
    if( tab !== undefined ){
        setTimeout(function(){ $('[data-t="' + tab + '"]').click(); },200);
    }

    $(b).on('click', '.steps [data-t],.tabs [data-t]', function () {
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

    // Dynamic Data
    $('[data-dynamic]').each( function(a,b){

        var dyn = '<div class="field_set"><div class="trash"></div>';
        var i = 0;
        $.each( $(b).data('dynamic'), function(c,d){
            i++;
            dyn += '<div class="set">';

            if( d[0] === 'text' ) {
                dyn += '<label for="' + d[1] + '_' + i + '">'+ d[2] +'</label><input type="' + d[0] + '" placeholder="'+ d[2] +'" id="'+ d[1] +'_'+i+'">'
            } else if( d[0] === 'div' ) {
                dyn += '<div class="'+d[1]+'">'+location.href+'page</div>';
            } else if( d[0] === 'checkbox' || d[0] === 'radio' ) {
                dyn += '<input type="' + d[0] + '" id="'+ d[1] + '_' + i + '"><label for="' + d[1] + '_' + i + '">'+ d[2] +'</label>'
            }
            dyn += '</div>';
        });
        dyn += '</div>';
        $('<div class="aio_dynamics"><div class="fields">'+dyn+'</div><div class="btn add">+</div></div>').insertAfter($(b));
        $(this).data('dyn',dyn).hide();
    });

    // Prevent Default
    $(b).on('click','a .prevdef',function(e){
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

    // ALERT & notify
    $('body').on('click','.notify .close',function(){
        // Prepare for Removal
        $(this).parents('.notify').addClass('out');
        // Remove Notification
        setTimeout(function(){ $(this).parents('.notify').remove() }, 2000 );
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
    $(b).on('click','[data-lang]',function(){
        var cl = $('[data-lang].on').data('lang');
        var nl = $(this).data('lang');
        var d = {'action':'get_translations','languages':[cl,nl],'method':'json'};
        //$.post(location.origin,{'action':'set_language','lang':nl});
        elog(nl);
        post('set_language',{'lang':nl},'',1);
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
    })

    .on('change','[data-languages]',function(){
        var nl = $(this).val();
        $.post(location.origin,{'action':'set_language','lang':nl},function(r){
            notify('Language Changed!');
            reload(3);
        });
    });
    
    // Fetch States

    $(b).on('change','select[data-states]',function(){
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

    $(b).on('keyup','input.fn',function(){
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

    appdebug = $(b).hasClass('debug') ? true : false;
});

$(document).mouseup(function(e) {
    var cp = $('.color_picker_wrap');
    if (!cp.is(e.target) && cp.has(e.target).length === 0) {
        cp.removeClass('on').children('.color-picker').html('');
    }
});

function scroll_to( element, parent, speed ) {
    speed = speed !== undefined && speed !== '' ? speed : 1000;
    parent = parent !== undefined && parent !== '' ? parent : 'html,body';
    if( !$( parent ).data['scrollbar'] ) {
        $(parent).animate({scrollTop: $(element).offset().top}, speed);
    } else {
        const scrollbar = Scrollbar.init( $( parent )[0] );
        scrollbar.scrollIntoView( $( element )[0] );
    }
}

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

function init_color_picker( e, c ) {
    var cp = $('.color-picker');
    $(e).data('value') !== undefined ? $(cp).data('value',$(e).data('value')) : $(cp).data('value',$(e)); // Reads target element to set value, and sets it to color picker
    $(e).data('background') !== undefined ? $(cp).data('background',$(e).data('background')) : '';
    var v = {};
    v.color = $(e).val() !== '' && $(e).val() !== undefined ? $(e).val() : '#fff';
    v.width = $(e).data('width') !== '' && $(e).data('width') !== undefined ? $(e).data('width') : '200';
    var colorPicker = new iro.ColorPicker('.color-picker',c);
    colorPicker.on('color:change', onColorChange);
    onColorChange(colorPicker.hexString)
}

function onColorChange( color ) {
    var cp = $('.color-picker');
    if( $(cp).data('value') !== undefined ) {
        $($(cp).data('value')).val(color.hexString)
        $($(cp).data('value')).css({'border-color':color.hexString});
    };
    $('.color_picker_wrap input').val(color.hexString);
    $(cp).data('background') !== undefined ? $(cp).data('background').css({'background-color':color.hexString}) : '';
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

    s = s !== undefined && s !== '' ? '[data-'+s+']' : '';

    var data = {};

    // Loop thru the input elements
    $(e).find(":input"+s+":not(:button)","select"+s,"textarea"+s).each(function () {

        // Define Pre and Key
        var pk;
        var n = $(this).attr('name');
        var k = $(this).data('key');
        var id = $(this).attr('id');
        if( k !== undefined ){
            pk = pre + k;
        } else if( id !== undefined ){
            pk = pre + id;
        } else if( n !== undefined ) {
            pk = pre + n;
        } else {
            pk = pre + $(this).attr('class');
        }

        var m = $('[name='+n+']');
        var v;

        v = $(this).hasClass('fn') ? ufn( $(this).val() ) : $(this).val(); // Un Format Number

        if( $(this).attr('type') === 'checkbox' ){

            var t = $(this).is(':checked');

            if( m.length > 1 ) {
                v = $('[name=' + $(this).attr('name') + ']').map(function(){
                    if( $(this).is(':checked') )
                        return $(this).val();
                }).toArray();
            } else {
                v = t === true ? 1 : 0;
            }

        } else if( $(this).attr('type') === 'radio' ) {

            k = $(this).is(':checked') ? $(this).attr('name') : '';
            k = $(this).data('key') !== undefined ? $(this).data('key') : k;
            //v = $(this).is(':checked') ? $(this).val() : '';
            v = $('input[name='+$(this).attr('name')+']:checked').val();
            data[ pk ] = v;
            return true;
        } else if( $(this).is( "select" ) && $(this).attr('multiple') !== undefined ) {

            v = $(this).val().join(", ");

        }

        // Finally push the value
        data[ pk ] = v;

    });

    //elog(data);

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
    if( s === '' || s === undefined ) {
        $(e).find(":input:not(:button)","select","textarea").each(function () {
            $(this).val("").trigger('chosen:updated');
        });
    } else {
        $(e).find(":input[data-"+s+"]:not(:button)","select[data-"+s+"]","textarea[data-"+s+"]").each(function () {
            $(this).val("").trigger('chosen:updated');
        });
    }
}

var appdebug = $(b).hasClass('debug') ? true : false;

function process_data( e ){

    //$(e).attr('disabled',true);

    var p = $(e).parents('[data-t]');

    p.addClass('load');
    var title = $(p).data('title');
    var pre = $(p).data('pre');

    // Check for empty values
    if( $(p).data('sempty') !== '' && $(p).data('sempty') !== undefined ) {
        if( sempty( p, $(p).data('sempty') ) ) {
            $(p).removeClass('load');
            $(e).attr('disabled',false);
            notify('Input fields seem to be empty! Please fill and try again!!');
            return;
        }
    }

    // Disable Send Button
    if( $(p).data('reload') !== undefined && $(p).data('reload') !== null && parseInt( $(p).data('reload') ) > 0 ) {
        $(p).find('[onclick="process_data(this)"]').attr('disabled',true);
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

    elog(d);
    post( d.action, d, p.data('notify'), p.data('reload'), p.data('redirect'), 0, p.data('callback'), p.data('reset') );

}

function process_finish( r ) {
    elog( r );
    //$(e).attr('disabled',false);
}

function edit_data( e, modal, on ) {

    var t = $(e).data('t') ? $($(e).data('t')) : $(modal);
    var data = $(e).data('data');

    if( on !== undefined ) {
        $(t).addClass('on');
    }

    $('article').addClass('fade');

    /*
    $.each( $(modal).find('button,h1,h2,h3,h4,h5,h6,span,i,p'), function( a, b ){
        if( $(b).html().indexOf('Add') >= 0){
            $(b).html( $(b).html().replace('Add','Update') );
        }
        if( $(b).html().indexOf('ADD') >= 0){
            $(b).html( $(b).html().replace('ADD','UPDATE') );
        }
    });
    */
    $(modal).find('[data-add]').hide();
    $(modal).find('[data-update]').show();

    //elog(data); elog(t);

    $.each( data, function(i,d){
        if( i === 'id' ){
            t.addClass('on').data('id',d);
        } else {
            if( $('#'+i).attr('type') === 'checkbox' ){

                d === '1' || d === true ? $('#'+i).prop('checked',true) : $('#'+i).prop('checked',false);

            } if( $('#'+i).prop('type') == 'select-multiple' ) {
                var d = $.map(d.split(','), function(value){
                    return parseInt(value);
                });
                $('#'+i).val(d).change();
            } else {
                $('#'+i).val(d).change();
            }
        }
    });

    if( typeof files_ui === 'function' ){
        files_ui();
    }

    if( typeof file_ui === 'function' ){
        file_ui();
    }
}

function reset_modal(e) {
    var m = $(e).parents('[data-t]');
    /*
    $.each( $(e).parents('.modal').find('button,h1,h2,h3,h4,h5,h6,span,i,p'), function( a, b ){
        if( $(b).html().indexOf('Update') >= 0){
            $(b).html( $(b).html().replace('Update','Add') );
        }
        if( $(b).html().indexOf('UPDATE') >= 0){
            $(b).html( $(b).html().replace('UPDATE','ADD') );
        }
    });
    */
    $(m).find('[data-add]').show();
    $(m).find('[data-update]').hide();

    $(m).data('id','');

    // TODO: If modal has class to empty then empty fields

}

/* Example: <i class="ico trash" onclick="trash_data(<?php echo $cry->encrypt('table|column|'.$id); ?>)"></i> */

function trash_data( q ) {
    var d = { 'action':'trash_data', 'query':q };
    elog(d);
    if( confirm('Are you sure to delete ?') ){
        post( 'trash_data', { 'query': q }, 2, 2 )
    }
}

function elog( d ) {
    if( appdebug ) {
        if (window.console) {
            if (Function.prototype.bind) {
                elog = Function.prototype.bind.call(console.log, console);
            }
            else {
                elog = function() {
                    Function.prototype.apply.call(console.log, console, arguments);
                };
            }
            elog.apply(this, arguments);
        }
    }
}

function getParam( param ) {
    let p = new URLSearchParams(window.location.search);
    return p.get( param );
}

// ALERTS

function alerted( $m ) {
    //alert( $m )
}

function notify( text, duration ) {
    // Process duration of notification
    duration = duration !== undefined && duration !== '' && duration > 0 ? duration * 1000 : 6000;

    // Create notification message
    var r = Math.random().toString(36).substring(7);
    var n = '<div class="notify in n_'+r+'"><div class="data"><div class="close"></div><div class="message">'+text+'</div></div><div class="time"></div></div>'
    var id = '.n_' + r;
    let ns = $('.notices');

    // Animate Timer
    var perc = 100;
    var timer = setInterval(function(){
        perc--;
        $( id + ' .time' ).css({ 'width': 'calc('+perc+'% - 20px)' });
    }, (duration + 400) / 100 );
    setTimeout(function(){ clearInterval(timer) },duration);

    // Add Notification
    ns.hasClass('b') ? ns.prepend(n) : ns.append(n);
    setTimeout(function(){ $(id).removeClass('in') },100);

    // Prepare for Removal
    setTimeout(function(){ $(id).addClass('out') },duration+1000);

    // Remove Notification
    setTimeout(function(){ $(id).remove() },duration+2000);
}

function slide_toggle( e, t ){
    $(b).on('click',e,function(){
        $(this).find(t).slideToggle();
    })
}

function reload( time_seconds ){

    var t = time_seconds !== undefined && time_seconds !== '' ? time_seconds * 1000 : 5000;
    setTimeout(function(){ location.reload() },t);

}

function post( action, data, notify_time, reload_time, redirect, redirect_time, callback, reset ) {
    // Google Recaptcha V3 Start
    if( callback !== undefined && callback !== '' && callback.indexOf('recaptcha') >= 0 ) {
        var site_key = $('script[data-recaptcha]').attr('src').replace('https://www.google.com/recaptcha/api.js?render=','');
        if( site_key !== '' && site_key !== undefined && site_key !== null ) {
            grecaptcha.ready(function() {
                grecaptcha.execute(site_key,{action:'recaptcha_verify'}).then(function(t) {
                    data.recaptcha_token = t;
                    post( action, data, notify_time, reload_time, redirect, redirect_time, callback.replace('recaptcha,','').replace('recaptcha',''), reset  )
                });
            });
        }
        return;
    }
    // Google Recaptcha V3 End
    elog(callback);
    var d = $.extend({}, {'action':action}, data);
    $.post( location.origin, d, function(r) {
        elog(r);
        try {
            r = JSON.parse( r );
            elog(r);
            if( notify_time !== undefined && notify_time !== '' ) {
                notify( r[1], notify_time );
            }
            if( r[0] !== 0 && reload_time !== undefined && reload_time !== '' && reload_time !== '0' ) {
                $.isNumeric( reload_time ) ? setTimeout(function(){ location.reload() }, reload_time * 1000 ) : location.href = reload_time;
            }
            if( r[0] !== 0 && reset !== undefined && reset !== '' && reset !== '0' ) {
                $('[data-'+reset+']').val('');
            }
            if( callback !== undefined && callback !== '' ) {
                callback = callback.split(',');
                $.each( callback, function(i,call){
                    if( call !== 'recaptcha' ) {
                        eval( call + '(' + JSON.stringify( r ) + ')' );
                    }
                });
            }
            if( redirect !== undefined && redirect !== '' ) {
                redirect_time = redirect_time !== undefined && redirect_time !== '' ? redirect_time : 0;
                if( r[0] !== 0 ) {
                    setTimeout(function(){
                        location.href = redirect;
                    }, redirect_time * 1000)
                }
            }
            //this[callback](r);
        }
        catch( rat ) {
            elog( rat );
        }
    });
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

function is_mobile() {
    return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
}