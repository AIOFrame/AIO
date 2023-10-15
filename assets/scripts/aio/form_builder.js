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
            $(p).find('[data-key='+i+']').val( ( props[i] !== undefined ? props[i] : '') );
        });
        $('.aio_field_props').data('target',id);

        build_fields( $(this).parents('[data-form-builder-field]') );
    })

    .on('keyup change','[data-form-prop]',function(){
        // Fetch Property Data
        //let p = $(this).find('.aio_field_props');
        let t = '#' + $(this).parents('.aio_field_props').data('target');
        //console.log( t );
        let k = $(this).data('key');
        let v = $(this).val();
        //console.log( v );

        // Update field prop to data
        $( t ).data( k, v );

        // Update Visibility
        let f = $( t ).find( '[data-key]' );
        k === 'l' ? $( t ).find( 'label' ).html( v ) : '';
        k === 'l' ? f.attr( 'title', v ) : '';
        k === 'p' ? f.attr( 'placeholder', v ) : '';
        k === 'i' ? f.attr( 'data-key', v ) : '';
        k === 'v' ? f.val( v ) : '';
        k === 'a' ? f.attr( 'data-a', v ) : '';
        k === 'min' ? f.attr( 'minlength', v ) : '';
        k === 'max' ? f.attr( 'maxlength', v ) : '';
        k === 'c' ? $( t ).attr('class','').addClass('col-12 col-md-'+v).find( '[data-key]' ).attr( 'data-c', v ) : '';
        k === 'r' && $(f).is(':checked') ? f.attr( 'required', true ) : f.attr( 'required', false );
        k === 'tr' && $(f).is(':checked') ? f.attr( 'data-l', 'true' ) : f.attr( 'data-l', 'false' );
        console.log( k );
        console.log( v );
        // Store Form Structure
        build_fields( $(this).parents('[data-form-builder-field]') );
    })


});

function build_fields( target ) {
    // Get fields
    let data = [];
    //console.log( target );
    let fields = $(target).find('[data-field]');
    $( fields ).each(function (i, fg) {
        //let d = $(f).data();
        let f = $(fg).find('[data-key]');
        console.log( $(f) );
        console.log( $(f).data('key') );
        let o = { 't': $(fg).data('field'), 'i': $(f).attr('data-key'), 'p': $(f).attr('placeholder'), 'l': $(f).attr('title'), 'v': $(f).val(), 'a': $(f).attr('data-a'), 'c': $(f).attr('data-c'), 'tr': $(f).attr('data-tr') === 'true', 'r': $(f).attr('required') };
        //console.log( o );
        data.push( o )
    });
    //console.log( data );
    // Fill the input
    $(target).prev().find('[data-key]').val( JSON.stringify( data ) );
}

function render_fields( target ) {

}