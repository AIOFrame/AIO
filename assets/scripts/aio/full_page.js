(function( $ ){
    $.fn.fps =  $.fn.fullpagescroll = function() {

        // Add wrapper class
        $(this).css({'height':'100%','width':'100%','overflow-y':'auto','position':'relative'});

        // Add pages class
        $(this).children().css({'height':'100%','width':'100%','overflow':'hidden','position':'relative'});
        $(this).children(':nth-child('+(1)+')').addClass('on');

        // On scroll detect direction and animate
        var SD = true;
        $(this).on('mousewheel DOMMouseScroll touchMove',function(e){
            e.preventDefault();
            if( SD ){
                var sa = e.originalEvent.wheelDelta;
                var h = $(this).height();
                var a = $(this).children('.on').index() + 1;
                SD = false;
                elog(a);
                $(this).children().removeClass('on');
                if( sa > 0 && a > 1 ) {
                    $(this).children(':nth-child('+(a-1)+')').addClass('on');
                    $(this).animate({ 'scrollTop': '-='+h }, 600);
                } else if( sa < 0 && a < $(this).children().length) {
                    $(this).children(':nth-child('+(a+1)+')').addClass('on');
                    $(this).animate({ 'scrollTop': '+='+h }, 600);
                }
                setTimeout(function () {
                    SD = true;
                }, 1600);
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