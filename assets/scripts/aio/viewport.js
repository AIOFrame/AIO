$.fn.inView = function() {
    var eT = $(this).offset().top;
    var eB = eT + $(this).outerHeight();
    var vT = $(window).scrollTop();
    var vB = vT + $(window).height();
    return eB > vT && eT < vB;
};

/*$(window).on('resize scroll', function() {
    $('.color').each(function() {
        var activeColor = $(this).attr('id');
        if ($(this).isInViewport()) {
            $('#fixed-' + activeColor).addClass(activeColor + '-active');
        } else {
            $('#fixed-' + activeColor).removeClass(activeColor + '-active');
        }
    });
});*/