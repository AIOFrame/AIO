document.addEventListener('DOMContentLoaded', function () {

    // Auto Fill Product Properties
    $('body').on('click','[data-edit-action]',function () {
        let p = $(this).data('data');
        let props = p['props'];
        $.each( props, function ( i, pp ) {
            //console.log( i );
            console.log( pp['prod_pr_type'] );
            console.log( pp['prod_pr_meta'] );
            $('[data-keyed-array="properties"][data-key='+pp['prod_pr_type']+'][value='+pp['prod_pr_meta']+']').attr('checked',true);
        } );
        //console.log(p);
    })

})