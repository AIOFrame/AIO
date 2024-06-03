$(document).ready(function(){

    $('body').on('click','table [data-toggle-checks]',function () {
        let n = $(this).parent().index();
        $(this).parents('table').find( 'tbody td:nth-child(' + ( n + 1 ).toString() + ') [type=checkbox]' ).attr('checked',$(this).is(':checked'));
    })

});