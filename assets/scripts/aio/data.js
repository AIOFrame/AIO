if (typeof window.elog === 'undefined' ) {
    window.elog = function(r) {
        if( $('body').hasClass('debug') )
            console.log(r);
    }
}

/**
 * Gets values of all inputs of specific data attribute within an element
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

    // Loop thru the input elements
    $(parent).find(":input"+a+":not(:button)","select"+a,"textarea"+a).each(function () {

        // Define Pre and Key
        let pre_key;
        let name = $(this).attr('name');
        name = name !== undefined ? name.replace('[]',''): undefined;
        let key = $(this).data('key');
        let id = $(this).attr('id');
        if( key !== undefined )
            pre_key = pre + key;
        else if ( id !== undefined )
            pre_key = pre + id;
        else if( name !== undefined )
            pre_key = pre + name;
        else
            pre_key = pre + $(this).attr('class');

        let m = $(parent).find( '[name=' + name + ']' );

        // Set default value for text, number, email, textarea, select
        let value;
        value = $(this).hasClass('fn') ? ufn( $(this).val() ) : $(this).val(); // Un Format Number

        if( $(this).attr('type') === 'checkbox' ){
            let t = $(this).is(':checked');
            let arr = $(this).data('array');
            let v =  $(this).val();
            if ( $(this).hasClass('slide') ) {
                elog(0);
                value = $(this).is(':checked') ? 1 : 2;
            } else if ( arr !== undefined ) {
                elog(1);
                if( data[pre_key] === undefined || !$.isArray( data[pre_key] ) ) {
                    data[pre_key] = [];
                }
                if( t ) {
                    data[pre_key].push( v );
                }
            } else if (m.length > 1) {
                elog(2);
                value = $( '[name=' + $(this).attr('name') + ']' ).map(function () {
                    if( t ) {
                        return $(this).val();
                    }
                }).toArray();
            } else {
                elog(3);
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
            elog(pre_key);
            let arr = $(this).data('array');
            key = $(this).val() !== '' ? $(this).attr('name') : '';
            key = key === '' ? $(this).attr('id') : key;
            let key2 = $(this).data('key') !== undefined ? $(this).data('key') : key;
            data[arr] = data[arr] === undefined ? {} : data[arr];
            $(this).val() !== '' ? data[ arr ][ key2 ] = value : '';
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
            elog( b );
            elog( JSON.stringify(b) );
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
        notify( $(id).data('valid-email-notify') );
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
            if( b !== undefined && $(b).val() !== null && $(b).val() !== "" ){
                $(b).removeClass('empty');
            } else {
                // TODO: Check if the input is inside a tab and activate that tab
                $(b).addClass('empty');
                required.push( $(b).attr('title') );
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

function is_invalid( e, d ) {
    d = d === undefined || d === '' ? '' : '['+d+']';
    let invalid = [];
    if( $(e)[0] && ( $(e)[0].localName === 'div' || $(e)[0].localName === 'tr' || $(e)[0].localName === 'form' ) ){
        invalid = [];
        $.each($(e).find('input'+d+',select'+d+',textarea'+d),function(a,b){
            if( b !== undefined && $(b).attr('minlength') !== undefined && $(b).val().length < parseInt( $(b).attr('minlength') ) ) {
                invalid.push( $(b).attr('title') );
                $(b).addClass('empty');
            } else {
                $(b).removeClass('empty');
            }
        });
    } else {
        if( $(e).attr('minlength') !== undefined && $(e).val().length < parseInt( $(b).attr('minlength') ) ){
            invalid.push( $(b).attr('title') );
            $(e).addClass('empty');
        } else {
            $(e).removeClass('empty');
        }
    }
    return invalid;
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

function process_data( e ){
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

    // Confirm
    if( $(p).data('confirm') !== undefined ) {
        if( !confirm( $(p).data('confirm') ) ) {
            return;
        }
    }
    elog('testtt');
    elog(p);
    p = ( p.length !== 0 && p[0].tagName === 'DIV' ) ? p : $(e).parents('[data-data]');
    p.addClass('load');

    let pre = $(p).data('pre');
    pre = pre !== undefined ? pre : '';

    let data = $(p).data('data');
    data = data !== undefined ? data : '';

    //if( $(p).attr('required') !== undefined ) {
    // Check for empty values
    let required = is_empty( p, 'required' );
    if( required.length > 0 ) {
        $(p).removeClass('load');
        console.log(required);
        $(e).attr('disabled',false).removeClass('load');
        let empty_note = $(p).data('empty') !== undefined ? $(p).data('empty') : 'The following fields seem to be empty!';
        empty_note += '<div class="fields">';
        $(required).each(function(i,req){
            empty_note += '<div class="field">'+req+'</div>';
        });
        empty_note += '</div>';
        notify( empty_note );
        return;
    }

    let invalid = is_invalid( p );
    if( invalid.length > 0 ) {
        $(p).removeClass('load');
        console.log(invalid);
        $(e).attr('disabled',false).removeClass('load');
        let invalid_note = $(p).data('invalid') !== undefined ? $(p).data('invalid') : 'The following fields seem to be invalid!';
        invalid_note += '<div class="fields">';
        $(invalid).each(function(i,req){
            invalid_note += '<div class="field">'+req+'</div>';
        });
        invalid_note += '</div>';
        notify( invalid_note );
        return;
    }
    //}

    // Disable Send Button
    //if( $(p).data('reload') !== undefined && $(p).data('reload') !== null && parseInt( $(p).data('reload') ) > 0 ) {
    //}

    let d = get_values( p, data, pre );
    elog(d);
    d.action = $(e).data('action');
    d.t = $(p).data('t');
    d.pre = pre;
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
    let cb = p.data('callback') !== '' && p.data('callback') !== undefined ? 'process_finish,' + p.data('callback') : 'process_finish';
    post( d.action, d, p.data('notify'), p.data('reload'), p.data('redirect'), 0, cb, p.data('reset'), p );

}

function process_finish( p, r ) {
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
                console.log(tar);
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
    let con = $(e).data('confirm') !== undefined && $(e).data('confirm') !== '' ? $(e).data('confirm') : 'Are you sure to update ?';
    let n = notify_time !== undefined && notify_time !== '' ? notify_time : 2;
    let r = reload_time !== undefined && reload_time !== '' ? reload_time : 2;
    if( confirm( con ) ){
        post( action, data, n, r );
    }
}

function trash_data( e, action, target, logic, notify_time, reload_time ) {
    let data = { 'action': action, target: target, logic: logic };
    let con = $(e).data('confirm') !== undefined && $(e).data('confirm') !== '' ? $(e).data('confirm') : 'Are you sure to delete ?';
    let n = notify_time !== undefined && notify_time !== '' ? notify_time : 2;
    let r = reload_time !== undefined && reload_time !== '' ? reload_time : 2;
    if( confirm( con ) ){
        post( action, data, n, r );
    }
}

function post( action, data, notify_time, reload_time, redirect, redirect_time, callback, reset, p ) {
    //elog(callback);
    let d = $.extend({}, { 'action' : action }, data);
    $.post( location.origin, d, function(r) {
        //elog(r);
        try {
            r = JSON.parse( r );
            //elog(r);
            elog( notify_time );
            if( notify_time !== undefined && notify_time !== '' ) {
                elog(r);
                elog($(p).data('success'));
                if( r[0] === 1 && $(p).data('success') !== undefined ) {
                    notify( $(p).data('success'), notify_time );
                } else {
                    notify( r[1], notify_time );
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
