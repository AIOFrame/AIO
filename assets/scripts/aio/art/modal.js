$(document).ready(function(){

    // Modal Close Logics
    $('.modal .close').on('click',function(){
        $(this).parents('.modal').removeClass('on');
        var removeFade = true;
        $('.modal').each(function(a,b){
            if( $(b).hasClass('on') ) {
                removeFade = false;
            }
        });
        if( removeFade ) { $('article').removeClass('fade'); }
        reset_modal($(this));
    });
})

function reset_modal(e) {
    var m = $(e).parents('[data-t]');
    $(m).find('[data-add]').show();
    $(m).find('[data-update]').hide();

    $(m).data('id','');

    // TODO: If modal has class to empty then empty fields

}