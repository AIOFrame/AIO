$(document).ready( function(){

    // Filter
    $('#type').on('change',function(){

        var fv = $('#type').val();

        var b = $('.b');

        if (fv === 'a') {

            b.show();

        } else {

            b.hide();
            $('.b.'+fv).show();

        }

    });

    // Google Search Button
    $('.go').on('click',function(){

        var s = $(this).parent().next('.l').html();

        s = s.replace(/ /g,'+');
        window.open('https://google.com/search?q=' + s,'_blank');

    });

    // Stack Overflow Search Button
    $('.so').on('click',function(){

        var s = $(this).parent().next('.l').html();

        stack_search( s );

    });

    // Type in Search
    $('.search input').on('keyup',function(e){

        //if( e.key === 'Enter' ) {

            var sv = $(this).val();
            //elog(sv);

            $.each($('.error_log>.b'),function(a,b){


                if( $(b).find('.l').html().indexOf(sv) >= 0 || $(b).find('.t').html().indexOf(sv) >= 0 ) {
                    $(b).show();
                } else {
                    $(b).hide();
                }

            })

        //}

    });

    // Stack Overflow Search
    $('.search button').on('click',function(){

        var s = $('.search input').val();

        stack_search( s );

    });

    // Clear button
    $('.clear').on( 'click', function() {

        $.post( location.origin, { 'action': 'clear_log_viewer' }, function(r) {

            if( r = JSON.parse(r) ){

                notify( r[1] );
                $('.error_log').html('');
                /* if( r[0] === 1 ){
                    setTimeout(function(){ location.reload() },4000);
                } */

            }

        })

    })

});

function stack_search( s ) {

    s = s.replace(/ /g,'+');
    window.open('https://stackoverflow.com/search?q=' + s,'_blank');

}