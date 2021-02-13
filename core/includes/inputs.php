<?php

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
    echo _editable_data( $data, $remove );
}

function select_options( $options = '', $selected = '', $placeholder = '', $translate = 0 ) {
    $d = $options;
    $s = $selected;
    if( $placeholder !== '' ){
        $placeholder = $translate ? T($placeholder) : $placeholder;
        echo empty($s) ? '<option disabled selected>'.$placeholder.'</option>' : '<option disabled>'.$placeholder.'</option>';
    }
    if( is_array($d) ){
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

function render_options( $type = 'radio', $name = '', $values = [], $checked = '', $attr = '', $label_first = 0, $pre = '', $post = '' ) {
    if( is_array( $values ) ) {
        $type = $type == 'radio' ? 'type="radio"' : 'type="checkbox"';
        $valued = is_assoc( $values ) ? true : false; $x = 0;
        if( is_numeric( $pre ) ){
            $pre = $pre == 0 ? '<div class="col">' : '<div class="col-12 col-lg-'.$pre.'">';
            $post = '</div>';
        }
        $uq = rand(1,999);
        if( is_assoc( $values ) ) {
            foreach ($values as $val => $title) {
                $k = $valued ? $val . $x . '_' . $uq : str_replace(' ', '', $name) . $x;
                $value = $valued ? $val : $title;
                $c = $value == $checked ? 'checked' : '';
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
                $c = $value == $checked ? 'checked' : '';
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

function render_radios( $name, $values = [], $checked = '', $attr = '', $label_first = 0, $pre = '', $post = '' ){
    render_options( 'radio', $name, $values, $checked, $attr, $label_first, $pre, $post );
}

function render_checkboxs( $name, $values = [], $checked = '', $attr = '', $label_first = 0, $pre = '', $post = '' ){
    render_options( 'checkbox', $name, $values, $checked, $attr, $label_first, $pre, $post );
}

// Render Input Elements

function render_input( $type, $id, $label, $placeholder = '', $value = '', $attrs = '', $pre = '', $name = '', $post = '' ){
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

function render_inputs( $type = 'text', $array = [], $attrs = '', $pre = '', $post = '' ){
    if( !empty( $array ) && is_array( $array ) ){
        foreach( $array as $id ){
            $slug = isset($id[0]) && $id[0] !== '' ? $id[0] : '';
            $label = isset($id[1]) && $id[1] !== '' ? $id[1] : '';
            $place = isset($id[2]) && $id[2] !== '' ? $id[2] : '';
            $value = isset($id[3]) && $id[3] !== '' ? $id[3] : '';
            $attr = isset($id[4]) && $id[4] !== '' ? $id[4] : '';
            render_input( $type, $slug, $label, $place, $value, $attr.' '.$attrs, $pre, $post );
        }
    }
}

// Render Input type text Element

function text( $id, $label, $placeholder = '', $value = '', $attrs = '', $pre = '', $post = '' ) {
    render_input( 'text', $id, $label, $placeholder, $value, $attrs, $pre, $post );
}

function texts( $array, $attrs = '', $pre = '', $post = '' ){
    if( is_array( $array ) ){
        if( is_assoc( $array ) ){
            foreach( $array as $k => $v ){
                render_input( 'text', $k, $v, $attrs, $pre, $post );
            }
        } else {
            render_inputs( 'text', $array, $attrs, $pre, $post );
        }
    }
}