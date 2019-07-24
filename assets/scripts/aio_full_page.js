(function( $ ){
    $.fn.fps =  $.fn.fullpagescroll = function() {

        // Add wrapper class
        $(this).css({'height':'100%','width':'100%','overflow-y':'auto','position':'relative'});

        // Add pages class
        $(this).children().css({'height':'100%','width':'100%','overflow':'hidden','position':'relative'});
        $(this).children(':nth-child('+(1)+')').addClass('on');

        // On scroll detect direction and animate
        var scrolling = false;
        $(this).on('mousewheel',function(e){
            e.preventDefault();
            if( !scrolling ){
                var h = $(this).height();
                var a = $(this).children('.on').index() + 1;
                if(e.originalEvent.wheelDelta > 0 && a > 1) {
                    scrolling = true;
                    $(this).children().removeClass('on');
                    $(this).children(':nth-child('+(a-1)+')').addClass('on');
                    $(this).animate({ 'scrollTop': '-='+h }, 800, function() { scrolling = false; });
                } else if(e.originalEvent.wheelDelta < 0 && a < $(this).children().length) {
                    scrolling = true;
                    $(this).children().removeClass('on');
                    $(this).children(':nth-child('+(a+1)+')').addClass('on');
                    $(this).animate({ 'scrollTop': '+='+h }, 800, function() { scrolling = false; });
                }
            }
        })

    };

    $.fn.moveUp =  $.fn.slideUp = function() {

        var i = $(this).children('.on').index() - 1;
        if( i >= 0 ) {
            $(this).children().removeClass('on');
            $(this).children(':nth-child(' + (i + 1) + ')').addClass('on');
            $(this).animate({'scrollTop': i * $(this).height()}, 800);
        }
    };

    $.fn.moveDown =  $.fn.slideDown = function() {

        var i = $(this).children('.on').index() + 1;
        if( i < $(this).children().length ) {
            $(this).children().removeClass('on');
            $(this).children(':nth-child(' + (i + 1) + ')').addClass('on');
            $(this).animate({'scrollTop': i * $(this).height()}, 800);
        }
    };

    $.fn.moveTo =  $.fn.slideTo = function(i) {

        i = i - 1;
        if( i >= 0 && i < $(this).children().length ) {
            $(this).children().removeClass('on');
            $(this).children(':nth-child(' + (i + 1) + ')').addClass('on');
            $(this).animate({'scrollTop': i * $(this).height()}, 800);
        }
    };

})( jQuery );

function fps_page( i ) {
    i = i-1;
    $(this).children(':nth-child('+i+')').addClass('on');
    $(this).animate({ 'scrollTop': i*$(window).height() }, 800, function() { scrolling = false; });
}