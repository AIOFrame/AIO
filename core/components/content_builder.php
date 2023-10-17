<?php
$f = new FORM();
$c = new CMS();
pre( '', 'aio_content_builder' );

    // Content View
    pre( 'aio_cb_view', 'aio_cb_view' );

    post();
    el( 'button', 'add_button', '', '+' );

    // Structural Templates
    pre( 'aio_cb_templates', 'aio_cb_templates' );
        // Widget Template
        div( '', 'widget', '', 'data-widget-template' );
        // Rows Template
        div( '', 'row', '', 'data-row-template' );
        // Column Template
        div( '', 'col', '', 'data-col-template' );
        // Add Button Template
        el( 'button', 'add_button', '', '+', 'data-add-template' );
    post();

    // Widget Picker
    pre( 'aio_cb_widget_picker', 'aio_cb_widget_picker' );
        $widgets = $c->_widgets();
        $widget_buttons = '';
        if( !empty( $widgets ) ) {
            foreach( $widgets as $wk => $wd ) {
                $widget_buttons = _div( $wk, $wk.' add_widget', _el( 'i', 'mat-ico '.$wd['icon'], '', $wd['icon'] ) . _el( 'div', 'widget_title', '', $wd['name'], '', 1 ), 'data-add-widget' );
            }
        }
        modal( 'Widgets', 'b20', '', $widget_buttons );
    post();

    // Widget Form
    pre( 'aio_cb_widget_form', 'aio_cb_widget_form' );
    post();

post();
get_style('content_builder');
get_script('content_builder');