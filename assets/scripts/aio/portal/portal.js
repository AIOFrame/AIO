$(document).ready(function() {

    let b = $('body');

    // MENU SCRIPTS
    $(b).on('click','#menu',function(){
        $(this).toggleClass('on');
        $('aside').toggleClass('on');
        $('.modal').removeClass('on');
    })

    .on('keyup search','aside [type=search]',function(){
        let s = $(this).val().toLowerCase();
        $('aside .set a').each(function(i,e){
            $(e).parent().parent().addClass('dn');
            let n = $(e).find('.title').text().toLowerCase();
            if( n.indexOf(s) >= 0 ) {
                $(e).parent().parent().removeClass('dn');
            }
        })
    })

    .on('click','[data-toggle-light]',function () {
        $('body').toggleClass('d');
        localStorage.setItem('dark_mode',$(b).hasClass('d'));
    })

    /* .on('click','#expand',function(){
        $('#user_panel').toggleClass('on');
        $(this).toggleClass('on');
    }); */

    .on('click', '[data-e]', function () {
        $('[data-e]').removeClass('on');
        $(this).addClass('on');
        $('.vertical_blocks').scrollTo($($(this).data('e')), {
            duration: 800
        });
    })

    .on('click','aside ul i',function(){
        $('aside ul li').not($(this).parents('li')).removeClass('open');
        $(this).parents('li').toggleClass('open');
    })

    // Set Regions
    .on('click','[data-set-region]',function () {
        let r = $(this).data('set-region');
        let a = $(this).parent().data('action');
        post( a, { 'action': a, 'cca2': r }, 2, 2 );
    });

    // Auto Opens Modal
    if( $(b).hasClass('add') ) {
        $('.actions.float [data-on]').click();
        window.history.replaceState({}, document.title, location.origin + location.pathname);
    }

    // Auto Close Menus
    $(document).mouseup(function(e) {
        let c = $('#menu, aside');
        if (!c.is(e.target) && c.has(e.target).length === 0) {
            $('aside,#menu').removeClass('on');
        }
    });

});

function logout( action, path ) {
    if( confirm('Are you sure to log out?') ) {
        path = path === undefined ? '' : path;
        post( action, { 'action': action }, 2, 2, location.origin + '/' + path );
    }
}