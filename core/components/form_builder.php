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
            div( '', 'form-btn', _el( 'i', 'mat-ico', '', $icon ) . _el( 'span', '', '', $type), 'data-type="'.$type.'" data-field-btn' );
        }
        post();
    post();

    // Form View
    pre( '', 'aio_form_view row' );
    post();

    // Form View
    $fr = '[data-{{type}}-template] class="col-12"';
    pre( '', 'aio_templates', 'div', 'style="display: none"' );
        $f->text('text','Text','Text','','',str_replace('{{type}}','text',$fr));
        $f->textarea('textarea','Textarea','Textarea','','',str_replace('{{type}}','textarea',$fr));
    post();

    // Field Properties
    pre( '', 'aio_field_props' );

        h4( 'Basic Options', 1 );
        $f->text('t','Title','Title','','data-form-prop');
        $f->text('p','Placeholder','Placeholder','','data-form-prop');
        $f->text('i','Identity','Identity','','data-form-prop data-no-space');
        $f->text('v','Default Value','Default Value','','data-form-prop');

        h4( 'Advanced Options', 1 );
        $bootstrap_cols = [];
        for ($i = 12; $i > 0; $i--) {
            $bootstrap_cols[ $i ] = round( ( $i / 12 ) * 100 ) . '% (col-'.$i.')';
        }
        $f->text('a','Attributes','Attributes','','data-form-prop');
        _r();
            $f->text('min','Min','Min','','data-form-prop',6);
            $f->text('max','Max','Max','','data-form-prop',6);
            $f->slide('r','Required','Required','',0,'m','data-form-prop',6);
            $f->slide('l','Translate','Translate','',1,'m','data-form-prop',6);
            $f->select2('c','Bootstrap Column','Bootstrap Column',$bootstrap_cols,12,'data-form-prop',12,1);
        r_();

        //h4( 'Logics', 1 );
        //$logics = [ '=' => '= to', '!=' => '!= to', '<' => '< than', '<=' => '<= to', '>' => '> than', '>=' => '>= to', 'has' => 'contains' ];
        //el( 'button', 'trash', '', _el( 'i', 'mat-ico', '', 'trash' ) );
    post();
post();
get_style('form_builder');
get_script('form_builder');