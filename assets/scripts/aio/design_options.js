document.addEventListener('DOMContentLoaded', function () {

    $('[data-unified_trigger]').on('change',function (e) {
        let key = $(this).data('key').split('_')[1];
        console.log( key );
        $('[data-override_'+key+']').val( $(this).val() );
        console.log( $('[data-override_'+key+']') );
    })

});