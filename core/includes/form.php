<?php

class FORM {

    /**
     * Returns array into JSON string
     * @param array $data Database rows as array
     * @param string $remove If needed to remove, provide keys separated by ,
     * @return string
     * @author Shaikh <hey@shaikh.dev>
     */
    function _editable_data( $data = [], $remove = '' ): string {
        // TODO: Check the issue with encrypting array without db_
        $final = [];
        $remove = explode( ',', $remove );
        foreach( $data as $k => $v ){
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

    /**
     * Renders <select> options
     * @param array $options Indexed or Associative Array of options
     * @param string $selected Selected option or options separated by comma
     * @param string $placeholder Placeholder text
     * @param bool $translate Translate the option text or not
     */
    function select_options( array $options = [], string $selected = '', string $placeholder = '', bool $translate = true ) {
        $d = $options;
        $s = $selected;
        $placeholder = $translate ? T($placeholder) : $placeholder;
        if( $placeholder !== '' ){
            echo empty($s) ? '<option disabled selected>'.$placeholder.'</option>' : '<option disabled>'.$placeholder.'</option>';
        }
        if( is_array($d) ){
            // TODO: The following logic can be simplified
            if (is_assoc($d)) {
                foreach ($d as $k => $t) {
                    $t = $translate ? T($t) : $t;
                    if( is_array( $s ) && in_array( $k, $s ) ) { $sel = 'selected'; } else if( $k == $s ) { $sel = 'selected'; } else { $sel = ''; }
                    echo '<option value="' . $k . '" ' . $sel . '>' . $t . '</option>';
                }
                !empty($sel) ? elog($s) : '';
            } else {
                foreach ($d as $t) {
                    $t = $translate ? T($t) : $t;
                    if( is_array( $s ) && in_array( $t, $s ) ) { $sel = 'selected'; } else if( $t == $s ) { $sel = 'selected'; } else { $sel = ''; }
                    echo '<option value="' . $t . '" ' . $sel . '>' . $t . '</option>';
                }

            }
        } else if( is_numeric( $d ) ){
            for($x=0;$x<=$d;$x++){
                $t = $translate ? T($x) : $x;
                echo '<option value="' . $x . '" ' . ($x == $s ? "selected" : "") . '>' . $t . '</option>';
            }
        }
    }

    /**
     * Renders <select> element and <option>s inside
     * @param string $id ID and name of the element
     * @param string $label Label for the <label>
     * @param string $placeholder Placeholder text
     * @param array $options Indexed or Associative Array of options
     * @param string $selected Selected option or options separated by comma
     * @param string $attr Attributes like class or data tags
     * @param string $pre String to add before <select>. Tip: 6 will wrap with bootstrap col-lg-6
     * @param string $post String to add after </select>
     * @param bool $translate Translate the option text or not
     */
    function select( string $id = '', string $label = '', string $placeholder = '', array $options = [], string $selected = '', string $attr = '', string $pre = '', string $post = '', bool $translate = true ) {
        if( is_numeric( $pre ) ){
            $pre =  $pre == 0 ? '<div class="col">' : '<div class="col-12 col-lg-'.$pre.'">';
            $post = '</select></div>';
        }
        $post = empty( $post ) ? '</select>' : $post;
        $at = $attr !== '' ? ' '.$attr : '';
        echo $pre;
        echo !empty( $label ) ? '<label for="'.$id.'">'.T($label).'</label>' : '';
        $ph = !empty( $placeholder ) ? ' placeholder="'.$placeholder.'" data-placeholder="'.$placeholder.'"' : '';
        echo '<select name="'.$id.'" id="'.$id.'"'.$at.$ph.'">';
        if( str_contains( $attr, 'select2' ) ) {
            $placeholder = '';
            array_unshift( $options, '' );
        }
        //$placeholder = strpos( $attr, 'select2') !== false ? '' : $placeholder;
        $this->select_options( $options, $selected, $placeholder, $translate );
        echo $post;
    }

    /**
     * Renders an <input> Element
     * @param string $type Input type, Ex: 'text','radio','checkbox','textarea'
     * @param string $id ID and name of the element
     * @param string $label Label for the <label>
     * @param string $placeholder Placeholder text
     * @param string $value Value of the input if any
     * @param string $attrs Attributes like class or data tags
     * @param string $pre String to wrap before start of <input>. Tip: 6 will wrap with bootstrap col-lg-6
     * @param string $post End string to wrap after />
     * @param string $name Optional if different name is needed
     */
    function input( string $type, string $id, string $label, string $placeholder = '', string $value = '', string $attrs = '', string $pre = '', string $post = '', string $name = '' ){
        $type = $type == '' ? 'text' : $type;
        if( is_numeric( $pre ) ){
            $pre =  $pre == 0 ? '<div class="col">' : '<div class="col-12 col-lg-'.$pre.'">';
            $post = '</div>';
        }
        $ph = $placeholder !== '' ? ' placeholder="'.$placeholder.'"' : '';
        $at = $attrs !== '' ? ' '.$attrs : '';
        if( $type == 'textarea' ) {
            $va = $value !== '' ? $value : '';
        } else {
            $va = $value !== '' ? ' value="'.$value.'"' : '';
        }
        $n = $name !== '' ? $name : $id;
        switch( $type ){
            case 'textarea':
                $input = '<textarea id="'.$id.'" name="'.$n.'" '.$ph.$at.'>'.$va.'</textarea>';
                break;
            case 'slide':
            case 'toggle':
                $input = '<div><input type="hidden" id="'.$id.'" name="'.$n.'" '.$at.$ph.$va.'>';
                $ch = $value == 'true' || $value == '1' ? 'checked' : '';
                $input .= '<input type="checkbox" data-check="#'.$id.'" class="slide m" '.$ch.'></div>';
                break;
            default:
                $input = '<input type="'.$type.'" autocapitalize="none" id="'.$id.'" name="'.$n.'" '.$at.$ph.$va.'>';
                break;
        }
        echo $pre;
        echo !empty( $label ) ? '<label for="'.$id.'">'.T($label).'</label>' : '';
        echo $input.$post;
    }

    /**
     * Renders multiple <input> elements
     * @param string $type Input type, Ex: 'text','radio','checkbox','textarea'
     * @param array $array Array of array of ['id','label','placeholder','value','attr'] of inputs
     * @param string $attrs Attributes like class or data applicable to all
     * @param string $pre String to wrap before start of <input>. Tip: 6 will wrap with bootstrap col-lg-6
     * @param string $post End string to wrap after />
     */
    function inputs( string $type = 'text', array $array = [], string $attrs = '', string $pre = '', string $post = '' ){
        if( !empty( $array ) && is_array( $array ) ){
            foreach( $array as $id ){
                $slug = isset($id[0]) && $id[0] !== '' ? $id[0] : '';
                $label = isset($id[1]) && $id[1] !== '' ? $id[1] : '';
                $place = isset($id[2]) && $id[2] !== '' ? $id[2] : '';
                $value = isset($id[3]) && $id[3] !== '' ? $id[3] : '';
                $attr = isset($id[4]) && $id[4] !== '' ? $id[4] : '';
                $this->input( $type, $slug, $label, $place, $value, $attr.' '.$attrs, $pre, $post );
            }
        }
    }

    /**
     * Renders <input type="text"> element
     * Basically the text input function with $type = 'text' param
     * @param string $id ID and name of the element
     * @param string $label Label for the <label>
     * @param string $placeholder Placeholder text
     * @param string $value Value of the input if any
     * @param string $attrs Attributes like class or data tags
     * @param string $pre String to wrap before start of <input>. Tip: 6 will wrap with bootstrap col-lg-6
     * @param string $post End string to wrap after />
     */
    function text( string $id, string $label, string $placeholder = '', string $value = '', string $attrs = '', string $pre = '', string $post = '' ) {
        $this->input( 'text', $id, $label, $placeholder, $value, $attrs, $pre, $post );
    }

    /**
     * Renders multiple <input type="text"> elements
     * @param array $array Array of array of ['id','label','placeholder','value','attr']
     * @param string $attrs Attributes like class or data applicable to all
     * @param string $pre String to wrap before start of <input>. Tip: 6 will wrap with bootstrap col-lg-6
     * @param string $post End string to wrap after />
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
     * Renders <input type="radio"> or <input type="checkbox"> elements of same name
     * @param string $type Type either 'radio' or 'checkbox'
     * @param string $name Name of the input elements
     * @param array $values Array of values
     * @param string $checked Checked value or values separated by (,) comma
     * @param string $attr Attributes like class or data tags
     * @param bool $label_first If label should be before input element
     * @param string $pre String to wrap before start of <input>. Tip: 6 will wrap with bootstrap col-lg-6
     * @param string $post End string to wrap after />
     */
    function render_options( $type = 'radio', $name = '', $values = [], $checked = '', $attr = '', bool $label_first = false, string $pre = '', string $post = '' ) {
        if( is_array( $values ) ) {
            $type = $type == 'radio' ? 'type="radio"' : 'type="checkbox"';
            $valued = is_assoc( $values ); $x = 0;
            if( is_numeric( $pre ) ){
                $pre = $pre == 0 ? '<div class="col">' : '<div class="col-12 col-lg-'.$pre.'">';
                $post = '</div>';
            }
            $uq = rand(1,999);
            if( is_assoc( $values ) ) {
                foreach ($values as $val => $title) {
                    $k = $valued ? $val . $x . '_' . $uq : str_replace(' ', '', $name) . $x;
                    $value = $valued ? $val : $title;
                    $checked = is_array( $checked ) ? $checked : explode(',',$checked);
                    $c = in_array( $value, $checked ) ? 'checked' : '';
                    if ($label_first) {
                        echo $pre . '<label for="' . $k . '">' . $title . '</label><input ' . $attr . ' ' . $type . ' name="' . $name . '" data-key="'.$name.'" id="' . $k . '" value="' . $value . '" '. $c .' >' . $post;
                    } else {
                        echo $pre . '<input ' . $attr . ' ' . $type . ' name="' . $name . '" data-key="'.$name.'" id="' . $k . '" value="' . $value . '" '. $c .' ><label for="' . $k . '">' . $title . '</label>' . $post;
                    }
                    $x++;
                }
            } else {
                foreach ($values as $val) {
                    $k = $valued ? $val . $x . '_' . $uq : str_replace(' ', '', $name) . $x;
                    $title = is_array($val) && !empty($val[1]) ? $val[1] : $val;
                    $value = is_array($val) ? $val[0] : $val;
                    $data = is_array($val) && !empty($val[2]) ? $val[2] : '';
                    $checked = is_array( $checked ) ? $checked : explode(',',$checked);
                    $c = in_array( $value, $checked ) ? 'checked' : '';
                    if ($label_first) {
                        echo $pre . '<label for="' . $k . '">' . $title . '</label><input ' . $attr . ' ' . $type . ' ' . $data . ' data-key="'.$name.'" name="' . $name . '" id="' . $k . '" value="' . $value . '" '.$c.'>' . $post;
                    } else {
                        echo $pre . '<input ' . $attr . ' ' . $type . ' name="' . $name . '" data-key="'.$name.'" id="' . $k . '" value="' . $value . '" ' . $data . ' '.$c.'><label for="' . $k . '">' . $title . '</label>' . $post;
                    }
                    $x++;
                }
            }
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
     * Renders <input type="radio"> elements
     * @param string $name Name of the input elements
     * @param array $values Array of values
     * @param string $checked Checked value or values separated by (,) comma
     * @param string $attr Attributes like class or data tags
     * @param bool $label_first If label should be before input element
     * @param string $pre String to wrap before start of <input>. Tip: 6 will wrap with bootstrap col-lg-6
     * @param string $post End string to wrap after />
     */
    function radios( string $name, array $values = [], string $checked = '', string $attr = '', bool $label_first = false, string $pre = '', string $post = '' ){
        $this->render_options( 'radio', $name, $values, $checked, $attr, $label_first, $pre, $post );
    }

    /**
     * Renders <input type="checkbox"> elements
     * @param string $name Name of the input elements
     * @param array $values Array of values
     * @param string $checked Checked value or values separated by (,) comma
     * @param string $attr Attributes like class or data tags
     * @param bool $label_first If label should be before input element
     * @param string $pre String to wrap before start of <input>. Tip: 6 will wrap with bootstrap col-lg-6
     * @param string $post End string to wrap after />
     */
    function checkboxes( string $name, array $values = [], string $checked = '', string $attr = '', bool $label_first = false, string $pre = '', string $post = '' ){
        $this->render_options( 'checkbox', $name, $values, $checked, $attr, $label_first, $pre, $post );
    }

    /**
     * Renders File Uploading Elements
     * @param string $id ID and name of the element
     * @param string $label Text for the <label>
     * @param string $button_label Text for the <button>
     * @param string $value Value of the input if any
     * @param bool $multiple Upload single or multiple files
     * @param bool $show_history Show previously uploaded files
     * @param string $button_class Class for upload button
     * @param string $attrs Attributes like class or data tags
     * @param string $extensions Permitted file upload extensions separated by (,) comma Ex: jpg,svg
     * @param bool $deletable Uploaded files are deletable or not
     * @param string $path Path to upload, will be encrypted on render
     * @param string $pre String to wrap before start
     * @param string $post End string to wrap after />
     */
    function upload( string $id, string $label, string $button_label = 'Upload', string $value = '', bool $multiple = false, bool $show_history = false, string $button_class = '', string $attrs = '', string $extensions = '', bool $deletable = false, string $path = '', string $pre = '', string $post = '' ) {
        if( is_numeric( $pre ) ){
            $pre = $pre == 0 ? '<div class="upload_set col">' : '<div class="upload_set col-12 col-lg-'.$pre.'">';
            $post = '</div>';
        }
        $sh = $show_history !== '' ? ' data-history' : '';
        $ext = $extensions !== '' ? ' data-exts="'.$extensions.'"' : '';
        $del = $deletable !== '' ? ' data-delete' : '';
        $cry = Crypto::initiate();
        $pat = $path !== '' ? ' data-path="'.$cry->encrypt( $path ).'"' : '';
        $type = $multiple ? 'files' : 'file';
        echo $pre.'<label for="#'.$id.'">'.T($label).'</label><input id="'.$id.'" name="'.$id.'" type="text" data-'.$type.' value="'.$value.'" '.$attrs.'><button type="button" class="'.$button_class.'" data-url="#'.$id.'" onclick="file_upload(this)" '.$sh.$ext.$del.$pat.'>'.T($button_label).'</button>'.$post;
    }
}