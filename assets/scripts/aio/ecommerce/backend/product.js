document.addEventListener('DOMContentLoaded', function () {

    let var_el = $('[data-tab="#variations_data"],#variations_data');

    // Auto Fill Product Properties
    $('body').on('click','[data-edit-action]',function () {
        let p = $(this).data('data');
        let props = p['props'];
        $.each( props, function ( i, pp ) {
            //console.log( i );
            //console.log( pp['prod_pr_type'] );
            //console.log( pp['prod_pr_meta'] );
            $('[data-keyed-array="properties"][data-key='+pp['prod_pr_type']+'][value='+pp['prod_pr_meta']+']').attr('checked',true);
        } );
        if( p['type'] === 2 ) {
            $(var_el).removeClass('dn')
            //$('[data-variations-wrap]').find('[key=pid]').val(p['id']);
        } else {
            $(var_el).addClass('dn');
            //$('[data-variations-wrap]').find('[key=pid]').val('');
        }
        //console.log(p);
    })

    .on('click','[data-modal="#product_modal"]',function () {
        $(var_el).addClass('dn');
    })

    // Hide Variations
    .on('change','[data-key=type]',function () {
        /* let var_el = $('[data-tab="#variations_data"],#variations_data');
        if( $(this).val() === '2' ) {
            $(var_el).removeClass('dn');
        } else {
            $(var_el).addClass('dn');
        } */
    })

    // Add Variation Set
    .on('click','[data-add-var-action]',function () {
        let var_template = $('[data-var-template]').html();
        let acc_template = $('[data-acc-template]').html();
        let var_wrap = $('[data-variations-wrap]');
        var_template.replaceAll('dn','');
        acc_template = acc_template.replaceAll('{{var}}',var_template);
        var_wrap.append( acc_template );
        // Trigger Select
        $(var_wrap).find('.select2-container').remove();
        select_init( $(var_wrap).find('select') );
    })

    // Save Variation
    .on('click','.save_var',function () {

    })
    // Remove Variation
    .on('click','.trash_var',function () {
        let confirm = $('[data-variations-wrap]').data('confirm');
        if( confirm( confirm ) ) {
            $(this).parents('[data-variation-set]').remove();
        }
    })

})