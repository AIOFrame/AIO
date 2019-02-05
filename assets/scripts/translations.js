$(document).ready(function(){

    if( typeof ClipboardJS === 'function' ){
        new ClipboardJS('.btn, button');
    }

    // Load Default
    set_basic_translations();

    $('#language_selector').on('change',function(){
        set_basic_translations();
    });

    $('[data-clipboard-copy]').on('click',function () {
        clipboard = $($(this).data('clipboard-copy')).val();
    });

    // Load Existing Translations
    /* $.post(location.origin,{'action':'get_translations','lang':'en'},function(r){
        console.log(r);
    });*/

    $('#new_lang').on('click',function(){
        $('#modal_lang').show();
    });

    // Edit Sentence
    $('body').on('click','tbody td:not(:nth-child(3))',function(e){
        $('tbody tr').removeClass('on');
        var tr = $(this).parents('tr');
        tr.addClass('on');
        $('#save').data('row',tr.index());
        $('#en_translation').val(tr.find('td:first-child').html());
        $('#translation').val(tr.find('td:nth-child(2)').html());
    });

    $('.modal .close').on('click',function(){
        $(this).parents('.modal').hide();
    });

    // Save Sentence
    $('#translation').on('keyup',function(){
        save_row();
    })

    // Delete Sentence
    $('body').on('click','.trash',function(){
        if( confirm( 'Delete Row ?' ) ){
            $(this).parents('tr').remove();
        }
    })
});

function set_basic_translations() {
    var d = {'action':'get_translations','languages':['en',$('#language_selector').val()],'method':'json'};
    //console.log(d);
    $.post(location.origin,d,function(r){
        if( r = JSON.parse(r) ){
            $('tbody').html('');
            $.each(r[0],function(i){
                if( r[0][i] !== '' ){
                    var t = r[1][i] !== undefined ? r[1][i] : '';
                    $('tbody').append('<tr><td>'+r[0][i]+'</td><td>'+t+'</td><td><i class="ico trash"></i></td></tr>');
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
    var d = {'action':'get_untranslated'};
    $.post(location.origin,d,function(r){
        console.log(r);
        if( r = JSON.parse(r) ){
            if( r[0] === 1 ){
                $.each( r[1], function( i, word ) {
                    add_row( word );
                });
                build_translations();
            }
        }
    })
}

function add_row( string ) {
    string = string === undefined ? '' : string;
    $('tbody').append('<tr><td>'+string+'</td><td></td><td><i class="ico trash"></i></td></tr>').animate({ scrollTop: $('tbody')[0].scrollHeight }, 1000).find('tr:last-child').click();
}

function save_row() {
    var r = $('tbody tr:nth-child('+($('#save').data('row')+1)+')');
    if( r.length > 0 ){
        $(r).find('td:first-child').html($('#en_translation').val());
        $(r).find('td:nth-child(2)').html($('#translation').val());
    }
}

function add_lang() {

}

function build_translations() {
    //var d = [];
    var sln = $('#language_selector').val();
    var fl = []; var sl = [];
    $('tbody tr').each(function(i,e){
        fl.push( $(e).find('td:first-child').html() ); // = $(e).find('td:first-child').html();
        sl.push( $(e).find('td:last-child').html() );
        //d.push({ 'en': $(e).find('td:first-child').html(), sl:$(e).find('td:last-child').html() })
    });
    var d = { 'action': 'build_translations', 'languages': ['en',sln], 'translations': [fl, sl] };
    $.post( location.origin, d, function(r){
        console.log(r);
    })
}