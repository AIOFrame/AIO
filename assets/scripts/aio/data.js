if (typeof window.elog === 'undefined' ) {
    window.elog = function(r) {
        if( $('body').hasClass('debug') )
            console.log(r);
    }
}

$(document).ready(function(){
    $(document).on('change', '[data-process-change],[data-change-action]', function (e) {
        let action = $(this).data('process-change') !== undefined ? $(this).data('change-action') : '';
        if( action !== '' ) {
            post($(this).data('process-change'),{'data':$(this).val()},'','','','',$(this).data('callback'));
        }
    });

    $(document).on('keyup','[data-enter-submit]',function (e) {
        if( e.keyCode === 13 ) {
            process_data( $(e).parents('[data-t]') );
        }
    })
    .on('change','[auto-post],[auto-process],[auto-submit]',function () {
        $( $(this).parents('[data-t]') ).find('[onclick]').click();
    })
})

/**
 * Gets values of all inputs within an element (of specific data attribute if)
 * @param parent HTML Element Selector
 * @param attribute Data param to choose inputs
 * @param prepend Add a pre text to keys
 * @returns {{}}
 */
function get_values( parent, attribute, prepend ) {
    //elog('running');
    let a = attribute;
    a = a !== undefined && a !== '' ? '[data-'+a+']' : '';
    let pre = prepend;
    pre = pre !== undefined && pre !== '' ? pre : '';

    let data = {};

    // Loop through the input elements
    $(parent).find(":input"+a+":not(:button)","select"+a,"textarea"+a).each(function () {

        // Define Pre and Key
        let pre_key;
        let name = $(this).attr('name');
        name = name !== undefined ? name.replace('[]',''): undefined;
        let key = $(this).data('key');
        let id = $(this).attr('id');
        if( $(this).data('array') !== undefined )
            pre_key = pre + $(this).data('array');
        else if( key !== undefined )
            pre_key = pre + key;
        else if ( id !== undefined )
            pre_key = pre + id;
        else if( name !== undefined )
            pre_key = pre + name;
        else
            pre_key = pre + $(this).attr('class');
        console.log("pre Key");
        console.log(pre_key);

        let m = $(parent).find( '[name=' + name + ']' );

        // Set default value for text, number, email, textarea, select
        let value;
        value = $(this).hasClass('fn') ? ufn( $(this).val() ) : $(this).val(); // Un Format Number

        if( $(this).attr('type') === 'checkbox' ){
            let t = $(this).is(':checked');
            let arr = $(this).data('array');
            let v =  $(this).val();
            if ( $(this).hasClass('slide') ) {
                //elog(0);
                value = $(this).is(':checked') ? 1 : 2;
            } else if ( arr !== undefined ) {
                //elog(1);
                if( data[pre_key] === undefined || !$.isArray( data[pre_key] ) ) {
                    data[pre_key] = [];
                }
                if( t ) {
                    data[pre_key].push( v );
                }
            } else if (m.length > 1) {
                //elog(2);
                value = $( '[name=' + $(this).attr('name') + ']' ).map(function () {
                    if( t ) {
                        return $(this).val();
                    }
                }).toArray();
            } else {
                //elog(3);
                value = t === true ? 1 : 2;
            }
        }
        else if( $(this).attr('type') === 'radio' && $('input[name=' + $(this).attr('name') + ']:checked') ) {
            // key = $(this).is(':checked') ? $(this).attr('name') : '';
            // key = $(this).data('key') !== undefined ? $(this).data('key') : $(this).attr('name');
            //v = $(this).is(':checked') ? $(this).val() : '';
            data[ pre_key ] = $('input[name=' + $(this).attr('name') + ']:checked').val();
            return true;
        }
        else if( $(this).is( "select" ) && $(this).attr('multiple') !== undefined ) {
            value = $(this).val().join(", ");
        }
        if( $(this).data('array') !== undefined ) {
            //elog(key);
            //elog(pre_key);
            let arr = $(this).data('array');
            key = $(this).val() !== '' ? $(this).attr('name') : '';
            key = key === '' ? $(this).attr('id') : key;
            let key2 = $(this).data('key') !== undefined ? $(this).data('key') : key;
            data[pre_key] = data[pre_key] === undefined ? {} : data[pre_key];
            $(this).val() !== '' ? data[ pre_key ][ key2 ] = value : '';
            //return true;
        } else {
            // Finally push the value
            data[ pre_key ] = value;
        }

        elog(key);
        elog( 'Updated:' );
        elog( data );

    });
    $.each(data,function(a,b){
        if( typeof b === 'object' && b !== null ) {
            /* elog( b );
            elog( JSON.stringify(b) ); */
            data[a] = JSON.stringify(b);
        }
    });
    //elog(data);
    return data;
}

/**
 * Gets checkbox values
 * @param e Parent element
 * @param string
 * @returns {string|[]}
 */
function get_checkbox_values( e, string ) {
    let d = [];
    $('[name='+e+']').each(function(a,b){
        $(b).is(':checked') ? d.push($(b).val()) : '';
    });
    if( string ) {
        return d.join(', ');
    } else {
        return d;
    }
}

/**
 * Checks if email is properly formatted and adds 'empty' class
 * @param id Email ID
 * @returns {boolean}
 */
function email_valid( id ) {
    let v = _email_valid( id );
    !v ? $(id).removeClass('empty') : $(id).addClass('empty');
    if( v && $(id).data('valid-email-notify') !== undefined && $(id).data('valid-email-notify') !== '' ) {
        notify( $(id).data('valid-email-notify'), 5, 'error', 'warning' );
    }
    return v;
}

/**
 * Checks if email is properly formatted
 * @param id Email ID
 * @returns {boolean}
 */
function _email_valid( id ) {
    let pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
    return !pattern.test( $(id).val() );
}

/**
 * Check if input field is empty and add class "empty"
 * @param e Input element or Parent element identifier ID or Class
 * @param d Data attribute of input
 */
function is_empty( e, d ) {
    d = d === undefined || d === '' ? '' : '['+d+']';
    let required = [];
    if( $(e)[0] && ( $(e)[0].localName === 'div' || $(e)[0].localName === 'tr' || $(e)[0].localName === 'form' ) ){
        $.each($(e).find('input'+d+',select'+d+',textarea'+d),function(a,b){
            // TODO: Check if the input is inside a tab and activate that tab
            if( b !== undefined ) {
                if( $(b).attr('type') === 'checkbox' || $(b).attr('type') === 'radio' ) {
                    if( $(b).is(':checked') ) {
                        $(b).removeClass('empty');
                    } else {
                        $(b).addClass('empty');
                        required.push( $(b).attr('title') );
                    }
                } else {
                    if( $(b).val() !== null && $(b).val() !== '' ) {
                        $(b).removeClass('empty');
                    } else {
                        $(b).addClass('empty');
                        required.push( $(b).attr('title') );
                    }
                }
            }
        });
    } else {
        if( $(e).val() !== null && $(e).val() !== "" ){
            $(e).removeClass('empty');
        } else {
            $(e).addClass('empty');
            required.push( $(e).attr('title') );
        }
    }
    return required;
}

/*
This function runs a valid_field function on single input or a inputs inside an element wrap
 */
function validator( e, d ) {
    d = d === undefined || d === '' ? '' : '['+d+']';
    let invalid = [];
    // If Validating a group of inputs
    if( $(e)[0] && ( $(e)[0].localName === 'div' || $(e)[0].localName === 'tr' || $(e)[0].localName === 'form' ) ){
        invalid = [];
        $.each($(e).find('input'+d+',select'+d+',textarea'+d),function(a,b){
            let validation = valid_field( b );
            if( validation.length > 0 ) {
                invalid.push( [ $(b).attr('title'), validation ] );
                $(b).addClass('invalid');
            } else {
                //console.log('removed empty');
                $(b).removeClass('invalid');
            }
        });
    // else If validating a single input
    } else {
        let validation = valid_field( e );
        if( validation.length > 0 ){
            invalid.push( [ $(e).attr('title'), validation ] );
            $(e).addClass('invalid');
        } else {
            $(e).removeClass('invalid');
        }
    }
    return invalid;
}

function valid_field( field ) {
    //console.log( $(field) );
    let invalid_reasons = [];
    // Minimum Number / Amount Validation
    let min = $(field).attr('min');
    if( $(field) !== undefined && min !== undefined && $(field).val() < parseInt( min ) ) {
        let notice = $(field).data('min_notice');
        let message = notice !== undefined ? notice.replaceAll('{min}',min) : 'Number should be more than '+min+'!';
        invalid_reasons.push(message);
    }
    // Maximum Number / Amount Validation
    let max = $(field).attr('max');
    if( $(field) !== undefined && max !== undefined && $(field).val() > parseInt( max ) ) {
        let notice = $(field).data('max_notice');
        let message = notice !== undefined ? notice.replaceAll('{max}',min) : 'Number should be less than '+min+'!';
        invalid_reasons.push(message);
    }
    // Characters Minimum Length Validation
    let minlength = $(field).attr('minlength');
    if( $(field) !== undefined && minlength !== undefined && $(field).val().length < parseInt( minlength ) ) {
        let notice = $(field).data('minlength_notice');
        let message = notice !== undefined ? notice.replaceAll('{min}',min) : 'Char should be more than '+minlength+' characters!';
        invalid_reasons.push(message);
    }
    // Characters Maximum Length Validation
    let maxlength = $(field).attr('maxlength');
    if( $(field) !== undefined && maxlength !== undefined && $(field).val().length > parseInt( maxlength ) ) {
        let notice = $(field).data('maxlength_notice');
        let message = notice !== undefined ? notice.replaceAll('{max}',min) : 'Char should be less than '+maxlength+' characters!';
        invalid_reasons.push(message);
    }
    //console.log(invalid);
    // TODO: Validate Password
    if( $(field).attr('type') === 'email' ) {
        // Validate Email
        if( _email_valid( field ) ) {
            let notice = $(field).data('invalid_notice');
            let message = notice !== undefined ? notice : 'Email format is not valid!';
            invalid_reasons.push(message);
        }
        // Restrict Emails
        let domain = $(field).val().split('@')[1];
        let restrict_domains = $(field).data('restrict_domains');
        if( $(field) !== undefined && domain !== undefined && restrict_domains !== undefined && restrict_domains.indexOf(domain) >= 0 ) {
            let notice = $(field).data('restrict_domains_notice');
            let message = notice !== undefined ? notice : 'Email domain is restricted!';
            invalid_reasons.push(message);
        }
    }
    return invalid_reasons;
}

/**
 * Check if input field is empty
 * @param e Input element or Parent element identifier ID or Class
 * @param d Data attribute of input
 * @returns {boolean}
 */
function _is_empty( e, d ) {
    d = d === undefined || d === '' ? '' : '[data-'+d+']';
    if( $(e)[0] && ( $(e)[0].localName === 'div' || $(e)[0].localName === 'tr' || $(e)[0].localName === 'form' ) ) {
        let r = [];
        $.each($(e).find('input'+d+',select'+d+',textarea'+d),function(a,b){
            if( b !== undefined && $(b).val() !== null && $(b).val() !== "" ){
                r.push(false);
            } else {
                elog( b );
                r.push(true);
            }
        });
        return $.inArray(true, r) !== -1;
    } else {
        return $(e).val().length <= 0;
    }
}

/**
 * Empty and unselect input fields
 * @param e Input element or parent element
 * @param d Only empty the given data attribute
 */
function clear( e, d ){
    if( $(e)[0] && ( $(e)[0].localName === 'div' || $(e)[0].localName === 'tr' || $(e)[0].localName === 'form' ) ){
        elog($(e)[0].localName);
        $.each($(e).find('input'+d+',select'+d+',textarea'+d),function(a,b){
            $(b).val('');
        })
    } else {
        $(e).val('');
    }
}

// AJAX Data

function process_data( e, ne ){
    console.log(e);
    console.log(ne);
    //$(e).attr('disabled',true);
    let p;
    if( $(e).parents('[data-t]') !== undefined && $(e).parents('[data-t]') !== null && $(e).parents('[data-t]').length > 0 ) {
        p =  $(e).parents('[data-t]');
        $(p).find('[onclick="process_data(this)"]').attr('disabled',true).addClass('load');
        setTimeout(function(){
            $(p).removeClass('load').find('[onclick="process_data(this)"]').attr('disabled',false).removeClass('load');
        },5000);
        elog( $(e).parents('[data-t]') );
    } else { // TODO: Add logic to get all params from html button and parent element from where inputs inside will be validated
        p =  $(e);
        $(p).attr('disabled',true).addClass('load');
        setTimeout(function(){
            $(p).attr('disabled',false).removeClass('load');
        },5000);
    }
    elog('here');

    // Confirm
    let con = $(p).data('confirm');
    if( con !== undefined && con !== '' ) {
        if( !confirm( con ) ) {
            return;
        }
    }
    //elog('testtt');
    elog(p);
    if( p.hasClass('load') ) {
        return;
    }
    p = ( p.length !== 0 && p[0].tagName === 'DIV' ) ? p : $(e).parents('[data-data]');
    p.addClass('load');

    let pre = $(p).data('pre');
    pre = pre !== undefined ? pre : '';

    let data = $(p).data('data');
    data = data !== undefined ? data : '';

    //if( $(p).attr('required') !== undefined ) {
    // Check for empty values
    let breaker = [];

    let required = is_empty( p, 'required' );
    if( required.length > 0 ) {
        $(p).removeClass('load');
        console.log(required);
        $(e).attr('disabled',false).removeClass('load');
        let empty_note = $(p).data('empty') !== undefined ? $(p).data('empty') : 'The highlighted fields seem to be empty!';
        empty_note += '<div class="fields">';
        $(required).each(function(i,req){
            if( req !== '' && req !== undefined ) {
                empty_note += '<div class="field">'+req+'</div>';
            }
        });
        empty_note += '</div>';
        notify( empty_note, 5, 'error', 'warning' );
        breaker.push(1);
    }
    //elog('testtt');

    let invalid = validator( p );
    if( invalid.length > 0 ) {
        $(p).removeClass('load');
        console.log(invalid);
        $(e).attr('disabled',false).removeClass('load');
        let invalid_note = $(p).data('invalid') !== undefined ? $(p).data('invalid') : 'The highlighted fields seem to be invalid!';
        invalid_note += '<div class="fields invalid_fields">';
        $(invalid).each(function(i,validation){
            let field_title = validation[0];
            let reasons = validation[1];
            if( reasons.length > 0 ) {
                $(reasons).each(function (a,reason) {
                    let reasons_note = '<div class="reason">'+reason+'</div>';
                    invalid_note += '<div class="invalid"><div class="field">'+field_title+'</div>'+reasons_note+'</div>';
                });
            }
        });
        invalid_note += '</div>';
        notify( invalid_note, 99, 'error', 'warning' );
        breaker.push(1);
    }

    if( breaker.length > 0 ) {
        //notify( 'test' );
        return;
    }
    //}

    // Disable Send Button
    //if( $(p).data('reload') !== undefined && $(p).data('reload') !== null && parseInt( $(p).data('reload') ) > 0 ) {
    //}

    let d = get_values( p, data, pre );
    elog(d);
    d.action = $(e).data('action') !== undefined ? $(e).data('action') : $(p).data('t');
    if( $(p).data('t') !== '' && $(p).data('t') !== undefined ) {
        d.t = $(p).data('t');
    }
    if( $(p).data('pre') !== '' && $(p).data('pre') !== undefined ) {
        d.pre = pre;
    }
    elog(d);
    /* let a = $(p).data('a');
    if( a !== undefined && a !== null ) {
        d.a = a;
    } */
    let pos = $(p).data('post');
    if( pos !== undefined && pos !== null ) {
        d.post = pos;
    }
    if( d.action === undefined || d.action === null ) {
        elog('Action not set!');
    }

    let types = Array('id','by','action','h','d','dt','alerts','emails');

    $.each(types,function(x,a){
        if( $(p).data(a) !== undefined && $(p).data(a) !== '' ){
            d[a] = $(p).data(a);
        }
    });
    elog(d);

    // Validation Function, Note: Validation here means no validation in post function
    let validation = p.data('validation') !== '' && p.data('validation') !== undefined ? p.data('validation') : '';

    // Callback Function
    let cb = '';
    if( p.data('callback') !== '' && p.data('callback') !== undefined ) {
        cb = 'process_end,' + p.data('callback');
        d.callback = p.data('callback');
    } else {
        cb = 'process_end';
    }
    post( d.action, d, p.data('notify'), p.data('reload'), p.data('redirect'), 0, cb, p.data('reset'), p, validation );

}

function process_end( p, r ) {
    $(p).removeClass('load').find('[onclick="process_data(this)"]').attr('disabled',false).removeClass('load');
    elog( p );
}

function edit_data( e, modal ) {

    let t = $(e).data('t') ? $($(e).data('t')) : $(modal);
    let data = $(e).data('data');

    if( !$(t).hasClass('on') ) {
        $(t).addClass('on');
    }

    $('article').addClass('fade');
    $(modal).find('[data-add]').hide();
    $(modal).find('[data-update],[data-edit]').show();
    //elog(data);
    $.each( data, function(i,d){
        //elog(i);
        if( d === null ) {
            return;
        }
        if( i === 'id' ){
            t.data('id',d).find('[data-t]').data('id',d);
            $(t).hasClass('modal') ? t.addClass('on') : '';
        } else {
            let el = $(t).find('[data-key='+i+']');
            //console.log( el.attr('type') );
            if( el.attr('type') === 'checkbox' ){
                if( el.data('key') !== undefined ) {
                    //elog(el);
                    d = !$.isArray(d) ? JSON.parse(d) : [d];
                    //elog(d);
                    if( $.isArray(d) ) {
                        $(d).each(function(a,b){
                            let s = $('[data-key='+i+'][value='+b+']');
                            d === 1 || d === '1' || d === true || d.length > 0 ? s.prop('checked',true) : s.prop('checked',false);
                        })
                    } else {
                        d === 1 || d === '1' || d === true || d.length > 0 ? el.prop('checked',true) : el.prop('checked',false);
                    }
                } else {
                    let s = $('[data-key='+i+'][value='+d+']');
                    d === 1 || d === '1' || d === true || d.length > 0 ? s.prop('checked',true) : s.prop('checked',false);
                }
            } else if( el.attr('type') === 'radio' ) {
                let s = $('[data-key='+i+'][value='+d+']');
                d === '1' || d === true || d.length > 0 ? s.prop('checked',true) : s.prop('checked',false);
            } else if( el.prop('type') === 'select-multiple' ) {
                $(el).val(d.split(', ')).trigger('change'); //.find('option[value="' + v + '"]').prop('selected', true);
                /* $.each(d.split(', '), function(ix,v){
                    elog($(el).find('option[value="' + v + '"]'));
                }); */
                /* $.map(d.split(','), function(value){
                    return parseInt(value);
                });*/
                /* let tar = '#'+i;
                if( $(tar).length ) {
                    $(tar).val(d).change();
                } else {
                    $('[data-key="'+i+'"]').val(d).change();
                }*/
            } else {
                //console.log('here');
                let tar = '#'+i;
                //console.log(tar);
                if( $(tar).length ) {
                    //$(tar).remove();
                    $(tar).val(d).trigger('change');
                } else {
                    //let element = $('[data-key="'+i+'"]');
                    if( el.data('hidden-date') !== undefined ) {
                        //elog( element );
                        elog( el );
                        let date = new Date( d );
                        elog( date );
                        elog( '#'+el.attr('id')+'_alt' );
                        $( '#'+el.attr('id')+'_alt' ).val( [('0'+date.getDate()).slice(-2), ('0'+date.getMonth()).slice(-2), date.getFullYear()].join('-') ).trigger('change');
                    }
                    el.val(d).trigger('change');
                }
            }
            //elog('#'+i);
            //elog(d);
        }
    });

    typeof files_ui === 'function' ? files_ui() : '';
    typeof file_ui === 'function' ? file_ui() : '';
}

function update_data( e, action, target, keys, values, logic, notify_time, reload_time ) {
    let data = { 'action': action, target: target, keys: keys, values: values, logic: logic };
    let con = $(e).data('confirm');
    let n = notify_time !== undefined && notify_time !== '' ? notify_time : 2;
    let r = reload_time !== undefined && reload_time !== '' ? reload_time : 2;
    if( con !== undefined && con !== '' ){
        if( !confirm( con ) ) {
            return;
        }
    }
    post( action, data, n, r );
}

function trash_data( e, action, target, logic, notify_time, reload_time ) {
    let data = { 'action': action, target: target, logic: logic };
    let con = $(e).data('confirm');
    let n = notify_time !== undefined && notify_time !== '' ? notify_time : 2;
    let r = reload_time !== undefined && reload_time !== '' ? reload_time : 2;
    if( con !== undefined && con !== '' ){
        if( !confirm( con ) ) {
            return;
        }
    }
    post( action, data, n, r );
}

function post( action, data, notify_time, reload_time, redirect, redirect_time, callback, reset, p, validation ) {
    //elog(callback);

    // Custom Validation
    if( validation !== undefined && validation !== '' ) {
        let validations = validation.split(',');
        let invalid = [];
        $.each( validations, function(i,v_func){
            let vr = eval( v_func + '(' + JSON.stringify( data ) + ')' );
            console.log(vr);
            if( !vr ) {
                invalid.push(1);
            }
        });
        if( invalid.length > 0 ) {
            return;
        }
    }

    let d = $.extend({}, { 'action' : action }, data);
    //elog(d);
    $.post( location.origin, d, function(r) {
        //elog(r);
        try {
            elog(r);
            r = JSON.parse( r );
            elog(r);
            elog( notify_time );
            if( notify_time !== undefined && notify_time !== '' ) {
                elog(r);
                elog($(p).data('success'));
                if( r[0] === 1 && $(p).data('success') !== undefined ) {
                    notify( $(p).data('success'), notify_time, '', 'check_circle' );
                } else {
                    notify( r[1], notify_time, 'error', 'error' );
                }
            }
            if( r[0] !== 0 && reload_time !== undefined && reload_time !== '' && reload_time !== '0' ) {
                $.isNumeric( reload_time ) ? setTimeout(function(){ location.reload() }, reload_time * 1000 ) : location.href = reload_time;
            }
            if( r[0] !== 0 && reset !== undefined && reset !== '' && reset !== '0' ) {
                $('[data-'+reset+']').val('');
            }
            if( redirect !== undefined && redirect !== '' ) {
                redirect_time = redirect_time !== undefined && redirect_time !== '' ? redirect_time : reload_time;
                if( r[0] !== 0 ) {
                    setTimeout(function(){
                        location.href = redirect;
                    }, redirect_time * 1000)
                }
            }
            elog(callback);
            if( callback !== undefined && callback !== '' ) {
                let calls = callback.split(',');
                $.each( calls, function(i,call){
                    eval( call + '(' + JSON.stringify( r ) + ')' );
                });
            }
            //this[callback](r);
        }
        catch( rat ) {
            if( callback !== undefined && callback !== '' ) {
                callback = callback.split(',');
                $.each( callback, function(i,call){
                    eval( call + '(' + JSON.stringify( r ) + ')' );
                });
            }
            //elog( rat );
        }
    });
}

function redirect( r ) {
    elog(r);
}

function reload( time_seconds ){
    let t = time_seconds !== undefined && time_seconds !== '' ? time_seconds * 1000 : 5000;
    setTimeout(function(){ location.reload() },t);
}
