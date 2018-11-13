$(document).ready(function(){
    // Load Default
    set_basic_translations();

    // Load Existing Translations
    /* $.post(location.origin,{'action':'get_translations','lang':'en'},function(r){
        console.log(r);
    });*/

    $('#new_lang').on('click',function(){
        $('#modal_lang').show();
    })

    // Edit Sentence
    $('body').on('click','tbody tr',function(e){
        $('tbody tr').removeClass('on');
        $(this).addClass('on');
        $('#save').data('row',$(this).index());
        $('#en_translation').val($(this).find('td:first-child').html());
        $('#translation').val($(this).find('td:last-child').html());
    })

    $('.modal .close').on('click',function(){
        $(this).parents('.modal').hide();
    })

    // Save Sentence

    // Languages Build
});

function set_basic_translations() {
    var d = {'action':'get_translations','languages':['en','ru'],'method':'json'};
    //console.log(d);
    $.post(location.origin,d,function(r){
        if( r = JSON.parse(r) ){
            $.each(r[0],function(i){
                if( r[0][i] !== '' && r[1][i] !== ''){
                    $('tbody').append('<tr><td>'+r[0][i]+'</td><td>'+r[1][i]+'</td></tr>');
                }
            });
        }
    });
}

function get_translations() {
    var d = {'action':'get_translations','lang':'en','method':'json'};
    $.post(location.origin,d,function(r){
        if( r = JSON.parse(r) ){
            return r;
            /*console.log(r);
            $('tbody tr').each(function(i,e){
                if()
                $(e).find('td:first-child').html(r[i]);
            }) */
        }
    });
}

function get_untranslations() {
    var d = {'action':'get_untranslations'};
    $.post(location.origin,d,function(r){
        if( r = JSON.parse(r) ){
            console.log(r);
        }
    })
}

function add_row() {
    $('tbody').append('<tr><td></td><td></td></tr>');
    $('tbody tr:last-child').click();
    $("tbody").animate({ scrollTop: $('tbody')[0].scrollHeight }, 1000);
}

function save_row() {
    var r = $('tbody tr:nth-child('+($('#save').data('row')+1)+')');
    if( r.length > 0 ){
        $(r).find('td:first-child').html($('#en_translation').val());
        $(r).find('td:last-child').html($('#translation').val());
    }
}

function add_lang() {

}

function build_languages() {
    var d = [];
    $.each('tbody tr',function(i,e){
        d.push({ 'en': $(e).find('td:first-child').html(), '':$(e).find('td:last-child').html() })
    });
}