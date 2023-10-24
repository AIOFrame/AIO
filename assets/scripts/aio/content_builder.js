document.addEventListener('DOMContentLoaded', function () {

    // Add Widget
    $('body').on('click','[data-add-widget]',function () {
        let p = $(this).parents('[data-widget]');
        let type = p.data('widget');
        let fields = get_values( p, type, '' );

        // Widget Data
        let wd = {};
        wd.widget_data = fields;
        wd.widget_type = type;
        wd.widget_name = p.data('widget-name');
        wd.widget_image = p.data('widget-image');
        console.log( wd );

        // Get Templates
        let row = $('[data-row-template]').html();
        let col = $('[data-col-template]').html();
        let widget = $('[data-widget-template]').html();

        // Insert Widget to JSON
        let content_field_target = $(this).parents('[data-content-builder-field]').data('content-builder-field');
        console.log( content_field_target );
        let content_field = $( '[data-key="' + content_field_target + '"]' );
        let data = content_field.val() !== '' ? JSON.parse( content_field.val() ) : [];
        data.push(wd);
        $(content_field).val( JSON.stringify( data ) );

        // Render Content Builder Area
        build_content_area( content_field_target );
    })

})

function build_content_area( element ) {

}