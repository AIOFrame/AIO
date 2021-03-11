$(document).ready(function(){

    if( typeof ClipboardJS === 'function' ){
        new ClipboardJS('.btn, button');
    }

    // Load Default

    $('[data-clipboard-copy]').on('click',function () {
        clipboard = $($(this).data('clipboard-copy')).val();
    });

    if( jQuery.fn.select2 ){
        $('#lang_select,#lang_page').select2({});
    }

    // Load Existing Translations
    /* $.post(location.origin,{'action':'get_translations','lang':'en'},function(r){
        console.log(r);
    });*/

    // Type in Search
    $('body').on('keyup','.search input',function(e){
        var sv = $(this).val();
        //elog(sv);
        $.each($('#translations > div'),function(a,b){
            if( $(b).find('div:nth-child(1)').html().indexOf(sv) >= 0 ) {
                $(b).show();
            } else {
                $(b).hide();
            }
        })
    })

    // Filter Languages
    .on('keyup search','.filter_lang',function(){
        var fl = $('.filter_lang');
        if( fl.val().length > 2 ){
            $('[data-lang]').each(function(a,b){
                ~$(b).text().toLowerCase().indexOf( $('.filter_lang').val().toLowerCase() ) ? $(b).show() : $(b).hide();
            });
        } else {
            $('[data-lang]').show();
        }
    })

    // Edit Sentence
    .on('click','#aio_translations > div > div',function(e){
        $('#aio_translations > div').removeClass('on');
        var tr = $(this).parent('div');
        tr.addClass('on');
        $('#aio_lang_editor').addClass('on').data('row',tr.index());
        $('#aio_lang_editor #string').val(tr.find('div:nth-child(1)').html());
        $('#aio_lang_editor #translation').val(tr.find('div:nth-child(2)').html()).focus();
    })

    // Close Editor
    .on('click','#aio_lang_editor .close',function(){
        $('#aio_translations > div,#aio_lang_editor').removeClass('on');
    })

    // Save Sentence
    // var edit;
    // $('#string,#translation').on('keyup',function(){
    //     clearTimeout(edit);
    //     edit = setTimeout(function(){ update_translations(); }, 1500);
    // });

    // Delete Sentence
    .on('click','.trash',function(){
        if( confirm( 'This string will not be available to translate anymore in all languages ?' ) ){
            let id = $(this).data('id');
            post( 'remove_translation', { 'id': id }, 3, 3 );
        }
    })
});

function get_translations() {
    var d = {'action':'get_translations','lang':'en','method':'json'};
    $.post(location.origin,d,function(r){
        elog(r);
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
    $('#translations tbody').append('<tr><td>'+string+'</td><td></td><td><i class="ico trash"></i></td></tr>').animate({ scrollTop: $('tbody')[0].scrollHeight }, 1000).find('tr:last-child td:first-child').click();
}

function update_translation() {
    //if( $('#save').data('row') !== undefined && $('#save').data('row') !== '' ){

    // Update the Table
    var r = $('#translations div.on');
    if( r.length > 0 ){
        $(r).data('edited',true).find('div:first-child').html($('#string').val());
        $(r).find('div:nth-child(2)').html($('#translation').val());
    }

    // Update Translations
    var ln = $('#lang_select').val();
    var d = { 'action': 'update_translation', 'language': ln, 'string': $('#string').val(), 'translation': $('#translation').val() };
    elog(d);

    if( ln !== null ) {
        $.post( location.origin, d, function(re){
            if( re = JSON.parse(re) ){
                notify(re[1]);
                if( re[0] === 1 ) {
                    $('#aio_lang_editor').removeClass('on');
                    console.log($(r));
                }
            }
        });
    } else {
        notify('Please select a language and then add translations!');
    }
    //$('#translations tbody tr').data('edited','');
    //}
}

function add_lang() {

}

function build_translations() {
    //var d = [];
    /*
    var ts = []; var sl = [];
    $('#translations tbody tr').each(function(i,e){
        if( $(e).data('edited') !== undefined && $(e).data('edited') ) {
            //fl.push( $(e).find('td:first-child').html() ); // = $(e).find('td:first-child').html();
            //sl.push( $(e).find('td:nth-child(2)').html() );
            ts.push([$(e).find('td:first-child').html(), $(e).find('td:nth-child(2)').html()])
        }
        // //
    });
    var d = { 'action': 'update_translations', 'language': sln, 'translations': ts };
    elog(d);
    $.post( location.origin, d, function(r){
        if( r = JSON.parse(r) ){
            notify(r[1]);
        }
    })*/
}