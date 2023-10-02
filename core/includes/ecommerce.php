<?php
// TODO:

class ECOMMERCE {

    function __construct() {

    }

    private array $property_types = [ 'check' => 'Multi Check Box', 'radio' => 'Single Radio Box', 'drop' => 'Select Dropdown', 'color' => 'Color Picker', 'range' => 'Range Picker' ];
    public array $product_statuses = [ 1 => 'Publicly Visible', 2 => 'Disabled', 3 => 'Draft', 4 => 'History' ];

    // Backend

    /**
     * Add / Update product
     * @param string $modal_class
     * @return void
     */
    function product_form( string $modal_class = '' ): void {
        $f = new FORM();
        $statuses = $this->product_statuses;
        unset( $statuses[4] );
        $visibility_fields = [
            [ 't' => 'date', 'id' => 'birth', 'n' => 'Visible From', 'c' => 6 ],
            [ 't' => 'date', 'id' => 'expiry', 'n' => 'Visible Till', 'c' => 6 ],
            [ 'type' => 'select', 'id' => 'status', 'title' => 'Status', 'o' => $statuses, 'v' => 1, 'a' => 'required', 'k' => 1 ],
            [ 'id' => 'password', 'n' => 'Password', 'c' => 12.1 ],
        ];
        $seo_fields = [
            [ 't' => 'textarea', 'id' => 'meta_desc', 'n' => 'Meta Description' ],
            [ 't' => 'textarea', 'id' => 'meta_words', 'n' => 'Meta Keywords' ],
            [ 'id' => 'meta_author', 'n' => 'Meta Author', 'c' => 12.1 ],
        ];
        $main_fields = [
            [ 'id' => 'title', 'title' => 'Product Title', 'a' => 'required', 'v' => 'fake_sentence' ],
            [ 'id' => 'url', 'title' => 'URL Slug', 'p' => 'Ex: leather-shoes', 'a' => 'data-no-space', 'v' => 'fake_slug' ],
            [ 't' => 'richtext', 'id' => 'content', 'n' => 'Product Description', 'c' => 12.1, 'v' => 'fake_sentence' ]
        ];
        $price_fields = [
            [ 'i' => 'regular_price', 'n' => 'Regular Price', 'c' => 6, 'v' => 100 ],
            [ 'i' => 'sale_price', 'n' => 'Sale Price', 'c' => 6, 'v' => 80 ],
            [ 'i' => 'sale_from', 'n' => 'Sale From', 't' => 'date', 'c' => 6.1 ],
            [ 'i' => 'sale_to', 'n' => 'Sale Till', 't' => 'date', 'c' => 6.1 ]
        ];
        $image_fields = [
            [ 'i' => 'image', 't' => 'upload', 'n' => 'Product Picture', 'a' => 'required', 'b' => 'Upload', 's' => .6, 'e' => 'png,svg,webp,jpg,jpeg' ],
            [ 'i' => 'gallery', 't' => 'upload', 'n' => 'Product Gallery', 'b' => 'Upload', 'm' => 8, 'v' => 'fake_images', 's' => .6, 'e' => 'png,svg,webp,jpg,jpeg' ],
        ];
        $r = $f->_random();
        !empty( $modal_class ) ? pre_modal( 'product', $modal_class ) : '';
        $f->pre_process( 'data-wrap id="product_form"', 'update_product_ajax', $r, 'p_', 2, 2 );
        _r();
        _c(8);
        $f->form( $main_fields, '', $r );

        pre_tabs( 'material mb20' );
        tab( 'Description', 1 );
        tab( 'Inventory' );
        tab( 'Tax' );
        tab( 'Properties' );
        tab( 'Variations' );
        post_tabs();
        pre( 'product_tab_data' );

            pre( 'description_data' );
            $desc_form = [
                [ 'i' => 'weight', 'n' => 'Weight', 't' => 'number', 'v' => 1 ],
                [ 'i' => 'width', 'n' => 'Width', 't' => 'number', 'v' => 10 ],
                [ 'i' => 'height', 'n' => 'Height', 't' => 'number', 'v' => 15 ],
                [ 'i' => 'depth', 'n' => 'Length / Depth', 't' => 'number', 'v' => 20 ],
                [ 't' => 'select', 'i' => 'shipping', 'n' => 'Shipping Method', 'o' => [], 'c' => 12 ],
            ];
            $f->form( $desc_form, 'settings', $r );
            post();

            pre( 'inventory_data' );
            $inventory_form = [
                [ 'i' => 'sku', 'n' => 'SKU', 'v' => 'ABC12345' ],
                [ 'i' => 'quantity', 'n' => 'Available quantity', 't' => 'number', 'v' => 50 ],
                [ 'i' => 'low_threshold', 'n' => 'Low stock alert on reaching quantity', 't' => 'number', 'v' => 5 ],
                [ 'i' => 'max', 'n' => 'Max quantity per order', 't' => 'number', 'v' => 1 ],
                [ 'i' => 'backorder', 'n' => 'Allow Backorder', 't' => 'radios', 'o' => [ 1 => 'Allow Backorders', 2 => 'Allow with notice to Buyer', 3 => 'Restrict Backorders' ], 'i_p' => 6, 'c' => 4, 's' => 3 ],
            ];
            $f->form( $inventory_form, 'settings', $r );
            post();

            pre( 'tax_data' );
            $tax_form = [
                [ 't' => 'select', 'i' => 'tax_category', 'n' => 'Tax Group', 'o' => [ 1 => 'Group 1 - 5%', 2 => 'Group 2 - 10%' ], 'c' => 12 ],
                [ 'i' => 'tax', 'n' => 'Override with custom tax %', 't' => 'number', 'v' => 5 ],
            ];
            $f->form( $tax_form, 'settings', $r );
            post();

            pre( 'properties_data' );
            $properties_form = [

            ];
            $f->form( $properties_form, 'settings', $r );
            post();

            pre( 'variations_data' );
            post();

        post();

        c_();
        _c(4);
        accordion( 'Prices', $f->_form( $price_fields, 'row', $r ), 'br15 w on' );
        accordion( 'Images', $f->_form( $image_fields, 'row', $r ), 'br15 w on' );
        accordion( 'SEO', $f->_form( $seo_fields, 'row', $r ), 'br15 w' );
        accordion( 'Visibility', $f->_form( $visibility_fields, 'row', $r ), 'br15 w' );
        $f->process_trigger('Save Product','w r');
        c_();
        r_();
        $hidden_fields = [
            [ 'id' => 'date', 'a' => 'class="dn"', 'v' => date('Y-m-d H:i:s') ],
            [ 'id' => 'id', 'a' => 'class="dn"' ],
        ];
        $f->form( $hidden_fields, 'row', $r );
        $f->post_process();
        !empty( $modal_class ) ? post_modal() : '';

        // Content Builder

        // Description
            // Primary Material
            // Secondary Material
            // Tertiary Material
            // Color
            // Size Guide
            // Weight
            // Width
            // Height
            // Depth
            // Shipping

        // Variations
            // Title
            // SKU
            // Picture
            // ...
    }

    function inventory(): void {
        // Shows list of inventory to be easily editable
    }

    /**
     * Renders Product HTML
     * @param array $p Product data as array
     * @param string $link_pre
     * @param int $title_tag
     * @param string|int $wrap_class
     * @param string $link_class
     * @param string $title_class
     * @param string $price_class
     * @param string $actions_class
     * @return void
     */
    function product( array $p, string $link_pre, int $title_tag = 2, string|int $wrap_class = '', string $link_class = '', string $title_class = '', string $price_class = '', string $actions_class = '' ): void {
        // Prices
        $regular_price = $p['prod_meta']['regular_price'] ?? 0;
        $sale_price = $p['prod_meta']['sale_price'] ?? 0;
        $price = $this->_price( $regular_price, $sale_price, $price_class );
        is_numeric( $wrap_class ) ? _c( $wrap_class ) : pre( '', $wrap_class );
            pre( 'product_'.$p['prod_id'], $link_class.' product_loop product_'.$p['prod_id'], 'a', 'href="'.APPURL.$link_pre.$p['prod_url'].'" title="'.$p['prod_title'].'"' );
                pre( '', 'image_wrap' );
                    img( storage_url( $p['prod_image'] ), '', 'image', $p['prod_title'], $p['prod_title'] );
                post();
                el( 'h'.$title_tag, $title_class.' title', '', $p['prod_title'] );
                $sale_price < $regular_price ? el( 'i', 'sale_tag tag', '', T('Sale') ) : '';
                echo $price;
                // TODO: Add product actions
                // skel( $p['prod_meta'] );
            post('a');
        is_numeric( $wrap_class ) ? c_() : post();
    }

    /**
     * Render Products (Archive)
     * @param string $content_style
     * @param string $wrapper_class
     * @param string|int $cols
     * @return void
     */
    function products( string $content_style = 'table', string $wrapper_class = '', string|int $cols = 4 ): void {
        if( in_array( $content_style, [ 'tables', 'table', 'list' ] ) ) {
            $this->products_list( $wrapper_class );
        } else if( in_array( $content_style, [ 'cards', 'card' ] ) ) {
            $this->product_cards( $wrapper_class, $cols );
        }
    }

    function products_list( string $wrapper_class = '' ): void {
        $products = $this->_products();
        $status = $this->product_statuses;
        if( empty( $products ) ) {
            no_content( 'No products added yet!', '', $wrapper_class );
        } else {
            $c = new CODE();
            $f = new FORM();
            $rate = REGION['rate'] ?? 1;
            $curr = REGION['symbol'];
            $table[] = [ 'head' => [ 'ID', 'Name', 'Visibility', 'Properties', 'Price', 'Sales', 'Status', 'Actions' ] ];
            foreach( $products as $p ) {
                //skel( $p );
                $table[]['body'] = [
                    $p['prod_id'],
                    $p['prod_title'].'<div><small>/'.$p['prod_url'].'</small></div>',
                    easy_date($p['prod_date']).'<div><small>'.T('Updated').': '.easy_date($p['prod_update']).'</small></div>',
                    (!empty($p['prod_birth'])?'<div><small>'.T('Visible from').': '.easy_date($p['prod_birth']).'</small></div>':'').(!empty($p['prod_expiry'])?'<div><small>'.T('Visible till').': '.easy_date($p['prod_expiry']).'</small></div>':''),
                    '<s>' . ( $rate * ( $p['prod_meta']['regular_price'] ?? 0 ) ) . '</s> ' . ( $rate * ( $p['prod_meta']['sale_price'] ?? 0 ) ) . ' ' . $curr,
                    0,
                    $status[ $p['prod_status'] ] ?? '',
                    _pre('','acts').$f->_edit_html( '#product_modal', $p, 'div', '', '', '', 'mat-ico', 'edit' )._post()
                ];
            }
            table_view( 'products_list', $table, $wrapper_class );
        }
    }

    function product_cards( string $wrapper_class = '', string|int $cols = 4, string $edit_modal = '' ): void {
        $products = $this->_products();
        $status = $this->product_statuses;
        if( empty( $products ) ) {
            no_content( 'No products added yet!', '', $wrapper_class );
        } else {
            $cards = [];
            foreach( $products as $p ) {
                $cards[] = _card( 'br15', $p['prod_title'], '', '/'.$p['prod_url'], '', '', $status[ $p['prod_status'] ] ?? '', '', [], [], $edit_modal, $p, 'products', "prod_id = {$p['prod_id']}" );
            }
            grid_view( 'product_cards', $cards, $wrapper_class, $cols );
        }
    }

    function _products(): array {
        $d = new DB();
        //$data = [ 'id', 'date', 'update', 'title', 'url', 'password', 'status', 'birth', 'expiry', 'by' ];
        $products = $d->select( 'products' );
        foreach( $products as $pk => $p ) {
            $data_meta = $d->select( 'product_meta', '', 'prod_meta_product = \''.$p['prod_id'].'\'' );
            $meta = [];
            foreach( $data_meta as $dm ) {
                $meta[ $dm['prod_meta_name'] ] = $dm['prod_meta_value'];
            }
            $products[ $pk ]['prod_meta'] = $meta;
        }
        return $products;
    }

    /**
     * If current page is archive page
     * @return bool
     */
    function is_archive(): bool {
        return 0;
    }

    /**
     * If current page is product page
     * @return bool
     */
    function is_product(): bool {
        return 0;
    }

    function product_filters() {

    }

    /**
     * Renders popup to add product
     * @param string $title
     * @param string $size
     * @return void
     */
    function product_modal( string $title = 'Product', string $size = 'm' ): void {

    }

    function get_product( string|int $identity, string $find_by = 'id', bool $admin = false ): array {
        $d = new DB();
        $query = $admin ? '' : 'prod_status = 1 AND ';
        $query .= $find_by == 'id' ? 'prod_id = \''.$identity.'\'' : 'prod_'.$find_by.' = \''.$identity.'\'';
        $product = $d->select( 'products', '', $query, 1 );
        if( !empty( $product ) && is_numeric( $product['prod_id'] ) ) {
            $product = replace_in_keys( $product, 'prod_' );
            $meta = $d->select( 'product_meta', 'prod_meta_name,prod_meta_value', 'prod_meta_product = \''.$product['id'].'\'' );
            if( !empty( $meta ) ) {
                foreach( $meta as $m ) {
                    $product['meta'][ $m['prod_meta_name'] ] = $m['prod_meta_value'];
                }
            }
        }
        return $product;
    }

    function _price( string|int|float|null $regular = 0, string|int|float|null $sale = 0, string $class = '' ): string {
        $rate = REGION['rate'] ?? 1;
        $curr = REGION['symbol'];
        $regular = !empty( $regular ) ? (float)$regular : 0;
        $sale = !empty( $sale ) ? (float)$sale : 0;
        if( $regular == 0 || $sale == 0 ) {
            return '';
        }
        if( $sale < $regular ) {
            $price = _div( '', $class.' price', _el( 's', 'regular', '', $rate * $regular ) . ' ' . _el( 'span', 'sale', '', $rate * $sale ) . ' '.$curr );
        } else {
            $price = _div( '', $class.' price', _el( 'span', 'sale', '', $rate * $regular ) . ' '.$curr );
        }
        return $price;
    }

    /**
     * Shows ECommerce Store Options
     * @return void
     */
    function store_options(): void {
        $f = new FORM();
        $d = new DB();
        $font_sizes = [ 'sm' => 'Small', 'm' => 'Medium', 'l' => 'Large', 'xl' => 'Large +' ];
        $aligns = [ 'l' => 'Left', 'c' => 'Center', 'r' => 'Right' ];
        $font_styles = [ 'n' => 'Normal', 'strong' => 'Bold', 'i' => 'Italic', 'bi' => 'Bold Italic' ];

        // Fetch saved data
        $general_form_ops = [ 'default_products_view', 'product_placeholder', 'show_product_view_toggle', 'show_grid_sizes', 'show_grid_s' ];
        $filters_form_ops = [ 'show_filters', 'filters_type', 'price_filter', 'filters_style', 'filters_position' ];
        $store_form_ops = [ 'cat_content_align', 'cat_title_show', 'cat_title_style', 'cat_title_size', 'cat_cat_show', 'cat_cat_style', 'cat_cat_size', 'cat_price_show', 'cat_price_style', 'cat_price_size', 'cat_price_var', 'cat_price_var_pre', 'cat_tag_show', 'cat_tag_style', 'cat_tag_position', 'cat_icons_style', 'cat_icons_position', 'cat_to_cart_style' ];
        $product_form_ops = [ 'product_gallery_position', 'product_gallery_style', 'product_gallery_arrows', 'product_gallery_dots', 'product_mini_gallery', 'product_mini_gallery_position', 'product_gallery_full', 'product_title_style', 'product_title_size', 'product_cat_style', 'product_cat_size', 'product_price_style', 'product_price_size', 'product_tag_show', 'product_tag_style', 'product_tag_position', 'product_show_share', 'product_content_style', 'product_show_desc', 'product_show_prop', 'product_show_reviews', 'product_show_related' ];
        $review_form_ops = [ 'enable_reviews', 'strict_purchased_reviews', 'moderate_reviews', 'review_images' ];
        $stock_form_ops = [ 'managed_stock', 'low_stock_threshold', 'stock_managers' ];
        $orders_form_ops = [ 'guest_checkout', 'checkout_guest_login', 'checkout_guest_register' ];
        $all_ops = array_merge( $general_form_ops, $filters_form_ops, $store_form_ops, $product_form_ops, $review_form_ops, $stock_form_ops, $orders_form_ops );
        $ops = $d->get_options( $all_ops );

        $f->option_params_wrap( '', 2, 2, $all_ops );
        pre_tabs( 'three mb30' );
            tab( 'Store', 1, '', 'store' );
            tab( 'Reviews', 0, '', 'hotel_class' );
            tab( 'Stock', 0, '', 'inventory' );
            //tab( 'Tax', 0, '', 'receipt' );
            tab( 'Orders', 0, '', 'local_mall' );
        post_tabs();

        pre( 'tab_data' );

            pre( 'store_data' );
                pre_tabs( 'material mb20' );
                    tab( 'General', 1 );
                    tab( 'Filters' );
                    tab( 'Store Page' );
                    tab( 'Product Page' );
                post_tabs();
                pre( 'store_tab_data' );

                    // General
                    pre( 'general_data' );
                    $general_form = [
                        [ 'i' => 'default_products_view', 'n' => 'Default products view', 'o' => [ 'Grid', 'List' ], 't' => 'radios', 'inputs_pre' => 3, 'c' => 4, 's' => $ops['default_products_view'] ?? [ 'Grid' ] ],
                        [ 'i' => 'product_placeholder', 'n' => 'Product image placeholder', 't' => 'upload', 'b' => 'Upload', 'c' => 4, 'v' => $ops['product_placeholder'] ?? '' ],
                        [ 'i' => 'show_product_view_toggle', 'n' => 'Grid / list toggle', 't' => 'slide', 'off' => 'Hide', 'on' => 'Show', 'c' => 4, 'v' => $ops['show_product_view_toggle'] ?? 2 ],
                        //[ 'i' => 'weight_unit', 'n' => 'Weight Unit', 'o' => [ 'mg', 'gram', 'kg', 'oz', 'lb' ], 'v' => 'kg', 't' => 'select', 'c' => 4 ],
                        //[ 'i' => 'size_unit', 'n' => 'Size Unit', 'o' => [ 'mm', 'cm', 'm', 'in', 'ft' ], 't' => 'select', 'c' => 4 ],
                        [ 'i' => 'show_grid_sizes', 'n' => 'Grid columns selection', 't' => 'slide', 'off' => 'Hide', 'on' => 'Show', 'c' => 4, 'v' => $ops['show_grid_sizes'] ?? 2 ],
                        [ 'i' => 'show_grid_s', 'n' => 'Grid columns', 't' => 'checkboxes', 'o' => [ 3 => '3 Columns', 4 => '4 Columns', 6 => '6 Columns', 8 => '8 Columns' ], 'k' => 1, 'inputs_pre' => 3, 'c' => 8, 's' => $ops['show_grid_s'] ?? [ 3, 4 ] ],
                    ];
                    $f->form( $general_form, 'settings', 'store' );
                    post();

                    // Filters
                    pre( 'filters_data', 'off' );
                    $filters_form = [
                        [ 'i' => 'show_filters', 'n' => 'Show filters', 'off' => 'Hide', 'on' => 'Show', 't' => 'slide', 'v' => $ops['show_filters'] ?? 1 ],
                        [ 'i' => 'filters_type', 'n' => 'Filter Parameters in URL', 'off' => 'Hide', 'on' => 'Show', 't' => 'slide', 'v' => $ops['filters_type'] ?? 1 ],
                        [ 'i' => 'price_filter', 'n' => 'Price filter', 'off' => 'Hide', 'on' => 'Show', 't' => 'slide', 'v' => $ops['price_filter'] ?? 1 ],
                        //[ 'i' => 'filters_style', 'n' => 'Filters Style', 'o' => [ 'cs' => 'Checkboxes', 'bs' => 'Check Buttons' ], 't' => 'radios', 'inputs_pre' => 3, 's' => $ops['filters_style'] ?? [ 'tac' ] ],
                        [ 'i' => 'filters_position', 'n' => 'Filters Position', 'o' => [ 'l' => 'Left', 't' => 'Top', 'r' => 'Right', 'f' => 'Floating' ], 'k' => 1, 't' => 'radios', 'inputs_pre' => 3, 's' => $ops['filters_position'] ?? [ 't' ] ],
                    ];
                    $f->form( $filters_form, 'settings', 'store' );
                    post();

                    // Product Options
                    pre( 'store_page_data', 'off' );
                    $store_form = [
                        [ 'i' => 'cat_content_align', 'n' => 'Product Content Alignment', 'o' => $aligns, 't' => 'radios', 'inputs_pre' => 3, 's' => $ops['cat_content_align'] ?? [ 'c' ] ],
                        [ 'i' => 'cat_title_show', 'n' => 'Product Title', 'off' => 'Hide', 'on' => 'Show', 't' => 'slide', 'v' => $ops['cat_title_show'] ?? 1 ],
                        [ 'i' => 'cat_title_style', 'n' => 'Title Style', 'o' => $font_styles, 't' => 'radios', 'inputs_pre' => 3, 's' => $ops['cat_title_style'] ?? [ 'n' ] ],
                        [ 'i' => 'cat_title_size', 'n' => 'Title Size', 'o' => $font_sizes, 't' => 'radios', 'inputs_pre' => 3, 's' => $ops['cat_title_size'] ?? [ 'sm' ] ],
                        [ 'i' => 'cat_cat_show', 'n' => 'Category Title', 'off' => 'Hide', 'on' => 'Show', 't' => 'slide', 'v' => $ops['cat_cat_show'] ?? 1 ],
                        [ 'i' => 'cat_cat_style', 'n' => 'Category Title Style', 'o' => $font_styles, 't' => 'radios', 'inputs_pre' => 3, 's' => $ops['cat_cat_style'] ?? [ 'strong' ] ],
                        [ 'i' => 'cat_cat_size', 'n' => 'Category Title Size', 'o' => $font_sizes, 't' => 'radios', 'inputs_pre' => 3, 's' => $ops['cat_cat_size'] ?? [ 'm' ] ],
                        [ 'i' => 'cat_price_show', 'n' => 'Show Price', 'off' => 'Hide', 'on' => 'Show', 't' => 'slide', 'v' => $ops['cat_price_show'] ?? 1 ],
                        [ 'i' => 'cat_price_style', 'n' => 'Price Title Style', 'o' => $font_styles, 't' => 'radios', 'inputs_pre' => 3, 's' => $ops['cat_price_style'] ?? [ 'n' ] ],
                        [ 'i' => 'cat_price_size', 'n' => 'Price Title Size', 'o' => $font_sizes, 't' => 'radios', 'inputs_pre' => 3, 's' => $ops['cat_price_size'] ?? [ 'l' ] ],
                        [ 'i' => 'cat_price_var', 'n' => 'Variation Price', 'o' => [ 'low' => 'Show starting price only', 'range' => 'Show Range', 'high' => 'Show highest price only'  ], 't' => 'radios', 'inputs_pre' => 3, 's' => $ops['cat_price_var'] ?? [ 'range' ] ],
                        [ 'i' => 'cat_price_var_pre', 'n' => 'Variation Price Pretext', 'p' => 'Ex: Starting, From, Upto etc.', 'v' => $ops['cat_price_var_pre'] ?? 'From' ],
                        [ 'i' => 'cat_tag_show', 'n' => 'Show Sale / New Tags', 'off' => 'Hide', 'on' => 'Show', 't' => 'slide', 'v' => $ops['cat_tag_show'] ?? 1 ],
                        [ 'i' => 'cat_tag_style', 'n' => 'Sale / New Tag Style', 'o' => [ 'round' => 'Rounded', 'square' => 'Squared', 'rect' => 'Rectangle' ], 't' => 'radios', 'inputs_pre' => 3, 's' => $ops['cat_tag_style'] ?? [ 'round' ] ],
                        [ 'i' => 'cat_tag_position', 'n' => 'Sale / New Tag Position', 'o' => [ 't l' => 'Top Left', 't r' => 'Top Right' ], 't' => 'radios', 'inputs_pre' => 3, 's' => $ops['cat_tag_position'] ?? [ 't l' ] ],
                        [ 'i' => 'cat_icons_style', 'n' => 'Product Icons Style', 'o' => [ 'h' => 'Horizontal', 'v' => 'Vertical' ], 't' => 'radios', 'inputs_pre' => 3, 's' => $ops['cat_icons_style'] ?? [ 'v' ] ],
                        [ 'i' => 'cat_icons_position', 'n' => 'Product Icons Position', 'o' => [ 't l' => 'Top Left', 't r' => 'Top Right' ], 't' => 'radios', 'inputs_pre' => 3, 's' => $ops['cat_icons_position'] ?? [ 't r' ] ],
                        [ 'i' => 'cat_to_cart_style', 'n' => 'Add to Cart Style', 'o' => [ 'icon' => 'Icon only', 'text' => 'Text only', 'hide' => 'Hidden' ], 't' => 'radios', 'inputs_pre' => 3, 's' => $ops['cat_to_cart_style'] ?? [ 'icon' ] ],
                        // Order
                    ];
                    $f->form( $store_form, 'settings', 'store' );
                    post();

                    // Product Page
                    pre( 'product_page_data', 'off' );
                    $product_form = [
                        [ 'i' => 'product_gallery_position', 'n' => 'Gallery Position', 'o' => $aligns, 't' => 'radios', 'inputs_pre' => 3, 's' => $ops['product_gallery_position'] ?? [ 'c' ] ],
                        [ 'i' => 'product_gallery_style', 'n' => 'Gallery Style', 'o' => [ '4g' => '4x4 Grid', 's' => 'Slider' ], 't' => 'radios', 'inputs_pre' => 3, 's' => $ops['product_gallery_style'] ?? [ 's' ] ],
                        [ 'i' => 'product_gallery_arrows', 'n' => 'Gallery Arrows', 'off' => 'Hide', 'on' => 'Show', 't' => 'slide', 'v' => $ops['product_gallery_arrows'] ?? 1 ],
                        [ 'i' => 'product_gallery_dots', 'n' => 'Gallery Dots Pagination', 'off' => 'Hide', 'on' => 'Show', 't' => 'slide', 'v' => $ops['product_gallery_dots'] ?? 1 ],
                        [ 'i' => 'product_mini_gallery', 'n' => 'Mini Gallery Pagination', 'off' => 'Hide', 'on' => 'Show', 't' => 'slide', 'v' => $ops['product_mini_gallery'] ?? 1 ],
                        [ 'i' => 'product_mini_gallery_position', 'n' => 'Mini Gallery Position', 'o' => $aligns, 't' => 'radios', 'inputs_pre' => 3, 's' => $ops['product_mini_gallery_position'] ?? [ 'c' ] ],
                        [ 'i' => 'product_gallery_full', 'n' => 'Full Screen Gallery Expansion', 'off' => 'Hide', 'on' => 'Show', 't' => 'slide', 'v' => $ops['product_gallery_full'] ?? 1 ],
                        [ 'i' => 'product_title_style', 'n' => 'Title Style', 'o' => $font_styles, 't' => 'radios', 'inputs_pre' => 3, 's' => $ops['product_title_style'] ?? [ 'n' ] ],
                        [ 'i' => 'product_title_size', 'n' => 'Title Size', 'o' => $font_sizes, 't' => 'radios', 'inputs_pre' => 3, 's' => $ops['product_title_size'] ?? [ 'xl' ] ],
                        [ 'i' => 'product_cat_style', 'n' => 'Category Title Style', 'o' => $font_styles, 't' => 'radios', 'inputs_pre' => 3, 's' => $ops['product_cat_style'] ?? [ 'strong' ] ],
                        [ 'i' => 'product_cat_size', 'n' => 'Category Size', 'o' => $font_sizes, 't' => 'radios', 'inputs_pre' => 3, 's' => $ops['product_cat_size'] ?? [ 'm' ] ],
                        [ 'i' => 'product_price_style', 'n' => 'Price Title Style', 'o' => $font_styles, 't' => 'radios', 'inputs_pre' => 3, 's' => $ops['product_price_style'] ?? [ 'n' ] ],
                        [ 'i' => 'product_price_size', 'n' => 'Price Title Size', 'o' => $font_sizes, 't' => 'radios', 'inputs_pre' => 3, 's' => $ops['product_price_size'] ?? [ 'xl' ] ],
                        [ 'i' => 'product_tag_show', 'n' => 'Show Sale / New Tags', 'off' => 'Hide', 'on' => 'Show', 't' => 'slide', 'v' => $ops['product_tag_show'] ?? 1 ],
                        [ 'i' => 'product_tag_style', 'n' => 'Sale / New Tag Style', 'o' => [ 'round' => 'Rounded', 'square' => 'Squared', 'rect' => 'Rectangle'  ], 't' => 'radios', 'inputs_pre' => 3, 's' => $ops['product_tag_style'] ?? [ 'round' ] ],
                        [ 'i' => 'product_tag_position', 'n' => 'Sale / New Tag Position', 'o' => [ 't l' => 'Top Left', 't r' => 'Top Right' ], 't' => 'radios', 'inputs_pre' => 3, 's' => $ops['cat_to_cart_style'] ?? [ 't l' ] ],
                        [ 'i' => 'product_show_share', 'n' => 'Show Share Icons', 'off' => 'Hide', 'on' => 'Show', 't' => 'slide', 'v' => $ops['product_show_share'] ?? 1 ],
                        [ 'i' => 'product_content_style', 'n' => 'Content Style', 'o' => [ 'a' => 'Accordion', 't' => 'Tabs', 's' => 'Stacked' ], 't' => 'radios', 'inputs_pre' => 3, 's' => $ops['cat_to_cart_style'] ?? [ 't' ] ],
                        [ 'i' => 'product_show_desc', 'n' => 'Show Description', 'off' => 'Hide', 'on' => 'Show', 't' => 'slide', 'v' => $ops['product_show_desc'] ?? 1 ],
                        [ 'i' => 'product_show_prop', 'n' => 'Show Properties', 'off' => 'Hide', 'on' => 'Show', 't' => 'slide', 'v' => $ops['product_show_prop'] ?? 1 ],
                        [ 'i' => 'product_show_reviews', 'n' => 'Show Reviews', 'off' => 'Hide', 'on' => 'Show', 't' => 'slide', 'v' => $ops['product_show_reviews'] ?? 1 ],
                        [ 'i' => 'product_show_related', 'n' => 'Show Related', 'off' => 'Hide', 'on' => 'Show', 't' => 'slide', 'v' => $ops['product_show_related'] ?? 1 ],
                        // Order
                    ];
                    $f->form( $product_form, 'settings', 'store' );
                    post();
                post();
            post();

            pre( 'reviews_data', 'off' );
            $review_form = [
                [ 't' => 'slide', 'i' => 'enable_reviews', 'n' => 'Product Reviews', 'off' => 'Disable', 'on' => 'Enable', 'c' => 4, 'v' => $ops['enable_reviews'] ?? 1 ],
                [ 't' => 'radios', 'i' => 'reviews_access', 'n' => 'Who can add review ?', 'o' => [ 'anyone' => 'Anyone, Even guests!', 'logged' => 'Registers users', 'buyers' => 'Only if they purchased product' ], 'c' => 3, 'i_p' => 4, 's' => $ops['reviews_access'] ?? 'buyers' ],
                [ 't' => 'slide', 'i' => 'moderate_reviews', 'n' => 'Moderate reviews', 'off' => 'No', 'on' => 'Yes', 'c' => 4, 'v' => $ops['moderate_reviews'] ?? 1 ],
                [ 't' => 'number', 'i' => 'review_images', 'n' => 'Max images in reviews', 'c' => 4, 'p' => 'Ex: 6 or 4 or 0 for no images', 'v' => $ops['review_images'] ?? 4 ],
            ];
            $f->form( $review_form, 'settings', 'store' );
            post();

            pre( 'stock_data', 'off' );
            $stock_form = [
                [ 't' => 'slide', 'i' => 'managed_stock', 'n' => 'Stock management', 'off' => 'Not needed', 'on' => 'Managed', 'v' => $ops['managed_stock'] ?? 1 ],
                [ 't' => 'number', 'i' => 'low_stock_threshold', 'n' => 'Low Stock Threshold', 'v' => $ops['low_stock_threshold'] ?? 5 ],
                [ 't' => 'select', 'i' => 'stock_managers', 'n' => 'Stock Managers', 'o' => [], 'v' => $ops['stock_managers'] ?? '' ],
                [ 't' => 'number', 'i' => 'max_quantity', 'n' => 'Max quantity per order', 'v' => $ops['max_quantity'] ?? 5 ],
            ];
            $f->form( $stock_form, 'settings', 'store' );
            post();
            /* pre( 'tax_data', 'off' );
            $tax_form = [
                [ 't' => 'slide', 'i' => 'tax_inclusive', 'n' => 'Enable guest checkout ?', 'c' => 4 ],
                [ 't' => 'slide', 'i' => 'checkout_guest_login', 'n' => 'Show login at checkout ?', 'c' => 4 ],
                [ 't' => 'slide', 'i' => 'checkout_guest_register', 'n' => 'Show registration at checkout ?', 'c' => 4 ],
            ];
            $f->form( $tax_form, 'row', 'store' );
            post(); */
            // Tax
                // Tax Inclusive
                // Tax based on [ 'Buyer Delivery Address', 'Buyer Billing Address', 'Store Address' ]
            pre( 'orders_data', 'off' );
            $orders_form = [
                [ 't' => 'slide', 'i' => 'guest_checkout', 'n' => 'Guest checkout', 'off' => 'Disable', 'on' => 'Enable', 'c' => 4, 'v' => $ops['guest_checkout'] ?? 1 ],
                [ 't' => 'slide', 'i' => 'checkout_guest_login', 'n' => 'Login at checkout', 'off' => 'Hide', 'on' => 'Show', 'c' => 4, 'v' => $ops['checkout_guest_login'] ?? 1 ],
                [ 't' => 'slide', 'i' => 'checkout_guest_register', 'n' => 'Sign up at checkout', 'off' => 'Hide', 'on' => 'Show', 'b' => 'Upload', 'c' => 4, 'v' => $ops['checkout_guest_register'] ?? 1 ],
            ];
            $f->form( $orders_form, 'settings', 'store' );
            post();
        post();

        $f->process_options( 'Save Store Options','store grad','','.tac');
        $f->post_process();
        file_upload();
    }

    /**
     * Shows ECommerce Archive Options
     * @return void
     */
    function product_archive_options(): void {
        // TODO: E Commerce Archive Options
    }

    /**
     * Shows ECommerce Product Options
     * @return void
     */
    function product_options(): void {
        // TODO: E Commerce Product Options
    }

    function property_form( string $title = '', string $modal_class = '' ): void {
        $f = new FORM();
        $c = new CODE();
        //$d = new DB();
        $prop_type_fields = [
            [ 'i' => 'name', 'n' => 'Property Type', 'p' => 'Ex: Material, Color etc.', 'c' => 8, 'a' => 'required' ],
            [ 't' => 'slide', 'i' => 'status', 'n' => 'Status', 'off' => '', 'on' => '', 'c' => 4, 'v' => 1 ],
            [ 'i' => 'desc', 'n' => 'Description', 'c' => 12 ],
            [ 'i' => 'type', 't' => 'select2', 'n' => 'Selection Type', 'o' => $this->property_types, 'k' => 1, 'c' => 4, 'v' => 'check', 'a' => 'required' ],
            [ 'i' => 'image', 't' => 'upload', 'e' => 'jpg,svg', 'n' => 'Image', 'b' => 'Upload', 'c' => 4 ],
            [ 'i' => 'icon', 't' => 'upload', 'e' => 'svg', 'n' => 'Icon', 'b' => 'Upload', 'c' => 4 ],
            //[ 'i' => 'multiple', 't' => 'slide', 'n' => 'User can filter multiple', 'off' => 'No', 'on' => 'Yes', 'c' => 4 ],
        ];
        $r = $f->_random();
        !empty( $modal_class ) ? pre_modal( $title, $modal_class ) : '';
        $f->pre_process( 'data-wrap', 'product_props', $r, 'prod_prop_', 2, 2 );
        $f->form( $prop_type_fields, 'row', $r );
        $f->process_trigger('Save '. $title,'','','','.tac');
        $f->post_process();
        !empty( $modal_class ) ? post_modal() : '';
    }

    function get_property( int $id ): array {
        $d = new DB();
        return $d->select( 'product_props', '', 'prod_prop_id = \''.$id.'\'', 1 );
    }

    function properties( string $target_form ): void {
        $d = new DB();
        $props = $d->select( 'product_props' );
        if( empty( $props ) ) {
            no_content( 'No product properties set!' );
        } else {
            $f = new FORM();
            $pts = $this->property_types;
            $table[] = [ 'head' => [ 'Icon', 'Name', 'Type', 'Status', 'Actions' ] ];
            foreach( $props as $p ) {
                $icon = !empty( $p['prod_prop_icon'] ) ? $p['prod_prop_icon'] : '';
                $status = $p['prod_prop_status'] == 1 ? $f->_slide( 'status', '', '', '', 1, 'm', 'disabled' ) : $f->_slide( 'status', '', '', '', 0, 'm', 'disabled' );
                $type = $pts[ $p['prod_prop_type'] ] ?? '-';
                $actions = '<div class="acts">';
                $actions .= $f->_view_html(APPURL.'admin/products/prop/'.$p['prod_prop_id'],'div','','','','mat-ico','open_in_new');
                $actions .= $f->_edit_html( $target_form,$p,'div','','','','mat-ico','edit');
                $actions .= $f->_trash_html('product_props','prod_prop_id = '.$p['prod_prop_id'],'div','','','','mat-ico',2,2,'Are you sure to remove property type? This will affect filters and products!','delete_forever');
                $actions .= '</div>';
                $table[] = [ 'body' => [ $icon, $p['prod_prop_name'], $type, $status, $actions ] ];
            }
            table( $table, 'tac' );
        }
    }

    function property_meta_form( int $id, string $title = '', string $modal_class = '' ): void {
        $f = new FORM();
        $c = new CODE();
        //$d = new DB();
        $prop_meta_fields = [
            [ 'i' => 'name', 'n' => $title.' Name', 'p' => 'Ex: Leather, Red, XXL etc.', 'c' => 9, 'a' => 'required' ],
            [ 't' => 'slide', 'i' => 'status', 'n' => 'Status', 'off' => '', 'on' => '', 'c' => 3, 'v' => 1 ],
            //[ 'i' => 'desc', 'n' => 'Description', 'c' => 12 ],
            //[ 'i' => 'type', 't' => 'select2', 'n' => 'Selection Type', 'o' => $this->property_types, 'k' => 1, 'c' => 4, 'v' => 'check', 'a' => 'required' ],
            [ 'i' => 'icon', 't' => 'upload', 'e' => 'svg', 'n' => 'Icon', 'b' => 'Upload', 'c' => 6 ],
            [ 'i' => 'color', 't' => 'color', 'n' => 'Color', 'c' => 6 ],
            //[ 'i' => 'image', 't' => 'upload', 'e' => 'jpg,svg', 'n' => 'Image', 'b' => 'Upload', 'c' => 4 ],
            //[ 'i' => 'multiple', 't' => 'slide', 'n' => 'User can filter multiple', 'off' => 'No', 'on' => 'Yes', 'c' => 4 ],
        ];
        $r = $f->_random();
        !empty( $modal_class ) ? pre_modal( $title, $modal_class ) : '';
        $f->pre_process( 'data-wrap', 'product_prop_meta', $r, 'prod_prop_meta_', 2, 2, [ "property" => $id ] );
        $f->form( $prop_meta_fields, 'row', $r );
        $f->process_trigger('Save '. $title,'','','','.tac');
        $f->post_process();
        !empty( $modal_class ) ? post_modal() : '';
    }

    function properties_meta( int $id, string $target_form ): void {
        $d = new DB();
        $props = $d->select( 'product_prop_meta', '', 'prod_prop_meta_property = '.$id );
        if( empty( $props ) ) {
            no_content( 'No product properties meta found!' );
        } else {
            $c = new CODE();
            $f = new FORM();
            $pts = $this->property_types;
            $table[] = [ 'head' => [ 'Icon', 'Name', 'Color', 'Status', 'Actions' ] ];
            foreach( $props as $p ) {
                $icon = !empty( $p['prod_prop_meta_icon'] ) ? $p['prod_prop_meta_icon'] : '';
                $status = $p['prod_prop_meta_status'] == 1 ? $f->_slide( 'status', '', '', '', 1, 'm', 'disabled' ) : $f->_slide( 'status', '', '', '', 0, 'm', 'disabled' );
                $actions = _pre('','acts');
                //$actions .= $f->_view_html(APPURL.'admin/products/prop/'.$p['prod_prop_id'],'div','','','','mat-ico','open_in_new');
                $actions .= $f->_edit_html( $target_form, $p, 'div','','','','mat-ico','edit');
                $actions .= $f->_trash_html('product_prop_meta','prod_prop_meta_id = '.$p['prod_prop_meta_id'],'div','','','','mat-ico',1,1,'Are you sure to remove property meta? This will affect filters and products!','delete_forever');
                $actions .= _post();
                $table[] = [ 'body' => [ $icon, $p['prod_prop_meta_name'], $p['prod_prop_meta_color'], $status, $actions ] ];
            }
            table( $table, 'tac' );
        }
    }

    function orders(): void {
        // TODO: Orders Table
        // TODO: Order Cards
    }

    /**
     * Renders HTML for Point of Sale
     * @return void
     */
    function pos(): void {

    }

    /**
     * Returns array of orders
     * @return array
     */
    function get_orders(): array {
        return [];
    }

    /**
     * Returns array of order and items
     * @return array
     */
    function get_order(): array {
        return [];
    }

    /**
     * Creates order
     * @return array
     */
    function create_order(): array {
        return [];
    }

    function order(): void {
        // TODO: Render Single Order
    }

    function order_row( int $id ): void {
        // TODO: Render Order Item Row
    }

    function track_order(): void {
        // TODO: Render Order Tracking HTML
    }

    /**
     * Returns array of cart items of current user
     * @param int $user User ID
     * @return array
     */
    function cart_items( int $user ): array {
        return [];
    }

    function mini_cart( string $close_content = '', string $wrapper_id = '', string $wrapper_class = '', string $cart_item_class = '', string $image_class = '', string $description_class = '' ): void {
        $f = new FORM();
        global $options;
        $empty_cart_content = isset( $options['empty_cart_content'] ) ? T( $options['empty_cart_content'] ) : T( 'Your cart seems empty! Please browse and add a few products to proceed to checkout' );
        $sub_total_title = isset( $options['sub_total_title'] ) ? T( $options['sub_total_title'] ) : T( 'Sub Total' );
        pre( 'aio_mini_cart_wrap', 'aio_mini_cart_wrap' );
            pre( 'aio_mini_cart_wrap', 'aio_mini_cart_wrap dn' );
            post();
            div( 'aio_mini_cart_empty', 'aio_mini_cart_empty', $empty_cart_content, 'data-empty-cart' );
            div( 'aio_mini_cart_total', 'aio_mini_cart_total dn df', _div( '', 'title', $sub_total_title ) . _div( '', 'subtotal', _el( 'span', 'amount', '', 0, 'data-amount' ) . ' ' . _el( 'span', 'currency', '', REGION['symbol'], 'data-currency-symbol' ) ) );
            pre( 'aio_mini_cart_item_template', 'aio_mini_cart_item_template dn', 'div', 'data-cart-item-template' );
                pre( '', $cart_item_class );
                    pre( '', 'cart_item df', 'a', 'href="{{url}}"' );
                        pre( '', $image_class );
                            img( '{{image}}', '', 'cart_item_image' );
                        post();
                        pre( '', $description_class );
                            div( '', 'cart_item_title', '{{title}}' );
                            div( '', 'cart_item_params', '{{params}}' );
                            div( '', 'cart_item_price', '{{price}} x {{quantity}}' );
                        post();
                    post( 'a' );
                    $f->pre_process( '', 'remove_item_from_cart_ajax', '', '', 0, 0, [], '', 'render_cart', 'Are you sure to remove item from cart?' );
                    $f->process_trigger( $close_content, 'remove_item' );
                    $f->post_process();
                post();
            post();
        post();
    }

    function cart(): void {

    }

    /**
     * Renders user orders table / cards for user profile page
     * @return void
     */
    function user_orders(): void {

    }

    /**
     * Renders Modal Viewer for User Order
     * @return void
     */
    function user_order(): void {

    }

    /**
     * Renders User Wishlist
     * @return void
     */
    function user_wishlist(): void {

    }

    /**
     * Renders user addresses table / cards for user
     * @return void
     */
    function user_addresses(): void {

    }

    /**
     * Renders modal to view / update user address
     * @return void
     */
    function user_address_modal(): void {

    }

    function update_product_metas( int $product_id, array $meta, int $new_product_id = 0 ): int {
        $db = new DB();
        $success = 0;
        foreach( $meta as $meta_key => $meta_value ) {
            if( !empty( $product_id ) && $meta_value !== '' ) {
                if( !empty( $new_product_id ) ) {
                    $update = $db->update( 'product_meta', [ 'product_meta_value', 'product_meta_product' ], [ $meta_value, $new_product_id ], 'product_meta_name = \''.$meta_key.'\' AND product_meta_product = \''.$product_id.'\'' );
                    $update ? $success++ : '';
                } else {
                    $data = [ 'name' => $meta_key, 'value' => $meta_value, 'product' => $product_id ];
                    $insert = $db->insert( 'product_meta', prepare_keys( $data, 'prod_meta_' ), prepare_values( $data ) );
                    $insert ? $success++ : '';
                }
            }
        }
        return $success;
    }

    function _product_meta( int $product_id, string $metas = 'quantity,max,tax,regular_price,sale_price' ): array {
        $d = new DB();
        $query = $d->ids_string_to_query( $metas, 'prod_meta_name' );
        $meta = [];
        $mts = $d->select( 'product_meta', 'prod_meta_name,prod_meta_value', 'prod_meta_product = \''.$product_id.'\' AND '.$query );
        if( !empty( $mts ) ) {
            foreach( $mts as $m ) {
                $meta[ $m['prod_meta_name'] ] = $m['prod_meta_value'];
            }
        }
        return $meta;
    }

}

function update_product_ajax(): void {
    $p = replace_in_keys( $_POST, 'p_' );
    if( !empty( $p['title'] ) ) {
        unset( $p['pre'] );
        unset( $p['t'] );
        $id = $p['id'] ?? 0;
        unset( $p['id'] );
        $p['content'] = htmlspecialchars( $p['content'] );
        $p['by'] = get_user_id();
        $p['update'] = date('Y-m-d H:i:s');
        $p['url'] = !empty( $p['url'] ) ? $p['url'] : strtolower( str_replace( ' ', '-', $p['title'] ) );
        $p['date'] = $p['date'] ?? date('Y-m-d H:i:s');
        $db = new DB();

        // Check if page exists with same slug
        $exist = $db->select( 'products', 'prod_id', "prod_url = '{$p['url']}'" );
        if( $exist ) {
            ef('Product with same url exist! Please change product title and url!!');
            return;
        }

        // Prepare Product Data
        $product_data = [];
        $prod_params = [ 'birth', 'content', 'by', 'update', 'date', 'expiry', 'gallery', 'image', 'meta_author', 'meta_desc', 'meta_words', 'password', 'title', 'url', 'status' ];
        foreach( $prod_params as $param_key ) {
            $product_data[ $param_key ] = $p[ $param_key ] ?? '';
            unset( $p[ $param_key ] );
        }
        //skel( $product_data );

        // Always insert a new product
        $saved = $db->insert( 'products', prepare_keys( $product_data, 'prod_' ), prepare_values( $product_data ) );
        if( $saved ) {
            $e = new ECOMMERCE();
            if( !empty( $id ) ) {
                // Update earlier product if exist as history
                $update = $db->update( 'products', [ 'prod_status', 'prod_parent' ], [ 4, $saved ], "prod_id = {$id}" );
                if( $update[0] > 0 ) {
                    $e->update_product_metas( (int)$id, $p, $update[0] );
                    es('Successfully updated product!');
                } else {
                    ef('Failed to update product!');
                }
            } else {
                $e->update_product_metas( $saved, $p );
                es('Successfully added new product!');
            }
            // Update meta
        } else {
            ef('Failed to save product, please consult administrator!');
        }
    } else {
        ef('Failed due to product title not set!');
    }
}

/**
 * AJAX Function to add product / variation to cart
 */
function update_item_to_cart_ajax(): void {
    if( isset( $_POST['product'] ) && is_numeric( $_POST['product'] ) ) {
        $d = new DB();
        $quantity = $_POST['quantity'] ?? 1;
        $max = $_POST['max'] ?? $d->get_options( 'max_quantity' );
        $pid = $_POST['product'];
        $uid = get_user_id();
        $exist = $d->select( 'cart', 'cart_id,cart_quantity', 'cart_product = \''.$pid.'\' && cart_user = \''.$uid.'\'', 1 );
        if( $exist ) {
            if( $exist['cart_quantity'] >= $max ) {
                ef( 'You have reached the maximum limit of quantity to purchase for this product!' );
            } else {
                $update = $d->update( 'cart', [ 'cart_quantity' ], [ $exist['cart_quantity'] + 1 ], 'cart_id = \''.$exist['cart_id'].'\'' );
                $update ? es( 'Successfully updated cart item quantity!' ) : ef( 'Failed to update cart item! Please consult support!!' );
            }
        } else {
            $data = [ 'user' => $uid, 'product' => $pid, 'quantity' => $quantity ];
            $add = $d->insert( 'cart', prepare_keys( $data, 'cart_' ), prepare_values( $data ) );
            $add ? es( 'Successfully added product to cart!' ) : ef( 'Failed to update cart item! Please consult support!!' );
        }
    } else {
        ef( 'Failed to get product ID! Please consult support!!' );
    }
}

function remove_item_from_cart_ajax(): void {
    if( isset( $_POST['id'] ) ) {
        $d = new DB();
        $action = $d->delete( 'cart', 'cart_id = \''.$_POST['id'].'\' && cart_user = \''.get_user_id().'\'' );
        $action ? es('Removed items from cart!') : ef('Failed to remove item from cart! Please consult support!!');
    }
}

function load_cart_ajax(): void {
    $d = new DB();
    $items = $d->select( [ 'cart', [ 'products', 'prod_id', 'cart_product' ] ], 'cart_quantity,prod_id,prod_title,prod_url,prod_image', 'cart_user = \''.get_user_id().'\'' );
    if( !empty( $items ) ) {
        $ec = new ECOMMERCE();
        foreach( $items as $i => $p ) {
            $items[ $i ]['meta'] = $ec->_product_meta( $p['prod_id'] );
        }
        //skel( $items );
        es( json_encode( $items ) );
    } else {
        ef( 'No items in Cart!' );
    }
}

function update_item_to_wishlist_ajax(): void {

}

function remove_item_from_wishlist_ajax(): void {

}

/**
 * Returns array of product details
 * @return array
 */
function get_product_ajax(): array {
    return [];
}

/**
 * Returns array of products
 * @return array
 */
function get_products_ajax(): array {
    return [];
}

/**
 * Returns array of orders
 * @return array
 */
function get_orders_ajax(): array {
    return [];
}

/**
 * Returns array of order and items
 * @return array
 */
function get_order_ajax(): array {
    return [];
}

/**
 * Creates order
 * @return void
 */
function create_order_ajax(): void {

}