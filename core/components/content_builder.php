<?php
$f = new FORM();
$c = new CMS();
pre( '', 'aio_content_builder' );

    // Content View
    pre( 'aio_cb_view', 'aio_cb_view' );

    post();
    el( 'button', 'add_button', '', '+', 'data-on=".choose_widgets_modal"' );

    // Structural Templates
    pre( 'aio_cb_templates', 'aio_cb_templates', 'div', 'style="display: none"' );
        // Widget Template
        div( '', 'widget', '', 'data-widget-template' );
        // Rows Template
        div( '', 'row', '', 'data-row-template' );
        // Column Template
        div( '', 'col', '', 'data-col-template' );
        // Add Button Template
        el( 'button', 'add_button', '', '+', 'data-add-template data-on=".choose_widgets_modal"' );
    post();

    // Widget Picker
    pre( 'aio_cb_widget_picker', 'aio_cb_widget_picker' );
        // Get Widgets List
        $widgets = $c->_widgets();
        $widget_add_buttons = '';
        if( !empty( $widgets ) ) {
            //skel( $widgets );
            foreach( $widgets as $wk => $wd ) {
                skel( $wk );
                $widget_add_buttons = _div( '', 'col-12 col-md-3', _div( $wk, $wk.' add_widget', _el( 'i', 'mat-ico '.$wd['icon'], '', $wd['icon'] ) . _el( 'div', 'widget_title', '', $wd['name'], '', 1 ), 'data-off=".choose_widgets_modal" data-on="#'.strtolower($wk).'_modal" data-add-widget' ) );
            }
        }
        // Widget Picker Modal
        modal( 'Choose Widgets', 0, 'b80', '', _el( 'div', 'row', '', $widget_add_buttons ) );
    post();

    // Widget Modals
    pre( 'aio_cb_widget_modals', 'aio_cb_widget_modals' );
    if( !empty( $widgets ) ) {
        foreach( $widgets as $wk => $wd ) {
            $form = is_array( $wd['form'] ) ? $wd['form'] : json_decode( $wd['form'], 1 );
            //skel( $form );
            modal( $wd['name'], 0, 'b80', '', $form, [], $wk );
        }
    }
    post();

    // Widget Form
    pre( 'aio_cb_widget_form', 'aio_cb_widget_form' );
    post();

post();
get_style('content_builder');
get_script('content_builder');