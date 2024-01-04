//let debug = !!$('body').hasClass('debug');

window.addEventListener('DOMContentLoaded',function () {

    // Changes checkbox value to 1 or 2
    $(document).on('change','[type=checkbox].slide,[data-check],[data-bool],[data-boolean]',function(){
        let v = $(this).is(':checked') ? '1' : '2';
        $(this).val(v);
        //$($(this).data('check')).val(v);
    })

    .on('change','[data-hidden-date]',function(){
        //elog( $(this) );
        let date = new Date( $(this).val() );
        $( $(this).data('alt') ).val( [('0'+date.getDate()).slice(-2), ('0'+date.getMonth()).slice(-2), date.getFullYear()].join('-') ).trigger('change');
    })

    .on('change','[type=number][data-step]',function (e) {
        let step = $(this).data('step');
        let val = parseInt($(this).val());
        /* console.log(step);
        console.log(val);
        console.log(e.key); */
        if( e.key === 'ArrowUp' ){
            e.preventDefault();
            $(this).val( val + step );
        } else if( e.key === 'ArrowDown' ){
            e.preventDefault();
            $(this).val( val - step );
        }
    })

    /* .on('change','[data-visible-date]',function(){
        elog( $(this) );
        let date = new Date( $(this).val() );
    }) */

    // Initiate Clipboard JS
    if( typeof ClipboardJS === 'function' ){
        var clipboard = new ClipboardJS('[data-clipboard-target],[data-clipboard-text]');

        clipboard.on('success', function(e) {
            notify('Copied!',1);
        });

        clipboard.on('error', function(e) {
            //console.log('Action:', e.action);
            //console.log('Trigger:', e.trigger);
        });
    }

    // Select2
    $('select.easy, select.select2').each(function (a,b) {
        select_init( b );
    });
    if( $.fn.select2 !== undefined ){

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

    // Air Date Picker
    if( typeof AirDatepicker !== 'undefined' ) {
        date_picker();
        // $('body').on('change','.dater',date_picker(this));
    }
    // Date Picker
    /* if( $.fn.datepicker !== undefined ){
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
    } */

});

function select_init( el ) {
    let select_config = { width:'100%' };
    if( $( el ).data('no-search') !== undefined ) {
        select_config.minimumResultsForSearch = -1;
    }
    select_config.closeOnSelect = $( el ).data('auto-close') !== undefined;
    if( $( el ).data('template') !== undefined ) {
        //console.log( $(b).data('template') );
        select_config.templateResult = $( el ).data('template');
        //select_config.templateSelection = $(b).data('template');
    }
    //console.log(select_config);
    $( el ).select2(select_config);
}

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

function date_picker( element ) {
    let e = element !== undefined ? element : $('.dater');
    $(e).each(function(i,date_field){
        let config = {
            locale: dateLocaleEn,
            autoClose: true,
            onSelect: function(fD, d, e) {
                $(date_field).trigger('change');
            }
        };
        if( $(date_field).attr('multiple') !== undefined ) {
            config.multipleDates = true;
        }
        if( $(date_field).attr('multiple') !== undefined ) {
            config.multipleDates = true;
            config.multipleDatesSeparator = $(date_field).attr('range') !== undefined ? '|' : ', ';
        }
        if( $(date_field).attr('range') !== undefined ) {
            config.range = true;
            config.multipleDatesSeparator = $(date_field).attr('range') !== undefined ? '|' : ', ';
        }
        if( $(date_field).attr('min') !== undefined ) {
            config.minDate = $(date_field).attr('min');
            console.log($(date_field).attr('min'));
        }
        if( $(date_field).attr('max') !== undefined ) {
            config.maxDate = $(date_field).attr('max');
        }
        config.position = $(date_field).attr('position') !== undefined ? $(date_field).attr('position') : 'top center';
        config.view = $(date_field).attr('view') !== undefined ? $(date_field).attr('view') : 'days';
        config.dateFormat = $(date_field).attr('format') !== undefined ? $(date_field).attr('format') : 'dd-MM-yyyy';
        config.altField = $(date_field).attr('alt') !== undefined ? $(date_field).attr('alt') : false;
        config.altFieldDateFormat = $(date_field).attr('alt-format') !== undefined ? $(date_field).attr('alt-format') : 'yyyy-MM-dd';
        new AirDatepicker( '#'+$(date_field).attr('id'), config );
    })
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
