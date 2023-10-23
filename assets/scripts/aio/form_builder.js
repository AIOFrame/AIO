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
        $(parent).find('.aio_form_view > .row').append(template);
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
        k === 'min' ? f.attr( 'min', v ) : '';
        k === 'minlength' ? f.attr( 'minlength', v ) : '';
        k === 'max' ? f.attr( 'max', v ) : '';
        k === 'maxlength' ? f.attr( 'maxlength', v ) : '';
        k === 'c' ? $( t ).attr('class','').addClass('col-12 col-md-'+v).find( '[data-key]' ).attr( 'data-c', v ) : '';
        k === 'r' && $('[data-form-prop][name=r]').is(':checked') ? f.attr( 'required', true ) : f.attr( 'required', false );
        k === 'tr' && $('[data-form-prop][name=tr]').is(':checked') ? f.attr( 'data-l', 'true' ) : f.attr( 'data-l', 'false' );
        console.log( k );
        console.log( v );
        // Store Form Structure
        build_fields( $(this).parents('[data-form-builder-field]') );
    })

    .on('keyup change','[data-form-prop][name=l]',function () {
        let id = $(this).val().toLowerCase().replaceAll(' ','_');
        $(this).parent().find('[data-form-prop][name=i]').val( id ).change();
    })

    .on('click','[data-trash]',function(){
        let id = $('.aio_field_props').data('target');
        $('#'+id).remove();
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
        let o = { 't': $(fg).data('field'), 'i': $(f).attr('data-key'), 'l': $(f).attr('title'), 'v': $(f).val() };
        // Placeholder
        let p = $(f).attr('placeholder');
        p !== undefined && p !== '' ? o['p'] = p : '';
        // Attributes
        let a = $(f).attr('data-a');
        a !== undefined && a !== '' ? o['a'] = a : '';
        console.log( $(f).attr('data-tr') );
        console.log( $(f).attr('required') );
        // Bootstrap Column
        let c = $(f).attr('data-c');
        c !== undefined && c !== '' ? o['c'] = c : '';
        //console.log( o );
        data.push( o )
    });
    //console.log( data );
    // Fill the input
    $(target).prev().find('[data-key]').val( JSON.stringify( data ) );
}

function render_fields( target ) {

}