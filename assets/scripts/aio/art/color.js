let debug = !!$('body').hasClass('debug');

$(document).ready(function(){
    let b = $('body');

    if( $('[data-color-picker]').length > 0 ) {
        $('[data-color-picker][data-preview]').each(function(a,b){
           if( $(b).val() !== '' ) {
               $( $(b).data('preview') ).css( 'background', 'var(--input_bg) url(\'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="25" height="25"><rect rx="12" ry="12" width="22" height="22" style="fill:%23'+$(b).val().substring(1)+'" /></svg>\') no-repeat calc(100% - 5px) center / 22px' );
           }
        });
        $('<div class="color_picker_wrap"><div class="color-picker"></div><div class="color_controls"><input type="text" value="#ffffff" class="code" onfocus="this.select();"></div><div class="close">close</div></div>').appendTo(b);
    }

    // Initiate Color Picker
    $(b).on('click','[data-color-picker]',function(){
        $('.color_picker_wrap').addClass('on'); //.data('value',$(this));
        init_color_picker( $(this), $(this).val() );
    })
    // Close color wrapper
    .on('click','.color_picker_wrap .close',function(){
        $('.color_picker_wrap').removeClass('on').children('.color-picker').html('');
        let removeFade = [];
        $('.modal').each(function(a,b){
            if( $(b).hasClass('on') ) {
                removeFade.push(1);
            }
        });
        if( removeFade.length === 0 ) { $('article').removeClass('fade'); }
    })
    // Update color code
    .on('keyup','.color_picker_wrap input',function(){
        let target = $('.color-picker').data('target');
        let v = $(this).val();
        let vl = v.length;
        if( vl === 4 || vl === 7 ) {
            $( target ).val( v );
            init_color_picker( target, v );
            generate_background( target, v );
        }
    })

})

/* $(document).mouseup(function(e) {
    let cp = $('.color_picker_wrap');
    if (!cp.is(e.target) && cp.has(e.target).length === 0) {
        cp.removeClass('on').children('.color-picker').html('');
    }
}); */

function init_color_picker( target_input, color ) {
    $('article').addClass('fade');
    let cp = $('.color-picker');
    let current_value = $(target_input).val();
    $(cp).html('');
    $(cp).data('target', ( $(target_input).data('target') !== undefined ? $(target_input).data('target') : target_input ) ); // Reads target element to set value, and sets it to color picker
    $(cp).data('background') !== undefined ? $(cp).data('background',$(target_input).data('background')) : '';
    $('.color_picker_wrap .code').val( current_value );

    // Prepare Color Picker Params
    let v = {};
    let c = color !== undefined ? color : ( current_value === '' || current_value === undefined ? '#aaaaaa' : current_value );
    let types = { 'box' : [ { component: iro.ui.Box }, { component: iro.ui.Slider, options: { sliderType: 'hue' } } ], 'wheel' : [ { component: iro.ui.Wheel } ] };
    v.color = c !== undefined ? c : ( $( target_input ).val() !== '' && $( target_input ).val() !== undefined ? $( target_input ).val() : '#aaaaaa' );
    v.width = $( target_input ).data('width') !== '' && $( target_input ).data('width') !== undefined ? $( target_input ).data('width') : '300';
    v.layout = $( target_input ).data('type') !== '' && $( target_input ).data('type') !== undefined ? types[ $( target_input ).data('type') ] : types['box'];

    // Initiate Color Picker Render
    let colorPicker = new iro.ColorPicker( '.' + cp.attr('class'), v );
    $('.color_picker_wrap input').focus();

    // On Color Change
    colorPicker.on( 'color:change', onColorChange );
}

function onColorChange( color ) {
    let cp = $('.color-picker');
    let target = $(cp).data('target');
    if( target !== undefined ) {
        target.val( color.hexString );
        if( target.data('border') !== undefined && $( target.data('border') ).length ) {
            $( target.data('border') ).css('border', '1px solid #' + color.hexString.substring(1) );
        }
        if( target.data('background') !== undefined && $( target.data('background') ).length ) {
            $( target.data('background') ).css('background-color', color.hexString );
        }
        if( target.data('preview') !== undefined && $( target.data('preview') ).length ) {
            generate_background( target.data('preview'), color.hexString );
        }
    }
    $('.color_picker_wrap input').val( color.hexString );
    //$(cp).data('background') !== undefined ? $(cp).data('background').css({'background-color':color.hexString}) : '';
}

function generate_background( target, color ) {
    $( target ).css( 'background', 'var(--input_bg) url(\'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="25" height="25"><rect rx="12" ry="12" width="22" height="22" style="fill:%23'+color.substring(1)+'" /></svg>\') no-repeat calc(100% - 5px) center / 22px' );
}