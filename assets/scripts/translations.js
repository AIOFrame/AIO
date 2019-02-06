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
    $('body').on('click','#translations tbody td:not(:nth-child(3))',function(e){
        $('#translations tbody tr').removeClass('on');
        var tr = $(this).parents('tr');
        tr.addClass('on');
        $('#editor').data('row',tr.index());
        $('#english_string').val(tr.find('td:first-child').html());
        $('#translation').val(tr.find('td:nth-child(2)').html()).focus();
    });

    $('.modal .close').on('click',function(){
        $(this).parents('.modal').hide();
    });

    // Save Sentence
    // var edit;
    // $('#english_string,#translation').on('keyup',function(){
    //     clearTimeout(edit);
    //     edit = setTimeout(function(){ update_translations(); }, 1500);
    // });

    // Delete Sentence
    $('body').on('click','.trash',function(){
        if( confirm( 'Delete Row ?' ) ){
            $(this).parents('tr').remove();
        }
    })
});

function set_basic_translations() {

    var d = {'action':'get_translations','lang':$('#language_selector').val(),'method':'json'};

    $.post(location.origin,d,function(r){
        if( r = JSON.parse(r) ){
            $('#translations tbody').html('');
            $.each(r,function(base,replace){
                $('#translations tbody').append('<tr><td>'+base+'</td><td>'+replace+'</td><td><i class="ico trash"></i></td></tr>');
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
    $('#translations tbody').append('<tr><td>'+string+'</td><td></td><td><i class="ico trash"></i></td></tr>').animate({ scrollTop: $('tbody')[0].scrollHeight }, 1000).find('tr:last-child td:first-child').click();
}

function update_translation() {
    //if( $('#save').data('row') !== undefined && $('#save').data('row') !== '' ){

    // Update the Table
    var r = $('#translations tbody tr:nth-child('+($('#editor').data('row')+1)+')');
    if( r.length > 0 ){
        $(r).data('edited',true).find('td:first-child').html($('#english_string').val());
        $(r).find('td:nth-child(2)').html($('#translation').val());
    }

    // Update Translations
    var ln = $('#language_selector').val();
    var d = { 'action': 'update_translation', 'language': ln, 'english_string': $('#english_string').val(), 'translation': $('#translation').val() };

    $.post( location.origin, d, function(r){
        if( r = JSON.parse(r) ){
            elog(r);
            notify(r[1]);
        }
    });
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