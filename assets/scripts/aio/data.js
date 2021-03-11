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

function edit_data( e, modal ) {

    var t = $(e).data('t') ? $($(e).data('t')) : $(modal);
    var data = $(e).data('data');

    if( !$(t).hasClass('on') ) {
        $(t).addClass('on');
    }

    $('article').addClass('fade');
    $(modal).find('[data-add]').hide();
    $(modal).find('[data-update]').show();

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

    typeof files_ui === 'function' ? files_ui() : '';
    typeof file_ui === 'function' ? file_ui() : '';
}

function trash_data( q ) {
    var d = { 'action':'trash_data', 'query':q };
    elog(d);
    if( confirm('Are you sure to delete ?') ){
        post( 'trash_data', { 'query': q }, 2, 2 )
    }
}

function post( action, data, notify_time, reload_time, redirect, redirect_time, callback, reset ) {
    //elog(callback);
    var d = $.extend({}, {'action':action}, data);
    $.post( location.origin, d, function(r) {
        //elog(r);
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