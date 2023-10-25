<?php
$f = new FORM();
$c = new CMS();
pre( '', 'aio_content_builder' );

    // Content View
    pre( 'aio_cb_view', 'aio_cb_view' );

    post();
    el( 'button', 'add_button', '+', '', ' data-show-widgets data-add-row' );

    // Structural Templates
    pre( 'aio_cb_templates', 'aio_cb_templates', 'div', 'style="display: none"' );
        // Widget Template
        $del_icon = _div( 'mat-ico', 'remove_circle', '', 'data-del' );
        $widget_head = _div( 'widget_head', _div( '', '{{widget_name}}', 'widget_name', 'data-name' ) . $del_icon );
        $widget_body = _div( 'widget_body', _img( '{{widget_image}}', '', 'widget_image' ), '', 'data-body' );
        div( '', _div( 'widget_set', $widget_head . $widget_body ), '', '{{widget_data}} data-widget-template data-data' );
        // Rows Template
        $row_left = _div( 'left', _div( 'mat-ico', 'drag_handle', '', 'data-drag' ) );
        $min_icon = _div( 'mat-ico', 'expand_less', '', 'data-min' );
        $max_icon = _div( 'mat-ico', 'expand_more', '', 'data-max' );
        $row_right = _div( 'right', $min_icon . $max_icon . $del_icon );
        $row_head = _div( 'row_title', $row_left . $row_right );
        $row_body = _div( 'row', '{{content}}', '', 'data-body' );
        div( '', _div( 'row_set', $row_head . $row_body ), '', 'data-row-template' );
        // Column Template
        div( '', _div( 'col', '{{content}}', '', 'data-col' ), '', 'data-col-template' );
        // Add Button Template
        el( 'button', 'add_button', '+', '', 'data-add-col data-show-widgets' );
    post();

    // Widget Picker
    pre( 'aio_cb_widget_picker', 'aio_cb_widget_picker' );
        // Get Widgets List
        $widgets = $c->_widgets();
        $widget_add_buttons = '';
        if( !empty( $widgets ) ) {
            //skel( $widgets );
            foreach( $widgets as $wk => $wd ) {
                //skel( $wk );
                $widget_id = strtolower( str_replace( ' ', '_', $wd['name'] ) );
                $image = _div('tac',_img($wd['image'],'','widget_image',$wd['name'],$wd['name'],'style="height: 100px"'));
                $widget_add_buttons .= _div( 'col-12 col-md-3', _div( $wk.' add_widget', $image . _el( 'div', 'widget_title', $wd['name'], '', '', 1 ), $wk, 'data-off=".choose_widgets_modal" data-on="#'.$widget_id.'_modal" data-widget-link' ) );
            }
        }
        // Widget Picker Modal
        modal( 'Choose Widgets', 0, 'b80', '', _el( 'div', 'row', $widget_add_buttons ) );
    post();

    // Widget Modals
    pre( 'aio_cb_widget_modals', 'aio_cb_widget_modals' );
    if( !empty( $widgets ) ) {
        foreach( $widgets as $wk => $wd ) {
            $form = is_array( $wd['form'] ) ? $wd['form'] : json_decode( $wd['form'], 1 );
            //skel( $form );
            pre_modal( $wd['name'], 'b', 0 );
                pre( '', '', 'div', 'data-widget="'.$wk.'" data-widget-name="'.$wd['name'].'" data-widget-image="'.$wd['image'].'"' );
                $f->form( $form, 'row', $wd['name'] );
                    pre( '', 'tac' );
                    b('add_widget','Add Widget','','data-add-widget');
                    post();
                post();
            post_modal();
            //modal( $wd['name'], 0, 'b80', '', $form, [], $wk );
        }
    }
    post();

    // Widget Form
    pre( 'aio_cb_widget_form', 'aio_cb_widget_form' );
    post();

post();
file_upload();
get_style('content_builder');
get_script('content_builder');