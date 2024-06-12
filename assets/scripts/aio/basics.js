let domain;
let b = $('body');
$(document).ready(function(){
    // MANIPULATOR
    $(document).on('click', '[data-toggle-on],[data-on],[data-off],[data-show],[data-hide],[data-toggle],[data-slide],[data-remove],[data-action],[data-click],[data-href]', function (e) {
        // Toggle class on
        $(this).data('toggle-on') === '' ? $(this).toggleClass('on') : $($(this).data('toggle-on')).toggleClass('on');
        // Adds class on
        $(this).data('on') === '' ? $(this).addClass('on') : $($(this).data('on')).addClass('on');
        // Removes class on
        $(this).data('off') === '' ? $(this).removeClass('on') : $($(this).data('off')).removeClass('on');
        // Show element
        $(this).data('show') === '' ? $(this).show() : $($(this).data('show')).show();
        // Hide element
        $(this).data('hide') === '' ? $(this).hide() : $($(this).data('hide')).hide();
        // Toggle Element
        $(this).data('toggle') === '' ? $(this).toggle() : $($(this).data('toggle')).toggle();
        // Slide Toggle Element
        $(this).data('slide') === '' ? $(this).slideToggle() : $($(this).data('slide')).slideToggle();
        // Removes element
        $(this).data('remove') === '' ? $(this).remove() : $($(this).data('remove')).remove();
        // Simulates click
        $($(this).data('click')).click();
        // Fade Modal on show
        if($($(this).data('show')).hasClass('modal') && $($(this).data('on')).data('fade') === undefined ){
            $('article').addClass('fade');
        }
        // Fade Modal on toggle class on
        if($($(this).data('on')).hasClass('modal') && $($(this).data('on')).data('fade') === undefined ){
            $('article').addClass('fade');
        }
        // Prevent default
        if($(this).data('href')){
            if( $(e.target).data('prevent-default') === undefined ){
                if( $(this).attr('target') !== undefined && $(this).attr('target') === '_blank' ) {
                    window.open( $(this).data('href'), '_blank').focus();
                } else {
                    location.href = $(this).data('href');
                }
            }
        }
    })

    .on('click','[data-dark]',function(){
        $(this).toggleClass('on');
        $(b).toggleClass('d');
        localStorage.setItem('dark_mode',$(b).hasClass('d'));
    })

    // CHANGE LANGUAGE
    /*.on('click','[data-lang]',function(){
        //var cl = $('[data-lang].on').data('lang');
        let nl = $(this).data('lang');
        console.log( nl );
        post( $(this).parent().data('language'),{'lang':nl},'',1);
    })
    .on('change','[data-languages]',function(){
        let nl = $(this).val();
        $.post(location.origin,{'action':'set_language','lang':nl},function(r){
            notify('Language Changed!');
            reload(3);
        });
    }) */

    // Set Regions
    .on('click','[data-set-region]',function () {
        let r = $(this).data('set-region');
        let a = $(this).parent().data('t');
        console.log( r );
        console.log( a );
        post( a, { 'action': a, 'iso2': r }, 2, 2 );
    })

    // Auto hide on click outside
    .mouseup(function(e) {
        let el = $('[data-auto-off]');
        if (!el.is(e.target) && el.has(e.target).length === 0) { el.removeClass('on'); }
        let eh = $('[data-auto-hide]');
        if (!eh.is(e.target) && eh.has(e.target).length === 0) { eh.hide(); }
    });

    // Load Dark Mode
    //let dark = localStorage.getItem('dark_mode');
    //dark === 'true' ? b.addClass('d') : b.removeClass('d');

    // Scroll Save

    restore_scroll();

    store_scroll();

    // Dynamic Data

    // Fetch States

    /* $(b).on('change','select[data-states]',function(){
        let t = $($(this).data('states'));
        if( $(this).data('states') !== '' ){
            $.post( location.origin, { 'action':'states', 'id': $(this).val() }, function(r){
                if( r = JSON.parse( r ) ){
                    if( $.isArray( r ) ){
                        elog(r);
                        let o = '';
                        $.each( r, function(i,s){
                            o += '<option value="' + s + '">' + s + '</option>';
                        });
                        $(t).html(o);
                        if($(t).hasClass('select2')) {
                            $(t).select2('destroy').select2({ width:'100%' });
                        }
                    }
                }
            });
        }
    }); */

    // Markup '[data-m]','[data-mt]','[data-mr]','[data-mb]','[data-ml]','[data-p]','[data-pt]','[data-pr]','[data-pb]','[data-pl]'

    //$.each($('[data-ml]'),function(a,b){ $(b).css({'margin-left':$(b).data('ml')}); });

    // Number Formatter



    // $('input.fn').each(function(i,e){
    //     let a = format_number($(e).val());
    //     $(this).val(a);
    // });
});



function scroll_to( element, parent, speed ) {
    speed = speed !== undefined && speed !== '' ? speed : 1000;
    parent = parent !== undefined && parent !== '' ? parent : 'html,body';
    if( !$( parent ).data['scrollbar'] ) {
        $(parent).animate({scrollTop: $(element).offset().top}, speed);
    } else {
        const scrollbar = Scrollbar.init( $( parent )[0] );
        scrollbar.scrollIntoView( $( element )[0] );
    }
}

function store_scroll() {
    let scrollTimer;
    $('[data-save-scroll]').on( 'scroll', function(e){

        clearTimeout(scrollTimer);
        scrollTimer = setTimeout(function() {

            let id = $($(e)[0].target).attr('id');
            if (id !== undefined) {

                localStorage[id + '_scroll'] = $($(e)[0].target).scrollTop();

            }

        }, 250);

    });
}

function restore_scroll() {
    $('[data-save-scroll]').each(function(a,b){

        if( $(b).attr('id') !== undefined ) {

            let scroll_pos = localStorage[ $(b).attr('id') + '_scroll' ];
            $(b).scrollTop(scroll_pos);

        }

    })
}

function slength( e, l ){
    let r = $(e).val().length >= l;
    r ? $(e).removeClass('empty') : $(e).addClass('empty');
    if( !r && $(e).data('length-notify') !== undefined && $(e).data('length-notify') !== '' ) {
        notify( $(e).data('length-notify') );
    }
    return !r;
}

function elog( d ) {
    if( $('body').hasClass('debug') ) {
        if (window.console) {
            if (Function.prototype.bind) {
                elog = Function.prototype.bind.call(console.log, console);
            }
            else {
                elog = function() {
                    Function.prototype.apply.call(console.log, console, arguments);
                };
            }
            elog.apply(this, arguments);
        }
    }
}

function in_viewport(e) {
    let rect     = e.getBoundingClientRect(),
        vWidth   = window.innerWidth || document.documentElement.clientWidth,
        vHeight  = window.innerHeight || document.documentElement.clientHeight,
        efp      = function (x, y) { return document.elementFromPoint(x, y) };

    // Return false if it's not in the viewport
    if (rect.right < 0 || rect.bottom < 0 || rect.left > vWidth || rect.top > vHeight)
        return false;

    // Return true if any of its four corners are visible
    return ( e.contains(efp(rect.left,  rect.top)) ||  e.contains(efp(rect.right, rect.top)) ||  e.contains(efp(rect.right, rect.bottom)) ||  e.contains(efp(rect.left,  rect.bottom)) );
}

function getParam( param ) {
    let p = new URLSearchParams(window.location.search);
    return p.get( param );
}

function slide_toggle( e, t ){
    $(b).on('click',e,function(){
        $(this).find(t).slideToggle();
    })
}

function is_mobile() {
    return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
}
