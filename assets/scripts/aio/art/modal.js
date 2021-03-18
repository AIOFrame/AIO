$(document).ready(function(){

    // Open Modal
    $('body').on('click','[data-modal]',function(){
        let e = $(this).data('modal');
        $(e).addClass('on');
        $(e).find('[data-add]').show();
        $(e).find('[data-update],[data-edit]').hide();
    })

    // Modal Close Logics
    .on('click','.modal .close',function(){
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
    var m = $(e).parents('[data-modal]');
    $(m).find('[data-add]').show();
    $(m).find('[data-update],[data-edit]').hide();

    $(m).data('id','');

    // TODO: If modal has class to empty then empty fields

}