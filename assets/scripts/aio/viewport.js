$.fn.inView = function() {
    let eT = $(this).offset().top;
    let eB = eT + $(this).outerHeight();
    let vT = $(window).scrollTop();
    let vB = vT + $(window).height();
    return eB > vT && eT < vB;
};

/*$(window).on('resize scroll', function() {
    $('.color').each(function() {
        let activeColor = $(this).attr('id');
        if ($(this).isInViewport()) {
            $('#fixed-' + activeColor).addClass(activeColor + '-active');
        } else {
            $('#fixed-' + activeColor).removeClass(activeColor + '-active');
        }
    });
});*/