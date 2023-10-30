document.addEventListener('DOMContentLoaded', function () {

    let var_el = $('[data-tab="#variations_data"],#variations_data');

    // Auto Fill Product Properties
    $('body').on('click','[data-edit-action]',function () {
        let p = $(this).data('data');
        let props = p['props'];
        let meta = p['meta'];
        $.each( props, function ( i, pp ) {
            //console.log( i );
            //console.log( pp['prod_pr_type'] );
            //console.log( pp['prod_pr_meta'] );
            $('[data-keyed-array="properties"][data-key='+pp['prod_pr_type']+'][value='+pp['prod_pr_meta']+']').attr('checked',true);
        });
        let r = $('[data-product-wrap]').data('data');
        $.each( meta, function( mk, mv ){
            //console.log( mk );
            //console.log( mv );
            //console.log( $( '[data-'+r+'][data-key=' + mk + ']' ) );
            $('[data-'+r+'][data-key=' + mk + ']').val(mv);
        });
        if( p['type'] === 2 ) {
            $(var_el).removeClass('dn')
            $('#variations_wrap').find('[data-key=id]').val(p['id']).attr('value',p['id']);
            $('[data-var-template]').find('[data-key=id]').val(p['id']).attr('value',p['id']);

            // Build Variations
            let vars = p['vars'];
            //console.log( vars );
            let i = 1;
            $.each( vars, function (vi, vp) {
                $('[data-add-var-action]').click();
                //console.log( vi );
                //console.log( vp );
                let props = vp['prod_props'];
                delete vp['prod_props'];
                let meta = vp['prod_meta'];
                delete vp['prod_meta'];
                let vw = $('[data-variations-wrap]>div:nth-child('+i+')');
                $(vw).find('[data-pv]').data('id',vp['prod_id']).attr('id',vp['prod_id']);
                $(vw).find('[data-trash-var]').data('id',vp['prod_id']).attr('id',vp['prod_id']);
                $.each( vp, function ( vpk, vpv ) {
                    let input = $(vw).find('[data-key='+vpk.replaceAll('prod_','v_')+']');
                    if( input.length > 0 ) {
                        $( input ).val( vpv );
                        //console.log( input );
                        //console.log( vpk );
                        //console.log( vpv );
                    }
                });
                $.each( meta, function( mk, mv ){
                    //console.log( mk );
                    //console.log( mv );
                    //console.log( $( '[data-'+r+'][data-key=' + mk + ']' ) );
                    $(vw).find('[data-key=v_' + mk + ']').val(mv);
                });
                //console.log( props );
                $.each( props, function ( i, pp ) {
                    //console.log( i );
                    console.log( pp['prod_pr_type'] );
                    console.log( pp['prod_pr_meta'] );
                    $(vw).find('[data-array="v_properties"][data-key='+pp['prod_pr_type']+']').val( pp['prod_pr_meta'] );
                });

                //console.log( vw );
                i++;
            })
        } else {
            $(var_el).addClass('dn');
            $('[data-variations-wrap],[data-var-template]').find('[data-key=id]').val('').attr('value','');
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
        .on('click','#product_modal .close',function () {
            $('[data-tab="#description_data"]').click();
            $('#variation_wrap').html('');
        })

})

function variation_callback( r ) {
    if( r[0] === 1 ) {

    }
}