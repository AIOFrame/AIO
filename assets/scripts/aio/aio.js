var domain;
let b = $('body');
$(document).ready(function(){
    let b = $('body');
    // MANIPULATOR
    // TODO: Change resetsrc and resetinput to reset
    $(document).on('click', '[data-action], [data-show], [data-on], [data-off], [data-hide], [data-slide], [data-remove], [data-toggle], [data-resetsrc], [data-resetinput], [data-click], [data-href],[data-force-on]', function (e) {
        if ($(this).data('togglescroll') === true) {
            scroll_lock();
        }
        if ($(this).data('blur') === true) {
            blurred();
        }
        if ($(this).data('targettoggleclass')) {
            $($(this).data('hide')).toggleClass($(this).data('targettoggleclass'));
            $($(this).data('action')).toggleClass($(this).data('targettoggleclass'));
            $($(this).data('show')).toggleClass($(this).data('targettoggleclass'));
        }
        $($(this).data('off')).removeClass('on');
        $($(this).data('force-on')).addClass('on');
        $($(this).data('on')).toggleClass('on');
        $($(this).data('remove')).remove();
        $($(this).data('hide')).hide();
        $($(this).data('show')).show();
        $($(this).data('toggle')).toggle();
        $($(this).data('slide')).slideToggle();
        $($(this).data('resetsrc')).attr('src', '');
        $($(this).data('resetinput')).val('');
        $($(this).data('click')).click();
        if($($(this).data('show')).hasClass('modal') && $($(this).data('show')).data('fade') === undefined ){
            $('article').addClass('fade');
        }
        if($($(this).data('on')).hasClass('modal') && $($(this).data('on')).data('fade') === undefined ){
            $('article').addClass('fade');
        }
        if($(this).data('href')){
            //elog($(e.target).data('prevent-default'));
            if( $(e.target).data('prevent-default') === undefined ){
                location.href = $(this).data('href');
            }
        }
    })

    .on('click','[data-dark]',function(){
        $(this).toggleClass('on');
        $(b).toggleClass('d');
        localStorage.setItem('dark_mode',$(b).hasClass('d'));
    })

    // Load Dark Mode
    var dark = localStorage.getItem('dark_mode');
    dark === 'true' ? b.addClass('d') : b.removeClass('d');

    // Scroll Save

    restore_scroll();

    store_scroll();

    // Dynamic Data

    // Fetch States

    /* $(b).on('change','select[data-states]',function(){
        var t = $($(this).data('states'));
        if( $(this).data('states') !== '' ){
            $.post( location.origin, { 'action':'states', 'id': $(this).val() }, function(r){
                if( r = JSON.parse( r ) ){
                    if( $.isArray( r ) ){
                        elog(r);
                        var o = '';
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
    //     var a = format_number($(e).val());
    //     $(this).val(a);
    // });



    var debug = !!$('body').hasClass('debug');
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
    var scrollTimer;
    $('[data-save-scroll]').on( 'scroll', function(e){

        clearTimeout(scrollTimer);
        scrollTimer = setTimeout(function() {

            var id = $($(e)[0].target).attr('id');
            if (id !== undefined) {

                localStorage[id + '_scroll'] = $($(e)[0].target).scrollTop();

            }

        }, 250);

    });
}

function restore_scroll() {
    $('[data-save-scroll]').each(function(a,b){

        if( $(b).attr('id') !== undefined ) {

            var scroll_pos = localStorage[ $(b).attr('id') + '_scroll' ];
            $(b).scrollTop(scroll_pos);

        }

    })
}

function slength( e, l ){
    var r = $(e).val().length >= l;
    r ? $(e).removeClass('empty') : $(e).addClass('empty');
    if( !r && $(e).data('length-notify') !== undefined && $(e).data('length-notify') !== '' ) {
        notify( $(e).data('length-notify') );
    }
    return !r;
}

var debug = !!$('body').hasClass('debug');

function elog( d ) {
    if( debug ) {
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