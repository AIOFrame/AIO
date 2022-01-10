<?php

class FORM {

    /**
     * Renders &lt;select&gt; options
     * @param array $options Indexed or Associative Array of options
     * @param string|null $selected Selected option or options separated by comma
     * @param string $placeholder Placeholder text
     * @param bool $keyed Yes if option value should be array key
     * @param bool $translate Translate the option text or not
     */
    function select_options( array $options = [], string|null $selected = '', string $placeholder = '', bool $keyed = false, bool $translate = true ) {
        $s = $selected;
        $placeholder = $translate ? T($placeholder) : $placeholder;
        if( $placeholder !== '' ){
            echo empty($s) ? '<option disabled selected>'.$placeholder.'</option>' : '<option disabled>'.$placeholder.'</option>';
        }
        foreach ( $options as $k => $t ) {
            $t = $translate ? T($t) : $t;
            $k = $keyed ? $k : $t;
            if( is_array( $s ) && in_array( $k, $s ) ) { $sel = 'selected'; } else if( $k == $s ) { $sel = 'selected'; } else { $sel = ''; }
            if( $t == 'select2_placeholder' ) { echo '<option></option>'; continue; }
            echo '<option value="' . $k . '" ' . $sel . '>' . $t . '</option>';
        }
        //!empty($sel) ? elog($s) : '';
    }

    /**
     * Renders &lt;select&gt; element and <option>s inside
     * @param string|array $identity ID and name of the element or array of [id, name]
     * @param string $label Label for the &lt;label&gt;
     * @param string $placeholder Placeholder text
     * @param array $options Indexed or Associative Array of options
     * @param string|null $selected Selected option or options separated by comma
     * @param string $attr Attributes like class or data tags
     * @param string $pre String to add before &lt;select&gt;. Tip: 6 will wrap with bootstrap col-lg-6
     * @param string $post String to add after &lt;/select&gt;
     * @param bool $keyed Yes if option value should be array key
     * @param bool $translate Translate the option text or not
     */

    function select( string|array $identity = '', string $label = '', string $placeholder = '', array $options = [], string|null $selected = '', string $attr = '', string $pre = '', string $post = '', bool $keyed = false, bool $translate = true ) {
        if( is_numeric( $pre ) ){
            $pre =  $pre == 0 ? '<div class="col">' : '<div class="col-12 col-lg-'.$pre.'">';
            $post = '</select></div>';
        }
        $post = empty( $post ) ? '</select>' : '</select>' . $post;
        $at = $attr !== '' ? ' '.$attr : '';
        $id = is_array($identity) ? $identity[0] : $identity;
        $name = is_array($identity) ? $identity[1] : $identity;
        echo $pre;
        echo !empty( $label ) ? '<label for="'.$id.'">'.T($label).'</label>' : '';
        $ph = !empty( $placeholder ) ? ' placeholder="'.$placeholder.'" data-placeholder="'.$placeholder.'"' : '';
        echo '<select name="'.$name.'" data-key="'.$name.'" id="'.$id.'"'.$at.$ph.'>';
        //if( str_contains( $attr, 'select2' ) ) {
        if( strpos( $attr, 'select2' ) !== false ) {
            $placeholder = '';
            $options = [ '' => 'select2_placeholder' ] + $options;
            //array_unshift( $options, 'select2_placeholder' );
        }
        //$placeholder = strpos( $attr, 'select2') !== false ? '' : $placeholder;
        $this->select_options( $options, $selected, $placeholder, $keyed, $translate );
        echo $post;
    }

    /**
     * Renders an &lt;input&gt; Element
     * @param string $type Input type, Ex: 'text','radio','checkbox','textarea'
     * @param string|array $identity ID and name of the element
     * @param string $label Label for the &lt;label&gt;
     * @param string $placeholder Placeholder text
     * @param string|null $value Value of the input if any
     * @param string $attrs Attributes like class or data tags
     * @param string $pre String to wrap before start of &lt;input&gt;. Tip: 6 will wrap with bootstrap col-lg-6
     * @param string $post End string to wrap after /&gt;
     * @param string $name Optional if different name is needed
     */
    function input( string $type, string|array $identity, string $label = '', string $placeholder = '', string|null $value = '', string $attrs = '', string $pre = '', string $post = '', string $name = '' ){
        $type = $type == '' ? 'text' : $type;
        if( is_numeric( $pre ) ){
            $pre =  $pre == 0 ? '<div class="col">' : '<div class="col-12 col-lg-'.$pre.'">';
            $post = '</div>';
        }
        $ph = $placeholder !== '' ? ' placeholder="'.$placeholder.'"' : '';
        $at = $attrs !== '' ? ' '.$attrs : '';
        $id = is_array($identity) ? $identity[0] : $identity;
        $name = is_array($identity) ? $identity[1] : $identity;
        if( $type == 'textarea' ) {
            $va = $value !== '' ? $value : '';
        } else {
            $va = $value !== '' ? ' value="'.$value.'"' : '';
        }
        $n = $name !== '' ? $name : $id;
        switch( $type ){
            case 'textarea':
                $input = '<textarea id="'.$id.'" data-key="'.$n.'" name="'.$n.'" '.$ph.$at.'>'.$va.'</textarea>';
                break;
            case 'slide':
            case 'toggle':
                $input = '<div><input type="hidden" id="'.$id.'" data-key="'.$n.'" name="'.$n.'" '.$at.$ph.$va.'>';
                $ch = $value == 'true' || $value == '1' ? 'checked' : '';
                $input .= '<input type="checkbox" data-check="#'.$id.'" class="slide m" '.$ch.'></div>';
                break;
            case 'select':
                $this->select($id,$label,$placeholder,[],$value,$attrs,$pre,$post,0,1);
                break;
            default:
                $input = '<input type="'.$type.'" autocapitalize="none" id="'.$id.'" data-key="'.$n.'" name="'.$n.'" '.$at.$ph.$va.'>';
                break;
        }
        echo $pre;
        echo !empty( $label ) ? '<label for="'.$id.'">'.T($label).'</label>' : '';
        echo $input.$post;
    }

    /**
     * Renders multiple &lt;input&gt; elements
     * @param string $type Input type, Ex: 'text','radio','checkbox','textarea'
     * @param array $array Array of sub array of ['id','label','placeholder','value','attr','pre','post'] of inputs
     * @param string $attrs Attributes like class or data applicable to all
     * @param string $pre String to wrap before start of &lt;input&gt;. Tip: 6 will wrap with bootstrap col-lg-6
     * @param string $post End string to wrap after /&gt;
     */
    function inputs( string $type = 'text', array $array = [], string $attrs = '', string $pre = '', string $post = '' ){
        if( !empty( $array ) && is_array( $array ) ){
            foreach( $array as $id ){
                $slug = isset($id[0]) && $id[0] !== '' ? $id[0] : '';
                $label = isset($id[1]) && $id[1] !== '' ? $id[1] : '';
                $place = isset($id[2]) && $id[2] !== '' ? $id[2] : '';
                $value = isset($id[3]) && $id[3] !== '' ? $id[3] : '';
                $attr = isset($id[4]) && $id[4] !== '' ? $id[4] : '';
                $ipre = isset($id[5]) && $id[5] !== '' ? $id[5] : $pre;
                $ipost = isset( $id[6] ) && $id[6] !== '' ? $id[6] : $post;
                $this->input( $type, $slug, $label, $place, $value, $attr.' '.$attrs, $ipre, $ipost );
            }
        }
    }

    /**
     * Renders &lt;input type="text"> element
     * Basically the text input function with $type = 'text' param
     * @param string|array $id ID and name of the element
     * @param string $label Label for the &lt;label&gt;
     * @param string $placeholder Placeholder text
     * @param string|null $value Value of the input if any
     * @param string $attrs Attributes like class or data tags
     * @param string $pre String to wrap before start of &lt;input&gt;. Tip: 6 will wrap with bootstrap col-lg-6
     * @param string $post End string to wrap after /&gt;
     */
    function text( string|array $id, string $label = '', string $placeholder = '', string|null $value = '', string $attrs = '', string $pre = '', string $post = '' ) {
        $this->input( 'text', $id, $label, $placeholder, $value, $attrs, $pre, $post );
    }

    /**
     * Renders &lt;input type="textarea"&gt; element
     * Basically the text input function with $type = 'textarea' param
     * @param string|array $id ID and name of the element
     * @param string $label Label for the &lt;label&gt;
     * @param string $placeholder Placeholder text
     * @param string|null $value Value of the input if any
     * @param string $attrs Attributes like class or data tags
     * @param string $pre String to wrap before start of &lt;input&gt;. Tip: 6 will wrap with bootstrap col-lg-6
     * @param string $post End string to wrap after /&gt;
     */
    function textarea( string|array $id, string $label = '', string $placeholder = '', string|null $value = '', string $attrs = '', string $pre = '', string $post = '' ) {
        $this->input( 'textarea', $id, $label, $placeholder, $value, $attrs, $pre, $post );
    }

    /**
     * Renders multiple &lt;input type="text"&gt; elements
     * @param array $array Array of an array of ['id','label','placeholder','value','attr']
     * @param string $attrs Attributes like class or data applicable to all
     * @param string $pre String to wrap before start of input. Tip: 6 will wrap with bootstrap col-lg-6
     * @param string $post End string to wrap after /&gt;
     */
    function texts( array $array, string $attrs = '', string $pre = '', string $post = '' ){
        if( is_array( $array ) ){
            if( is_assoc( $array ) ){
                foreach( $array as $k => $v ){
                    $this->input( 'text', $k, $v, $attrs, $pre, $post );
                }
            } else {
                $this->inputs( 'text', $array, $attrs, $pre, $post );
            }
        }
    }

    /**
     * Renders &lt;input type="radio"&gt; or &lt;input type="checkbox"&gt; elements of same name
     * @param string $type Type either 'radio' or 'checkbox'
     * @param string|array $identity Name of the input elements
     * @param array $values Array of values
     * @param string|array $checked Checked value or values separated by (,) comma
     * @param string $attr Attributes like class or data tags
     * @param bool $label_first If label should be before input element
     * @param string $pre String to wrap before start of &lt;input&gt;. Tip: 6 will wrap with bootstrap col-lg-6
     * @param string $post End string to wrap after /&gt;
     */
    function render_options( string $type = 'radio', string $label = '', string|array $identity = '', array $values = [], string|array $checked = '', string $attr = '', bool $label_first = false, string $pre = '', string $post = '', string $inputs_wrap = '', string $inputs_pre = '', string $inputs_post = '' ) {
        if( is_array( $values ) ) {
            $type = $type == 'radio' ? 'type="radio"' : 'type="checkbox"';
            $id = is_array($identity) ? $identity[0] : $identity;
            $name = is_array($identity) ? $identity[1] : $identity;
            $valued = is_assoc( $values ); $x = 0;
            if( is_numeric( $pre ) ){
                $pre = $pre == 0 ? '<div class="col">' : '<div class="col-12 col-lg-'.$pre.'">';
                $post = '</div>';
            }
            $wrap_inputs_pre = !empty( $inputs_wrap ) ? '<div class="'.$inputs_wrap.'">' : '';
            $wrap_inputs_post = !empty( $inputs_wrap ) ? '</div>' : '';
            if( is_numeric( $inputs_pre ) ) {
                $inputs_pre = $inputs_pre == 0 ? '<div class="col">' : '<div class="col-12 col-lg-'.$inputs_pre.'">';
                $inputs_post = '</div>';
                $wrap_inputs_pre = '<div class="row '.$inputs_wrap.'">';
                $wrap_inputs_post = '</div>';
            }
            $key = 'data-key="'.$name.'"';
            if( strpos( $attr, 'data-array') !== false ) {
                $name = $name . '[]';
            }
            $uq = rand(1,999);
            echo $pre;
            echo !empty($label) ? '<label class="db">'.T($label).'</label>' : '';
            echo $wrap_inputs_pre;
            if( is_assoc( $values ) ) {
                foreach ($values as $val => $title) {
                    $tip = $title !== '' ? 'title="'.$title.'"' : '';
                    $k = $valued ? $val . $x . '_' . $uq : str_replace(' ', '', $name) . $x;
                    $value = $valued ? $val : $title;
                    $checked = is_array( $checked ) ? $checked : explode(',',$checked);
                    $c = in_array( $value, $checked ) ? 'checked' : '';
                    if ($label_first) {
                        echo $inputs_pre . '<label for="' . $k . '" '.$tip.' class="' . $name . '_' . $value . '">' . $title . '</label><input ' . $attr . ' ' . $type . ' name="' . $name . '" '.$key.' id="' . $k . '" value="' . $value . '" '. $c .' >' . $inputs_post;
                    } else {
                        echo $inputs_pre . '<input ' . $attr . ' ' . $type . ' name="' . $name . '" '.$key.' id="' . $k . '" value="' . $value . '" '. $c .' ><label for="' . $k . '" '.$tip.' class="' . $name . '_' . $value . '">' . $title . '</label>' . $inputs_post;
                    }
                    $x++;
                }
            } else {
                foreach ($values as $val) {
                    $k = $valued ? $val . $x . '_' . $uq : str_replace(' ', '', $name) . $x;
                    $title = is_array($val) && !empty($val[1]) ? $val[1] : $val;
                    $tip = $title !== '' ? 'title="'.$title.'"' : '';
                    $value = is_array($val) ? $val[0] : $val;
                    $data = is_array($val) && !empty($val[2]) ? $val[2] : '';
                    $checked = is_array( $checked ) ? $checked : explode(',',$checked);
                    $c = in_array( $value, $checked ) ? 'checked' : '';

                    if ($label_first) {
                        echo $inputs_pre . '<label for="' . $k . '" '.$tip.' class="' . $name . '_' . $value . '">' . $title . '</label><input ' . $attr . ' ' . $type . ' ' . $data . ' data-key="'.$name.'" name="' . $name . '" id="' . $k . '" value="' . $value . '" '.$c.'>' . $inputs_post;
                    } else {
                        echo $inputs_pre . '<input ' . $attr . ' ' . $type . ' name="' . $name . '" data-key="'.$name.'" id="' . $k . '" value="' . $value . '" ' . $data . ' '.$c.'><label for="' . $k . '" '.$tip.' class="' . $name . '_' . $value . '">' . $title . '</label>' . $inputs_post;
                    }
                    $x++;
                }
            }
            echo $wrap_inputs_post;
            echo $post;
            /* if (is_assoc( $d )) {
                foreach ($d as $k => $t) {
                    echo $before . '<label for="cb_' . $k . '" ><input ' . $attrs . '  id="cb_' . $k . '" type="' . $tp . '" value="' . $k . '" ' . (in_array($k, $s) ? "checked" : "") . '>' . $t . '</label>' . $after;
                }
            } else {
                foreach ($d as $t) {
                    echo $before . '<label for="cb_' . str_replace(' ', '_', $t) . '" ><input' . $attrs . 'id="cb_' . str_replace(' ', '_', $t) . '" type="' . $tp . '" value="' . $t . '" ' . (in_array($t, $t) ? "checked" : "") . '>' . $t . '</label>' . $after;
                }
            } */
        }
    }

    /**
     * Renders &lt;input type="radio"&gt; elements
     * @param string|array $name Name of the input elements
     * @param array $values Array of values
     * @param string $checked Checked value or values separated by (,) comma
     * @param string $attr Attributes like class or data tags
     * @param bool $label_first If label should be before input element
     * @param string $pre String to wrap before start of &lt;input&gt;. Tip: 6 will wrap with bootstrap col-lg-6
     * @param string $post End string to wrap after /&gt;
     */
    function radios( string|array $name, string $label = '', array $values = [], string $checked = '', string $attr = '', bool $label_first = false, string $pre = '', string $post = '', string $inputs_wrap = '', string $inputs_pre = '', string $inputs_post = '' ){
        $this->render_options( 'radio', $label, $name, $values, $checked, $attr, $label_first, $pre, $post, $inputs_wrap, $inputs_pre, $inputs_post );
    }

    /**
     * Renders &lt;input type="checkbox"&gt; elements
     * @param string|array $name Name of the input elements
     * @param array $values Array of values
     * @param array|string $checked Checked value or values separated by (,) comma
     * @param string $attr Attributes like class or data tags
     * @param bool $label_first If label should be before input element
     * @param string $pre String to wrap before start of &lt;input&gt;. Tip: 6 will wrap with bootstrap col-lg-6
     * @param string $post End string to wrap after /&gt;
     */
    function checkboxes( string|array $name, string $label = '', array $values = [], string|array $checked = '', string $attr = '', bool $label_first = false, string $pre = '', string $post = '', string $inputs_wrap = '', string $inputs_pre = '', string $inputs_post = '' ){
        $this->render_options( 'checkbox', $label, $name, $values, $checked, $attr, $label_first, $pre, $post, $inputs_wrap, $inputs_pre, $inputs_post );
    }

    /**
     * Renders File Uploading Elements
     * @param string|array $identity ID and name of the element
     * @param string $label Text for the &lt;label&gt;
     * @param string $button_label Text for the &lt;button&gt;
     * @param string $value Value of the input if any
     * @param bool $multiple Upload single or multiple files
     * @param bool $show_history Show previously uploaded files
     * @param string $button_class Class for upload button
     * @param string $attrs Attributes like class or data tags
     * @param string $extensions Permitted file upload extensions separated by (,) comma Ex: jpg,svg
     * @param string $size Permitted file size in Mb Ex: 10
     * @param bool $deletable Uploaded files are deletable or not
     * @param string $path Path to upload, will be encrypted on render
     * @param string $pre String to wrap before start
     * @param string $post End string to wrap after /&gt;
     */
    function upload( string|array $identity, string $label, string $button_label = 'Upload', string $value = '', bool $multiple = false, bool $show_history = false, string $button_class = '', string $attrs = '', string $extensions = '', string $size = '', bool $deletable = false, string $path = '', string $pre = '', string $post = '' ) {
        $id = is_array($identity) ? $identity[0] : $identity;
        $name = is_array($identity) ? $identity[1] : $identity;
        if( is_numeric( $pre ) ){
            $pre = $pre == 0 ? '<div class="upload_set col">' : '<div class="upload_set col-12 col-lg-'.$pre.'">';
            $post = '</div>';
        }
        $sh = $show_history ? ' data-history' : '';
        $ext = $extensions !== '' ? ' data-exts="'.$extensions.'"' : '';
        $sz = $size !== '' ? ' data-size="'.$size.'"' : '';
        $del = $deletable ? ' data-delete' : '';
        $cry = Crypto::initiate();
        $pat = $path !== '' ? ' data-path="'.$cry->encrypt( $path ).'"' : '';
        $type = $multiple ? 'files' : 'file';
        $mul = $multiple ? 'data-files' : 'data-file';
        echo $pre.'<label for="'.$id.'">'.T($label).'</label><button type="button" class="aio_upload '.$button_class.'" data-url="#'.$id.'" onclick="file_upload(this)" '.$sh.$ext.$sz.$mul.$del.$pat.'>'.T($button_label).'</button><input id="'.$id.'" name="'.$name.'" data-key="'.$name.'" type="text" data-'.$type.' value="'.$value.'" '.$attrs.'>'.$post;
    }

    /**
     * Returns array into JSON string
     * @param array $data Database rows as array
     * @param string $remove If needed to remove, provide keys separated by ,
     * @return string
     * @author Shaikh <hey@shaikh.dev>
     */
    function _editable_data( $data = [], $remove = '' ): string {
        $final = [];
        $remove = explode( ',', $remove );
        foreach( $data as $k => $v ){
            if( is_numeric( $k ) )
                continue;
            $k = strpos( $k, '_') !== false ? ltrim( strstr($k,'_'), '_' ) : $k;
            if( $k == 'id' ) {
                $cry = CRYPTO::initiate();
                $final[ $k ] = $cry->encrypt( $v );
            } else if( !in_array( $k, $remove ) ){
                $final[ $k ] = $v;
            }
        }
        return json_encode( $final );
    }

    /**
     * Echo array into JSON string
     * @param array $data Database rows as array
     * @param string $remove If needed to remove, provide keys separated by ,
     * @author Shaikh <hey@shaikh.dev>
     */
    function editable_data( $data = [], $remove = '' ) {
        echo $this->_editable_data( $data, $remove );
    }

    function delete_data( string $table, string $logic ) {
        $c = Crypto::initiate();
        echo ' onclick="trash_data(\''.$c->encrypt('trash_data_ajax').'\',\''.$c->encrypt( $table ).'\',\''.$c->encrypt( $logic ).'\')"';
    }

    /**
     * Renders HTML
     * @param string $target Database Name
     * @param string $data Data attribute to fetch data
     * @param string $pre Pre Wrap String for Tables
     * @param int $notify Notification Time in Seconds
     * @param int $reload Reload in Seconds
     * @param array $hidden Hidden data for Database
     * @param string $success_text Text to notify upon successfully storing data
     */
    function process_params( string $target = '', string $data = '', string $pre = '', int $notify = 0, int $reload = 0, array $hidden = [], string $success_text = '' ) {
        $c = Crypto::initiate();
        $t = !empty( $target ) ? ' data-t="'.$c->encrypt( $target ).'"' : '';
        $nt = $notify > 0 ? ' data-notify="'.$notify.'"' : '';
        $rl = $reload > 0 ? ' data-reload="'.$reload.'"' : '';
        $d = !empty( $data ) ? ' data-data="'.$data.'"' : '';
        $p = !empty( $pre ) ? ' data-pre="'.$pre.'"' : '';
        $h = !empty( $hidden ) ? ' data-h="'.$c->encrypt_array( $hidden ).'"' : '';
        $st = !empty( $success_text ) ? ' data-success="'.T($success_text).'"' : '';
        echo $t.$nt.$rl.$d.$p.$h.$st;
    }

    /**
     * Renders HTML to process data
     * @param string $text Button text
     * @param string $class Button class
     * @param string $attr Additional attributes to button
     * @param string $action Default AJAX Action
     * @param string|int $pre Pre Wrap HTML or Bootstrap Column
     * @param string|int $post Post Wrap HTML
     */
    function process_button_html( string $text = '', string $class = '', string $attr = '', string $action = '', string|int $pre = '', int|string $post = '' ) {
        if( is_numeric( $pre ) ){
            $pre = $pre == 0 ? '<div class="col">' : '<div class="col-12 col-lg-'.$pre.'">';
        } else {
            $pre = !empty( $pre ) ? '<div class="'.$pre.'">' : '';
        }
        $post = !empty( $post ) ? $post : ( !empty( $pre ) ? '</div>' : '' );
        $c = Crypto::initiate();
        $action = empty( $action ) ? 'process_data_ajax' : $action;
        $a = 'data-action="'.$c->encrypt($action).'"';
        echo $pre.'<button onclick="process_data(this)" '.$a.' class="'.$class.'" '.$attr.'>'.T( $text ).'</button>'.$post;
    }

    /**
     * Renders HTML to open modal to edit data
     * @param string $element Modal element to open
     * @param array $array Data JSON array
     * @param string $html HTML either button or div or i
     * @param string $text Text to display
     * @param string $class Class
     * @param string $attr Additional attributes
     */
    function edit_button_html( string $element = '.modal', array $array = [], string $html = 'div', string $text = '', string $class = '', string $attr = '' ) {
        $c = Crypto::initiate();
        echo '<'.$html.' onclick="edit_data(this,\''.$element.'\')" data-data=\''.$this->_editable_data($array).'\' class="'.$class.'" '.$attr.'>'.T( $text ).'</'.$html.'>';
    }

    /**
     * Renders HTML to delete record from database
     * @param string $table Table name
     * @param string $logic Where column value equals to
     * @param string $html HTML either button or div or i
     * @param string $text Text to display
     * @param string $class Class
     * @param string $attr Additional attributes
     */
    function trash_html( string $table, string $logic, string $html = 'div', string $text = '', string $class = '', string $attr = '' ) {
        $c = Crypto::initiate();
        echo '<'.$html.' onclick="trash_data(\''.$c->encrypt('trash_data_ajax').'\',\''.$c->encrypt($table).'\',\''.$c->encrypt($logic).'\')" class="'.$class.'" '.$attr.'>'.T( $text ).'</'.$html.'>';
    }

    /**
     * String validator
     * @param string $type Type of validation. email
     * @param string $string
     * @param string $logic
     * @return bool
     */
    // TODO: Build this into an advanced validator
    function validate( string $type = 'email', string $string = '', string $logic = '' ): bool {
        if( $type = 'email' ) {
            $at = strpos($string, '@');
            $dot = strpos($string, '.');
            return ($at !== false && $dot !== false && $at > $dot);
        } else {
            return 0;
        }
    }

    /**
     * Renders Filters HTML
     * @param array $filters
     * @param string $clear_url Page path excluding APPURL Ex: user/payments
     */
    function filters( array $filters = [], string $clear_url = '' ) {
        $clear_url = !empty( $clear_url ) ? APPURL . $clear_url : APPURL . PAGEPATH;
        echo '<div class="auto_filters"><form class="row">';
        foreach( $filters as $f ) {
            $type = $f[0] ??= 'text';
            $id = $f[1] ??= '';
            $label = $f[2] ??= '';
            $place = $f[3] ??= $f[2];
            $val = $_POST[$id] ?? ($f[4] ?? '');
            $attrs = $f[5] ??= '';
            $pre = $f[6] ??= '';
            if( $type == 'select' ) {
                $options = $f[4] ??= [];
                $value = $_POST[ $id ] ?? ($_GET[ $id ] ?? '');
                $post = $f[7] ??= '';
                $keyed = $f[8] ??= '';
                $this->select( $id, $label, $place, $options, $value, $attrs, $pre, $post, $keyed );
            } else {
                $this->input( $type, $id, $label, $place, $val, $attrs, $pre );
            }
        }
        echo '<div class="col"><button type="submit" class="filter">'.T('Filter').'</button></div>';
        echo '<div class="col"><a href="'.$clear_url.'" class="clear">'.T('Clear').'</a></div>';
        echo '</form></div>';
    }
}