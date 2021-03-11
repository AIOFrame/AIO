$(document).ready(function(){

    $('body').on('click','.aio_dynamics .add',function(){
        var dyn = $(this).parents('.aio_dynamics').prev('input').data('dyn');
        $(this).parents('.aio_dynamics').find('.fields').append(dyn);
    })

    .on('click','.aio_dynamics .trash',function(){
        $(this).parents('.field_set').remove();
    })

    .on('change keyup','.aio_dynamics input',function () {
        var d = [];
        $.each( $(this).parents('.aio_dynamics').find('.field_set'), function(a,b){
            d.push( get_values( b ) );
        });
        elog(d);
        $(this).parents('.aio_dynamics').prev('input').val( JSON.stringify( d ) );
    });

    $('[data-dynamic]').each( function(a,b){

        var dyn = '<div class="field_set"><div class="trash"></div>';
        var i = 0;
        $.each( $(b).data('dynamic'), function(c,d){
            i++;
            dyn += '<div class="set">';

            if( d[0] === 'text' ) {
                dyn += '<label for="' + d[1] + '_' + i + '">'+ d[2] +'</label><input type="' + d[0] + '" placeholder="'+ d[2] +'" id="'+ d[1] +'_'+i+'">'
            } else if( d[0] === 'div' ) {
                dyn += '<div class="'+d[1]+'">'+location.href+'page</div>';
            } else if( d[0] === 'checkbox' || d[0] === 'radio' ) {
                dyn += '<input type="' + d[0] + '" id="'+ d[1] + '_' + i + '"><label for="' + d[1] + '_' + i + '">'+ d[2] +'</label>'
            }
            dyn += '</div>';
        });
        dyn += '</div>';
        $('<div class="aio_dynamics"><div class="fields">'+dyn+'</div><div class="btn add">+</div></div>').insertAfter($(b));
        $(this).data('dyn',dyn).hide();
    });

})