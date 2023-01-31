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
    function select_options( array $options = [], string|null $selected = '', string $placeholder = '', bool $keyed = false, bool $translate = false ): void {
        $s = explode( ',', str_replace( ' ', '', $selected ) );
        //skel( $s );
        $placeholder = $translate ? T($placeholder) : $placeholder;
        if( $placeholder !== '' ){
            echo empty($s) ? '<option disabled selected>'.$placeholder.'</option>' : '<option disabled>'.$placeholder.'</option>';
        }
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
                if( $data == 'select2_placeholder' ) { echo '<option></option>'; continue; }
                echo '<option value="' . $value . '" ' . $sel . $attr.'>' . $text . '</option>';
            }
        } else {
            foreach( $options as $k => $o ) {
                $sel = '';
                if( is_array( $o ) ) {
                    $k = $o[0];
                    $t = isset( $o[1] ) && $o[1] !== $o[0] ? $o[1] : $o[0];
                    $d = $o[2] ?? '';
                    $d = is_array( $d ) ? 'data-data=\''.json_encode( $d ).'\'' : 'data-data=\''.$d.'\'';
                    if( is_array( $s ) && in_array( $k, $s ) ) {
                        $sel = 'selected';
                    } else if( $k == $s ) {
                        $sel = 'selected';
                    }
                } else {
                    $k = $keyed ? $k : $o;
                    $d = '';
                    $t = $o;
                    if( is_array( $s ) && in_array( $o, $s ) ) {
                        $sel = 'selected';
                    } else if( $o == $s ) {
                        $sel = 'selected';
                    }
                }
                $t = $translate ? T( $t ) : $t;
                if( $t == 'select2_placeholder' ) { echo '<option></option>'; continue; }
                echo '<option '.$d.' value="' . $k . '" ' . $sel . '>' . $t . '</option>';
            }
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

    function select( string|array $identity = '', string $label = '', string $placeholder = '', array $options = [], string|null $selected = '', string $attr = '', string $pre = '', bool $keyed = false, bool $translate = false, string $post = '' ): void {
        $rand = rand( 0, 999999 );
        if( is_numeric( $pre ) ){
            $pre =  $pre == 0 ? '<div class="col">' : '<div class="col-12 col-md-'.$pre.'">';
            $post = '</select></div>';
        } else if( str_contains( $pre, '.' ) ) {
            $pre = '<div class="'.str_replace('.','',$pre).'">';
            $post = '</div>';
        }
        $post = empty( $post ) ? '</select>' : '</select>' . $post;
        $at = $attr !== '' ? ' '.$attr : '';
        $id = !empty( $identity ) ? ( is_array($identity) ? $identity[0] : $identity.'_'.$rand ) : '';
        $name = is_array( $identity ) ? $identity[1] : $identity;
        echo $pre;
        $label = !empty( $label ) ? T( $label ) : '';
        $req = str_contains( $attr, 'required' ) ? '<i>*</i>' : '';
        echo !empty( $label ) ? '<label for="'.$id.'">'. $label .$req.'</label>' : '';
        $ph = !empty( $placeholder ) ? ' placeholder="'.$placeholder.'" data-placeholder="'.$placeholder.'"' : '';
        echo '<select name="'.$name.'" title="'.$label.'" data-key="'.$name.'" id="'.$id.'"'.$at.$ph.'>';
        //if( str_contains( $attr, 'select2' ) ) {
        // TODO: Options to check if array is multi dimensional and append accordingly
        if( str_contains( $attr, 'select2') ) {
            $placeholder = '';
            $options = [ '' => 'select2_placeholder' ] + $options;
            //array_unshift( $options, 'select2_placeholder' );
        }
        //$placeholder = strpos( $attr, 'select2') !== false ? '' : $placeholder;
        $this->select_options( $options, $selected, $placeholder, $keyed, $translate );
        echo $post;
    }

    function select2( string $id = '', string $label = '', string $placeholder = '', array $options = [], string|null $selected = '', string $attr = '', string $pre = '', bool $keyed = false, bool $translate = false, string $post = '' ): void {
        $this->select( $id, $label, $placeholder, $options, $selected, $attr.' class="select2"', $pre, $keyed, $translate, $post );
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
    function input( string $type, string|array $identity, string $label = '', string $placeholder = '', string|null $value = '', string $attrs = '', string $pre = '', string $post = '', string $name = '' ): void {
        $rand = rand( 0, 999999 );
        $type = $type == '' ? 'text' : $type;
        if( is_numeric( $pre ) ){
            $pre =  $pre == 0 ? '<div class="col">' : '<div class="col-12 col-md-'.$pre.'">';
            $post = '</div>';
        } else if( str_contains( $pre, '.' ) ) {
            $pre = '<div class="'.str_replace('.','',$pre).'">';
            $post = '</div>';
        }
        $ph = $placeholder !== '' ? ' placeholder="'.$placeholder.'"' : '';
        $name = is_array( $identity ) ? $identity[1] : $identity;
        $id = !empty( $identity ) ? ( is_array($identity) ? $identity[0] : $identity.'_'.$rand ) : '';
        $n = $name !== '' ? $name : $id;
        $hidden_label = empty( $label ) ? $n : $label;
        $at = $attrs !== '' ? 'title="'.$hidden_label.'" '.$attrs : 'title="'.$hidden_label.'"';
        if( $type == 'textarea' ) {
            $va = $value !== '' ? $value : '';
        } else {
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
            default:
                $input = '<input type="'.$type.'" id="'.$id.'" data-key="'.$n.'" '.$at.$ph.$va.$nn.'>';
                break;
        }
        $req = str_contains( $attrs, 'required' ) ? '<i>*</i>' : '';
        echo $pre;
        echo !empty( $label ) ? '<label for="'.$id.'">'.T($label).$req.'</label>' : '';
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
    function inputs( string $type = 'text', array $array = [], string $attrs = '', string $pre = '', string $post = '' ): void {
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
    function text( string|array $id, string $label = '', string $placeholder = '', string|null $value = '', string $attrs = '', string $pre = '', string $post = '' ): void {
        $this->input( 'text', $id, $label, $placeholder, $value, $attrs, $pre, $post );
    }

    /**
     * Renders Date Picker
     * @param string|array $id
     * @param string $label
     * @param string $placeholder
     * @param string|null $value
     * @param string $attrs
     * @param string $position
     * @param string $pre
     * @param bool $range
     * @param bool $multiple
     * @param string $view
     * @param string $min Minimum Date yyyy-mm-dd
     * @param string $max Maximum Date yyyy-mm-dd
     * @param string $post
     * @return void
     */
    function date( string|array $id, string $label = '', string $placeholder = '', string|null $value = '', string $attrs = '', string $position = '', string $pre = '', bool $range = false, bool $multiple = false, string $view = '', string $min = '', string $max = '', string $post = '' ): void {
        $rand = rand(0,99999);
        $id = !empty( $id ) ? ( is_array( $id ) ? [ $id[0] ] : $id ) : $rand;
        $alt_id = is_array( $id ) ? [ $id[0].'_alt', $id[1].'_alt' ] : $id.'_alt';
        $range_attr = $range ? ' range' : '';
        $multiple_attr = $multiple ? ' multiple' : '';
        $view_attr = $view ? ' view="'.$view.'"' : '';
        $position = !empty( $position ) ? $position : 'bottom center';
        $attrs .= is_array( $alt_id ) ? ' data-alt="[data-key='.$alt_id[0].']"' : ' data-alt="[data-key='.$alt_id.']"';

        $visible_attr = is_array( $id ) ? 'class="dater" alt="#'.$id[0].'_'.$rand.'" position="'.$position.'"' : 'class="dater" alt="#'.$id.'_'.$rand.'" position="'.$position.'"';
        $visible_attr .= !empty( $min ) ? ' min="'.$min.'"' : '';
        $visible_attr .= !empty( $max ) ? ' max="'.$max.'"' : '';
        $visible_attr .= str_contains( $attrs, 'disabled' ) ? ' disabled' : '';

        // Hidden Input - Renders date format as per backend
        $value_ymd = !empty( $value ) ? easy_date( $value, 'Y-m-d' ) : '';
        $this->text( [ $id.'_'.$rand, $id ], '', '', $value_ymd, $attrs.' hidden data-hidden-date' );
        // Visible Input - Render date for easier user grasp
        $value_dmy = !empty( $value ) ? easy_date( $value, 'd-m-Y' ) : '';
        $this->text( $alt_id, $label, $placeholder, $value_dmy, $visible_attr.$range_attr.$multiple_attr.$view_attr.' data-visible-date no_post', $pre, $post );
    }

    /**
     * @param array $array
     * @param string $attrs
     * @param string $position
     * @param string $pre
     * @param bool $range
     * @param bool $multiple
     * @param string $post
     * @return void
     */
    function dates( array $array, string $attrs = '', string $position = '', string $pre = '', bool $range = false, bool $multiple = false, string $post = '' ): void {
        if( !empty( $array ) ){
            foreach( $array as $f ) {
                $id = $f[0] ?? '';
                $label = $f[1] ?? '';
                $ph = $f[2] ?? '';
                $value = $f[3] ?? '';
                $this->date( $id, $label, $ph, $value, $attrs, $position, $pre, $range, $multiple, $post );
            }
        }
    }

    function color( string|array $id, string $label = '', string $placeholder = '', string|null $value = '', string $attrs = '', string $pre = '', string $border = '', string $preview = '', string $post = '' ): void {
        $attrs .= ' data-color-picker';
        $attrs = !empty( $border ) ? $attrs . ' data-border="'.$border.'"' : $attrs;
        $attrs = !empty( $preview ) ? $attrs . ' data-preview="'.$preview.'"' : $attrs;
        $this->text( $id, $label, $placeholder, $value, $attrs, $pre, $post );
    }

    /**
     * Render a Google Map with search
     * @param string $latitude_field
     * @param string $longitude_field
     * @param string $address_field
     * @param string $area_field
     * @param string $city_field
     * @param string $country_field
     * @param string $coordinates
     * @param string $pre
     * @param string $latitude_value
     * @param string $longitude_value
     * @param string $zoom
     * @param string $type
     * @param string $post
     * @return void
     */
    function map( string $latitude_field = '', string $longitude_field = '', string $address_field = '', string $area_field = '', string $city_field = '', string $country_field = '', string $coordinates = '', string $pre = '', string $latitude_value = '', string $longitude_value = '', string $zoom = '13', string $type = 'terrain', string $post = '' ): void {
        if( is_numeric( $pre ) ){
            $pre = $pre == 0 ? '<div class="map_col col">' : '<div class="map_col col-12 col-md-'.$pre.'">';
            $post = '</div>';
        } else if( str_contains( $pre, '.' ) ) {
            $pre = '<div class="'.str_replace('.','',$pre).'">';
            $post = '</div>';
        }
        if( ( empty( $latitude_value ) || empty( $longitude_value ) ) && defined( 'DB_TYPE' ) ) {
            $db = new DB();
            $ops = $db->get_options([ 'default_map_lat', 'default_map_long', 'default_map_zoom', 'default_map_type' ]);
            $latitude_value = $ops['default_map_lat'] ?? '';
            $longitude_value = $ops['default_map_long'] ?? '';
            $zoom = $ops['default_map_zoom'] ?? $zoom;
            $type = $ops['default_map_type'] ?? $type;
            $style = $ops['style'] ?? '';
        }
        $def_lat = !empty( $latitude_value ) ? ' lat="'.$latitude_value.'"' : '';
        $def_long = !empty( $longitude_value ) ? ' long="'.$longitude_value.'"' : '';
        $def_zoom = !empty( $zoom ) ? ' level="'.$zoom.'"' : '';
        $def_type = !empty( $type ) ? ' type="'.$type.'"' : '';
        $def_style = !empty( $style ) ? ' design="'.$style.'"' : '';
        $co = !empty( $coordinates ) ? ' data-coordinates="'.$coordinates.'"' : '';
        $add = !empty( $address_field ) ? ' data-address="'.$address_field.'"' : '';
        $area = !empty( $area_field ) ? ' data-area="'.$area_field.'"' : '';
        $city = !empty( $city_field ) ? ' data-city="'.$city_field.'"' : '';
        $country = !empty( $country_field ) ? ' data-country="'.$country_field.'"' : '';
        $lat = !empty( $latitude_field ) ? ' data-lat="'.$latitude_field.'"' : '';
        $long = !empty( $longitude_field ) ? ' data-long="'.$longitude_field.'"' : '';
        $r = rand(0,999);
        echo $pre;
        echo '<div class="map_wrap">';
        $this->text(['search_'.$r,'search_'.$r],'','Search for Address...');
        echo '<div id="map_'.$r.'" class="google_map" search="search_'.$r.'" data-google-map-render'.$def_zoom.$def_lat.$def_long.$def_type.$def_style.$co.$add.$area.$city.$country.$lat.$long.'>';
        echo '</div></div>';
        echo $post;
        $m = new MAPS();
        $m->google_maps();
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
    function textarea( string|array $id, string $label = '', string $placeholder = '', string|null $value = '', string $attrs = '', string $pre = '', string $post = '' ): void {
        $this->input( 'textarea', $id, $label, $placeholder, $value, $attrs, $pre, $post );
    }

    /**
     * Renders multiple &lt;input type="text"&gt; elements
     * @param array $array Array of an array of ['id','label','placeholder','value','attr']
     * @param string $attrs Attributes like class or data applicable to all
     * @param string $pre String to wrap before start of input. Tip: 6 will wrap with bootstrap col-lg-6
     * @param string $post End string to wrap after /&gt;
     */
    function texts( array $array, string $attrs = '', string $pre = '', string $post = '' ): void {
        if( is_assoc( $array ) ){
            foreach( $array as $k => $v ){
                $this->input( 'text', $k, $v, $attrs, $pre, $post );
            }
        } else {
            $this->inputs( 'text', $array, $attrs, $pre, $post );
        }
    }

    /**
     * Renders &lt;input type="radio"&gt; or &lt;input type="checkbox"&gt; elements of same name
     * @param string $type Type either 'radio' or 'checkbox'
     * @param string|array $identity Name of the input elements
     * @param string|array $values Array of values
     * @param string|array $checked Checked value or values separated by (,) comma
     * @param string $attr Attributes like class or data tags
     * @param bool $label_first If label should be before input element
     * @param string $pre String to wrap before start of &lt;input&gt;. Tip: 6 will wrap with bootstrap col-lg-6
     * @param string $post End string to wrap after /&gt;
     */
    function render_options( string $type = 'radio', string $label = '', string|array $identity = '', string|array $values = [], string|array $checked = '', string $attr = '', bool $label_first = false, string $pre = '', string $post = '', string $inputs_wrap = '', string $inputs_pre = '', string $inputs_post = '' ): void {
        if( is_array( $values ) ) {
            $rand = rand( 0, 99999 );
            $type = $type == 'radio' ? 'type="radio"' : 'type="checkbox"';
            $id = is_array($identity) ? $identity[0] : $identity;
            $name = is_array($identity) ? $identity[1] : $identity;
            $valued = is_assoc( $values ); $x = 0;
            if( is_numeric( $pre ) ){
                $pre = $pre == 0 ? '<div class="col">' : '<div class="col-12 col-md-'.$pre.'">';
                $post = '</div>';
            } else if( str_contains( $pre, '.' ) ) {
                $pre = '<div class="'.str_replace('.','',$pre).'">';
                $post = '</div>';
            }
            $wrap_inputs_pre = !empty( $inputs_wrap ) ? '<div class="'.$inputs_wrap.'">' : '';
            $wrap_inputs_post = !empty( $inputs_wrap ) ? '</div>' : '';
            if( is_numeric( $inputs_pre ) ) {
                $inputs_pre = $inputs_pre == 0 ? '<div class="col">' : '<div class="col-12 col-md-'.$inputs_pre.'">';
                $inputs_post = '</div>';
                $wrap_inputs_pre = '<div class="row '.$inputs_wrap.'">';
                $wrap_inputs_post = '</div>';
            } else if( str_contains( $inputs_pre, '.' ) ) {
                $inputs_pre = '<div class="'.str_replace('.','',$inputs_pre).'">';
                $inputs_post = '</div>';
            }
            $key = 'data-key="'.$name.'"';
            if( $type !== 'type="radio"' && strpos( $attr, 'data-array') !== false ) {
                $name = $name . '[]';
            }
            $uq = rand(1,999);
            echo $pre;
            $label = !empty( $label ) ? T( $label ) : '';
            $req = str_contains( $attr, 'required' ) ? '<i>*</i>' : '';
            echo !empty($label) ? '<label class="db">'. $label .$req.'</label>' : '';
            echo $wrap_inputs_pre;
            if( is_assoc( $values ) ) {
                foreach ($values as $val => $title) {
                    $tip = $title !== '' ? 'title="'.strip_tags($title).'"' : '';
                    $k = $valued ? $val . $x . '_' . $uq : str_replace(' ', '', $name) . $x;
                    $value = $valued ? $val : $title;
                    $checked = is_array( $checked ) ? $checked : explode(',',$checked);
                    $c = in_array( $value, $checked ) ? 'checked' : '';
                    //skel( $checked );
                    /* if( is_array( $checked ) ) {
                        $c = '';
                    } else {
                        $c = $value == $checked ? 'checked="true"' : '';
                    } */
                    if ($label_first) {
                        echo $inputs_pre . '<label for="' . $k . '" '.$tip.' class="' . $name . '_' . $value . '">' . $title . $req . '</label><input ' . $tip . $attr . ' ' . $type . ' name="' . $name . '" '.$key.' id="' . $k . '" value="' . $value . '" '. $c .' >' . $inputs_post;
                    } else {
                        echo $inputs_pre . '<input ' . $tip . $attr . ' ' . $type . ' name="' . $name . '" '.$key.' id="' . $k . '" value="' . $value . '" '. $c .' ><label for="' . $k . '" '.$tip.' class="' . $name . '_' . $value . '">' . $title . $req . '</label>' . $inputs_post;
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
                        echo $inputs_pre . '<label for="' . $k . '" '.$tip.' class="' . $name . '_' . $value . '">' . $title . '</label><input ' . $tip . $attr . ' ' . $type . ' ' . $data . ' data-key="'.$name.'" name="' . $name . '" id="' . $k . '" value="' . $value . '" '.$c.'>' . $inputs_post;
                    } else {
                        echo $inputs_pre . '<input ' . $tip . $attr . ' ' . $type . ' name="' . $name . '" data-key="'.$name.'" id="' . $k . '" value="' . $value . '" ' . $data . ' '.$c.'><label for="' . $k . '" '.$tip.' class="' . $name . '_' . $value . '">' . $title . '</label>' . $inputs_post;
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
        } else {
            // TODO: If value is single
        }
    }

    /**
     * Renders &lt;input type="radio"&gt; elements
     * @param string|array $name Name of the input elements
     * @param array $values Array of values
     * @param string|array $checked Checked value or values separated by (,) comma
     * @param string $attr Attributes like class or data tags
     * @param bool $label_first If label should be before input element
     * @param string $pre String to wrap before start of &lt;input&gt;. Tip: 6 will wrap with bootstrap col-lg-6
     * @param string $post End string to wrap after /&gt;
     */
    function radios( string|array $name, string $label = '', array $values = [], string|array $checked = '', string $attr = '', bool $label_first = false, string $pre = '', string $post = '', string $inputs_wrap = '', string $inputs_pre = '', string $inputs_post = '' ): void {
        $this->render_options( 'radio', $label, $name, $values, $checked, $attr, $label_first, $pre, $post, $inputs_wrap, $inputs_pre, $inputs_post );
    }

    /**
     * Renders &lt;input type="checkbox"&gt; elements
     * @param string|array $name Name of the input elements
     * @param string|array $values Array of values
     * @param array|string $checked Checked value or values separated by (,) comma
     * @param string $attr Attributes like class or data tags
     * @param bool $label_first If label should be before input element
     * @param string $pre String to wrap before start of &lt;input&gt;. Tip: 6 will wrap with bootstrap col-lg-6
     * @param string $post End string to wrap after /&gt;
     */
    function checkboxes( string|array $name, string $label = '', string|array $values = '', string|array $checked = '', string $attr = '', bool $label_first = false, string $pre = '', string $post = '', string $inputs_wrap = '', string $inputs_pre = '', string $inputs_post = '' ): void {
        $this->render_options( 'checkbox', $label, $name, $values, $checked, $attr, $label_first, $pre, $post, $inputs_wrap, $inputs_pre, $inputs_post );
    }

    function slide( string|array $key, string $label = '', string $off_text = '', string $on_text = '', bool $checked = true, string $size = 'm', string $attr = '', string $pre = '', string $post = '' ): void {
        $checked = $checked ? 'checked' : '';
        $rand = rand( 0, 99999 );
        $id = is_array( $key ) ? $key[0] : $key.'_'.$rand;
        $name = is_array( $key ) ? $key[1] : $key;
        if( is_numeric( $pre ) ){
            $pre = $pre == 0 ? '<div class="col">' : '<div class="col-12 col-md-'.$pre.'">';
            $post = '</div>';
        } else if( str_contains( $pre, '.' ) ) {
            $pre = '<div class="'.str_replace('.','',$pre).'">';
            $post = '</div>';
        }
        $key = 'data-key="'.$name.'"';
        $tip = $label !== '' ? 'title="'.$label.'"' : '';
        echo $pre;
        echo !empty($label) ? '<label class="db">'.T( $label ).'</label>' : '';
        echo !empty( $off_text ) ? '<label for="' . $id . '" '.$tip.' class="slide_label off">' . $off_text . '</label>' : '';
        echo '<input ' . $attr . ' class="slide ' . $size . '" type="checkbox" name="' . $name . '" '.$key.' id="' . $id . '" '. $checked .' >';
        echo !empty( $on_text ) ? '<label for="' . $id . '" '.$tip.' class="slide_label on">' . $on_text . '</label>' : '';
        echo $post;
    }

    /**
     * Renders File Uploading Elements
     * @param string|array $identity ID and name of the element
     * @param string $label Text for the &lt;label&gt;
     * @param string $button_label Text for the &lt;button&gt;
     * @param string $value Value of the input if any
     * @param int $multiple Upload single or quantity of multiple files
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
    function upload( string|array $identity, string $label, string $button_label = 'Upload', string $value = '', int $multiple = 1, bool $show_history = false, string $button_class = '', string $attrs = '', string $extensions = '', string $size = '', bool $deletable = false, string $path = '', string $pre = '', string $post = '' ): void {
        $rand = rand( 0, 99999 );
        $id = $identity.'_'.$rand; // is_array($identity) ? $identity[0] : $identity;
        $name = $identity; // is_array($identity) ? $identity[1] : $identity;
        if( is_numeric( $pre ) ){
            $pre = $pre == 0 ? '<div class="upload_set col">' : '<div class="upload_set col-12 col-lg-'.$pre.'">';
            $post = '</div>';
        } else if( str_contains( $pre, '.' ) ) {
            $pre = '<div class="upload_set '.str_replace('.','',$pre).'">';
            $post = '</div>';
        }
        $sh = $show_history ? ' data-history' : '';
        $ext = $extensions !== '' ? ' data-exts="'.$extensions.'"' : '';
        $sz = $size !== '' ? ' data-size="'.$size.'"' : '';
        $del = $deletable ? ' data-delete' : '';
        $cry = Encrypt::initiate();
        $pat = $path !== '' ? ' data-path="'.$cry->encrypt( $path ).'"' : '';
        $type = $multiple > 1 ? 'files' : 'file';
        $mul = $multiple > 1 ? 'data-files="'.$multiple.'"' : 'data-file';
        $req = str_contains( $attrs, 'required' ) ? '<i>*</i>' : '';
        $label = !empty( $label ) ? '<label for="'.$id.'">'.T($label).$req.'</label>' : '';
        echo $pre.$label.'<button type="button" class="aio_upload '.$button_class.'" data-url="#'.$id.'" onclick="file_upload(this)" '.$sh.$ext.$sz.$mul.$del.$pat.'>'.T($button_label).'</button><input id="'.$id.'" name="'.$name.'" data-key="'.$name.'" type="text" data-'.$type.' value="'.$value.'" '.$attrs.'>'.$post;
    }

    /**
     * Renders code editor with hidden &lt;input type="textarea"&gt; element
     * @param string|array $id ID and name of the element
     * @param string $label Label for the &lt;label&gt;
     * @param string|null $value Value of the input if any
     * @param string $attrs Attributes like class or data tags
     * @param string $pre String to wrap before start of &lt;input&gt;. Tip: 6 will wrap with bootstrap col-lg-6
     * @param string $post End string to wrap after /&gt;
     */
    function code( string|array $id, string $label = '', string|null $value = '', string $attrs = '', string $pre = '', string $post = '' ): void {
        get_script('ace');
        echo '<script>ace.config.set("basePath", "'. APPURL . 'assets/ext/ace/" );</script>';
        if( is_numeric( $pre ) ){
            $pre =  $pre == 0 ? '<div class="col">' : '<div class="col-12 col-md-'.$pre.'">';
            $post = '</div>';
        } else if( str_contains( $pre, '.' ) ) {
            $pre = '<div class="'.str_replace('.','',$pre).'">';
            $post = '</div>';
        }
        echo $pre;
        $this->input( 'textarea', $id, $label, $value, '', $attrs . ' style="display:none !important"' );
        echo '<div id="'.$id.'_code" style="min-height: 200px"></div>';
        ?>
        <script>
            $(document).ready(function(){
                let <?php echo $id; ?>_code = ace.edit( '<?php echo $id; ?>_code' );
                <?php echo $id; ?>_code.session.setMode("ace/mode/html");
                <?php echo $id; ?>_code.session.setValue($('[data-key=<?php echo $id; ?>]').val(),-1);
                <?php echo $id; ?>_code.session.on('change', function(d) {
                    $('[data-key=<?php echo $id; ?>]').val( <?php echo $id; ?>_code.getValue() );
                });
            });
        </script>
        <?php
        echo $post;
    }

    /**
     * Renders rich WYSIWYG editor with hidden &lt;input type="textarea"&gt; element
     * @param string|array $id ID and name of the element
     * @param string $label Label for the &lt;label&gt;
     * @param string|null $value Value of the input if any
     * @param string $attrs Attributes like class or data tags
     * @param string $pre String to wrap before start of &lt;input&gt;. Tip: 6 will wrap with bootstrap col-lg-6
     * @param string $post End string to wrap after /&gt;
     */
    function richtext( string|array $id, string $label = '', string|null $value = '', string $attrs = '', string $pre = '', string $post = '' ): void {
        get_style('https://cdn.jsdelivr.net/npm/trumbowyg/dist/ui/trumbowyg.min.css');
        get_script('https://cdn.jsdelivr.net/npm/trumbowyg/dist/trumbowyg.min.js');
        if( is_numeric( $pre ) ){
            $pre =  $pre == 0 ? '<div class="col">' : '<div class="col-12 col-md-'.$pre.'">';
            $post = '</div>';
        } else if( str_contains( $pre, '.' ) ) {
            $pre = '<div class="'.str_replace('.','',$pre).'">';
            $post = '</div>';
        }
        echo $pre;
        //echo '<div id="'.$id.'_rte"><div>';
        $this->input( 'textarea', $id, $label, '', $value, $attrs );
        ?>
        <script>
            document.addEventListener('DOMContentLoaded',function(){
                $('[data-key=<?php echo $id; ?>]').trumbowyg({ autogrow: true }).on('tbwchange tbwfocus', function(e){
                    $('[data-key=<?php echo $id; ?>]').val( $( e.currentTarget ).val() );
                });
            });
        </script>
        <?php
        echo $post;
    }

    /**
     * Returns array into JSON string
     * @param array $data Database rows as array
     * @param string $remove If needed to remove, provide keys separated by ,
     * @return string
     * @author Shaikh <hey@shaikh.dev>
     */
    function _editable_data( array $data = [], string $remove = '' ): string {
        $final = [];
        $remove = explode( ',', $remove );
        foreach( $data as $k => $v ){
            if( is_numeric( $k ) )
                continue;
            $k = strpos( $k, '_') !== false ? ltrim( strstr($k,'_'), '_' ) : $k;
            if( $k == 'id' ) {
                $cry = Encrypt::initiate();
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
    function editable_data( array $data = [], string $remove = '' ) {
        echo $this->_editable_data( $data, $remove );
    }

    function delete_data( string $table, string $logic ) {
        $c = Encrypt::initiate();
        echo ' onclick="trash_data(\''.$c->encrypt('trash_data_ajax').'\',\''.$c->encrypt( $table ).'\',\''.$c->encrypt( $logic ).'\')"';
    }

    /**
     * Renders HTML parameters for automated data saving
     * @param string $target Database Name
     * @param string $data Data attribute to fetch data
     * @param string $pre Pre Wrap String for Tables
     * @param int $notify Notification Time in Seconds
     * @param int $reload Reload in Seconds
     * @param array $hidden Hidden data for Database
     * @param string $success_text Text to notify upon successfully storing data
     * @param string $callback A JS Function to callback on results
     * @param string $confirm A confirmation popup will execute further code
     */
    function process_params( string $target = '', string $data = '', string $pre = '', int $notify = 0, int $reload = 0, array $hidden = [], string $success_text = '', string $callback = '', string $confirm = '' ): void {
        $c = Encrypt::initiate();
        $t = !empty( $target ) ? ' data-t="'.$c->encrypt( $target ).'"' : 'data-t';
        $nt = $notify > 0 ? ' data-notify="'.$notify.'"' : '';
        $rl = $reload > 0 ? ' data-reload="'.$reload.'"' : '';
        $d = !empty( $data ) ? ' data-data="'.$data.'"' : '';
        $p = !empty( $pre ) ? ' data-pre="'.$pre.'"' : '';
        $h = !empty( $hidden ) ? ' data-h="'.$c->encrypt_array( $hidden ).'"' : '';
        $st = !empty( $success_text ) ? ' data-success="'.T($success_text).'"' : '';
        $cb = !empty( $callback ) ? ( str_contains( $callback, '_ajax' ) ? ' data-callback="'.$c->encrypt($callback).'"' : ' data-callback="'.$callback.'"') : '';
        $cn = !empty( $confirm ) ? ' data-confirm="'.T($confirm).'"' : '';
        echo $t.$nt.$rl.$d.$p.$h.$st.$cb.$cn;
    }

    /**
     * Renders HTML for Options Auto Save
     * @param string $data
     * @param int $notify
     * @param int $reload
     * @param array|string $autoload
     * @param array|string $unique
     * @param array|string $encrypt
     * @param string $success_text
     * @param string $callback
     * @param string $confirm
     * @return void
     */
    function option_params( string $data = '', int $notify = 0, int $reload = 0, array|string $autoload = [], array|string $unique = [], array|string $encrypt = [], string $success_text = 'Successfully Updated Preferences!', string $callback = '', string $confirm = '' ): void {
        $h = [];
        !empty( $autoload ) ? $h['autoload'] = $autoload : '';
        !empty( $unique ) ? $h['unique'] = $unique : '';
        !empty( $encrypt ) ? $h['encrypt'] = $encrypt : '';
        $this->process_params( '', $data, '', $notify, $reload, $h, $success_text, $callback, $confirm );
        //skel( $h );
    }

    /**
     * Renders HTML to process data
     * @param string $text Button text
     * @param string $class Button class
     * @param string $attr Additional attributes to button
     * @param string $action Default AJAX Action
     * @param string|int $pre Pre Wrap HTML or Bootstrap Column
     * @param string|int $post Post Wrap HTML
     * @param string $element HTML Element
     * @param string $confirm Message to show as confirmation before process
     */
    function process_html( string $text = '', string $class = '', string $attr = '', string $action = '', string|int $pre = '', int|string $post = '', string $element = 'button', string $confirm = '' ): void {
        if( is_numeric( $pre ) ){
            $pre = $pre == 0 ? '<div class="col">' : '<div class="col-12 col-lg-'.$pre.'">';
            $post = '</div>';
        }  else if( str_contains( $pre, '.' ) ) {
            $pre = '<div class="'.str_replace('.','',$pre).'">';
            $post = '</div>';
        }
        //$post = !empty( $post ) ? $post : ( !empty( $pre ) ? '</div>' : '' );
        $c = Encrypt::initiate();
        $action = empty( $action ) ? 'process_data_ajax' : $action;
        $a = 'data-action="'.$c->encrypt($action).'"';
        $click = $confirm !== '' ? 'onclick="if(confirm(\''.T($confirm).'\')){process_data(this)}else{event.stopPropagation();event.preventDefault();}"' : 'onclick="process_data(this)"';
        echo $pre.'<'.$element.' '.$click.' '.$a.' class="'.$class.'" '.$attr.'><span class="loader"></span>'.T( $text ).'</'.$element.'>'.$post;
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
        $this->process_html( $text, $class, $attr, 'process_options_ajax', $pre, $post, $element, $confirm );
    }

    /**
     * Renders HTML to view page
     * @param string $url URL to visit
     * @param string $html HTML either button or div or i
     * @param string $text Text to display
     * @param string $class Class
     * @param string $attr Additional attributes
     * @param string $i_class Applied class to i element and places before text
     * @param string|int $pre Pre Wrap HTML or Bootstrap Column
     * @param string|int $post Post Wrap HTML
     */
    function view_html( string $url = '', string $html = 'div', string $text = '', string $class = '', string $attr = '', string $i_class = '', string|int $pre = '', string|int $post = '' ): void {
        if( is_numeric( $pre ) ){
            $pre = $pre == 0 ? '<div class="col">' : '<div class="col-12 col-lg-'.$pre.'">';
            $post = '</div>';
        }  else if( str_contains( $pre, '.' ) ) {
            $pre = '<div class="'.str_replace('.','',$pre).'">';
            $post = '</div>';
        }
        //$post = !empty( $post ) ? $post : ( !empty( $pre ) ? '</div>' : '' );
        //$c = Encrypt::initiate();
        $i = !empty( $i_class ) ? '<i class="'.$i_class.'"></i>' : '';
        $title = str_contains( $attr, 'title' ) ? '' : 'title="'.T('View').'"';
        echo $pre.'<'.$html.' data-href="'.$url.'" '.$title.' class="'.$class.'" '.$attr.'>'.$i.T( $text ).'</'.$html.'>'.$post;
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
        //$c = Encrypt::initiate();
        if( is_numeric( $pre ) ){
            $pre = $pre == 0 ? '<div class="col">' : '<div class="col-12 col-lg-'.$pre.'">';
            $post = '</div>';
        } else if( str_contains( $pre, '.' ) ) {
            $pre = '<div class="'.str_replace('.','',$pre).'">';
            $post = '</div>';
        }
        //$post = !empty( $post ) ? $post : ( !empty( $pre ) ? '</div>' : '' );
        $i = !empty( $i_class ) || !empty( $i_text ) ? '<i class="'.$i_class.'">'.$i_text.'</i>' : '';
        $title = str_contains( $attr, 'title' ) ? '' : 'title="'.T('Edit').'"';
        echo $pre.'<'.$html.' onclick="edit_data(this,\''.$element.'\')" data-data=\''.$this->_editable_data($array).'\' class="'.$class.'" '.$title.' '.$attr.'>'.$i.T( $text ).'</'.$html.'>'.$post;
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
     * @param int $notify_time Time to notify
     * @param int $reload_time Time to Reload
     * @param string $confirmation Text for confirmation
     * @param string $i_text Text for i element
     * @param string|int $pre Pre Wrap HTML or Bootstrap Column
     * @param string|int $post Post Wrap HTML
     */
    function trash_html( string $table, string $logic, string $html = 'div', string $text = '', string $class = '', string $attr = '', string $i_class = '', int $notify_time = 2, int $reload_time = 2, string $confirmation = '', string $i_text = '', string|int $pre = '', string|int $post = '' ): void {
        if( is_numeric( $pre ) ){
            $pre = $pre == 0 ? '<div class="col">' : '<div class="col-12 col-lg-'.$pre.'">';
            $post = '</div>';
        } else if( str_contains( $pre, '.' ) ) {
            $pre = '<div class="'.str_replace('.','',$pre).'">';
            $post = '</div>';
        }
        //$post = !empty( $post ) ? $post : ( !empty( $pre ) ? '</div>' : '' );
        $c = Encrypt::initiate();
        $i = !empty( $i_class ) || !empty( $i_text ) ? '<i class="'.$i_class.'">'.$i_text.'</i>' : '';
        $attr .= !empty( $confirmation ) ? ' data-confirm="'.T($confirmation).'"' : '';
        $title = str_contains( $attr, 'title' ) ? '' : 'title="'.T('Delete').'"';
        echo $pre.'<'.$html.' onclick="trash_data(this,\''.$c->encrypt('trash_data_ajax').'\',\''.$c->encrypt($table).'\',\''.$c->encrypt($logic).'\','.$notify_time.','.$reload_time.')" class="'.$class.'" '.$title.' '.$attr.'>'.$i.T( $text ).'</'.$html.'>'.$post;
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
        if( is_numeric( $pre ) ){
            $pre = $pre == 0 ? '<div class="col">' : '<div class="col-12 col-lg-'.$pre.'">';
            $post = '</div>';
        } else if( str_contains( $pre, '.' ) ) {
            $pre = '<div class="'.str_replace('.','',$pre).'">';
            $post = '</div>';
        }
        //$post = !empty( $post ) ? $post : ( !empty( $pre ) ? '</div>' : '' );
        $c = Encrypt::initiate();
        $i = !empty( $i_class ) ? '<i class="'.$i_class.'"></i>' : '';
        $attr .= !empty( $confirmation ) ? ' data-confirm="'.T($confirmation).'"' : '';
        echo $pre.'<'.$html.' onclick="update_data(this,\''.$c->encrypt('update_data_ajax').'\',\''.$c->encrypt($table).'\',\''.$c->encrypt_array($keys).'\',\''.$c->encrypt_array($values).'\',\''.$c->encrypt($logic).'\','.$notify_time.','.$reload_time.')" class="'.$class.'" title="'.T('Update').'" '.$attr.'>'.$i.T( $text ).'</'.$html.'>'.$post;
    }

    function steps( array $steps = [], string $active = '', bool $translate = true ): void {
        echo '<div class="steps_head">';
        if( !empty( $steps ) ) {
            foreach( $steps as $step ) {
                $title = $translate ? T( $step ) : $step;
                $id = strtolower( str_replace( ' ', '_', $step ) );
                $class = $active == $step || $active == $id ? 'step on' : 'step';
                echo '<div class="'.$class.'" data-step="#'.$id.'">'.$title.'</div>';
            }
        }
        echo '</div>';
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
    function filters( array $filter_params = [], string $clear_url = '', string $method = 'GET', string $filter_text = 'Filter', string $class = '' ): void {
        $clear_url = !empty( $clear_url ) ? APPURL . $clear_url : APPURL . PAGEPATH;
        echo '<div class="filters_wrap '.$class.'"><form method="'.$method.'" class="auto_filters"><div class="row"><div class="col-12 col-md-10 inputs"><div class="row">';
        foreach( $filter_params as $f ) {
            $type = $f['type'] ?? 'text';
            $id = $f['id'] ??= '';
            $label = $f['label'] ??= ($f['title'] ??= '');
            $place = $f['place'] ??= ($f['placeholder'] ??= $label);
            $val = $f['value'] ?? ($method == 'POST' ? ($_POST[$id] ?? '') : ($_GET[$id] ?? ''));
            $attrs = $f['attr'] ??= '';
            $pre = $f['pre'] ??= ($f['col'] ??= '');
            if( $type == 'select' ) {
                $options = $f['options'] ?? [];
                $value = $_POST[ $id ] ?? ($_GET[ $id ] ?? '');
                $keyed = $f['keyed'] ??= 0;
                $this->select2( $id, $label, $place, $options, $value, $attrs, $pre, $keyed );
            } else if( $type == 'date' ) {
                $this->date( $id, $label, $place, $val, $attrs, 'bottom center', $pre );
            } else {
                $this->input( $type, $id, $label, $place, $val, $attrs, $pre );
            }
        }
        echo '</div></div><div class="col-12 col-md-2 filter_actions"><div class="row"><div class="col"><button type="submit" class="filter mat-ico-after">'. $filter_text .'</button></div>';
        echo '<div class="col"><a href="'.$clear_url.'" class="btn clear">'.T('Clear').'</a></div>';
        echo '</div></div></div></form></div>';
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
            if( is_array( $qa ) && isset( $qa['id'] ) && isset( $qa['logic'] ) ) {
                $value = '';
                if( $method == 'GET' ) {
                    $value = $_GET[ $qa['id'] ] ?? '';
                } else {
                    $value = $_POST[ $qa['id'] ] ?? '';
                }
                if( !empty( $value ) ) {
                    $where = $qa['logic'];
                    $q = str_contains( $where, 'LIKE' ) ? $where.' \'%'.$value.'%\'' : $where.' \''.$value.'\'';
                    $query = !empty( $query ) ? $query.' AND '.$q : $q;
                }
            }
        }
        return $query;
    }
}
