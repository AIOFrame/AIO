document.addEventListener('DOMContentLoaded', function () {

    let autofill = [ 't', 'p', 'i', 'v', 'a', 'r', 't', 'c', 'min', 'max' ];

    $('body').on('click','[data-field-btn]',function(){
        let parent = $(this).parents('.aio_form_builder');
        let type = $(this).data('type');
        let target = $(this).parents('[data-form-builder-field]').data('form-builder-field');
        let random = Math.floor((Math.random() * 1000000) + 1);
        // Set Template
        let template = $(parent).find('[data-'+type+'-template]')[0].outerHTML;
        template = template.replaceAll('data-'+type+'-template=""','data-field="'+type+'" id="'+type+'_'+random+'"');
        // Insert Template
        $(parent).find('.aio_form_view').append(template);
        // Set fields structure
        build_fields( target );
    })

    // Fetch field properties and fill property fields
    .on('click focus','[data-field]',function () {
        let parent = $(this).parents('.aio_form_builder');
        let p = $(parent).find('.aio_field_props');
        let props = $(this).data();
        let id = $(this).attr('id');
        console.log( $(this) );
        console.log(id);
        //let f = $(this).find('input').unique;
        // Auto Fill Data and set target field
        //console.log(autofill);
        $('[data-field]').removeClass('on');
        $(this).addClass('on');
        $( autofill ).each( function (a, i) {
            //console.log('[data-key='+i+']');
            //console.log( props[i] );
            //console.log( f );
            $(p).find('[data-key='+i+']').attr('target',id).val( ( props[i] !== undefined ? props[i] : '') );
        });
    })

    .on('keyup change','[data-form-prop]',function(){
        // Fetch Property Data
        //let p = $(this).find('.aio_field_props');
        let t = '#' + $(this).attr('target');
        console.log( t );
        let k = $(this).data('key');
        let v = $(this).val();
        //console.log( v );

        // Update field prop to data
        $( t ).data( k, v );

        // Update Visibility
        k === 't' ? $( t ).find( 'label' ).html( v ) : '';
        k === 'p' ? $( t ).find( 'input' ).attr( 'placeholder', v ) : '';
        k === 'i' ? $( t ).find( 'input' ).attr( 'data-key', v ) : '';
        k === 'v' ? $( t ).find( 'input' ).val( v ) : '';
        k === 'a' ? $( t ).find( 'input' ).attr( 'data-a', v ) : '';
        k === 'min' ? $( t ).find( 'input' ).attr( 'minlength', v ) : '';
        k === 'max' ? $( t ).find( 'input' ).attr( 'maxlength', v ) : '';
        k === 'c' ? $( t ).attr('class','').addClass('col-12 col-md-'+v).find( 'input' ).attr( 'data-c', v ) : '';
        k === 'r' && v === '2' ? $( t ).find( 'input' ).attr( 'required', true ) : $( t ).find( 'input' ).attr( 'required', false );
        k === 'l' && v === '2' ? $( t ).find( 'input' ).attr( 'data-l', 'true' ) : $( t ).find( 'input' ).attr( 'data-l', 'false' );
        console.log( k );
        console.log( v );
    })


});

function build_fields( target ) {
    // Get fields
    //let fields = $('[data-key='+target+']').val();
    //fields = fields !== '' ? JSON.parse( fields ) : [];
}

function render_fields( target ) {

}