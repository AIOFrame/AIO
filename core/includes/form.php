<?php

class FORM {

    public array $input_options = [
        'input_radius' => 8,
        'input_border_top' => 2,
        'input_border_right' => 2,
        'input_border_bottom' => 2,
        'input_border_left' => 2,
        'input_margin_top' => 0,
        'input_margin_right' => 0,
        'input_margin_bottom' => 20,
        'input_margin_left' => 0,
        'input_padding_top' => 15,
        'input_padding_right' => 15,
        'input_padding_bottom' => 15,
        'input_padding_left' => 15,
        'input_bg_light' => '#fff',
        'input_border_color_light' => '#eee',
        'input_border_color_active_light' => '#000',
        'input_color_light' => '#666',
        'input_color_active_light' => '#000',
        'input_bg_dark' => '#222',
        'input_border_color_dark' => '#333',
        'input_border_color_active_dark' => '#444',
        'input_color_dark' => '#444',
        'input_color_active_dark' => '#fff',
    ];

    public array $themed_options = [
        'input_bg',
        'input_border_color',
        'input_border_color_active',
        'input_color',
        'input_color_active',
    ];

    /**
     * Renders &lt;select&gt; options
     * @param array $options Indexed or Associative Array of options
     * @param string|null $selected Selected option or options separated by comma
     * @param string $placeholder Placeholder text
     * @param bool $keyed Yes if option value should be array key
     * @param bool $translate Translate the option text or not
     */
    function select_options( array $options = [], string|null $selected = '', string $placeholder = '', bool $keyed = false, bool $translate = false ): void {
        echo $this->__select_options( $options, $selected, $placeholder, $keyed, $translate );
    }

    /**
     * Renders &lt;select&gt; options
     * @param array $options Indexed or Associative Array of options
     * @param string|null $selected Selected option or options separated by comma
     * @param string $placeholder Placeholder text
     * @param bool $keyed Yes if option value should be array key
     * @param bool $translate Translate the option text or not
     */
    function __select_options( array $options = [], string|null $selected = '', string $placeholder = '', bool $keyed = false, bool $translate = false ): string {
        $s = explode( ',', str_replace( ' ', '', $selected ) );
        //skel( $s );
        $placeholder = $translate ? T($placeholder) : $placeholder;
        $return = '';
        if( $placeholder !== '' ){
            $return .= empty($s) ? '<option disabled selected>'.$placeholder.'</option>' : '<option disabled>'.$placeholder.'</option>';
        }
        //skel( $options );
        // TODO: support optgroup feature
        // TODO: If multi dimensional array then and also add data attr, possibly auto fill logic
        if( is_assoc( $options ) ) {
            foreach ( $options as $value => $data ) {
                $sel = '';
                $attr = '';
                $text = '';
                if( is_array( $data ) && isset( $data['text'] ) && isset( $data['value'] ) ) {
                    //skel( $data );
                    $text = $translate ? T( $data['text'] ) : $data['text'];
                    $value = $data['value'];
                    $attrs = array_diff_key( $data, array_flip( [ 'text', 'value' ] ));
                    //skel( $attrs );
                    foreach( $attrs as $ak => $av ) {
                        $attr .= ' data-'.$ak.'="'.$av.'"';
                    }
                } else {
                    $text = $translate ? T($data) : $data;
                    $value = $keyed ? $value : $data; //|| $k !== $t
                }
                //skel( in_array( $value, $s ) );
                if( is_array( $s ) && in_array( $value, $s ) ) {
                    $sel = 'selected';
                } else if( $value == $s ) {
                    $sel = 'selected';
                }
                if( $data == 'select2_placeholder' ) { $return .= '<option></option>'; continue; }
                $return .= '<option value="' . $value . '" ' . $sel . $attr.'>' . $text . '</option>';
            }
        } else {
            foreach( $options as $k => $o ) {
                $sel = '';
                //$d = '';
                $a = '';
                $c = '';
                if( is_array( $o ) ) {
                    $v = $o['value'] ?? ( $o['val'] ?? ( $o['v'] ?? '' ) );
                    $n = $o['name'] ?? ( $o['n'] ?? '' );
                    //$d = $o['data'] ?? ( $o['d'] ?? '' );
                    $a = $o['attr'] ?? ( $o['a'] ?? '' );
                    $t = $o['title'] ?? ( $o['t'] ?? $n );
                    $c = $o['class'] ?? ( $o['c'] ?? '' );
                    //$d = !empty( $d ) ? ( is_array( $d ) ? 'data-data=\''.json_encode( $d ).'\'' : 'data-data=\''.$d.'\'' ) : '';
                    $c = !empty( $c ) ? 'class=\''.$c.'\'' : '';
                    $sel = $v == $selected ? 'selected' : '';
                } else {
                    $v = $keyed ? $k : $o;
                    $n = $o;
                    $t = $n;
                    if( is_array( $s ) && in_array( $o, $s ) ) {
                        $sel = 'selected';
                    } else if( $o == $s ) {
                        $sel = 'selected';
                    }
                }
                //skel( $d );
                $t = "title='{$t}'";
                $n = $translate ? T( $n ) : $n;
                if( $n == 'select2_placeholder' ) { $return .= '<option></option>'; continue; }
                //skel( '<option '.$d.' '.$a.' '.$t.' value="' . $v . '" ' . $sel . '>' . $n . '</option>' );
                $return .= '<option '.$c.' '.$a.' '.$t.' value="' . $v . '" ' . $sel . '>' . $n . '</option>';
            }
        }
        //skel( $return );
        return $return;
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
     * @param string|float|int $pre Prepend wrap html or element with class before date Ex: '<div class="wrap">' or '.wrap' or '6'
     * @param string $post Append wrap html or element with class after date Ex: '</div>' Auto closes div if class or int provided in $pre
     * @param bool $keyed Yes if option value should be array key
     * @param bool $translate Translate the option text or not
     * @tip You can run select2() instead that will render select input with js select2 that has searchable dropdown
     */
    function select( string|array $identity = '', string $label = '', string $placeholder = '', array $options = [], string|null $selected = '', string $attr = '', string|float|int $pre = '', bool $keyed = false, bool $translate = false, string $post = '' ): void {
        echo $this->__select( $identity, $label, $placeholder, $options, $selected, $attr, $pre, $keyed, $translate, $post );
    }

    /**
     * Renders &lt;select&gt; element and <option>s inside
     * @param string|array $identity ID and name of the element or array of [id, name]
     * @param string $label Label for the &lt;label&gt;
     * @param string $placeholder Placeholder text
     * @param string|array $options Indexed or Associative Array of options
     * @param string|null $selected Selected option or options separated by comma
     * @param string $attr Attributes like class or data tags
     * @param string|float|int $pre Prepend wrap html or element with class before date Ex: '<div class="wrap">' or '.wrap' or '6'
     * @param string $post Append wrap html or element with class after date Ex: '</div>' Auto closes div if class or int provided in $pre
     * @param bool $keyed Yes if option value should be array key
     * @param bool $translate Translate the option text or not
     * @tip You can run select2() instead that will render select input with js select2 that has searchable dropdown
     */
    function __select( string|array $identity = '', string $label = '', string $placeholder = '', string|array $options = [], string|null $selected = '', string $attr = '', string|float|int $pre = '', bool $keyed = false, bool $translate = false, string $post = '' ): string {
        $rand = rand( 0, 999999 );
        $_p = $this->__pre( $pre );
        $p_ = $this->__post( $pre, $post );
        $post = empty( $p_ ) ? '</select>' : '</select>' . $p_;
        $at = $attr !== '' ? ' '.$attr : '';
        $id = !empty( $identity ) ? ( is_array($identity) ? $identity[0] : $identity.'_'.$rand ) : '';
        $name = is_array( $identity ) ? $identity[1] : $identity;
        $return = $_p;
        $req = str_contains( $attr, 'required' ) ? '<i>*</i>' : '';
        $return .= !empty( $label ) ? '<label for="'.$id.'">'. $label .$req.'</label>' : '';
        $ph = !empty( $placeholder ) ? ' placeholder="'.$placeholder.'" data-placeholder="'.$placeholder.'"' : '';
        $return .= '<select name="'.$name.'" title="'.$label.'" data-key="'.$name.'" data-auto-close id="'.$id.'"'.$at.$ph.'>';
        //if( str_contains( $attr, 'select2' ) ) {
        // TODO: Options to check if array is multi dimensional and append accordingly
        if( str_contains( $attr, 'select2') ) {
            $placeholder = '';
            $options = is_array( $options ) && is_assoc( $options ) ? [ '' => 'select2_placeholder' ] + $options : $options;
            //array_unshift( $options, 'select2_placeholder' );
        }
        //$placeholder = strpos( $attr, 'select2') !== false ? '' : $placeholder;
        $return .= is_array( $options ) ? $this->__select_options( $options, $selected, $placeholder, $keyed, $translate ) : $options;
        $return .= $post;
        return $return;
    }

    function select2( string $id = '', string $label = '', string $placeholder = '', array $options = [], string|null $selected = '', string $attr = '', string|float|int $pre = '', bool $keyed = false, bool $translate = false, string $post = '' ): void {
        $this->select( $id, $label, $placeholder, $options, $selected, $attr.' class="select2"', $pre, $keyed, $translate, $post );
    }

    function __select2( string $id = '', string $label = '', string $placeholder = '', string|array $options = [], string|null $selected = '', string $attr = '', string|float|int $pre = '', bool $keyed = false, bool $translate = false, string $post = '' ): string {
        return $this->__select( $id, $label, $placeholder, $options, $selected, $attr.' class="select2"', $pre, $keyed, $translate, $post );
    }

    /**
     * Renders an &lt;input&gt; Element
     * @param string $type Input type, Ex: 'text','radio','checkbox','textarea'
     * @param string|array $identity ID and name of the element
     * @param string $label Label for the &lt;label&gt;
     * @param string $placeholder Placeholder text
     * @param string|null $value Value of the input if any
     * @param string $attrs Attributes like class or data tags
     * @param string|float|int $pre Prepend wrap html or element with class before date Ex: '<div class="wrap">' or '.wrap' or '6'
     * @param string $post Append wrap html or element with class after date Ex: '</div>' Auto closes div if class or int provided in $pre
     * @param string $name Optional if different name is needed
     */
    function input( string $type, string|array $identity, string $label = '', string $placeholder = '', string|null $value = '', string $attrs = '', string|float|int $pre = '', string $post = '', string $name = '' ): void {
        echo $this->__input( $type, $identity, $label, $placeholder, $value, $attrs, $pre, $post, $name );
    }

    /**
     * Renders an &lt;input&gt; Element
     * @param string $type Input type, Ex: 'text','radio','checkbox','textarea'
     * @param string|array $identity ID and name of the element
     * @param string $label Label for the &lt;label&gt;
     * @param string $placeholder Placeholder text
     * @param string|null $value Value of the input if any
     * @param string $attrs Attributes like class or data tags
     * @param string|int|float $pre Prepend wrap html or element with class before date Ex: '<div class="wrap">' or '.wrap' or '6'
     * @param string $post Append wrap html or element with class after date Ex: '</div>' Auto closes div if class or int provided in $pre
     * @param string $name Optional if different name is needed
     * @return string
     */
    function __input( string $type, string|array $identity, string $label = '', string $placeholder = '', string|null $value = '', string $attrs = '', string|int|float $pre = '', string $post = '', string $name = '' ): string {
        $rand = rand( 0, 999999 );
        $type = $type == '' ? 'text' : $type;
        $_p = $this->__pre( $pre );
        $p_ = $this->__post( $pre, $post );
        $ph = $placeholder !== '' ? ' placeholder="'. $placeholder .'"' : '';
        $name = is_array( $identity ) ? $identity[1] : $identity;
        $id = !empty( $identity ) ? ( is_array($identity) ? $identity[0] : $identity.'_'.$rand ) : '';
        $n = $name !== '' ? $name : $id;
        $hidden_label = empty( $label ) ? $n : $label;
        $at = $attrs !== '' ? ' title="'.$hidden_label.'" '.$attrs : ' title="'.$hidden_label.'"';
        $value = str_contains( $value, 'fake_' ) ? $this->fake( $value ) : ( str_contains( $value, 'fake_' ) ? '' : $value );
        if( $type == 'textarea' ) {
            $va = $value !== '' ? $value : '';
        } else {
            $value = is_array( $value ) ? implode( ' ', $value ) : $value;
            $va = $value !== '' ? ' value="'.$value.'"' : '';
        }
        $nn = str_contains( $attrs, 'no_post' ) ? '' : ' name="'.$n.'"';
        switch( $type ){
            case 'textarea':
                $input = '<textarea id="'.$id.'" data-key="'.$n.'" '.$ph.$at.$nn.'>'.$va.'</textarea>';
                break;
            case 'slide':
            case 'toggle':
                $input = '<div><input type="hidden" id="'.$id.'" data-key="'.$n.'" '.$at.$ph.$va.$nn.'>';
                $ch = $value == 'true' || $value == '1' ? 'checked' : '';
                $input .= '<input type="checkbox" data-check="#'.$id.'" class="slide m" '.$ch.'></div>';
                break;
            case 'range':
                $input = '<input type="range" id="'.$id.'" data-key="'.$n.'" '.$at.$ph.$va.$nn.' oninput="this.previousElementSibling.getElementsByTagName(\'span\')[0].innerHTML = this.value">';
                break;
            default:
                $input = '<input type="'.$type.'" id="'.$id.'" data-key="'.$n.'" '.$at.$ph.$va.$nn.'>';
                break;
        }
        $req = str_contains( $attrs, 'required' ) ? '<i>*</i>' : '';
        $title = !empty( $label ) ? '<label for="'.$id.'">' . $label . $req . ( $type == 'range' ? '<span>'.$value.'</span>' : '' ) . '</label>' : '';
        return $_p . $title . $input . $p_;
    }

    /**
     * Renders multiple &lt;input&gt; elements
     * @param string $type Input type, Ex: 'text','radio','checkbox','textarea'
     * @param array $array Array of sub array of ['id','label','placeholder','value','attr','pre','post'] of inputs
     * @param string $attrs Attributes like class or data applicable to all
     * @param string|float|int $pre Prepend wrap html or element with class before date Ex: '<div class="wrap">' or '.wrap' or '6'
     * @param string $post Append wrap html or element with class after date Ex: '</div>' Auto closes div if class or int provided in $pre
     */
    function inputs( string $type = 'text', array $array = [], string $attrs = '', string|float|int $pre = '', string $post = '' ): void {
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
     * @param string|float|int $pre Prepend wrap html or element with class before date Ex: '<div class="wrap">' or '.wrap' or '6'
     * @param string $post Append wrap html or element with class after date Ex: '</div>' Auto closes div if class or int provided in $pre
     */
    function text( string|array $id, string $label = '', string $placeholder = '', string|null $value = '', string $attrs = '', string|float|int $pre = '', string $post = '' ): void {
        echo $this->__input( 'text', $id, $label, $placeholder, $value, $attrs, $pre, $post );
    }

    /**
     * Returns &lt;input type="text"> element
     * Basically the text input function with $type = 'text' param
     * @param string|array $id ID and name of the element
     * @param string $label Label for the &lt;label&gt;
     * @param string $placeholder Placeholder text
     * @param string|null $value Value of the input if any
     * @param string $attrs Attributes like class or data tags
     * @param string|float|int $pre Prepend wrap html or element with class before date Ex: '<div class="wrap">' or '.wrap' or '6'
     * @param string $post Append wrap html or element with class after date Ex: '</div>' Auto closes div if class or int provided in $pre
     */
    function __text( string|array $id, string $label = '', string $placeholder = '', string|null $value = '', string $attrs = '', string|float|int $pre = '', string $post = '' ): string {
        return $this->__input( 'text', $id, $label, $placeholder, $value, $attrs, $pre, $post );
    }

    /**
     * Renders multiple &lt;input type="text"&gt; elements
     * @param array $array Array of an array of ['id','label','placeholder','value','attr']
     * @param string $attrs Attributes like class or data applicable to all
     * @param string|float|int $pre Prepend wrap html or element with class before date Ex: '<div class="wrap">' or '.wrap' or '6'
     * @param string $post Append wrap html or element with class after date Ex: '</div>' Auto closes div if class or int provided in $pre
     */
    function texts( array $array, string $attrs = '', string|float|int $pre = '', string $post = '' ): void {
        if( is_assoc( $array ) ){
            foreach( $array as $k => $v ){
                $this->input( 'text', $k, $v, $attrs, $pre, $post );
            }
        } else {
            $this->inputs( 'text', $array, $attrs, $pre, $post );
        }
    }

    /**
     * Renders &lt;input type="textarea"&gt; element
     * Basically the text input function with $type = 'textarea' param
     * @param string|array $id ID and name of the element
     * @param string $label Label for the &lt;label&gt;
     * @param string $placeholder Placeholder text
     * @param string|null $value Value of the input if any
     * @param string $attrs Attributes like class or data tags
     * @param string|float|int $pre Prepend wrap html or element with class before date Ex: '<div class="wrap">' or '.wrap' or '6'
     * @param string $post Append wrap html or element with class after date Ex: '</div>' Auto closes div if class or int provided in $pre
     */
    function textarea( string|array $id, string $label = '', string $placeholder = '', string|null $value = '', string $attrs = '', string|float|int $pre = '', string $post = '' ): void {
        $this->input( 'textarea', $id, $label, $placeholder, $value, $attrs, $pre, $post );
    }

    /**
     * Renders &lt;input type="textarea"&gt; element
     * Basically the text input function with $type = 'textarea' param
     * @param string|array $id ID and name of the element
     * @param string $label Label for the &lt;label&gt;
     * @param string $placeholder Placeholder text
     * @param string|null $value Value of the input if any
     * @param string $attrs Attributes like class or data tags
     * @param string|float|int $pre Prepend wrap html or element with class before date Ex: '<div class="wrap">' or '.wrap' or '6'
     * @param string $post Append wrap html or element with class after date Ex: '</div>' Auto closes div if class or int provided in $pre
     */
    function __textarea( string|array $id, string $label = '', string $placeholder = '', string|null $value = '', string $attrs = '', string|float|int $pre = '', string $post = '' ): string {
        return $this->__input( 'textarea', $id, $label, $placeholder, $value, $attrs, $pre, $post );
    }

    /**
     * Renders &lt;input type="radio"&gt; elements
     * @param string|array $name Name of the input elements
     * @param array $values Array of values
     * @param string|array $checked Checked value or values separated by (,) comma
     * @param string $attr Attributes like class or data tags
     * @param bool $label_first If label should be before input element
     * @param string|float|int $pre Prepend wrap html or element with class before date Ex: '<div class="wrap">' or '.wrap' or '6'
     * @param string $post Append wrap html or element with class after date Ex: '</div>' Auto closes div if class or int provided in $pre
     */
    function radios( string|array $name, string $label = '', array $values = [], string|array $checked = '', string $attr = '', bool $label_first = false, string|float|int $pre = '', string $post = '', string $inputs_wrap = '', string $inputs_pre = '', string $inputs_post = '' ): void {
        //skel( $values );
        $this->render_options( 'radio', $label, $name, $values, $checked, $attr, $label_first, $pre, $post, $inputs_wrap, $inputs_pre, $inputs_post );
    }

    /**
     * Renders &lt;input type="radio"&gt; elements
     * @param string|array $name Name of the input elements
     * @param string|array $values Array of values
     * @param string|array $checked Checked value or values separated by (,) comma
     * @param string $attr Attributes like class or data tags
     * @param bool $label_first If label should be before input element
     * @param string|float|int $pre Prepend wrap html or element with class before date Ex: '<div class="wrap">' or '.wrap' or '6'
     * @param string $post Append wrap html or element with class after date Ex: '</div>' Auto closes div if class or int provided in $pre
     */
    function __radios( string|array $name, string $label = '', string|array $values = [], string|array $checked = '', string $attr = '', bool $label_first = false, string|float|int $pre = '', string $post = '', string $inputs_wrap = '', string $inputs_pre = '', string $inputs_post = '' ): string {
        return $this->__render_options( 'radio', $label, $name, $values, $checked, $attr, $label_first, $pre, $post, $inputs_wrap, $inputs_pre, $inputs_post );
    }

    /**
     * Renders &lt;input type="checkbox"&gt; elements
     * @param string|array $name Name of the input elements
     * @param string|array $values Array of values
     * @param array|string $checked Checked value or values separated by (,) comma
     * @param string $attr Attributes like class or data tags
     * @param bool $label_first If label should be before input element
     * @param string|float|int $pre Prepend wrap html or element with class before date Ex: '<div class="wrap">' or '.wrap' or '6'
     * @param string $post Append wrap html or element with class after date Ex: '</div>' Auto closes div if class or int provided in $pre
     */
    function checkboxes( string|array $name, string $label = '', string|array $values = '', string|array $checked = '', string $attr = '', bool $label_first = false, string|float|int $pre = '', string $post = '', string $inputs_wrap = '', string $inputs_pre = '', string $inputs_post = '' ): void {
        $this->render_options( 'checkbox', $label, $name, $values, $checked, $attr, $label_first, $pre, $post, $inputs_wrap, $inputs_pre, $inputs_post );
    }

    /**
     * Renders &lt;input type="checkbox"&gt; elements
     * @param string|array $name Name of the input elements
     * @param string|array $values Array of values
     * @param array|string $checked Checked value or values separated by (,) comma
     * @param string $attr Attributes like class or data tags
     * @param bool $label_first If label should be before input element
     * @param string|float|int $pre Prepend wrap html or element with class before date Ex: '<div class="wrap">' or '.wrap' or '6'
     * @param string $post Append wrap html or element with class after date Ex: '</div>' Auto closes div if class or int provided in $pre
     */
    function __checkboxes( string|array $name, string $label = '', string|array $values = '', string|array $checked = '', string $attr = '', bool $label_first = false, string|float|int $pre = '', string $post = '', string $inputs_wrap = '', string $inputs_pre = '', string $inputs_post = '' ): string {
        return $this->__render_options( 'checkbox', $label, $name, $values, $checked, $attr, $label_first, $pre, $post, $inputs_wrap, $inputs_pre, $inputs_post );
    }

    /**
     * Renders checkbox but slide toggle button
     * @param string|array $key
     * @param string $label
     * @param string $off_text
     * @param string $on_text
     * @param string $checked
     * @param string $size
     * @param string $attr
     * @param string|float|int $pre Prepend wrap html or element with class before date Ex: '<div class="wrap">' or '.wrap' or '6'
     * @param string $post Append wrap html or element with class after date Ex: '</div>' Auto closes div if class or int provided in $pre
     * @return void
     */
    function slide( string|array $key, string $label = '', string $off_text = '', string $on_text = '', string $checked = '1', string $size = 'm', string $attr = '', string|float|int $pre = '', string $post = '' ): void {
        echo $this->__slide( $key, $label, $off_text, $on_text, $checked, $size, $attr, $pre, $post );
    }

    /**
     * Renders checkbox but slide toggle button
     * @param string|array $key
     * @param string $label
     * @param string $off_text
     * @param string $on_text
     * @param string $checked
     * @param string $size
     * @param string $attr
     * @param string|float|int $pre Prepend wrap html or element with class before date Ex: '<div class="wrap">' or '.wrap' or '6'
     * @param string $post Append wrap html or element with class after date Ex: '</div>' Auto closes div if class or int provided in $pre
     * @return string
     */
    function __slide( string|array $key, string $label = '', string $off_text = '', string $on_text = '', string $checked = '1', string $size = 'm', string $attr = '', string|float|int $pre = '', string $post = '' ): string {
        //skel( $checked );
        $checked = $checked == 1 ? 'checked' : '';
        $rand = rand( 0, 99999 );
        $id = is_array( $key ) ? $key[0] : $key.'_'.$rand;
        $name = is_array( $key ) ? $key[1] : $key;
        $_p = $this->__pre( $pre );
        $p_ = $this->__post( $pre, $post );
        $key = 'data-key="'.$name.'"';
        $tip = $label !== '' ? 'title="'.$label.'"' : '';
        $return = $_p;
        $return .= !empty($label) ? '<label class="db">'. $label .'</label>' : '';
        $return .= __d( 'slide_set' );
        $return .= !empty( $off_text ) ? '<label for="' . $id . '" '.$tip.' class="slide_label off">' . $off_text . '</label>' : '';
        $return .= '<input ' . $attr . ' class="slide ' . $size . '" type="checkbox" name="' . $name . '" '.$key.' id="' . $id . '" '. $checked .' >';
        $return .= !empty( $on_text ) ? '<label for="' . $id . '" '.$tip.' class="slide_label on">' . $on_text . '</label>' : '';
        $return .= d__();
        $return .= $p_;
        return $return;
    }

    function __slides( string|array $key, string $label = '', string $off_text = '', string $on_text = '', array $options = [], string $size = 'm', string $attr = '', string|float|int $pre = '', string $post = '' ): string {
        //skel( $checked );
        $_p = $this->__pre( $pre );
        foreach( $options as $o ) {

        }
        $p_ = $this->__post( $pre, $post );
        return $_p.$p_;
    }

    /**
     * Renders Date Picker
     * @param string|array $id ID of the date picker element
     * @param string $label Label for the date picker
     * @param string $placeholder Default placeholder
     * @param string|null $value Pre-fill date value if any
     * @param string $attrs Hidden Field Attributes
     * @param string $position Position of the date picker popup
     * @param string|float|int $pre Prepend wrap html or element with class before date Ex: '<div class="wrap">' or '.wrap' or '6'
     * @param bool $range 1 / Yes if the date is range picker between 2 dates, Seperator will be -
     * @param bool $multiple 1 / Yes if multiple dates can be picked
     * @param string $view_attr Visible Field Attributes
     * @param string $min Minimum Date yyyy-mm-dd
     * @param string $max Maximum Date yyyy-mm-dd
     * @param string $post Append wrap html or element with class after date Ex: '</div>' Auto closes div if class or int provided in $pre
     * @tip Date picker renders a hidden date field that is POSTed and a visible date field that fits with custom user readable date format
     * @return void
     */
    function date( string|array $id, string $label = '', string $placeholder = '', string|null $value = '', string $attrs = '', string $position = '', string|float|int $pre = '', bool $range = false, bool $multiple = false, string $view_attr = '', string $min = '', string $max = '', string $post = '' ): void {
        echo $this->__date( $id, $label, $placeholder, $value, $attrs, $position, $pre, $range, $multiple, $view_attr, $min, $max, $post );
    }

    /**
     * Renders Date Picker
     * @param string|array $id ID of the date picker element
     * @param string $label Label for the date picker
     * @param string $placeholder Default placeholder
     * @param string|null $value Pre-fill date value if any
     * @param string $attrs Hidden Field Attributes
     * @param string $position Position of the date picker popup
     * @param string|float|int $pre Prepend wrap html or element with class before date Ex: '<div class="wrap">' or '.wrap' or '6'
     * @param bool $range 1 / Yes if the date is range picker between 2 dates, Seperator will be -
     * @param bool $multiple 1 / Yes if multiple dates can be picked
     * @param string $view_attr Visible Field Attributes
     * @param string $min Minimum Date yyyy-mm-dd
     * @param string $max Maximum Date yyyy-mm-dd
     * @param string $post Append wrap html or element with class after date Ex: '</div>' Auto closes div if class or int provided in $pre
     * @tip Date picker renders a hidden date field that is POSTed and a visible date field that fits with custom user readable date format
     * @return string
     */
    function __date( string|array $id, string $label = '', string $placeholder = '', string|null $value = '', string $attrs = '', string $position = '', string|float|int $pre = '', bool $range = false, bool $multiple = false, string $view_attr = '', string $min = '', string $max = '', string $post = '' ): string {
        $rand = rand(0,99999);
        global $options;
        $cal_ico = $options['ico_calendar'] ?? 'calendar_month';
        $icon_preview = __div( ( $options['icon_class'] ?? 'mico' ) . ' l ' . $cal_ico . ' icon_preview', $cal_ico );
        $id = !empty( $id ) ? ( is_array( $id ) ? [ $id[0] ] : $id ) : $rand;
        $alt_id = is_array( $id ) ? [ $id[0].'_alt', $id[1].'_alt' ] : $id.'_alt';
        $range_attr = $range ? ' range' : '';
        $multiple_attr = $multiple ? ' multiple' : '';
        $view_attr = $view_attr ? ' view="'.$view_attr.'"' : '';
        $position = !empty( $position ) ? $position : 'bottom center';
        $attrs .= is_array( $alt_id ) ? ' data-alt="[data-key='.$alt_id[0].']"' : ' data-alt="[data-key='.$alt_id.']"';
        $post = $icon_preview . $this->__post( $pre, $post );
        //$post = !empty( $pre ) && !empty( $post ) ? $post : ( $icon_preview );

        $visible_attr = is_array( $id ) ? 'class="dater" alt="#'.$id[0].'_'.$rand.'" position="'.$position.'"' : 'class="dater" alt="#'.$id.'_'.$rand.'" position="'.$position.'"';
        $visible_attr .= !empty( $min ) ? ' min="'.$min.'"' : '';
        $visible_attr .= !empty( $max ) ? ' max="'.$max.'"' : '';
        $visible_attr .= str_contains( $attrs, 'disabled' ) ? ' disabled' : '';

        // Hidden Input - Renders date format as per backend
        $value_ymd = !empty( $value ) ? ( $value !== 'fake_date' ? easy_date( $value, 'Y-m-d' ) : $value ) : '';
        $return = $this->__text([$id . '_' . $rand, $id], '', '', $value_ymd, $attrs . ' hidden data-hidden-date');
        // Visible Input - Render date for easier user grasp
        $value_dmy = !empty( $value ) ? ( $value !== 'fake_date' ? easy_date( $value, 'd-m-Y' ) : $value ) : '';
        //skel( $icon_preview );
        $return .= $this->__text( $alt_id, $label, $placeholder, $value_dmy, $visible_attr.$range_attr.$multiple_attr.$view_attr.' data-visible-date no_post', $pre, $post );
        return $return;
    }

    /**
     * Renders multiple date picker fields
     * @param array $array
     * @param string $attrs
     * @param string $position
     * @param string|float|int $pre Prepend wrap html or element with class before date Ex: '<div class="wrap">' or '.wrap' or '6'
     * @param bool $range
     * @param bool $multiple
     * @param string $post Append wrap html or element with class after date Ex: '</div>' Auto closes div if class or int provided in $pre
     * @return void
     */
    function dates( array $array, string $attrs = '', string $position = '', string|float|int $pre = '', bool $range = false, bool $multiple = false, string $post = '' ): void {
        echo $this->__dates( $array, $attrs, $position, $pre, $range, $multiple, $post );
    }

    /**
     * Renders multiple date picker fields
     * @param array $array
     * @param string $attrs
     * @param string $position
     * @param string|float|int $pre Prepend wrap html or element with class before date Ex: '<div class="wrap">' or '.wrap' or '6'
     * @param bool $range
     * @param bool $multiple
     * @param string $post Append wrap html or element with class after date Ex: '</div>' Auto closes div if class or int provided in $pre
     * @return string
     */
    function __dates( array $array, string $attrs = '', string $position = '', string|float|int $pre = '', bool $range = false, bool $multiple = false, string $post = '' ): string {
        $return = '';
        if( !empty( $array ) ){
            foreach( $array as $f ) {
                $id = $f[0] ?? '';
                $label = $f[1] ?? '';
                $ph = $f[2] ?? '';
                $value = $f[3] ?? '';
                $return .= $this->__date( $id, $label, $ph, $value, $attrs, $position, $pre, $range, $multiple, $post );
            }
        }
        return $return;
    }

    /**
     * Renders calling code dropdown and phone number input
     * @param string $code_id
     * @param string $phone_id
     * @param string $code_label
     * @param string $phone_label
     * @param string $code_placeholder
     * @param string $phone_placeholder
     * @param string $code_default
     * @param string $phone_default
     * @param string $attr
     * @param string|float|int $pre Prepend wrap html or element with class before date Ex: '<div class="wrap">' or '.wrap' or '6'
     * @return void
     */
    function phone( string $code_id, string $phone_id, string $code_label = 'Code', string $phone_label = 'Phone', string $code_placeholder = '', string $phone_placeholder = '', string $code_default = '', string $phone_default = '', string $attr = '', string|float|int $pre = '' ): void {
        echo $this->__phone( $code_id, $phone_id, $code_label, $phone_label, $code_placeholder, $phone_placeholder, $code_default, $phone_default, $attr, $pre );
    }

    /**
     * Renders calling code dropdown and phone number input
     * @param string $code_id
     * @param string $phone_id
     * @param string $code_label
     * @param string $phone_label
     * @param string $code_placeholder
     * @param string $phone_placeholder
     * @param string $code_default
     * @param string $phone_default
     * @param string $attr
     * @param string|float|int $pre Prepend wrap html or element with class before date Ex: '<div class="wrap">' or '.wrap' or '6'
     * @param string $post
     * @return string
     */
    function __phone( string $code_id, string $phone_id, string $code_label = 'Code', string $phone_label = 'Phone', string $code_placeholder = '', string $phone_placeholder = '', string $code_default = '', string $phone_default = '', string $attr = '', string|float|int $pre = '', string $post = '' ): string {
        $codes = get_calling_codes();
        if( empty( $code_default ) ) {
            $o = new OPTIONS();
            global $options;
            $r = $o->current_region_prefix();
            $code_default = ( $options[ $r . 'default_phone_code' ] ?? '' );
        }
        return $this->__pre( $pre )
        . __r()
        . $this->__select2( $code_id, $code_label, $code_placeholder, $codes, $code_default, $attr, 5, 1 )
        . $this->__input( 'number', $phone_id, $phone_label, $phone_placeholder, $phone_default, $attr, 7 )
        . r__()
        . $this->__post( $pre, $post );
    }

    function captcha( string $id, string $label = 'Type the Captcha', string $placeholder = '', string $attr = '', string|float|int $pre = '', string $post = '', int $captcha_length = 5, array $text_color_rgb = [], array $bg_rgb = [] ): void {
        echo $this->__captcha( $id, $label, $placeholder, $attr, $pre, $post, $captcha_length, $text_color_rgb, $bg_rgb );
    }

    function __captcha( string $id, string $label = 'Type the Captcha', string $placeholder = '', string $attr = '', string|float|int $pre = '', string $post = '', int $captcha_length = 5, array $text_color_rgb = [], array $bg_rgb = [] ): string {
        //$captcha = substr( str_shuffle( '!@#$%^&*()_+{}[]ABCDEFGHJKMNOPQRSTUVWXYZ!@#$%^&*()_+{}[]abcdefghjkmnopqrstuvwxyz023456789!@#$%^&*()_+{}[]' ), 0, $captcha_length );
        $c = Encrypt::initiate();
        //global $options;
        //$ico = $options['icon_class'] ?? 'mico';
        //$reload = $options['ico_reload'] ?? 'replay';
        $icon_texts = [ 'heart', 'house', 'star', 'car', 'plane', 'boat', 'prize', 'moon' ];
        $icons = [
            'heart' => 'favorite',
            'house' => 'home',
            'star' => 'grade',
            'car' => 'directions_car',
            'plane' => 'flight',
            'boat' => 'sailing',
            'prize' => 'emoji_events',
            'moon' => 'nightlight'
        ];
        $r = rand( 0, count( $icon_texts ) - 1 );
        $captcha = $icon_texts[ $r ];
        $enc_captcha = $c->encrypt( $captcha );
        $return = $this->__pre( $pre )
            . __r()
                . __c( 4, 'captcha_wrap' )
                    . __text_to_image( T('Choose') . ' ' . $captcha, 'img', 40, rand(10, 35), 0, 10, 0, 1, ( $text_color_rgb ?? [ 'r' => 220, 'g' => 220, 'b' => 220 ] ), ( $bg_rgb ?? [ 'r' => 195, 'g' => 255, 'b' => 243 ] ) )
                . c__()
                . __c( 8, 'captcha_choices' )
                    . __r( 'gx-0' );
                        /* foreach( $icons as $in => $iv ) {
                            $return .= $this->__radios( 'verify_captcha', '', [ $iv ], '', '', 0, '.col ' );
                        } */
                        $icons = shuffle_assoc( $icons );
                        //shuffle( $icons );
                        $return .= $this->__radios( 'verify_captcha', '', $icons, 'heart', str_replace( 'required', '', $attr), '', '', '', '', '.col' )
                    . r__()
                . c__();
                /* . __c( 1 )
                    . __div( $ico . ' ico mt30 cp ' . $reload, $reload, '', 'onclick="reload_captcha()" data-captcha="'. ( defined('APPDEBUG') && APPDEBUG ? $c->encrypt('reload_captcha_ajax') : 'reload_captcha_ajax' ) .'"' )
                . c__() */
                //$this->__input( 'text', 'verify_captcha', $label, $placeholder, ( defined('APPDEBUG') && APPDEBUG  ? $captcha : '' ), $attr, 6 )
                //$return .= $this->__checkboxes( 'verify_captcha', 'Choose the icon', $icons, '', '', '', '', '', '.row', '.col' )
            $return .= $this->__input( 'hidden', 'enc_captcha', '', '', $enc_captcha, $attr )
            . r__()
        . $this->__post( $pre, $post );
            return $return;
    }

    /**
     * Renders color picker
     * @param string|array $id Unique ID of the element
     * @param string $label Label for the Color Picker
     * @param string $placeholder
     * @param string|null $value
     * @param string $attrs
     * @param string|float|int $pre Prepend wrap html or element with class before date Ex: '<div class="wrap">' or '.wrap' or '6'
     * @param string $border
     * @param string|float|int $preview
     * @param string $post Append wrap html or element with class after date Ex: '</div>' Auto closes div if class or int provided in $pre
     * @return void
     */
    function color( string|array $id, string $label = '', string $placeholder = '', string|null $value = '', string $attrs = '', string|float|int $pre = '', string $border = '', string|float|int $preview = '', string $post = '' ): void {
        echo $this->__color( $id, $label, $placeholder, $value, $attrs, $pre, $border, $preview, $post );
    }

    /**
     * Returns color picker
     * @param string|array $id Unique ID of the element
     * @param string $label Label for the Color Picker
     * @param string $placeholder
     * @param string|null $value
     * @param string $attrs
     * @param string|float|int $pre Prepend wrap html or element with class before date Ex: '<div class="wrap">' or '.wrap' or '6'
     * @param string $border
     * @param string|float|int $preview
     * @param string $post Append wrap html or element with class after date Ex: '</div>' Auto closes div if class or int provided in $pre
     * @return string
     */
    function __color( string|array $id, string $label = '', string $placeholder = '', string|null $value = '', string $attrs = '', string|float|int $pre = '', string $border = '', string|float|int $preview = '', string $post = '' ): string {
        $attrs .= ' data-color-picker';
        $attrs = !empty( $border ) ? $attrs . ' data-border="'.$border.'"' : $attrs;
        $attrs .= !empty( $preview ) ? ' data-preview="'.$preview.'"' : ' data-self-preview';
        global $color_picker;
        global $options;
        if( $color_picker == 1 ) {
            //$code = '<div class="color_picker_wrap"><div class="color-picker"></div><div class="color_controls"></div><div class="close">close</div></div>';
            $code = '';
        } else {
            $code = __div( 'dn', __div( 'color_picker_wrap', __div( 'color-picker' ) . __div( 'color_controls', '<input type="text" value="#ffffff" class="code" onfocus="this.select();">' ) . __div( ( $options['icon_class'] ?? '' ) . ' close ' . ( $options['ico_close'] ?? '' ), ( $options['ico_close'] ?? 'close' ) ) ), '', 'color-picker-html' );
            $color_picker = 1;
        }
        return $this->__text( $id, $label, $placeholder, $value, $attrs, $pre, $post ) . $code;
    }

    /**
     * Render an interactive Google Map picker with search
     * @param string $latitude_field Input field ID to be filled with latitude value
     * @param string $longitude_field Input field ID to be filled with longitude value
     * @param string $address_field Input field ID to be filled with address
     * @param string $area_field Input field ID to be filled with area
     * @param string $city_field Input field ID to be filled with city
     * @param string $country_field Input field ID to be filled with country
     * @param string $coordinates Input field ID to be filled with co-ordinates
     * @param string|float|int $pre Prepend wrap html or element with class after date Ex: '</div>' Auto closes div if class or int provided in $pre
     * @param int $height
     * @param string $latitude_value Default map starting latitude value
     * @param string $longitude_value Default map starting longitude value
     * @param bool $show_search
     * @param bool $show_full
     * @param string $zoom Default map zoom level
     * @param bool $show_zoom
     * @param string $type Default map type
     * @param bool $show_type
     * @param string $post Append wrap html or element with class after date Ex: '</div>' Auto closes div if class or int provided in $pre
     * @return void
     */
    function map( string $latitude_field = '', string $longitude_field = '', string $address_field = '', string $area_field = '', string $city_field = '', string $country_field = '', string $coordinates = '', string|float|int $pre = '', int $height = 200, string $latitude_value = '', string $longitude_value = '', bool $show_search = true, bool $show_full = true, string $zoom = '13', bool $show_zoom = true, string $type = 'terrain', bool $show_type = false, string $post = '' ): void {
        echo $this->__map( $latitude_field, $longitude_field, $address_field, $area_field, $city_field, $country_field, $coordinates, $pre, $height, $latitude_value, $longitude_value, $show_search, $show_full, $zoom, $show_zoom, $type, $show_type, $post );
    }

    /**
     * Returns an interactive Google Map picker with search
     * @param string $latitude_field Input field ID to be filled with latitude value
     * @param string $longitude_field Input field ID to be filled with longitude value
     * @param string $address_field Input field ID to be filled with address
     * @param string $area_field Input field ID to be filled with area
     * @param string $city_field Input field ID to be filled with city
     * @param string $country_field Input field ID to be filled with country
     * @param string $coordinates Input field ID to be filled with co-ordinates
     * @param string|float|int $pre Prepend wrap html or element with class after date Ex: '</div>' Auto closes div if class or int provided in $pre
     * @param int $height Set the height of the Google map box (default 200px)
     * @param string $latitude_value Default map starting latitude value
     * @param string $longitude_value Default map starting longitude value
     * @param bool|int $show_search To display an input to show search box to find address
     * @param bool|int $show_full To display full screen toggle control
     * @param string|int $zoom Default map zoom level
     * @param bool|int $show_zoom Show zoom controls (default true)
     * @param string $type Default map type
     * @param bool|int $show_type Show map type controls (default false)
     * @param string $post Append wrap html or element with class after date Ex: '</div>' Auto closes div if class or int provided in $pre
     * @return string
     */
    function __map( string $latitude_field = '', string $longitude_field = '', string $address_field = '', string $area_field = '', string $city_field = '', string $country_field = '', string $coordinates = '', string|float|int $pre = '', int $height = 200, string $latitude_value = '', string $longitude_value = '', bool|int $show_search = true, bool|int $show_full = true, string|int $zoom = 13, bool|int $show_zoom = true, string $type = 'terrain', bool|int $show_type = false, string $post = '' ): string {
        //$height = $height !== '' ? $height : 400;
        if( is_numeric( $pre ) ){
            $pre = $pre == 0 ? __pre( '', 'map_col col' ) : __pre( '', 'map_col col-12 col-md-'.$pre );
            $post = __post();
        } else if( str_contains( $pre, '.' ) ) {
            $pre = __pre( '', str_replace('.','',$pre) );
            $post = __post();
        }
        $r = defined( 'REGION' ) && isset( REGION['cca2'] ) ? strtolower( REGION['cca2'] ).'_' : '';
        if( defined( 'DB_TYPE' ) ) {
            $db = new DB();
            $m = new MAPS();
            $options_array = $m->options;
            $options_array = defined( 'REGION' ) ? prepare_values( $options_array, $r ) : $options_array;
            $ops = $db->get_options( $options_array );
            //skel( $ops );
            $latitude_value = $ops[$r.'default_map_lat'] ?? '';
            $longitude_value = $ops[$r.'default_map_long'] ?? '';
            $map_marker = $ops[$r.'map_marker'] ?? 'red';
            $show_search = !empty( $show_search ) ? $show_search : ( $ops[$r.'show_map_search'] ?? '' );
            $show_full = !empty( $show_full ) ? $show_full : ( $ops[$r.'show_map_full'] ?? '' );
            $show_type = !empty( $show_type ) ? $show_type : ( $ops[$r.'show_map_type'] ?? '' );
            $show_zoom = !empty( $show_zoom ) ? $show_zoom : ( $ops[$r.'show_map_zoom'] ?? '' );
            $zoom = !empty( $zoom ) ? $zoom : ( $ops[$r.'default_map_zoom'] ?? '' );
            $type = !empty( $type ) ? $type : ( $ops[$r.'default_map_type'] ?? '' );
            $style = $ops[$r.'map_style'] ?? '';
        }
        $def_lat = !empty( $latitude_value ) ? ( str_contains( $latitude_value, 'fake_' ) ? ' lat="'.$this->fake( $latitude_value ).'"' : ' lat="'.$latitude_value.'"' ) : '';
        $def_long = !empty( $longitude_value ) ? ( str_contains( $longitude_value, 'fake_' ) ? ' long="'.$this->fake( $longitude_value ).'"' : ' long="'.$longitude_value.'"' ) : '';
        //$def_long = !empty( $longitude_value ) ? ' long="'.$longitude_value.'"' : '';
        $def_zoom = !empty( $zoom ) ? ' level="'.$zoom.'"' : '';
        $zoom_control = $show_zoom == 1 ? ' zoom_controls="1"' : '';
        $type_control = $show_type == 1 ? ' type_controls="1"' : '';
        $full_control = $show_full == 1 ? ' full_screen_controls="1"' : '';
        $def_type = !empty( $type ) ? ' type="'.$type.'"' : '';
        $def_style = !empty( $style ) ? ' data-style="'.$style.'"' : '';
        $marker = !empty( $map_marker ) ? ' data-marker="'.$map_marker.'"' : '';
        $co = !empty( $coordinates ) ? ' data-coordinates="'.$coordinates.'"' : '';
        $add = !empty( $address_field ) ? ' data-address="'.$address_field.'"' : '';
        $area = !empty( $area_field ) ? ' data-area="'.$area_field.'"' : '';
        $city = !empty( $city_field ) ? ' data-city="'.$city_field.'"' : '';
        $country = !empty( $country_field ) ? ' data-country="'.$country_field.'"' : '';
        $lat = !empty( $latitude_field ) ? ' data-lat="'.$latitude_field.'"' : '';
        $long = !empty( $longitude_field ) ? ' data-long="'.$longitude_field.'"' : '';
        $height = $height > 0 ? 'style="height:'.$height.'px" ' : '';
        $r = rand(0,999);
        $return = $pre;
        $return .= __pre( '', 'map_wrap' );
        if( $show_search == 1 )
            $return .= $this->__input('search',['search_'.$r,'search_'.$r],'','Search for Address...');
        $search = $show_search == 1 ? ' search="search_'.$r.'" ' : '';
        $return .= __pre( 'map_'.$r, 'google_map', 'div', $height.$search.'data-google-map-render'.$marker.$def_zoom.$zoom_control.$full_control.$type_control.$def_lat.$def_long.$def_type.$def_style.$co.$add.$area.$city.$country.$lat.$long );
        $return .= __post().__post();
        $m = new MAPS();
        $return .= $m->__google_maps();
        $return .= $post;
        return $return;
    }

    /**
     * Renders &lt;input type="radio"&gt; or &lt;input type="checkbox"&gt;
     * @param string $type Type either 'radio' or 'checkbox'
     * @param string|array $identity Name of the input elements
     * @param string|array $values Array of values
     * @param string|array $checked Checked value or values separated by (,) comma
     * @param string $attr Attributes like class or data tags
     * @param bool $label_first If label should be before input element
     * @param string|float|int $pre Prepend wrap html or element with class before date Ex: '<div class="wrap">' or '.wrap' or '6'
     * @param string $post Append wrap html or element with class after date Ex: '</div>' Auto closes div if class or int provided in $pre
     */
    function render_options( string $type = 'radio', string $label = '', string|array $identity = '', string|array $values = [], string|array $checked = '', string $attr = '', bool $label_first = false, string|float|int $pre = '', string $post = '', string $inputs_wrap = '', string $inputs_pre = '', string $inputs_post = '' ): void {
        echo $this->__render_options( $type, $label, $identity, $values, $checked, $attr, $label_first, $pre, $post, $inputs_wrap, $inputs_pre, $inputs_post );
    }

    /**
     * Renders &lt;input type="radio"&gt; or &lt;input type="checkbox"&gt;
     * @param string $type Type either 'radio' or 'checkbox'
     * @param string|array $identity Name of the input elements
     * @param string|array $values Array of values
     * @param string|array $checked Checked value or values separated by (,) comma
     * @param string $attr Attributes like class or data tags
     * @param bool $label_first If label should be before input element
     * @param string|float|int $pre Prepend wrap html or element with class before date Ex: '<div class="wrap">' or '.wrap' or '6'
     * @param string $post Append wrap html or element with class after date Ex: '</div>' Auto closes div if class or int provided in $pre
     */
    function __render_options( string $type = 'radio', string $label = '', string|array $identity = '', string|array $values = [], string|array $checked = '', string $attr = '', bool $label_first = false, string|float|int $pre = '', string $post = '', string $inputs_wrap = '', string $inputs_pre = '', string $inputs_post = '' ): string {
        $return = '';
        if( is_array( $values ) ) {
            $rand = rand( 0, 99999 );
            $type = $type == 'radio' ? 'type="radio"' : 'type="checkbox"';
            $id = is_array($identity) ? $identity[0] : $identity;
            $name = is_array($identity) ? $identity[1] : $identity;
            $valued = is_assoc( $values ); $x = 0;
            $_p = $this->__pre( $pre );
            $p_ = $this->__post( $pre, $post );
            $wrap_inputs_pre = !empty( $inputs_wrap ) ? '<div class="'.$inputs_wrap.'">' : '';
            $wrap_inputs_post = !empty( $inputs_wrap ) ? '</div>' : '';
            $_ip = $this->__pre( $inputs_pre );
            $ip_ = $this->__post( $inputs_pre, $inputs_post );
            $key = 'data-key="'.$name.'"';
            if( $type !== 'type="radio"' && strpos( $attr, 'data-array') !== false ) {
                $name = $name . '[]';
            }
            $uq = rand(1,999);
            $return .= $_p;
            $req = str_contains( $attr, 'required' ) ? '<i>*</i>' : '';
            $return .= !empty($label) ? '<label class="db">'. $label .$req.'</label>' : '';
            $return .= $wrap_inputs_pre;
            if( is_assoc( $values ) ) {
                foreach ($values as $val => $title) {
                    $tip = $title !== '' ? 'title="'.strip_tags($title).'"' : '';
                    $k = $valued ? $val . $x . '_' . $uq : str_replace(' ', '', $name) . $x;
                    $value = $valued ? $val : $title;
                    $checked = is_array( $checked ) ? $checked : explode(',',$checked);
                    $c = in_array( $value, $checked ) ? 'checked' : '';
                    if ($label_first) {
                        $return .= $_ip . '<label for="' . $k . '" '.$tip.' class="' . $name . '_' . $value . '">' . $title . $req . '</label><input ' . $tip . $attr . ' ' . $type . ' name="' . $name . '" '.$key.' id="' . $k . '" value="' . $value . '" '. $c .' >' . $ip_;
                    } else {
                        $return .= $_ip . '<input ' . $tip . $attr . ' ' . $type . ' name="' . $name . '" '.$key.' id="' . $k . '" value="' . $value . '" '. $c .' ><label for="' . $k . '" '.$tip.' class="' . $name . '_' . $value . '">' . $title . $req . '</label>' . $ip_;
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
                        $return .= $_ip . '<label for="' . $k . '" '.$tip.' class="' . $name . '_' . $value . '">' . $title . '</label><input ' . $tip . $attr . ' ' . $type . ' ' . $data . ' data-key="'.$name.'" name="' . $name . '" id="' . $k . '" value="' . $value . '" '.$c.'>' . $ip_;
                    } else {
                        $return .= $_ip . '<input ' . $tip . $attr . ' ' . $type . ' name="' . $name . '" data-key="'.$name.'" id="' . $k . '" value="' . $value . '" ' . $data . ' '.$c.'><label for="' . $k . '" '.$tip.' class="' . $name . '_' . $value . '">' . $title . '</label>' . $ip_;
                    }
                    $x++;
                }
            }
            $return .= $wrap_inputs_post;
            $return .= $p_;
            /* if (is_assoc( $d )) {
                foreach ($d as $k => $t) {
                    echo $before . '<label for="cb_' . $k . '" ><input ' . $attrs . '  id="cb_' . $k . '" type="' . $tp . '" value="' . $k . '" ' . (in_array($k, $s) ? "checked" : "") . '>' . $t . '</label>' . $after;
                }
            } else {
                foreach ($d as $t) {
                    echo $before . '<label for="cb_' . str_replace(' ', '_', $t) . '" ><input' . $attrs . 'id="cb_' . str_replace(' ', '_', $t) . '" type="' . $tp . '" value="' . $t . '" ' . (in_array($t, $t) ? "checked" : "") . '>' . $t . '</label>' . $after;
                }
            } */
        } else {
            // TODO: If value is single
        }
        return $return;
    }

    /**
     * Renders File Uploading Elements
     * @param string|array $identity ID and name of the element
     * @param string $label Text for the &lt;label&gt;
     * @param string $button_label Text for the &lt;button&gt;
     * @param string $value Value of the input if any
     * @param int $multiple Upload single or quantity of multiple files, 1 means infinite, 1+ ex. 2 means max 2 files
     * @param bool $show_history Show previously uploaded files
     * @param string $button_class Class for upload button
     * @param string $attrs Attributes like class or data tags
     * @param string $extensions Permitted file upload extensions separated by (,) comma Ex: jpg,svg
     * @param string $size Permitted file size in Mb Ex: 10
     * @param bool $deletable Uploaded files are deletable or not
     * @param string $path Path to upload, will be encrypted on render
     * @param string|float|int $pre Prepend wrap html or element with class before date Ex: '<div class="wrap">' or '.wrap' or '6'
     * @param string $post Append wrap html or element with class after date Ex: '</div>' Auto closes div if class or int provided in $pre
     */
    function upload( string|array $identity, string $label, string $button_label = 'Upload', string $value = '', int $multiple = 1, bool $show_history = false, string $button_class = '', string $attrs = '', string $extensions = '', string $size = '', bool $deletable = false, string $path = '', string|float|int $pre = '', string $post = '' ): void {
        echo $this->__upload( $identity, $label, $button_label, $value, $multiple, $show_history, $button_class, $attrs, $extensions, $size, $deletable, $path, $pre, $post );
    }

    /**
     * Returns File Uploading Elements
     * @param string|array $identity ID and name of the element
     * @param string $label Text for the &lt;label&gt;
     * @param string $button_label Text for the &lt;button&gt;
     * @param string $value Value of the input if any
     * @param int $multiple Upload single or quantity of multiple files, 1 means infinite, 1+ ex. 2 means max 2 files
     * @param bool $show_history Show previously uploaded files
     * @param string $button_class Class for upload button
     * @param string $attrs Attributes like class or data tags
     * @param string $extensions Permitted file upload extensions separated by (,) comma Ex: jpg,svg
     * @param string $size Permitted file size in Mb Ex: 10
     * @param bool $deletable Uploaded files are deletable or not
     * @param string $path Path to upload, will be encrypted on render
     * @param string|float|int $pre Prepend wrap html or element with class before date Ex: '<div class="wrap">' or '.wrap' or '6'
     * @param string $post Append wrap html or element with class after date Ex: '</div>' Auto closes div if class or int provided in $pre
     */
    function __upload( string|array $identity, string $label, string $button_label = 'Upload', string $value = '', int $multiple = 1, bool $show_history = false, string $button_class = '', string $attrs = '', string $extensions = '', string $size = '', bool $deletable = false, string $path = '', string|float|int $pre = '', string $post = '' ): string {
        global $options;
        $rand = rand( 0, 99999 );
        $id = $identity.'_'.$rand; // is_array($identity) ? $identity[0] : $identity;
        $name = $identity; // is_array($identity) ? $identity[1] : $identity;
        $_p = $this->__pre( $pre );
        $p_ = $this->__post( $pre, $post );
        $sh = $show_history ? ' data-history' : '';
        $ext = $extensions !== '' ? ' data-exts="'.str_replace('.','',$extensions).'"' : '';
        $sz = $size !== '' ? ' data-size="'.$size.'"' : '';
        $del = $deletable ? ' data-delete' : '';
        $cry = Encrypt::initiate();
        $pat = $path !== '' ? ' data-path="'.$cry->encrypt( $path ).'"' : '';
        $type = $multiple > 0 ? 'files' : 'file';
        $mul = $multiple > 0 ? ' data-files="'.$multiple.'" ' : ' data-file ';
        $req = str_contains( $attrs, 'required' ) ? '<i>*</i>' : '';
        $label = !empty( $label ) ? '<label for="'.$id.'">'.$label.$req.'</label>' : '';
        $value = str_contains( $value, 'fake_' ) ? $this->fake( $value ) : ( str_contains( $value, 'fake_' ) ? '' : $value );
        $ico = __div( ( $options['icon_class'] ?? 'mico' ) . ' ico ' . ( $options['ico_file_upload'] ?? '' ), $options['ico_file_upload'] ?? '' );
        return $_p.$label.'<button type="button" class="aio_upload '.$button_class.'" data-url="#'.$id.'" onclick="file_upload(this)" '.$sh.$ext.$sz.$mul.$del.$pat.'>'.$button_label.$ico.'</button><input id="'.$id.'" name="'.$name.'" data-key="'.$name.'" type="text" data-'.$type.' value="'.$value.'" '.$attrs.'>'.$p_;
    }

    /**
     * Renders code editor with hidden &lt;input type="textarea"&gt; element
     * @param string|array $id ID and name of the element
     * @param string $label Label for the &lt;label&gt;
     * @param string|null $value Value of the input if any
     * @param string $attrs Attributes like class or data tags
     * @param string|float|int $pre Prepend wrap html or element with class before date Ex: '<div class="wrap">' or '.wrap' or '6'
     * @param string $post Append wrap html or element with class after date Ex: '</div>' Auto closes div if class or int provided in $pre
     */
    function code( string|array $id, string $label = '', string|null $value = '', string $attrs = '', string|float|int $pre = '', string $post = '' ): void {
        get_script('ace');
        echo $this->__code( $id, $label, $value, $attrs, $pre, $post );
    }

    /**
     * Returns code editor with hidden &lt;input type="textarea"&gt; element
     * @param string|array $id ID and name of the element
     * @param string $label Label for the &lt;label&gt;
     * @param string|null $value Value of the input if any
     * @param string $attrs Attributes like class or data tags
     * @param string|float|int $pre Prepend wrap html or element with class before date Ex: '<div class="wrap">' or '.wrap' or '6'
     * @param string $post Append wrap html or element with class after date Ex: '</div>' Auto closes div if class or int provided in $pre
     */
    function __code( string|array $id, string $label = '', string|null $value = '', string $attrs = '', string|float|int $pre = '', string $post = '' ): string {
        $return = '<script>document.addEventListener(\'DOMContentLoaded\', function () { ace.config.set("basePath", "'. APPURL . 'assets/ext/ace/" ); })</script>';
        $_p = $this->__pre( $pre );
        $p_ = $this->__post( $pre, $post );
        $return .= $_p;
        $return .= $this->__input( 'textarea', $id, $label, $value, '', $attrs . ' data-html-characters style="display:none !important"' );
        $return .= '<div id="'.$id.'_code" style="min-height: 200px"></div>';
        $return .= "<script>document.addEventListener('DOMContentLoaded', function () { let dk = $('[data-key={$id}]'); let {$id}_code = ace.edit('{$id}_code');{$id}_code.session.setMode('ace/mode/html');{$id}_code.session.setValue($('[data-key=\"{$id}\"]').val(),-1);{$id}_code.session.on('change', function(d) {dk.val({$id}_code.getValue())});});</script>"; // $('[data-key=\"{$id}]\"').val();
        $return .= $p_;
        return $return;
    }

    /**
     * Renders rich WYSIWYG editor with hidden &lt;input type="textarea"&gt; element
     * @param string|array $id ID and name of the element
     * @param string $label Label for the &lt;label&gt;
     * @param string|null $value Value of the input if any
     * @param string $attrs Attributes like class or data tags
     * @param string|float|int $pre Prepend wrap html or element with class before date Ex: '<div class="wrap">' or '.wrap' or '6'
     * @param string $post Append wrap html or element with class after date Ex: '</div>' Auto closes div if class or int provided in $pre
     */
    function richtext( string|array $id, string $label = '', string|null $value = '', string $attrs = '', string|float|int $pre = '', string $post = '' ): void {
        echo $this->__richtext( $id, $label, $value, $attrs, $pre, $post );
    }

    /**
     * Renders rich WYSIWYG editor with hidden &lt;input type="textarea"&gt; element
     * @param string|array $id ID and name of the element
     * @param string $label Label for the &lt;label&gt;
     * @param string|null $value Value of the input if any
     * @param string $attrs Attributes like class or data tags
     * @param string|float|int $pre Prepend wrap html or element with class before date Ex: '<div class="wrap">' or '.wrap' or '6'
     * @param string $post Append wrap html or element with class after date Ex: '</div>' Auto closes div if class or int provided in $pre
     */
    function __richtext( string|array $id, string $label = '', string|null $value = '', string $attrs = '', string|float|int $pre = '', string $post = '' ): string {
        $_p = $this->__pre( $pre );
        $p_ = $this->__post( $pre, $post );
        $r = rand( 0, 999999 );
        $return = $_p;
        $return .= $this->__textarea( ( is_array( $id ) ? $id : [ $id . '_' . $r, $id ] ), $label, '', $value, $attrs . ' data-rich-text' );
        $return .= __get_style('https://cdn.jsdelivr.net/npm/trumbowyg/dist/ui/trumbowyg.min.css');
        $return .= __get_script('https://cdn.jsdelivr.net/npm/trumbowyg/dist/trumbowyg.min.js');
        $return .= $p_;
        return $return;
    }

    function content_builder( string|array $id, string $label = '', string $placeholder = '', string|null $value = '', string $attrs = '', string|float|int $pre = '', int|float $height = 400, string $post = '' ): void {
        if( in_array_any( [ 'cms', 'pages' ], FEATURES ) ) {
            $cms = new CMS();
            $cms->content_builder($id, $label, $placeholder, $value, $attrs, $pre, $height, $post);
        }
    }

    function render_builder_content( string|array $content = [] ): void {

    }

    /* function _content_builder( string|array $id, string $label = '', string $placeholder = '', string|null $value = '', string $attrs = '', string|float|int $pre = '', string $post = '' ): string {
        get_style('aio/content_builder');
        get_script('aio/content_builder');
        $random = $this->_random();
        $r = $this->__pre( $pre );
        $r .= $this->_textarea( $id, $label, $placeholder, $value, $attrs.' data-aio-content-builder="#aio_content_builder_wrap_'.$random.'" style="display: none"' );
        $r .= __div( 'aio_content_builder_wrap_'.$random, 'aio_content_builder_wrap', __div( 'content_area_'.$random, 'content_area' ) . __div( 'content_widgets_'.$random, 'content_widgets' ) );
        $r .= $this->_post( $pre, $post );
        return $r;
    } */

    function form_builder( string|array $id, string $label = '', string|null $value = '', string $attrs = '', string|float|int $pre = '', int|float $height = 200, string $post = '' ): void {
        $this->pre( $pre );
        pre( '', 'd', 'div', 'style="display:none"' );
            $this->textarea( $id, $label, '', $value, $attrs . ' data-form-field="'.$id.'"' );
        post();
        pre( '', '', 'div', 'style="height:'.$height.'px" data-form-builder-field="'.$id.'"' );
            get_comp('form_builder');
        post();
        $this->post( $pre, $post );
    }

    /**
     * Returns array into JSON string
     * @param array $data Database rows as array
     * @param string $remove If needed to remove, provide keys separated by ,
     * @return string
     * @author Shaikh <hey@shaikh.dev>
     */
    function __editable_data( array $data = [], string $remove = '' ): string {
        $final = [];
        $remove = explode( ',', $remove );
        //skel( htmlspecialchars_decode( $data['_content'] ) );
        foreach( $data as $k => $v ){
            if( is_numeric( $k ) )
                continue;
            $k = strpos( $k, '_') !== false ? ltrim( strstr($k,'_'), '_' ) : $k;
            if( $k == 'id' ) {
                $cry = Encrypt::initiate();
                $final[ $k ] = defined( 'APPDEBUG' ) && APPDEBUG ? $v : $cry->encrypt( $v );
            } else if( !in_array( $k, $remove ) ){
                $final[ $k ] = !empty( $v ) ? ( !is_array( $v ) ? ( $v != strip_tags( htmlspecialchars_decode( $v ) ) ? htmlspecialchars( $v ) : $v ) : $v ) : ''; //!empty( $v ) && ( $v != strip_tags( htmlspecialchars_decode( $v ) ) ) ? htmlentities( $v ) : $v;
            }
        }
        $final = json_encode( $final );
        //skel( $final );
        return "data-data='{$final}'";
    }

    /**
     * Echo array into JSON string
     * @param array $data Database rows as array
     * @param string $remove If needed to remove, provide keys separated by ,
     * @author Shaikh <hey@shaikh.dev>
     */
    function editable_data( array $data = [], string $remove = '' ): void {
        echo $this->__editable_data( $data, $remove );
    }

    function delete_data( string $table, string $logic ): void {
        $c = Encrypt::initiate();
        echo ' onclick="trash_data(\''.$c->encrypt('trash_data_ajax').'\',\''.$c->encrypt( $table ).'\',\''.$c->encrypt( $logic ).'\')"';
    }

    /**
     * Renders HTML parameters for automated data saving
     * @param string $attr
     * @param string $target Database name if the data is supposed to store directly to db or ajax function name with _ajax at the end
     * @param string $data Data attribute of inputs to gather data
     * @param string $pre Pre-wrap string for database table columns
     * @param array $hidden Hidden data for Database
     * @param string $success_alert Text to notify upon successfully storing data
     * @param string $callback A JS Function to callback on results
     * @param string $confirm A confirmation popup will execute further code
     * @param string $redirect Redirect user to page on successful submission
     * @param string $validator Frontend JS script to add custom validation to the form data
     * @param string $reset_fields Reset input fields with data attribute (Tip: Use 1 to reset provided data fields)
     */
    function pre_process( string $attr = '', string $target = '', string $data = '', string $pre = '', array $hidden = [], string $success_alert = '', string $callback = '', string $confirm = '', string $redirect = '', string $validator = '', string $reset_fields = '' ): void {
        echo $this->__pre_process( $attr, $target, $data, $pre, $hidden, $success_alert, $callback, $confirm, $redirect, $validator, $reset_fields );
    }

    /**
     * @param string $attr Wrapper element attribute
     * @param string $target Database name if the data is supposed to store directly to db or ajax function name with _ajax at the end
     * @param string $data Data attribute of inputs to gather data
     * @param array $hidden Hidden data for Database
     * @param string $pre Pre-wrap string for database table columns
     * @param string $success_alert Text to notify upon successfully storing data
     * @param string $callback A JS Function to callback on results
     * @param string $confirm A confirmation popup will execute further code
     * @param string $redirect Redirect user to page on successful submission
     * @param string $validator Frontend JS script to add custom validation to the form data
     * @param string $reset_fields Reset input fields with data attribute (Tip: Use 1 to reset provided data fields)
     * @return string
     */
    function __pre_process( string $attr = '', string $target = '', string $data = '', string $pre = '', array $hidden = [], string $success_alert = '', string $callback = '', string $confirm = '', string $redirect = '', string $validator = '', string $reset_fields = '' ): string {
        global $options;
        $c = Encrypt::initiate();
        $r = $attr == '_' ? '' : '<div '.$attr.' ';
        if( APPDEBUG ) {
            $target = !empty( $target ) ? $target : 'process_data_ajax';
            $hidden = json_encode( $hidden );
        } else {
            $target = !empty( $target ) ? $c->encrypt( $target ) : $c->encrypt( 'process_data_ajax' );
            $hidden = $c->encrypt_array( $hidden );
        }
        $t = ' data-t="'.$target.'"';
        $notify = $options['notify_time'] ?? 4;
        $reload = $options['reload_time'] ?? 4;
        $nt = $notify > 0 ? ' data-notify="'.$notify.'"' : '';
        $rl = $reload > 0 ? ' data-reload="'.$reload.'"' : '';
        $d = !empty( $data ) ? ' data-data="'.$data.'"' : '';
        $p = !empty( $pre ) ? ' data-pre="'.$pre.'"' : '';
        $h = !empty( $hidden ) ? ' data-h=\''.$hidden.'\'' : '';
        $st = !empty( $success_alert ) ? ' data-success="'.$success_alert.'"' : '';
        $cb = !empty( $callback ) ? ( str_contains( $callback, '_ajax' ) ? ' data-callback="'.$c->encrypt($callback).'"' : ' data-callback="'.$callback.'"') : '';
        $v = !empty( $validator ) ? ' data-validation="'.$validator.'"' : '';
        $rd = !empty( $redirect ) ? ' data-redirect="'.$redirect.'"' : '';
        $rf = !empty( $reset_fields ) ? ( $reset_fields == 1 ? ' data-reset="'.$data.'"' : ' data-reset="'.$reset_fields.'"' ) : '';
        $cn = !empty( $confirm ) ? ' data-confirm="'.$confirm.'"' : '';
        $r .= $t.$nt.$rl.$d.$p.$h.$st.$cb.$v.$rd.$cn.$rf;
        $r .= $attr == '_' ? '' : '>';
        return $r;
    }

    /**
     * Renders Form
     * @param array $fields Array of an array of field inputs [ [ 'type' => 'textarea', 'id' => 'name', 'title' => 'Contact Name', 'place' => 'Enter name...', 'attr' => 'data-con', 'col' => 8 ] ]
     * @param string $form_type Type of wrap around the form ( 'get' or 'post' or 'row' or 'settings' )
     * @param string $data_attr Common data attribute for all inputs
     */
    function form( array $fields = [], string $form_type = '', string $data_attr = '', string $group_by = '', string $wrap_class = '' ): void {
        echo $this->__form( $fields, $form_type, $data_attr, $group_by, $wrap_class );
    }

    /**
     * Returns Form
     * @param array $fields Array of an array of field inputs [ [ 'type' => 'textarea', 'id' => 'name', 'title' => 'Contact Name', 'place' => 'Enter name...', 'attr' => 'data-con', 'col' => 8 ] ]
     * @param string $form_type Type of wrap around the form ( 'get' or 'post' or 'row' )
     * @param string $data_attr Common data attribute for all inputs
     */
    function __form( array $fields = [], string $form_type = '', string $data_attr = '', string $group_by = '', string $wrap_class = '' ): string {
        $return = in_array( $form_type, [ 'get', '$_GET' ] ) ? '<form method="get" class="'.$wrap_class.'">' : ( in_array( $form_type, [ 'post', '$_POST' ] ) ? '<form method="post" class="'.$wrap_class.'">' : ( in_array( $form_type, [ 'row', 'r' ] ) ? __d( 'form row ' . $wrap_class ) : ( $form_type == 'settings' ? __d( 'settings form ' . $wrap_class ) : ( in_array( $form_type, [ 'steps', 's' ] ) ? __d( 'steps form ' . $wrap_class ) : __d( 'form ' . $wrap_class ) ) ) ) );
        // x = count( fields ) && $col == 12 ?
        //if( $form_type == 'steps' || $form_type == 's' ) {
            //div( 'step_heads', $this->_form(  ) )
        //}
        $steps = [];
        $class = '';
        foreach( $fields as $f ) {
            $type = $f['type'] ?? ( $f['ty'] ?? ( $f['t'] ?? 'text' ) );
            //skel( $type );
            $label = $f['label'] ?? ( $f['l'] ?? ( $f['title'] ?? ( $f['name'] ?? ( $f['n'] ?? '' ) ) ) );
            //$return = '';
            $id = $f['id'] ?? ( $f['i'] ?? '' );
            $place = $f['place'] ?? ($f['placeholder'] ?? ( $f['p'] ?? $label));
            //$val = $f['value'] ?? ($method == 'POST' ? ($_POST[$id] ?? '') : ($_GET[$id] ?? ''));
            $val = $f['value'] ?? ( $f['va'] ?? ( $f['v'] ?? ( $_POST[$id] ?? ( $_GET[$id] ?? '' ) ) ) );
            $data = !empty( $data_attr ) ? ' data-'.$data_attr.' ' : '';
            $attrs = $data . ( $f['attr'] ?? ($f['a'] ?? '') );
            $required = $f['required'] ?? ( $f['r'] ?? '' );
            $required ? ( $attrs .= ' required' ) : '';
            $pre = $form_type == 'settings' ? '<div class="setting_set">' : ( $f['pre'] ?? ($f['col'] ?? ( $f['c'] ?? ( $f['_p'] ?? ( $form_type == 'row' || $form_type == 'r' ? 12 : '<div>' ) ) )) );
            $class = $f['class'] ?? ( $f['c'] ?? '' );
            $post = $f['post'] ?? ( $f['p_'] ?? $this->__post( $class ) );
            $post = !empty( $pre ) && empty( $post ) ? __post() : $post;
            if( in_array( $type, [ 'accordion', 'acc', 'a' ] ) ) {
                $return .= __accordion( $label, $this->__form( $f['fields'] ?? $f['form'], $type, $data_attr ) );
            } else if( in_array( $type, [ 'el', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'div', 'span', 'i', 'button' ] ) ) {
                $content = $f['content'] ?? ( $f['value'] ?? ( $f['v'] ?? '' ) );
                $wrap = $f['wrap'] ?? ( $f['wc'] ?? '' );
                $return .= __el( $type, $class, $content );
            } else if( in_array( $type, [ 'column', 'col' ] ) ) {
                $class = is_numeric( $class ) ? $this->__col( $class ) : $class;
                $sub_type = $f['sub_type'] ?? ( $f['type'] ?? 'row' );
                $col_fields = $f['fields'] ?? ( $f['form'] ?? ( $f['f'] ?? [] ) );
                $return .= !empty( $col_fields ) ?  __div( 'col '. $class, $this->__form( $col_fields, $sub_type, $data_attr ) ) : __div( $class );
            } else if( in_array( $type, [ 'row', 'r' ] ) ) {
                $class = $f['class'] ?? ( $f['c'] ?? '' );
                $return .= __div( 'col-12 '. $class, $this->__form( $f['fields'] ?? $f['form'], $type, $data_attr ) );
            } else if( in_array( $type, [ 'row_start', 'rs', '_r' ] ) ) {
                $return .= __pre( '', 'row' );
            } else if( in_array( $type, [ 'row_end', 're', 'r_' ] ) ) {
                $return .= '</div>';
            } else if( in_array( $type, [ 'steps', 'step', 'st' ] ) ) {
                $ico = $f['icon'] ?? ( $f['ico'] ?? ( $f['i'] ?? '' ) );
                $ic = $f['icon_class'] ?? ( $f['ic'] ?? 'mat-ico' );
                $color = $s['color'] ?? ( $s['c'] ?? '' );
                $step_fields = $f['fields'] ?? ( $f['form'] ?? ( $f['f'] ?? [] ) );
                $style = $f['form_type'] ?? ( $f['fields_style'] ?? ( $f['type'] ?? ( $f['style'] ?? ( $f['ft'] ?? ( $f['fs'] ?? 'row' ) ) ) ) );
                if( is_array( $step_fields ) && !empty( $step_fields ) ) {
                    $steps[] = [ 'title' => $label, 'icon' => $ico, 'icon_class' => $ic, 'color' => $color, 'content' => $this->__form( $step_fields, $style, $data_attr ) ]; //$this->_form( $step_fields )
                }
            } else if( in_array( $type, [ 'select', 'select2', 'dropdown', 's', 's2', 'd' ] ) ) {
                $options = $f['options'] ?? ( $f['os'] ?? ( $f['o'] ?? [] ) );
                $value = $_POST[ $id ] ?? ( $_GET[ $id ] ?? $val );
                $keyed = $f['keyed'] ?? ( $f['k'] ?? 0 );
                $trans = $f['translate'] ?? ( $f['tr'] ?? 0 );
                $attrs = isset( $f['multiple'] ) || isset( $f['m'] ) ? $attrs . ' multiple' : $attrs;
                $return .= $this->__select2( $id, $label, $place, $options, $value, $attrs, $pre, $keyed, $trans, $post );
            } else if( in_array( $type, [ 'rich', 'richtext', 'r' ] ) ) {
                $return .= $this->__richtext( $id, $label, $val, $attrs, $pre, $post );
            //} else if( in_array( $type, [ 'content_builder', 'content_build', 'content', 'cb' ] ) ) {
                //$return .= $this->_content( $id, $label, $place, $val, $attrs, $pre, $post );
            } else if( $type == 'date' || $type == 'dt' ) {
                $range = $f['range'] ?? ( $f['r'] ?? 0 );
                $min = $f['min'] ?? '';
                $max = $f['max'] ?? '';
                $multiple = $f['multiple'] ?? ( $f['m'] ?? 0 );
                $pos = $f['position'] ?? ( $f['dp'] ?? ( $f['pos'] ?? ( $f['d_p'] ?? 'top center' ) ) );
                $return .= $this->__date( $id, $label, $place, $val, $attrs, $pos, $pre, $range, $multiple, '', $min, $max, $post );
            } else if( in_array( $type, [ 'range', 'r', 'slider' ] ) ) {
                $min = $f['min'] ?? '';
                $max = $f['max'] ?? '';
                $attrs .= !empty( $min ) ? ' min="'.$min.'"' : '';
                $attrs .= !empty( $max ) ? ' max="'.$max.'"' : '';
                $return .= $this->__input( 'range', $id, $label, $place, $val, $attrs, $pre, $post );
            } else if( in_array( $type, [ 'slide', 'slides', 'toggle', 't' ] ) ) {
                $off_text = $f['off'] ?? ( $f['no'] ?? '' );
                $on_text = $f['on'] ?? ( $f['yes'] ?? '' );
                $return .= $type == 'slide' ? $this->__slide( $id, $label, $off_text, $on_text, $val, '', $attrs, $pre, $post ) : $this->__checkboxes( $id, $label, );
            } else if( $type == 'color' || $type == 'cl' ) {
                $border = $f['border'] ?? ( $f['b'] ?? '' );
                $preview = $f['preview'] ?? ( $f['view'] ?? '' );
                $return .= $this->__color( $id, $label, $place, $val, $attrs, $pre, $border, $preview, $post );
            } else if( in_array( $type, [ 'checkboxes', 'radios', 'checkbox', 'radio', 'cb', 'r' ] ) ) {
                $values = $f['values'] ?? ( $f['options'] ?? ( $f['os'] ?? ( $f['o'] ?? ( $f['v'] ?? [] ) ) ) );
                //skel( $f );
                $checked = $f['checked'] ?? ( $f['check'] ?? ( $f['selected'] ?? ( $f['s'] ?? ( $f['v'] ?? '' ) ) ) );
                $label_first = $f['label_first'] ?? ( $f['lf'] ?? 0 );
                $inputs_pre = $f['inputs_pre'] ?? ( $f['i_p'] ?? ( $f['_ip'] ?? ( $f['_i'] ?? '' ) ) );
                $inputs_pre = is_numeric( $inputs_pre ) ? '.col-12 col-md-'.$inputs_pre : $inputs_pre;
                $inputs_pre .= empty( $inputs_pre ) ? '.check_set' : ' check_set';
                $inputs_post = $f['inputs_post'] ?? ( $f['ip_'] ?? ( $f['i_'] ?? '' ) );
                $inputs_wrap = $f['inputs_wrap'] ?? ( $f['iw'] ?? ( is_numeric( $inputs_pre ) || is_float( $inputs_pre ) ? 'row' : '' ) );
                $return .= $type == 'checkboxes' ? $this->__checkboxes( $id, $label, $values, $checked, $attrs, $label_first, $pre, $post, $inputs_wrap, $inputs_pre, $inputs_post ) : $this->__radios( $id, $label, $values, $checked, $attrs, $label_first, $pre, $post, $inputs_wrap, $inputs_pre, $inputs_post );
            } else if( $type == 'phone' || $type == 'p' ) {
                $id_2 = $f['id2'] ?? ( $f['i2'] ?? '' );
                $label_2 = $f['label2'] ?? ( $f['l2'] ?? ( $f['title2'] ?? ( $f['name2'] ?? ( $f['n2'] ?? '' ) ) ) );
                $place_2 = $f['place2'] ?? ($f['placeholder2'] ?? ( $f['p2'] ??= ''));
                $val_2 = $f['value2'] ?? ( $f['va2'] ?? ( $f['v2'] ?? ( $_POST[$id_2] ?? ( $_GET[$id_2] ?? '' ) ) ) );
                $return .= $this->__phone( $id, $id_2, $label, $label_2, $place, $place_2, $val, $val_2, $attrs, $pre, $post );
            } else if( $type == 'captcha' || $type == 'cap' ) {
                $bg_rgb = $f['bg_color'] ?? ( $f['bg_rgb'] ?? ( $f['bg'] ?? [] ) );
                $text_color = $f['text_color'] ?? ( $f['text_color_rgb'] ?? ( $f['text_rgb'] ?? ( $f['tc'] ?? [] ) ) );
                $length = $f['max_length'] ?? ( $f['length'] ?? ( $f['l'] ?? 5 ) );
                $return .= $this->__captcha( $id, $label, $place, $attrs, $pre, $post, $length, $text_color, $bg_rgb );
            } else if( in_array( $type, [ 'uploads', 'upload', 'files', 'file', 'u', 'f' ] ) ) {
                $btn_label = $f['btn_label'] ?? ( $f['button'] ?? ( $f['b'] ?? ( $f['placeholder'] ?? ( $f['place'] ?? ( $f['p'] ?? ( $label ?? 'Upload' ) ) ) ) ) );
                $multiple = $f['multiple'] ?? ( $f['m'] ?? ( $type == 'uploads' ? 1 : ( $type == 'files' ? 1 : 0 ) ) );
                $history = $f['history'] ?? ( $f['h'] ?? 0 );
                $btn_class = $f['btn_class'] ?? ( $f['h'] ?? 0 );
                $exts = $f['extensions'] ?? ( $f['exts'] ?? ( $f['ex'] ?? ( $f['e'] ?? '' ) ) );
                $size = $f['size'] ?? ( $f['s'] ?? '.2' );
                $deletable = $f['deletable'] ?? ( $f['delete'] ?? ( $f['del'] ?? ( $f['d'] ?? 0 ) ) );
                $path = $f['path'] ?? '';
                $return .= $this->__upload( $id, $label, $btn_label, $val, $multiple, $history, $btn_class, $attrs, $exts, $size, $deletable, $path, $pre, $post );
            } else if( in_array( $type, [ 'google_maps', 'gmaps', 'maps', 'map', 'm' ] ) ) {
                $latitude_field = $f['latitude_field'] ?? ( $f['latitude'] ?? ( $f['lat'] ?? ( $f['x'] ?? '' ) ) );
                $longitude_field = $f['longitude_field'] ?? ( $f['longitude'] ?? ( $f['long'] ?? ( $f['y'] ?? '' ) ) );
                $address_field = $f['address_field'] ?? ( $f['address'] ?? ( $f['addr'] ?? ( $f['adr'] ?? '' ) ) );
                $area_field = $f['area_field'] ?? ( $f['area'] ?? ( $f['ar'] ?? '' ) );
                $city_field = $f['city_field'] ?? ( $f['city'] ?? ( $f['ct'] ?? '' ) );
                $country_field = $f['country_field'] ?? ( $f['country'] ?? ( $f['co'] ?? '' ) );
                $coordinates = $f['coordinates'] ?? ( $f['gps'] ?? ( $f['cor'] ?? '' ) );
                $height = $f['height'] ?? ( $f['hi'] ?? ( $f['h'] ?? 200 ) );
                $latitude_value = $f['latitude_value'] ?? ( $f['lat_val'] ?? ( $f['lat_v'] ?? ( $f['ltv'] ?? ( $f['xv'] ?? '' ) ) ) );
                $longitude_value = $f['longitude_value'] ?? ( $f['long_val'] ?? ( $f['long_v'] ?? ( $f['lgv'] ?? ( $f['yv'] ?? '' ) ) ) );
                $zoom = $f['map_zoom'] ?? ( $f['zoom'] ?? ( $f['zm'] ?? ( $f['z'] ?? 13 ) ) );
                $type = $f['map_type'] ?? ( $f['mt'] ?? 'terrain' );
                $show_search = $f['show_search'] ?? ( $f['search'] ?? ( $f['ss'] ?? 1 ) );
                $show_full = $f['show_full'] ?? ( $f['full'] ?? ( $f['sf'] ?? 1 ) );
                //skel( $show_full );
                $show_zoom = $f['show_zoom'] ?? ( $f['zoom'] ?? ( $f['sz'] ?? 1 ) );
                $show_type = $f['show_type'] ?? ( $f['type'] ?? ( $f['st'] ?? 0 ) );
                $return .= $this->__map( $latitude_field, $longitude_field, $address_field, $area_field, $city_field, $country_field, $coordinates, $pre, $height, $latitude_value, $longitude_value, $show_search, $show_full, $zoom, $show_zoom, $type, $show_type, $post );
            } else if( $type == 'code' || $type == 'c' ) {
                $return .= $this->__code( $id, $label, $val, $attrs, $pre, $post );
            } else if( $type == 'break' || $type == 'br' ) {
                $c = $f['class'] ?? ( $f['c'] ?? 12 );
                $c = 'col-md-'.$c . ( str_contains( $c, 'col-' ) ? $c : ' col-12' );
                $return .= __div( $c, __el( 'hr', 'break' ) );
            } else {
                //skel( $pre );
                //skel( !empty( $pre ) && empty( $f['post'] ) );
                $return .= $this->__input( $type, $id, $label, $place, $val, $attrs, $pre, $post );
            }
        }
        //skel( $steps );
        if( count( $fields ) == count( $steps ) ) {
            $return .= __steps( $steps, $form_type . ' ' . $class );
        }
        $return .= in_array( $form_type, [ 'get', 'post', 'form' ] ) ? '</form>' : '</div>';
        //skel( count( $fields ) );
        return $return;
    }

    /**
     * Renders HTML for Options Auto Save
     * @param string $attr
     * @param string $data
     * @param array|string $autoload
     * @param array|string $unique
     * @param array|string $encrypt
     * @param string $success_text
     * @param string $callback
     * @param string $confirm
     * @return void
     */
    function option_params( string $attr = '', string $data = '', array|string $autoload = [], array|string $unique = [], array|string $encrypt = [], string $success_text = 'Successfully Updated Preferences!', string $callback = '', string $confirm = '' ): void {
        $h = [];
        !empty( $autoload ) ? $h['autoload'] = $autoload : '';
        !empty( $unique ) ? $h['unique'] = $unique : '';
        !empty( $encrypt ) ? $h['encrypt'] = $encrypt : '';
        $this->pre_process( $attr, '', $data, '', $h, $success_text, $callback, $confirm );
        //skel( $h );
    }

    /**
     * Returns HTML for Options Auto Save
     * @param string $attr
     * @param string $data
     * @param int $notify
     * @param int $reload
     * @param array|string $autoload
     * @param array|string $unique
     * @param array|string $encrypt
     * @param string $success_text
     * @param string $callback
     * @param string $confirm
     * @return string
     */
    function __option_params( string $attr = '', string $data = '', int $notify = 0, int $reload = 0, array|string $autoload = [], array|string $unique = [], array|string $encrypt = [], string $success_text = 'Successfully Updated Preferences!', string $callback = '', string $confirm = '' ): string {
        $h = [];
        !empty( $autoload ) ? $h['autoload'] = $autoload : '';
        !empty( $unique ) ? $h['unique'] = $unique : '';
        !empty( $encrypt ) ? $h['encrypt'] = $encrypt : '';
        return $this->__pre_process( $attr, '', $data, '', $h, $success_text, $callback, $confirm );
        //skel( $h );
    }

    function option_params_wrap( string $data = '', string $attr = '', array|string $autoload = [], array|string $unique = [], array|string $encrypt = [], string $success_text = 'Successfully Updated Preferences!', string $callback = '', string $confirm = '' ): void {
        $h = [];
        !empty( $autoload ) ? $h['autoload'] = $autoload : '';
        !empty( $unique ) ? $h['unique'] = $unique : '';
        !empty( $encrypt ) ? $h['encrypt'] = $encrypt : '';
        //echo '<div class="row"';
        $this->pre_process( $attr, 'process_options_ajax', $data, '', $h, $success_text, $callback, $confirm );
        //echo '>';
    }

    /**
     * Renders HTML to trigger process data
     * @param string $text Button text
     * @param string $class Button class
     * @param string $attr Additional attributes to button
     * @param string $action Default AJAX Action
     * @param string|int $pre Pre Wrap HTML or Bootstrap Column
     * @param string|int $post Post Wrap HTML
     * @param string $element HTML Element
     * @param string $confirm Message to show as confirmation before process
     * @param string $icon Icon text to display
     * @param string $icon_class Icon class to display
     */
    function process_trigger( string $text = '', string $class = '', string $attr = '', string $action = '', string|int $pre = '', int|string $post = '', string $element = 'button', string $confirm = '', string $icon = '', string $icon_class = '' ): void {
        echo $this->__process_trigger( $text, $class, $attr, $action, $pre, $post, $element, $confirm, $icon, $icon_class );
    }

    /**
     * Returns HTML to trigger process data
     * @param string $text Button text
     * @param string $class Button class
     * @param string $attr Additional attributes to button
     * @param string $action Default AJAX Action
     * @param string|int $pre Pre Wrap HTML or Bootstrap Column
     * @param string|int $post Post Wrap HTML
     * @param string $element HTML Element
     * @param string $confirm Message to show as confirmation before process
     * @param string $icon Icon text to display
     * @param string $icon_class Icon class to display
     */
    function __process_trigger( string $text = 'Save', string $class = '', string $attr = '', string $action = '', string|int $pre = '', int|string $post = '', string $element = 'button', string $confirm = '', string $icon = '', string $icon_class = '' ): string {
        $_p = $this->__pre( $pre );
        $p_ = $this->__post( $pre, $post );
        $c = Encrypt::initiate();
        global $options;
        $icon_class = $icon_class . ' ' . ( $options['icon_class'] ?? 'mico' );
        //$action = empty( $action ) ? 'process_data_ajax' : $action;
        $a = $attr;
        $a .= !empty( $action ) ? ' data-action="'. ( APPDEBUG ? $action : $c->encrypt($action) ) .'"' : '';
        $a .= $confirm !== '' ? ' onclick="if(confirm(\''.$confirm.'\')){process_data(this);}else{event.stopPropagation();event.preventDefault();}"' : ' onclick="process_data(this)"';
        //$a .= $reload > 0 ? ' data-reload="'.$reload.'"' : '';
        //$a .= $notify > 0 ? ' data-notify="'.$notify.'"' : '';
        $text .= __el( 'span', 'loader' );
        if( !empty( $icon ) ) {
            $text .= __el( 'span', $icon_class . ' ico ' . $icon, $icon );
        } else {
            $save_ico = $options['ico_save'] ?? 'save';
            $text .= __el( 'span', $icon_class . ' ico ' . $save_ico, $save_ico );
        }
        return $_p . __el( $element, $class . ' save', $text, '', $a ) . $p_;
        //return $_p.'<'.$element.$a.' class="'.$class.'" '.$attr.'><span class="loader"></span>'.T( $text ).'</'.$element.'>'.$p_;
    }

    function post_process(): void {
        echo $this->__post_process();
    }

    function __post_process(): string {
        return '</div>';
    }

    /**
     * Renders HTML to process options
     * @param string $text Button text
     * @param string $class Button class
     * @param string $attr Additional attributes to button
     * @param string|int $pre Pre Wrap HTML or Bootstrap Column
     * @param string|int $post Post Wrap HTML
     * @param string $element HTML Element
     * @param string $confirm Message to show as confirmation before process
     */
    function process_options( string $text = '', string $class = '', string $attr = '', string|int $pre = '', int|string $post = '', string $element = 'button', string $confirm = '' ): void {
        $this->process_trigger( $text, $class, $attr, 'process_options_ajax', $pre, $post, $element, $confirm );
    }

    /**
     * Renders HTML to view page
     * @param string $url URL to visit
     * @param string $html HTML either button or div or i
     * @param string $text Text to display
     * @param string $class Class
     * @param string $attr Additional attributes
     * @param string $i_class Applied class to i element and places before text
     * @param string $i_text Text for i element
     * @param string|int $pre Pre Wrap HTML or Bootstrap Column
     * @param string|int $post Post Wrap HTML
     */
    function view_html( string $url = '', string $html = 'div', string $text = '', string $class = 'grad', string $attr = '', string $i_class = '', string $i_text = '', string|int $pre = '', string|int $post = '' ): void {
        echo $this->__view_html( $url, $html, $text, $class, $attr, $i_class, $i_text, $pre, $post );
    }

    /**
     * Returns HTML to view page
     * @param string $url URL to visit
     * @param string $html HTML either button or div or i
     * @param string $text Text to display
     * @param string $class Class
     * @param string $attr Additional attributes
     * @param string $i_class Applied class to i element and places before text
     * @param string $i_text Text for i element
     * @param string|int $pre Pre Wrap HTML or Bootstrap Column
     * @param string|int $post Post Wrap HTML
     */
    function __view_html( string $url = '', string $html = 'div', string $text = '', string $class = 'grad', string $attr = '', string $i_class = '', string $i_text = '', string|int $pre = '', string|int $post = '' ): string {
        $_p = $this->__pre( $pre );
        $p_ = $this->__post( $pre, $post );
        global $options;
        $icon_class = $options['icon_class'] ?? 'mico';
        $i_class = ( !empty( $i_class ) ? $i_class : $icon_class ) . ' ico';
        $edit_icon = $options['ico_view'] ?? 'file_open';
        $i_text = !empty( $i_text ) ? $i_text : $edit_icon;
        $i = !empty( $i_class ) ? __el( 'i', $i_class, $i_text ) : '';
        $title = str_contains( $attr, 'title' ) ? '' : 'title="'.T('View').'"';
        //skel( $html );
        //skel( $p_ );
        return $_p.'<'.$html.' data-href="'.$url.'" '.$title.' class="'.$class.'" '.$attr.'>'.$i. $text .'</'.$html.'>'.$p_;
    }

    /**
     * Renders HTML to open modal to edit data
     * @param string $element Modal element to open
     * @param array $array Data JSON array
     * @param string $html HTML either button or div or i
     * @param string $text Text to display
     * @param string $class Class
     * @param string $attr Additional attributes
     * @param string $i_class Class for i element positioned before text
     * @param string $i_text Text for i element
     * @param string|int $pre Pre Wrap HTML or Bootstrap Column
     * @param string|int $post Post Wrap HTML
     */
    function edit_html( string $element = '.modal', array $array = [], string $html = 'div', string $text = '', string $class = '', string $attr = '', string $i_class = '', string $i_text = '', string|int $pre = '', string|int $post = '' ): void {
        echo $this->__edit_html( $element, $array, $html, $text, $class, $attr, $i_class, $i_text, $pre, $post );
    }

    /**
     * Returns HTML to open modal to edit data
     * @param string $element Modal element to open
     * @param array $array Data JSON array
     * @param string $html HTML either button or div or i
     * @param string $text Text to display
     * @param string $class Class
     * @param string $attr Additional attributes
     * @param string $i_class Class for i element positioned before text
     * @param string $i_text Text for i element
     * @param string|int $pre Pre Wrap HTML or Bootstrap Column
     * @param string|int $post Post Wrap HTML
     */
    function __edit_html( string $element = '.modal', array $array = [], string $html = 'div', string $text = '', string $class = '', string $attr = '', string $i_class = '', string $i_text = '', string|int $pre = '', string|int $post = '' ): string {
        //$c = Encrypt::initiate();
        global $options;
        $icon_class = $options['icon_class'] ?? 'mico';
        $i_class = ( !empty( $i_class ) ? $i_class : $icon_class ) . ' ico';
        $edit_icon = $options['ico_edit'] ?? 'border_color';
        $i_text = !empty( $i_text ) ? $i_text : $edit_icon;
        $_p = $this->__pre( $pre );
        $p_ = $this->__post( $pre, $post );
        //$post = !empty( $post ) ? $post : ( !empty( $pre ) ? '</div>' : '' );
        $i = !empty( $i_class ) || !empty( $i_text ) ? __i( $i_class, $i_text ) : '';
        $title = str_contains( $attr, 'title=' ) ? '' : 'title="'.T('Edit').'"';
        $data = $title . ' data-edit-action class="'.$class.' edit" onclick="edit_data(this,\''.$element.'\')" ' . $this->__editable_data($array) . $attr;
        return $_p . __el( $html, '', $i. $text , '', $data ) . $p_;
    }

    /**
     * Renders HTML to delete record from database
     * @param string $table Table name
     * @param string $logic Where column value equals to
     * @param string $html HTML either button or div or i
     * @param string $text Text to display
     * @param string $class Class
     * @param string $attr Additional attributes
     * @param string $i_class Class for i element positioned before text
     * @param string $confirmation Text for confirmation
     * @param string $i_text Text for i element
     * @param string|int $pre Pre Wrap HTML or Bootstrap Column
     * @param string|int $post Post Wrap HTML
     */
    function trash_html( string $table, string $logic, string $html = 'div', string $text = '', string $class = '', string $attr = '', string $confirmation = 'Are you sure to Delete ?', string $i_class = '', string $i_text = '', string|int $pre = '', string|int $post = '' ): void {
        echo $this->__trash_html( $table, $logic, $html, $text, $class, $attr, $confirmation, $i_class, $i_text, $pre, $post );
    }

    /**
     * Returns HTML to delete record from database
     * @param string $table Table name
     * @param string $logic Where column value equals to
     * @param string $html HTML either button or div or i
     * @param string $text Text to display
     * @param string $class Class
     * @param string $attr Additional attributes
     * @param string $i_class Class for i element positioned before text
     * @param string $confirmation Text for confirmation
     * @param string $i_text Text for i element
     * @param string|int $pre Pre Wrap HTML or Bootstrap Column
     * @param string|int $post Post Wrap HTML
     */
    function __trash_html( string $table, string $logic, string $html = 'div', string $text = '', string $class = '', string $attr = '', string $confirmation = 'Are you sure to Delete ?', string $i_class = '', string $i_text = '', string|int $pre = '', string|int $post = '' ): string {
        global $options;
        $icon_class = $options['icon_class'] ?? 'mico';
        $i_class = ( !empty( $i_class ) ? $i_class : $icon_class ) . ' ico';
        $trash_icon = $options['ico_delete'] ?? 'delete';
        $i_text = !empty( $i_text ) ? $i_text : $trash_icon;
        $_p = $this->__pre( $pre );
        $p_ = $this->__post( $pre, $post );
        //$post = !empty( $post ) ? $post : ( !empty( $pre ) ? '</div>' : '' );
        $c = Encrypt::initiate();
        $target = APPDEBUG ? $table : $c->decrypt( $table );
        $action = str_contains( $target, '_ajax' ) ? $target : 'trash_data_ajax';
        $action = APPDEBUG ? $action : $c->encrypt( $action );
        $logic = APPDEBUG ? $logic : $c->encrypt( $logic );
        //$logic = str_replace( '\'', '\"', $logic );
        //skel( $logic );
        $table = str_contains( $target, '_ajax' ) ? '' : ( APPDEBUG ? $table : $c->encrypt($table) );
        $i = !empty( $i_class ) ? __i( $i_class, $i_text ) : '';
        $attr .= !empty( $confirmation ) ? ' data-confirm="'.$confirmation.'"' : '';
        $title = str_contains( $attr, 'title' ) ? '' : 'title="'.T('Delete').'"';
        $notify_time = 2;
        $reload_time = 2;
        return $_p . __el( $html, $class.' delete', $i . $text , '', $title.' '.$attr.' data-delete-action onclick="trash_data(this,\''.$action.'\',\''.$table.'\',\''.$logic.'\','.$notify_time.','.$reload_time.')"' ) . $p_;
        //return $_p.'<'.$html.'  class="'.$class.'" '.$title.' '.$attr.'>'.$i.T( $text ).'</'.$html.'>'.$p_;
    }

    // TODO: disable_html() similar to trash_html()

    /**
     * Renders HTML to disable record from database
     * @param string $table Table name
     * @param array $keys Data keys
     * @param array $values Data values
     * @param string $logic Where column value equals to
     * @param string $html HTML either button or div or i
     * @param string $text Text to display
     * @param string $class Class
     * @param string $attr Additional attributes
     * @param string $i_class Applied class to i element and places before text
     * @param int $notify_time Time to notify
     * @param int $reload_time Time to Reload
     * @param string $confirmation Text for confirmation
     * @param string|int $pre Pre Wrap HTML or Bootstrap Column
     * @param string|int $post Post Wrap HTML
     */
    function update_html( string $table, array $keys, array $values, string $logic, string $html = 'div', string $text = '', string $class = '', string $attr = '', string $i_class = '', int $notify_time = 2, int $reload_time = 2, string $confirmation = '', string|int $pre = '', string|int $post = '' ): void {
        $_p = $this->__pre( $pre );
        $p_ = $this->__post( $pre, $post );
        //$post = !empty( $post ) ? $post : ( !empty( $pre ) ? '</div>' : '' );
        $c = Encrypt::initiate();
        $i = !empty( $i_class ) ? __el( 'i', $i_class ) : '';
        $attr .= !empty( $confirmation ) ? ' data-confirm="'.$confirmation.'"' : '';
        echo $_p.'<'.$html.' onclick="update_data(this,\''.$c->encrypt('update_data_ajax').'\',\''.$c->encrypt($table).'\',\''.$c->encrypt_array($keys).'\',\''.$c->encrypt_array($values).'\',\''.$c->encrypt($logic).'\','.$notify_time.','.$reload_time.')" class="'.$class.'" title="'.T('Update').'" '.$attr.'>'.$i. $text .'</'.$html.'>'.$p_;
    }

    function steps( array $steps = [], string $active = '', bool $translate = true ): void {
        _d( 'steps_head' );
        if( !empty( $steps ) ) {
            foreach( $steps as $step ) {
                $title = $translate ? T( $step ) : $step;
                $id = strtolower( str_replace( ' ', '_', $step ) );
                $class = $active == $step || $active == $id ? 'step on' : 'step';
                div( $class, $title, '', 'data-step="#'.$id.'"' );
            }
        }
        d_();
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
     * @param array $filter_params [ 'type', 'id', 'label / title', 'place', 'value', 'attr', 'pre', 'options', 'keyed' ]
     * @param string $clear_url Page path excluding APPURL Ex: user/payments
     * @param string $method
     * @param string $filter_text
     * @param string $class
     */
    function filters( array $filter_params = [], string $clear_url = '', string $method = 'GET', string $filter_text = 'Filter', string $class = '', string|int $inputs_class_col = 10, string|int $actions_class_col = 2 ): void {
        $clear_url = !empty( $clear_url ) ? APPURL . $clear_url : APPURL . PAGEPATH;
        //echo '<div class="row"><div class="col-12 col-md-10 inputs">';
        pre( '', 'filters_wrap '.$class );
            pre( '', 'auto_filters', 'form', 'method="'.$method.'"' );
                _r( 'jcc' );
                    _c( $inputs_class_col, 'inputs' );
                        $this->form( $filter_params, 'row' );
                    c_();
                    _c( $actions_class_col, 'filter_actions' );
                        _r();
                            _c( 6 );
                                b( 'filter mat-ico-after', $filter_text );
                            c_();
                            _c( 6 );
                                a( $clear_url, T('Clear'), 'btn clear', T('Clear search filters.') );
                            c_();
                        r_();
                    c_();
                r_();
            post( 'form' );
        post();
        //echo '<div class="col-12 col-md-2 filter_actions"><div class="row"><div class="col"><button type="submit" class="filter mat-ico-after">'. $filter_text .'</button></div>';
        //echo '<div class="col"><a href="'.$clear_url.'" class="btn clear">'.T('Clear').'</a></div>';
        //echo '</div></div></div></div>';
    }

    /**
     * Returns string of GET filters to SQL Query
     * @param array $filter_params Array of query and filter [ [ 'user_since >', 'date_from' ] ]
     * @param string $query Array of query and filter [ [ 'user_since >', 'date_from' ] ]
     * @param string $method POST or GET form data
     * @return string
     */
    function filters_to_query( array $filter_params = [], string $query = '', string $method = 'GET' ): string {
        foreach( $filter_params as $qa ) {
            $id = $qa['id'] ?? ( $qa['i'] ?? '' );
            $logic = $qa['logic'] ?? ( $qa['query'] ?? ( $qa['q'] ?? '' ) );
            if( is_array( $qa ) && isset( $id ) && isset( $logic ) ) {
                $value = '';
                if( $method == 'GET' ) {
                    $value = $_GET[ $id ] ?? '';
                } else {
                    $value = $_POST[ $id ] ?? '';
                }
                if( !empty( $value ) ) {
                    //$where = $logic;
                    //$q = str_contains( $where, 'LIKE' ) ? $where.' \'%'.$value.'%\'' : $where.' \''.$value.'\'';
                    $q = str_replace( '*', $value, $logic );
                    $query = !empty( $query ) ? $query.' AND '.$q : $q;
                }
            }
        }
        return $query;
    }

    /**
     * Fetches fake data only for debugging purposes
     * @param string $key Key of fake data needed
     */
    function fake( string $key = '' ) {
        if( defined( 'CONFIG' ) && isset( CONFIG['autofill'] ) && CONFIG['autofill'] ) {
            $key = str_replace( 'fake_', '', $key );

            // PLACEHOLDER IMAGES
            $images = [
                'image' => 'https://picsum.photos/300',
                'image_1' => 'https://picsum.photos/100',
                'image_2' => 'https://picsum.photos/200',
                'image_5' => 'https://picsum.photos/500',
                'image_l' => 'https://picsum.photos/1000',
                'image_xl' => 'https://picsum.photos/2000',
                'pic' => 'https://www.gravatar.com/avatar/2c7d99fe281ecd3bcd65ab915bac6dd5',
                'picture' => 'https://www.gravatar.com/avatar/2c7d99fe281ecd3bcd65ab915bac6dd5',
                'images' => 'https://picsum.photos/300,https://picsum.photos/300,https://picsum.photos/300',
            ];
            if( ( str_contains( $key, 'image' ) || str_contains( $key, 'pic' ) ) && isset( $images[ $key ] ) ) {
                return $images[ $key ];
            }

            // FAKER CONTENT
            $locale = defined('FAKER') ? FAKER : 'en_US';
            require_once VENDORLOAD;
            $fk = Faker\Factory::create( $locale );
            $replacements = [
                'email' => 'freeEmail',
                'mail' => 'freeEmail',
                'site' => 'domainName',
                'website' => 'domainName',
                'url' => 'domainName',
                'phone' => 'e164PhoneNumber',
                'mobile' => 'e164PhoneNumber',
                'contact' => 'e164PhoneNumber',
                'slogan' => 'catchPhrase',
                'design' => 'jobTitle',
                'title' => 'jobTitle',
                'job_title' => 'jobTitle',
                'designation' => 'jobTitle',
                'username' => 'userName',
                'login' => 'userName',
                'mac' => 'macAddress',
                'card_type' => 'creditCardType',
                'card_no' => 'creditCardNumber',
                'card' => 'creditCardNumber',
                'card_number' => 'creditCardNumber',
                'swift' => 'swiftBicNumber',
                'swift_no' => 'swiftBicNumber',
                'swift_code' => 'swiftBicNumber',
                'hex' => 'hexcolor',
                'color' => 'hexcolor',
                'rgb' => 'rgbCssColor',
                'color_name' => 'colorName',
                'address' => 'streetAddress',
                'postal' => 'postcode',
                'po_box' => 'postcode',
                'po' => 'postcode',
                'post' => 'postcode',
                'street' => 'streetName',
                'street_name' => 'streetName',
                'country_code' => 'countryCode',
                'country_iso2' => 'countryCode',
                'country_iso3' => 'countryISOAlpha3',
                'currency' => 'currencyCode',
                'lat' => 'latitude',
                'long' => 'longitude',
                'ip' => 'ip4',
            ];
            $key = $replacements[ $key ] ?? $key;
            /*foreach( $replacements as $r => $keys ) {
                if( in_array( $key, $keys ) ) {
                    $key = $r;
                }
            }*/
            $ka = explode( '_', $key );
            $x = !empty( $ka ) && count( $ka ) > 1 ? end( $ka ) : 2;
            if( $key == 'e164PhoneNumber' ) {
                return str_replace( '+1', '', $fk->{ $key } );
            } else if( str_contains( $key, 'words' ) ) {
                return $fk->words( $x );
            } else if( str_contains( $key, 'actions' ) ) {
                return str_replace( ' ', ',', $fk->words( $x ) );
            } else if( str_contains( $key, 'content' ) ) {
                return $fk->sentence( $x );
            } else if( str_contains( $key, 'para' ) ) {
                return $fk->paragraphs( $x );
            } else if( str_contains( $key, 'email' ) ) {
                return $fk->safeEmail();
            } else if( str_contains( $key, 'slug' ) ) {
                return $fk->slug();
            } else if( str_contains( $key, 'company_name' ) || str_contains( $key, 'company' ) ) {
                $ends = [ 'LTD.', 'LLC.', 'Inc.', 'Co.', 'Corp.', 'PBC.', 'LLP.' ];
                return ucwords( $fk->bs() . ' ' . $ends[array_rand( $ends )] );
            } else if( str_contains( $key, 'company_email' ) || str_contains( $key, 'org_email' ) ) {
                return $fk->companyEmail();
            } else {
                return $fk->{ $key };
            }
        } else {
            return '';
        }
    }

    function pre( string|int|float $pre ): void {
        echo $this->__pre( $pre );
    }

    function __col( string|int|float $pre ): string {
        if( is_float( $pre ) ) {
            $pre = explode( '.', $pre );
            return $pre[1] == 1 ? 'col-12 col-md-'.$pre[0].' end' : $pre[0];
        } else if( is_numeric( $pre ) ) {
            return $pre == 0 ? 'col' : 'col-12 col-md-'.$pre;
        } else {
            return '';
        }
    }

    function __pre( string|int|float $pre ): string {
        if( is_float( $pre ) ) {
            $pre = explode( '.', $pre );
            $return = $pre[1] == 1 ? __pre( '', 'col-12 col-md-'.$pre[0].' end' ) : __pre( '', $pre[0] );
        } else if( is_numeric( $pre ) ) {
            $return = $pre == 0 ? __pre('','col') : __pre('','col-12 col-md-'.$pre);
        } else {
            $return = str_contains( $pre, '.' ) ? __pre( '', str_replace( '.', '', $pre ) ) : ( str_contains( $pre, '[' ) || str_contains( $pre, ']' ) ? __pre('','','div',str_replace('[','',str_replace(']','',$pre))) : $pre );
        }
        return $return;
    }

    function post( float|int|string $pre, string $post = '' ): void {
        echo $this->__post( $pre, $post );
    }

    function __post( string|float|int $pre, string $post = '' ): string {
        return !empty( $post ) ? $post : ( is_numeric( $pre ) || str_contains( $pre, '.' ) || str_contains( $pre, '[' )|| str_contains( $pre, ']' ) ? '</div>' : '' );
    }

    function __random(): string {
        return substr(str_shuffle("abcdefghijklmnopqrstuvwxyz"), 0, 8);
    }
}
