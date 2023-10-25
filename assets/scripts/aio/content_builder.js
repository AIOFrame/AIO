document.addEventListener('DOMContentLoaded', function () {

    $('body')

    // Show Widgets
    .on('click','[data-show-widgets]',function(){
        let widgets = $('#choose_widgets_modal');
        if( $(this).data('add-row') !== undefined ) {
            widgets.addClass('on').data('add-row',true).data('add-col',undefined);
        } else if( $(this).data('add-col') !== undefined ) {
            widgets.addClass('on').data('add-col',true).data('add-row',undefined);
        }
    })
    // Add Widget
    .on('click','[data-add-widget]',function (e) {
        let m = $(this).parents('.aio_cb_widget_modals').find('.modal');
        let p = $(this).parents('[data-widget]');
        let type = p.data('widget');
        let fields = get_values( p, type, '' );
        let widgets = $('#choose_widgets_modal');

        // Widget Data
        let widget = {};
        widget.type = 'widget';
        widget.widget_data = fields;
        widget.widget_type = type;
        widget.widget_name = p.data('widget-name');
        widget.widget_image = p.data('widget-image');
        console.log( widget );

        // Get Templates
        let widget_wrap;

        if( widgets.data('add-row') ) {
            widget_wrap = { 'type': 'row', 'children': { 'type': 'col', 'children': widget } }
        } else if( widgets.data('add-col') ) {


        }

        // Insert Widget to JSON
        let content_field_target = $(this).parents('[data-content-builder-field]').data('content-builder-field');
        let content_field = $( '[data-key="' + content_field_target + '"]' );
        let data = content_field.val() !== '' ? JSON.parse( content_field.val() ) : [];
        data.push( widget_wrap );
        $(content_field).val( JSON.stringify( data ) );

        // Render Content Builder Area
        render_content( content_field );

        // Reset and Close
        //reset_modal( p );
        clear( m, '[data-key]' );
        m.removeClass('on');
    })

})

function render_content( element ) {
    console.log( element );


    // Loop Widget and prepare Content
    let content = '';
    let data = JSON.parse( element.val() );
    $.each( data, function (i, w) {
        let content_set = '';
        if( w.type === 'row' ) {
            let content_child_one = '';
            if( w.children !== undefined ) {
                content_child_one = build_content( 'row' );
            }
        }
        content += content_set;
        console.log( w );
    } )
}

function build_content( type, data ) {
    if( type === 'widget' ) {
        let temp = $('[data-widget-template]').html();
        return temp
            .replaceAll('{{widget_name}}',data.widget_name)
            .replaceAll('{{widget_image}}',data.widget_image)
            .replaceAll('{{widget_data}}','data-data="'+JSON.stringify(data.widget_data)+'"')
            .replaceAll('{{widget_type}}','data-type="'+data.widget_data+'"')
    } else {
        let temp = type === 'row' ? $('[data-row-template]').html() : $('[data-col-template]').html();
        return temp.replaceAll('{{content}}',data);
    }
}