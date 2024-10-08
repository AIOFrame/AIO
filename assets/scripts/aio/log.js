$(document).ready( function(){

    // Filter
    let filter = '.filters [data-type]';
    let b = $('.b');
    $('body').on('click',filter,function(e){
        $(filter).removeClass('on');
        $(e.target).addClass('on');
        let type = $(e.target).data('type');
        if( type === 'a' ){
            b.show();
        } else {
            b.hide();
            $('.b.'+type).show();
        }
    });

    // Search Button
    $('.go,.so').on('click',function() {
        let s = $(this).parent().next('.l').find('pre').html();
        s = s.replace(/ /g,'+');
        $(this).hasClass('go') ? window.open('https://google.com/search?q=' + s,'_blank') : window.open('https://stackoverflow.com/search?q=' + s,'_blank');
    });

    // Type in Search
    $('[type=search]').on('keyup search change',function(e){
        let sv = $(this).val();
        if( sv.length > 3 ) {
            $.each( $('.events_log > .b'),function(a,b){
                //console.log( $(b).find('.l').html() );
                //console.log( sv );
                if( $(b).find('.l') !== undefined && $(b).find('.l').html().indexOf(sv) >= 0 ) {
                    $(b).show();
                } else {
                    $(b).hide();
                }
            })
        } else {
            $(b).show();
        }
    });

});