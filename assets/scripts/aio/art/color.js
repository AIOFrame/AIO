let debug = !!$('body').hasClass('debug');

$(document).ready(function(){
    let b = $('body');

    if( $('[data-color-picker]').length > 0 ) {
        $('[data-color-picker][data-preview]').each(function(a,b){
           if( $(b).val() !== '' ) {
               $( $(b).data('preview') ).css( 'background', 'var(--input-bg-light) url(\'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="25" height="25"><rect rx="12" ry="12" width="22" height="22" style="fill:%23'+$(b).val().substring(1)+'" /></svg>\') no-repeat calc(100% - 5px) center / 22px' );
           }
        });
        $('<div class="color_picker_wrap"><div class="color-picker"></div><div class="controls"><input type="text" value="#ffffff" class="code"></div><div class="close">close</div></div>').appendTo(b);
    }

    // Initiate Color Picker
    $(b).on('click','[data-color-picker]',function(){
        $('.color_picker_wrap').addClass('on');
        init_color_picker($(this),$(this).val());
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
        //$('article').removeClass('fade');
    })
    // Update color code
    .on('keyup','.color_picker_wrap input',function(){
        if( $(this).val().length > 3 ) {
            init_color_picker( $(this), $(this).val() );
        }
    })

})

/* $(document).mouseup(function(e) {
    let cp = $('.color_picker_wrap');
    if (!cp.is(e.target) && cp.has(e.target).length === 0) {
        cp.removeClass('on').children('.color-picker').html('');
    }
}); */

function init_color_picker( e, value ) {
    $('article').addClass('fade');
    let cp = $('.color-picker');
    $(cp).html('');
    $(e).data('value') !== undefined ? $(cp).data('value',$(e).data('value')) : $(cp).data('value',$(e)); // Reads target element to set value, and sets it to color picker
    $(e).data('background') !== undefined ? $(cp).data('background',$(e).data('background')) : '';
    let v = {};
    let c = value === '' || value === undefined ? '#aaaaaa' : value;
    v.color = $(e).val() !== '' && $(e).val() !== undefined ? $(e).val() : c;
    v.width = $(e).data('width') !== '' && $(e).data('width') !== undefined ? $(e).data('width') : '300';
    let types = { 'box' : [ { component: iro.ui.Box }, { component: iro.ui.Slider, options: { sliderType: 'hue' } } ], 'wheel' : [ { component: iro.ui.Wheel } ] };
    v.layout = $(e).data('type') !== '' && $(e).data('type') !== undefined ? types[ $(e).data('type') ] : types['box'];
    let colorPicker = new iro.ColorPicker('.color-picker',v);
    colorPicker.on('color:change', onColorChange);
    //onColorChange(colorPicker.color);
}

function onColorChange( color ) {
    let cp = $('.color-picker');
    let v = $(cp).data('value');
    if( v !== undefined ) {
        v.val(color.hexString);
        if( v.data('border') !== undefined && $( v.data('border') ).length ) {
            $( v.data('border') ).css('border', '1px solid #' + color.hexString.substring(1) );
        }
        if( v.data('background') !== undefined && $( v.data('background') ).length ) {
            $( v.data('background') ).css('background-color', color.hexString );
        }
        if( v.data('preview') !== undefined && $( v.data('preview') ).length ) {
            $( v.data('preview') ).css( 'background', 'var(--input-bg-light) url(\'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="25" height="25"><rect rx="12" ry="12" width="22" height="22" style="fill:%23'+color.hexString.substring(1)+'" /></svg>\') no-repeat calc(100% - 5px) center / 22px' );
        }
    }
    $('.color_picker_wrap input').val(color.hexString);
    //$(cp).data('background') !== undefined ? $(cp).data('background').css({'background-color':color.hexString}) : '';
}