<?php
$f = new FORM();
$c = new CMS();
global $options;
pre( '', 'aio_content_builder' );

    // Content View
    pre( 'aio_cb_view', 'aio_cb_view' );

    post();

    // Add Buttons
    pre( '', 'aio_actions df aic' );
        el( 'button', 'add_button add_content', __el( 'i', 'add_content ' . ( $options['ico_add'] ?? '' ) . ' add ' . ( $options['icon_class'] ?? 'mico' ), ( $options['ico_add'] ?? 'post_add' ) ) . T('Add Content'), '', 'data-show-widgets data-add-row' );
        el( 'button', 'add_button add_row', __el( 'i', 'add_row ' . ( $options['ico_add_row'] ?? '' ) . ' add_row ' . ( $options['icon_class'] ?? 'mico' ), ( $options['ico_add'] ?? 'playlist_add' ) ) . T('Add Row'), '', 'data-add-row' );
        el( 'button', 'add_button add_row', __el( 'i', 'add_row ' . ( $options['ico_add_row'] ?? '' ) . ' add_row ' . ( $options['icon_class'] ?? 'mico' ), ( $options['ico_add'] ?? 'playlist_add' ) ) . T('Add Gap'), '', 'data-add-gap' );
    post();

    // Structural Templates
    pre( 'aio_cb_templates', 'aio_cb_templates', 'div', 'style="display: none"' );
        // Widget Template
        $del_icon = __div( ( $options['icon_class'] ?? 'mico' ), ( $options['icon_class'] ?? 'delete' ), '', 'data-del' );
        $widget_head = __div( 'widget_head', __div( '', '{{widget_name}}', 'widget_name', 'data-name' ) . $del_icon );
        $widget_body = __div( 'widget_body', _img( '{{widget_image}}', '', 'widget_image' ), '', 'data-body' );
        div( '', __div( 'widget_set', $widget_head . $widget_body ), '', '{{widget_data}} data-widget-template data-data' );
        // Rows Template
        $row_left = __div( 'left', __div( ( $options['icon_class'] ?? 'mico' ), ( $options['ico_drag'] ?? 'drag_indicator' ), '', 'data-drag' ) );
        $min_icon = ''; //_div( ( $options['icon_class'] ?? 'mico' ), ( $options['ico_contract'] ?? 'expand_less' ), '', 'data-up' );
        $max_icon =  ''; //_div( ( $options['icon_class'] ?? 'mico' ), ( $options['ico_expand'] ?? 'expand_more' ), '', 'data-down' );
        $row_right = __div( 'right', $min_icon . $max_icon . $del_icon );
        $row_head = __div( 'row_title', $row_left . $row_right );
        $row_body = __div( 'row', '{{content}}', '', 'data-body' );
        div( '', __div( 'row_set', $row_head . $row_body ), '', 'data-row-template' );
        // Column Template
        div( '', __div( 'col', '{{content}}', '', 'data-col' ), '', 'data-col-template' );
        // Add Button Template
        //el( 'button', 'add_button', '+', '', 'data-add-col data-show-widgets' );
    post();

    // Widget Picker
    pre( 'aio_cb_widget_picker', 'aio_cb_widget_picker' );
        // Get Widgets List
        $widgets = $c->_widgets();
        $widget_add_buttons = '';
        if( !empty( $widgets ) ) {
            //skel( $widgets );
            foreach( $widgets as $wk => $wd ) {
                //skel( $wd );
                $widget_id = strtolower( str_replace( ' ', '_', $wd['name'] ) );
                //$image = __div('tac',_img($wd['image'],'','widget_image',$wd['name'],$wd['name'],'style="height: 100px"'));
                $icon = __div( 'widget_icon xl ' . ( $options['icon_class'] ?? 'mico' ), ( $wd['ico'] ?? ( $options['add'] ?? 'add_circle' ) ) );
                $widget_add_buttons .= __div( 'col-12 col-md-3', __div( $wk.' add_widget', $icon . __el( 'div', 'widget_title', $wd['name'], '', '', 1 ), $wk, 'data-close_modal=".choose_widgets_modal" data-modal="#'.$widget_id.'_modal" data-widget-link' ) );
            }
        }
        // Widget Picker Modal
        modal( 'Choose Widgets', 0, 'b80', '', __el( 'div', 'row', $widget_add_buttons ) );
    post();

    // Widget Modals
    pre( 'aio_cb_widget_modals', 'aio_cb_widget_modals' );
    if( !empty( $widgets ) ) {
        foreach( $widgets as $wk => $wd ) {
            $form = is_array( $wd['form'] ) ? $wd['form'] : json_decode( $wd['form'], 1 );
            //skel( $form );
            pre_modal( $wd['name'], 'b', 0 );
                pre( '', '', 'div', 'data-widget="'.$wk.'" data-widget-name="'.$wd['name'].'" data-widget-icon="'.$wd['ico'].'"' );
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