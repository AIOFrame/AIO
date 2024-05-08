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
        widget.data = fields;
        widget.type = type;
        widget.name = p.data('widget-name');
        widget.ico = p.data('widget-ico');
        //console.log( widget );

        // Get Templates
        let widget_wrap;

        if( widgets.data('add-row') ) {
            widget_wrap = { 'type': 'row', 'children': { 'type': 'col', 'children': widget } }
        } else if( widgets.data('add-col') ) {


        }

        // Insert Widget to JSON
        let content_field_target = $(this).parents('[data-aio_cb_field]').data('aio_cb_field');
        //console.log( content_field_target );
        let content_field = $( '[data-aio_cb_code="' + content_field_target + '"]' );
        let data = [];
        try {
            data = JSON.parse( content_field.val() );
        } catch(e) {
            if( confirm( 'Page not built using AIO Page Builder, erase content and start fresh ?' ) ) {
                data = [];
            } else {
                alert( 'Widget cannot be added due to existing page content! Erase content and try again!!' );
                return console.error( e );
            }
        }
        console.log( data );
        console.log( widget_wrap );
        data.push( widget_wrap );
        console.log(data);
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
    let uid = $( element ).data('aio_cb_code');
    let view = $( '#view_' + uid );

    // Loop Widget and prepare Content
    let data = JSON.parse( element.val() );
    view.html( build_content( data ) );
}

function build_content( content ) {
    let view = '';
    $.each( content, function (i, set) {
        if( type === 'row' ) {
            let template = $('[data-row-template]').html();
            view += template.replaceAll('{{content}}',set.data);
        } else if( type === 'col' ) {
            let template = $('[data-col-template]').html();
            view += template.replaceAll('{{content}}',set.data);
        } else if( type === 'widget' ) {
            let template = $('[data-widget-template]').html();
            console.log( template );
            console.log( type );
            console.log( data );
            view += template
                .replaceAll('{{widget_name}}',data.name)
                .replaceAll('{{widget_image}}',data.ico)
                .replaceAll('{{widget_data}}','data-data="'+JSON.stringify(data.data)+'"')
                .replaceAll('{{widget_type}}','data-type="'+data.type+'"')
        }
    });
    return view;
}