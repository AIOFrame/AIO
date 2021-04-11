var debug = !!$('body').hasClass('debug');

$(document).ready(function(){

    // Changes checkbox value to 1 or 0
    $(document).on('change','[data-check],[data-bool],[data-boolean]',function(){
        var v = $(this).is(':checked') ? '1' : '0';
        $($(this).data('check')).val(v);
    })

    // Initiate Clipboard JS
    if( typeof ClipboardJS === 'function' ){
        var clipboard = new ClipboardJS('[data-clipboard-target],[data-clipboard-text]');

        clipboard.on('success', function(e) {
            notify('Copied!',1);
        });

        clipboard.on('error', function(e) {
            console.log('Action:', e.action);
            console.log('Trigger:', e.trigger);
        });
    }

    // Select2
    if( $.fn.select2 !== undefined ){
        $('select.easy, select.select2').select2({ width:'100%' });
    }

    // Format Numbers
    $('body').on('keyup','input.fn',function(){
        var a = format_number($(this).val());
        $(this).val(a);
    });

    $('.fn:not(input)').each(function(i,e){
        var a = format_number($(e).html());
        $(this).html(a);
    });

    // No blanks in input
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

/**
 * Check if input field is empty
 * @param e Input element or Parent element identifier ID or Class
 * @param d Data attribute of input
 * @returns {boolean}
 */
function _is_empty( e, d ) {
    d = d === undefined || d === '' ? '' : '[data-'+d+']';
    if( $(e)[0] && ( $(e)[0].localName === 'div' || $(e)[0].localName === 'tr' || $(e)[0].localName === 'form' ) ) {
        var r = [];
        $.each($(e).find('input'+d+',select'+d),function(a,b){
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
 * Check if input field is empty and add class "empty"
 * @param e Input element or Parent element identifier ID or Class
 * @param d Data attribute of input
 * @returns {boolean}
 */
function is_empty( e, d ) {
    d = d === undefined || d === '' ? '' : '[data-'+d+']';
    var result;
    if( $(e)[0] && ( $(e)[0].localName === 'div' || $(e)[0].localName === 'tr' || $(e)[0].localName === 'form' ) ){
        let r = [];
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

/* function clear_values( e, s ){
    if( s === '' || s === undefined ) {
        $(e).find(":input:not(:button)","select","textarea").each(function () {
            $(this).val("").trigger('chosen:updated');
        });
    } else {
        $(e).find(":input[data-"+s+"]:not(:button)","select[data-"+s+"]","textarea[data-"+s+"]").each(function () {
            $(this).val("").trigger('chosen:updated');
        });
    }
} */

/**
 * Checks if email is properly formatted
 * @param id Email ID
 * @returns {boolean}
 */
function _email_valid( id ) {
    var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
    return !pattern.test( $(id).val() );
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
 * Gets values of all inputs of specific data attribute within an element
 * @param e HTML Element Selector
 * @param s Data param to choose inputs
 * @param pre Add a pre text to keys
 * @returns {{}}
 */
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

        } else if( $(this).data('array') !== undefined ) {
            let arr = $(this).data('array');
            k = $(this).val() !== '' ? $(this).attr('name') : '';
            k = k === '' ? $(this).attr('id') : k;
            let key = $(this).data('key') !== undefined ? $(this).data('key') : k;
            data[arr] = data[arr] === undefined ? {} : data[arr];
            $(this).val() !== '' ? data[ arr ][ key ] = v : '';
            return true;
        }

        // Finally push the value
        data[ pk ] = v;

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