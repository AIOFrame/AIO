$(document).ready( function() {

    // CHANGE LANGUAGE
    $('body').on('click','[data-lang]',function(){
        post( $(this).parents('data-language'), { 'lang' : $(this).data('lang') }, '', 1 );
    })

    .on('change','[data-languages]',function(){
        post( $(this).data('language'), { 'lang' : $(this).val() }, '', 1 );
    });

});