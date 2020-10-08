$(document).ready( function() {

    // Filter Languages
    $('body').on('keyup search','.filter_lang',function(){
        var fl = $('.filter_lang');
        if( fl.val().length > 2 ){
            $('[data-lang]').each(function(a,b){
                ~$(b).text().indexOf( $('.filter_lang').val() ) ? $(b).show() : $(b).hide();
            });
        } else {
            $('[data-lang]').show();
        }
    })

});