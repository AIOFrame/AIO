(function( $ ){
    $.fn.fps =  $.fn.fullpagescroll = function() {

        $(this).children(':nth-child('+(1)+')').addClass('active');

        // On scroll detect direction and animate
        let SD = true;
        $(this).on('mousewheel DOMMouseScroll touchMove',function(e){
            //elog(e);
            //elog(e.target);
            if( SD && $(e.target).data('no-fps') === undefined ){
                e.preventDefault();
                let scroll_amount = e.originalEvent.wheelDelta;
                let height = $(this).height();
                let active = $(this).children('.active').index() + 1;
                SD = false;
                if( scroll_amount > 0 && active > 1 ) {
                    $(this).children().removeClass('active');
                    $(this).children(':nth-child('+(active-1)+')').addClass('active');
                    $(this).animate({ 'scrollTop': '-='+height }, 600, function(){ SD = true; });
                } else if( scroll_amount < 0 && active < $(this).children().length) {
                    $(this).children().removeClass('active');
                    $(this).children(':nth-child('+(active+1)+')').addClass('active');
                    $(this).animate({ 'scrollTop': '+='+height }, 600, function(){ SD = true; });
                } else {
                    SD = true;
                }
                /* setTimeout(function () {
                    SD = true;
                }, 1600); */
            }
        })

    };

    $.fn.moveUp =  $.fn.slideUp = $.fn.scrollUp = function() {

        let i = $(this).children('.active').index() - 1;
        if( i >= 0 ) {
            $(this).children().removeClass('active');
            $(this).children(':nth-child(' + (i + 1) + ')').addClass('active');
            $(this).animate({'scrollTop': i * $(this).height()}, 800);
        }
    };

    $.fn.moveDown =  $.fn.slideDown = $.fn.scrollDown = function() {

        let i = $(this).children('.active').index() + 1;
        if( i < $(this).children().length ) {
            $(this).children().removeClass('active');
            $(this).children(':nth-child(' + (i + 1) + ')').addClass('active');
            $(this).animate({'scrollTop': i * $(this).height()}, 800);
        }
    };

    $.fn.moveTo =  $.fn.slideTo = $.fn.scrollTo = function(i) {

        i = i - 1;
        if( i >= 0 && i < $(this).children().length ) {
            $(this).children().removeClass('active');
            $(this).children(':nth-child(' + (i + 1) + ')').addClass('active');
            $(this).animate({'scrollTop': i * $(this).height()}, 800);
        }
    };

})( jQuery );

function fps_page( i ) {
    i = i-1;
    $(this).children(':nth-child('+i+')').addClass('active');
    $(this).animate({ 'scrollTop': i*$(window).height() }, 800, function() { scrolling = false; });
}