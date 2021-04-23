//let debug = !!$('body').hasClass('debug');

$(document).ready(function(){

    // Changes checkbox value to 1 or 0
    $(document).on('change','[data-check],[data-bool],[data-boolean]',function(){
        let v = $(this).is(':checked') ? '1' : '0';
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
        let a = format_number($(this).val());
        $(this).val(a);
    });

    $('.fn:not(input)').each(function(i,e){
        let a = format_number($(e).html());
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