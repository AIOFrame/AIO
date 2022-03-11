//let debug = !!$('body').hasClass('debug');

$(document).ready(function(){

    // Changes checkbox value to 1 or 0
    $(document).on('change','[type=checkbox].slide,[data-check],[data-bool],[data-boolean]',function(){
        let v = $(this).is(':checked') ? '1' : '2';
        $(this).val(v);
        //$($(this).data('check')).val(v);
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
        $('select.easy, select.select2').each(function (a,b) {
            $(b).select2({ width:'100%' });
        });
    }

    if( $.fn.tagComplete !== undefined ){
        $('[data-tags]').each(function (a,b) {
            $(b).tagComplete();
        });
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

    let dateLocaleEn = {
        days: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
        daysShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
        daysMin: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
        months: ['January','February','March','April','May','June', 'July','August','September','October','November','December'],
        monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        today: 'Today',
        clear: 'Clear',
        dateFormat: 'yyyy-mm-dd',
        timeFormat: 'hh:ii aa',
        firstDay: 0
    };
    // Air Date Picker
    if( AirDatepicker !== undefined ) {
        $('.dater').each(function(i,date_field){
            new AirDatepicker( '#'+$(date_field).attr('id'), {
                locale: dateLocaleEn,
                position: $(date_field).data('position'),
                autoClose: true,
                multipleDates: $(date_field).attr('multiple'),
                dateFormat: 'yyyy-MM-dd',
                onSelect: function(fD, d, e) {
                    $(date_field).trigger('change');
                }
            })
        })
    }
    // Date Picker
    if( $.fn.datepicker !== undefined ){
        $.fn.datepicker.language['en'] = dateLocaleEn;
        $('.dater').datepicker({
            language: 'en',
            position: $(this).data('position'),
            autoClose: true,
            onSelect: function(fD, d, e) {
                $(e.el).trigger('change');
            }
        }).each(function(a,b){
            if( $(b).data('min') !== undefined && $(this).data('min') !== '' ){
                $(b).datepicker({ minDate: new Date($(b).data('min')) })
            }
            if( $(b).data('max') !== undefined && $(this).data('max') !== '' ){
                $(b).datepicker({ maxDate: new Date($(b).data('max')) })
            }
            if( $(b).attr('value') !== undefined && $(b).attr('value') !== '' ) {
                if( $(b).data('multiple-dates') !== undefined ) {
                    let dates = $(b).attr('value').split(',');
                    $.each( dates, function( index, value ) {
                        $(b).datepicker().data('datepicker').selectDate( new Date( value ) );
                    });
                } else {
                    $(b).datepicker().data('datepicker').selectDate(  new Date( $(b).attr('value') ) );
                }
            }
            if( $(b).attr('off') !== undefined && $(b).attr('off') !== '' ) {
                let dates = $(b).attr('value').split(',');
                $.each( dates, function( index, value ) {
                    $(b).datepicker().data('datepicker').disabled( new Date( value ) );
                });
            }
        });
    }

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
