$(document).ready(function(){

    // Open Modal
    $('body').on('click','[data-modal]',function(){
        $('body').addClass('modal_open');
        let e = $(this).data('modal');
        $(e).addClass('on');
        $(e).find('[data-add]').show();
        $(e).find('[data-update],[data-edit]').hide();
    })

    .on('click','[data-edit-action]',function () {
        $('body').addClass('modal_open');
    })

    // Modal Close Logics
    .on('click','.modal .close',function(){
        $(this).parent('.modal').removeClass('on');
        let removeFade = [];
        $('.modal').each(function(a,b){
            if( $(b).hasClass('on') ) {
                removeFade.push(1);
            }
        });
        //console.log( removeFade.length );
        if( removeFade.length === 0 ) { $('body').removeClass('modal_open'); }
        reset_modal(this);
    });
})

function reset_modal(e) {
    let m = $(e).hasClass('modal') ? $(e) : $(e).parent('.modal');
    $(m).find('[data-add]').show();
    $(m).find('[data-update],[data-edit]').hide();
    $(m).data('id','');
    clear( m, '[data-empty]' );
}
