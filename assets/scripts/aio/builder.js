$(document).ready(function () {

    $('body').on('keyup','.aio_dynamics .set:nth-child(2) input',function () {
        $(this).parents('.field_set').find('.set:nth-child(3) .url').html( location.href + $(this).val() );
    });

    // Navigation
    let Scrollbar = window.Scrollbar;
    Scrollbar.init(document.querySelector('.setup.on .data'));
    $('.n').on('click',function(){
        let required_fields = is_empty($(this).parents('.setup'), 'required');
        if( required_fields.length === 0 ) {
            $(this).parents('.setup').removeClass('on').next('.setup').addClass('on');
            Scrollbar.init(document.querySelector('.setup.on .data'));
        }
        // if( $(this).parents('.setup').hasClass('one') ) {
        // }
        // if( $(this).parents('.setup').hasClass('one') ) {
        //
        // }

    });
    $('.p').on('click',function(){
        $(this).parents('.setup').removeClass('on').prev('.setup').addClass('on');
        Scrollbar.init(document.querySelector('.setup.on .data'));
    });

    // Tip Display
    $('.setup label').on('mouseover',function(e){
        $(this).find('.tip').addClass('on');
    }).on('mouseout',function(e){
        $(this).find('.tip').removeClass('on');
    });/* .on('mousemove',function(e){
        $(this).find('.tip').css({ 'left': e.originalEvent.offsetX });
    }); */

    // Fonts
    $('#fonts').select2({
        templateSelection: function(item) {
            value = item.id;
            select_name = item.element.offsetParent.name;
            optgroup_label = $('select[name="'+ select_name +'"] option[value="'+ value +'"]').parent('optgroup').prop('label');
            if(typeof optgroup_label != 'undefined') {
                return optgroup_label+': ' + item.text;
            } else {
                return item.text;
            }
        },
        closeOnSelect: false
    });

});

function verify_step( step ) {
    switch ( step ) {
        case 1:
            break;
        case 2:
            break;
        case 3:
            break;
        case 4:
            break;
    }
}

function dropdown_hints( d ) {
    if (!d.id) {
        return d.text;
    }
    let title = d.text;
    let titles = title.split('|');
    return $( titles[0] + ' <small>' + titles[1] + '</small>' );
}