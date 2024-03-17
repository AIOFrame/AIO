$(document).ready(function(){

    // Open Modal
    $('body').on('click','[data-modal]',function(){
        let e = $(this).data('modal');
        if( $(e).length > 0 ) {
            $('body').addClass('modal_open');
        }
        $(e).addClass('on');
        $(e).find('[data-add]').show();
        $(e).find('[data-update],[data-edit]').hide();
    })

    .on('click','[data-edit-action]',function () {
        $('body').addClass('modal_open');
    })

    // Modal Close Logics
    .on('click','.modal .close,[data-close_modal]',function(){
        let modal = $(this).hasClass('close') ? $(this).parent('.modal') : $(this).data('close_modal');
        $( modal ).removeClass('on');

        // Remove background fade if all modals are closed
        let removeFade = [];
        $('.modal').each(function(a,b){
            if( $(b).hasClass('on') ) {
                removeFade.push(1);
            }
        });
        if( removeFade.length === 0 ) { $('body').removeClass('modal_open'); }
        reset_modal( modal );
    });
})

function reset_modal(e) {
    let m = $(e).hasClass('modal') ? $(e) : $(e).parent('.modal');
    $(m).find('[data-add]').show();
    $(m).find('[data-update],[data-edit]').hide();
    $(m).data('id','');
    clear( m, '[data-empty]' );
}
