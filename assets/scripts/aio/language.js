$(document).ready( function() {

    // Change language on button click
    $('body').on('click','[data-lang]',function(){
        //console.log( $(this).parents('data-language') );
        //console.log( $(this).data('lang') );
        post( $(this).parents('data-language'), { 'lang' : $(this).data('lang') } );
    })

    // Change language on select change
    .on('change','[data-languages]',function(){
        post( $(this).data('language'), { 'lang' : $(this).val() } );
    });

});