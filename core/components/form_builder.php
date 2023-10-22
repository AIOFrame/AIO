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
        'slide' => 'check_box',
        'password' => 'password',
        'color' => 'palette',
        'date' => 'event',
        //'datetime' => 'event_note',
        'time' => 'schedule',
        'range' => 'linear_scale',
        'email' => 'email',
        'code' => 'code',
        'file' => 'attach_file',
        'files' => 'attachment',
        'hidden' => 'hide_source',
        'number' => 'pin',
        'tel' => 'smartphone',
        //'url' => 'link',
        'richtext' => 'format_color_text',
        'country' => 'flag',
        'map' => 'map',
    ];
    $f->input('search','','','Search...','','data-fields-search');
        pre( '', 'aio_form_fields_wrap' );
        foreach( $field_types as $type => $icon ) {
            div( 'form-btn', _el( 'i', 'mat-ico', $icon ) . _el( 'span', '', $type), '', 'data-type="'.$type.'" data-field-btn' );
        }
        post();
    post();

    // Form View
    pre( '', 'aio_form_view' );
        pre( '', 'row' );
        post();
    post();

    // Form View
    $fr = '[data-{{type}}-template] class="col-12"';
    pre( '', 'aio_templates', 'div', 'style="display: none"' );
        $f->text('text','Text','','','',str_replace('{{type}}','text',$fr));
        $f->textarea('textarea','Textarea','','','',str_replace('{{type}}','textarea',$fr));
        $f->select('select','Select','',[],'','',str_replace('{{type}}','select',$fr));
        $f->radios('radios','Radios',[],'','',0,str_replace('{{type}}','radios',$fr));
        $f->checkboxes('checkboxes','Checkboxes',[],'','',0,str_replace('{{type}}','checkboxes',$fr));
        $f->radios('slide','Slide',[],'','',0,str_replace('{{type}}','slide',$fr));
        $f->input('password','password','Password','','','',str_replace('{{type}}','password',$fr));
        $f->input('text','color','Color','','','',str_replace('{{type}}','color',$fr));
        $f->input('date','date','Date','','',str_replace('{{type}}','date',$fr));
        //$f->input('datetime','datetime','DateTime','','','',str_replace('{{type}}','datetime',$fr));
        $f->input('time','time','Time','','','',str_replace('{{type}}','time',$fr));
        $f->input('range','range','Range','','','',str_replace('{{type}}','range',$fr));
        $f->input('email','email','Email','','','',str_replace('{{type}}','email',$fr));
        $f->input('text','code','Code','','','',str_replace('{{type}}','code',$fr));
        $f->input('file','file','File','','','',str_replace('{{type}}','file',$fr));
        $f->input('file','files','Files','','','',str_replace('{{type}}','files',$fr).' multiple');
        $f->input('text','hidden','Hidden','','','',str_replace('{{type}}','hidden',$fr));
        $f->input('number','number','Number','','','',str_replace('{{type}}','number',$fr));
        //$f->input('text','url','URL','','','',str_replace('{{type}}','url',$fr));
        $f->input('hidden','richtext','Richtext Editor','','','',str_replace('{{type}}','richtext',$fr));
        $f->select('country','Country','',[],'','',str_replace('{{type}}','country',$fr));
        $f->input('text','map','Google Map','','','',str_replace('{{type}}','map',$fr));
    post();

    // Field Properties
    pre( '', 'aio_field_props' );

        h4( 'Basic Options', 1 );
        $f->text('l','Title / Label','Title / Label','','data-form-prop');
        $f->text('p','Placeholder','Placeholder','','data-form-prop');
        $f->text('v','Default Value','Default Value','','data-form-prop');
        $f->text('i','Identity','Identity','','data-form-prop data-no-space');

        h4( 'Advanced Options', 1 );
        $bootstrap_cols = [];
        for ($i = 12; $i > 0; $i--) {
            $bootstrap_cols[ $i ] = round( ( $i / 12 ) * 100 ) . '% (col-'.$i.')';
        }
        $f->text('a','Attributes','Attributes','','data-form-prop');
        _r();
            $f->text('min','Min','Min','','data-form-prop',6);
            $f->text('max','Max','Max','','data-form-prop',6);
            $f->text('minlength','Min Length','Min Length','','data-form-prop',6);
            $f->text('maxlength','Max Length','Max Length','','data-form-prop',6);
            $f->slide('r','Required','Required','',0,'m','data-form-prop',6);
            $f->slide('tr','Translate','Translate','',1,'m','data-form-prop',6);
            $f->select2('c','Bootstrap Column','Bootstrap Column',$bootstrap_cols,12,'data-form-prop',12,1);
        r_();

        pre( '', 'custom_props' );
            h4( 'Custom', 1 );
        post();

        //h4( 'Logics', 1 );
        //$logics = [ '=' => '= to', '!=' => '!= to', '<' => '< than', '<=' => '<= to', '>' => '> than', '>=' => '>= to', 'has' => 'contains' ];
        //el( 'button', 'trash', '', _el( 'i', 'mat-ico', '', 'trash' ) );
    post();
post();
get_style('form_builder');
get_script('form_builder');