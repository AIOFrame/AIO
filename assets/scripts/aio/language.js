$(document).ready( function() {

    // CHANGE LANGUAGE
    $('body').on('click','[data-lang]',function(){
        //var cl = $('[data-lang].on').data('lang');
        var nl = $(this).data('lang');
        post('set_language',{'lang':nl},'',1);
    })

    .on('change','[data-languages]',function(){
        var nl = $(this).val();
        $.post(location.origin,{'action':'set_language','lang':nl},function(r){
            notify('Language Changed!');
            reload(3);
        });
    });

});