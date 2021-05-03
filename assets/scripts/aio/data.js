// Fields and Validation

/**
 * Gets values of all inputs of specific data attribute within an element
 * @param parent HTML Element Selector
 * @param attribute Data param to choose inputs
 * @param prepend Add a pre text to keys
 * @returns {{}}
 */
function get_values( parent, attribute, prepend ) {
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

        let m = $( '[name=' + name + ']' );
        let value;
        value = $(this).hasClass('fn') ? ufn( $(this).val() ) : $(this).val(); // Un Format Number

        if( $(this).attr('type') === 'checkbox' ){
            let t = $(this).is(':checked');
            let arr = $(this).data('array');
            if ( arr !== undefined ) {
                data[arr] = data[arr] === undefined ? [] : data[arr];
                t ? data[ arr ].push( $(this).val() ) : '';
            } else if (m.length > 1) {
                value = $( '[name=' + $(this).attr('name') + ']' ).map(function () {
                    if ($(this).is(':checked'))
                        return $(this).val();
                }).toArray();
            } else {
                value = t === true ? 1 : 0;
            }

        } else if( $(this).attr('type') === 'radio' ) {

            key = $(this).is(':checked') ? $(this).attr('name') : '';
            key = $(this).data('key') !== undefined ? $(this).data('key') : key;
            //v = $(this).is(':checked') ? $(this).val() : '';
            value = $('input[name='+$(this).attr('name')+']:checked').val();
            data[ pre_key ] = value;
            return true;
        } else if( $(this).is( "select" ) && $(this).attr('multiple') !== undefined ) {

            value = $(this).val().join(", ");

        } else if( $(this).data('array') !== undefined ) {
            let arr = $(this).data('array');
            key = $(this).val() !== '' ? $(this).attr('name') : '';
            key = key === '' ? $(this).attr('id') : key;
            let key2 = $(this).data('key') !== undefined ? $(this).data('key') : key;
            data[arr] = data[arr] === undefined ? {} : data[arr];
            $(this).val() !== '' ? data[ arr ][ key2 ] = value : '';
            return true;
        }

        // Finally push the value
        data[ pre_key ] = value;

    });
    $.each(data,function(a,b){
        if( typeof b === 'object' && b !== null ) {
            console.log( b );
            console.log( JSON.stringify(b) );
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
 * @returns {boolean}
 */
function is_empty( e, d ) {
    d = d === undefined || d === '' ? '' : '[data-'+d+']';
    let result;
    if( $(e)[0] && ( $(e)[0].localName === 'div' || $(e)[0].localName === 'tr' || $(e)[0].localName === 'form' ) ){
        let r = [];
        $.each($(e).find('input'+d+',select'+d+',textarea'+d),function(a,b){
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
        result = $.inArray(true, r) !== -1;
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
                console.log( b );
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
        $.each($(e).find('input'+d+',select'+d),function(a,b){
            $(b).val('');
        })
    } else {
        $(e).val('');
    }
}

// AJAX Data

function process_data( e ){
    //$(e).attr('disabled',true);
    let p = $(e).parents('[data-t]');
    p = p[0].tagName === 'DIV' ? p : $(e).parents('[data-data]');

    p.addClass('load');
    let pre = $(p).data('pre');
    pre = pre !== undefined ? pre : '';

    let data = $(p).data('data');
    data = data !== undefined ? data : '';

    // Check for empty values
    if( $(p).data('empty') !== '' && $(p).data('empty') !== undefined ) {
        if( is_empty( p, $(p).data('empty') ) ) {
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
    let d = get_values( p, data, pre );
    let action = $(e).data('action');
    d.action = $(e).data('action');
    d.t = $(p).data('t');
    d.pre = pre;
    if( d.action === undefined || d.action === null ) {
        console.log('Action not set!');
    }

    let types = Array('id','by','action','h','d','dt');

    $.each(types,function(x,a){
        if( $(p).data(a) !== undefined && $(p).data(a) !== '' ){
            d[a] = $(p).data(a);
        }
    });
    console.log(d);
    post( d.action, d, p.data('notify'), p.data('reload'), p.data('redirect'), 0, p.data('callback'), p.data('reset') );

}

function process_finish( r ) {
    //elog( r );
    //$(e).attr('disabled',false);
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
            //elog('#'+i);
            //elog(d);
        }
    });

    typeof files_ui === 'function' ? files_ui() : '';
    typeof file_ui === 'function' ? file_ui() : '';
}

function trash_data( a, t, l ) {
    let d = { 'action': a, 't': t, 'l': l };
    if( confirm('Are you sure to delete ?') ){
        post( a, d, 2, 2 );
    }
}

function post( action, data, notify_time, reload_time, redirect, redirect_time, callback, reset ) {
    //elog(callback);
    let d = $.extend({}, { 'action' : action }, data);
    $.post( location.origin, d, function(r) {
        console.log(r);
        try {
            r = JSON.parse( r );
            //elog(r);
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
            //elog( rat );
        }
    });
}

function reload( time_seconds ){

    var t = time_seconds !== undefined && time_seconds !== '' ? time_seconds * 1000 : 5000;
    setTimeout(function(){ location.reload() },t);

}