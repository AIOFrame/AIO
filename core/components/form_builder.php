<?php
$f = new FORM();
pre( '', 'aio_form_builder' );
    // Form Fields
    pre( '', 'aio_form_fields' );
    $field_types = [
        'text' => 'text_fields',
        'textarea' => 'subject',
        'select' => 'expand_more',
        'radio' => 'radio_button_checked',
        'checkbox' => 'check_box',
        'password' => 'password',
        'color' => 'palette',
        'date' => 'event',
        'datetime' => 'event_note',
        'time' => 'schedule',
        'range' => 'linear_scale',
        'email' => 'email',
        'file' => 'attach_file',
        'files' => 'attachment',
        'hidden' => 'hide_source',
        'number' => 'pin',
        'tel' => 'smartphone',
        'url' => 'link'
    ];
    $f->input('search','','','Search...','','data-fields-search');
        pre( '', 'aio_form_fields_wrap' );
        foreach( $field_types as $type => $icon ) {
            div( '', 'form-btn', _el( 'i', 'mat-ico', '', $icon ) . _el( 'span', '', '', $type), 'data-type="'.$type.'" data-field' );
        }
        post();
    post();

    // Form View
    pre( '', 'aio_form_view' );
    post();

    // Field Properties
    pre( '', 'aio_field_props' );

        h4( 'Basic Options', 1 );
        $f->text('title','Title','','','data-form-build');
        $f->text('place','Placeholder','','','data-form-build');
        $f->text('id','Identity','','','data-form-build');
        $f->text('value','Default Value','','','data-form-build');

        h4( 'Advanced Options', 1 );
        $bootstrap_cols = [];
        for ($i = 12; $i > 0; $i--) {
            $bootstrap_cols[ $i ] = round( ( $i / 12 ) * 100 ) . '% (col-'.$i.')';
        }
        $f->text('attr','Attributes','','','data-form-build');
        _r();
            $f->text('min','Min','','','data-form-build',6);
            $f->text('max','Max','','','data-form-build',6);
            $f->slide('required','Required','','',0,'m','data-form-build',6);
            $f->slide('translate','Translate','','',1,'m','data-form-build',6);
            $f->select2('col','Bootstrap Column','Set Column...',$bootstrap_cols,12,'data-form-build',12,1);
        r_();

        //h4( 'Logics', 1 );
        //$logics = [ '=' => '= to', '!=' => '!= to', '<' => '< than', '<=' => '<= to', '>' => '> than', '>=' => '>= to', 'has' => 'contains' ];
        //el( 'button', 'trash', '', _el( 'i', 'mat-ico', '', 'trash' ) );
    post();
post();
get_style('form_builder');
get_script('form_builder');