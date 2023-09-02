<?php
// TODO:

class ECOMMERCE {

    function __construct() {

    }

    // Backend

    /**
     * Add / Update product
     * @param string $modal_class
     * @return void
     */
    function product_form( string $modal_class = '' ): void {
        // Identity
            // Title
            // URL / Slug

        // Visibility
            // Visibility From
            // Visibility Till
            // Status
            // Password

        // SEO
            // Meta Description
            // Meta Keywords
            // Meta Author

        // Pricing
            // Regular Price
            // Sale Price
            // Sale From
            // Sale Till

        // Picture
        // Gallery

        // Save Product

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

        // Inventory
            // SKU
            // Quantity
            // Low Stock Threshold
            // Max Quantity per order
            // Allow Backorder [ Allow, Allow with alert to buyer, Restrict ]

        // Tax
            // Tax Group
            // Override Tax %

        // Properties
            // Dynamic Properties

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
     * @return void
     */
    function product(): void {}

    /**
     * Render Products (Archive)
     * @return void
     */
    function products(): void {
        // TODO: Render products as table
        // TODO: Render products as cards
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

    function get_products(): array {
        return [];
    }

    function get_product(): array {
        return [];
    }

    /**
     * Shows ECommerce Store Options
     * @return void
     */
    function store_options(): void {
        $c = new CODE();
        $f = new FORM();
        $c->pre_tabs( 'three mb30' );
            $c->tab( 'Store', 1, '', 'store' );
            $c->tab( 'Reviews', 0, '', 'hotel_class' );
            $c->tab( 'Stock', 0, '', 'inventory' );
            //$c->tab( 'Tax', 0, '', 'receipt' );
            $c->tab( 'Orders', 0, '', 'local_mall' );
        $c->post_tabs();

        $c->pre( 'tab_data' );

            $c->pre( 'store_data' );
                $c->pre_tabs( 'material mb20' );
                    $c->tab( 'General', 1 );
                    $c->tab( 'Filters' );
                    $c->tab( 'Category / Store Page' );
                    $c->tab( 'Product Page' );
                $c->post_tabs();
                $c->pre( 'store_tab_data' );

                    // General
                    $c->pre( 'general_data' );
                    $general_form = [
                        [ 'i' => 'default_products_view', 'n' => 'Default products view', 'o' => [ 'Grid', 'List' ], 't' => 'radios', 'inputs_pre' => 6, 'c' => 4 ],
                        [ 't' => 'upload', 'i' => 'product_placeholder', 'n' => 'Product image placeholder', 'b' => 'Upload', 'c' => 4 ],
                        [ 't' => 'slide', 'i' => 'show_product_view_toggle', 'n' => 'Grid / list toggle', 'off' => 'Hide', 'on' => 'Show', 'c' => 4 ],
                        //[ 'i' => 'weight_unit', 'n' => 'Weight Unit', 'o' => [ 'mg', 'gram', 'kg', 'oz', 'lb' ], 'v' => 'kg', 't' => 'select', 'c' => 4 ],
                        //[ 'i' => 'size_unit', 'n' => 'Size Unit', 'o' => [ 'mm', 'cm', 'm', 'in', 'ft' ], 't' => 'select', 'c' => 4 ],
                        [ 't' => 'slide', 'i' => 'show_grid_sizes', 'n' => 'Grid columns selection', 'off' => 'Hide', 'on' => 'Show', 'c' => 4 ],
                        [ 't' => 'checkboxes', 'i' => 'show_grid_s', 'n' => 'Grid columns', 'o' => [ '3 Columns', '4 Columns', '6 Columns', '8 Columns' ], 'inputs_wrap' => 'row', 'inputs_pre' => 3, 'c' => 8 ],
                    ];
                    $f->form( $general_form, 'settings', 'store' );
                    $c->post();

                    // Filters
                    $c->pre( 'filters_data', 'off' );
                    $filters_form = [
                        [ 'i' => 'show_filters', 'n' => 'Show filters', 'off' => 'Hide', 'on' => 'Show', 't' => 'slide' ],
                        [ 'i' => 'filters_type', 'n' => 'Filter Parameters in URL', 'off' => 'Hide', 'on' => 'Show', 't' => 'slide' ],
                        [ 'i' => 'price_filter', 'n' => 'Price filter', 'off' => 'Hide', 'on' => 'Show', 't' => 'slide' ],
                        [ 'i' => 'filters_style', 'n' => 'Filters Style', 'o' => [ 'Checkboxes', 'Check Buttons' ], 't' => 'radios', 'inputs_pre' => 3 ],
                        [ 'i' => 'filters_position', 'n' => 'Filters Position', 'o' => [ 'Left', 'Top', 'Right', 'Floating' ], 't' => 'radios', 'inputs_wrap' => 'row', 'inputs_pre' => 3 ],
                    ];
                    $f->form( $filters_form, 'settings', 'store' );
                    $c->post();

                    // Product Options
                    $c->pre( 'category_store_page_data', 'off' );
                    $filters_form = [
                        [ 'i' => 'product_content_align', 'n' => 'Product Content Alignment', 'o' => [ 'tal' => 'Left', 'tac' => 'Center', 'tar' => 'Right' ], 't' => 'radios', 'inputs_wrap' => 'row', 'inputs_pre' => 4 ],
                        [ 'i' => 'product_title_show', 'n' => 'Product Title', 'off' => 'Hide', 'on' => 'Show', 't' => 'slide' ],
                        [ 'i' => 'product_title_style', 'n' => 'Title Style', 'o' => [ 'n' => 'Normal', 'strong' => 'Bold' ], 't' => 'radios', 'inputs_wrap' => 'row', 'inputs_pre' => 4 ],
                        [ 'i' => 'product_title_size', 'n' => 'Title Size', 'o' => [ 'xs' => 'Small -', 'sm' => 'Small', 'm' => 'Medium', 'l' => 'Large', 'xl' => 'Large +' ], 'k' => 1, 't' => 'radios', 'inputs_wrap' => 'row', 'inputs_pre' => 2 ],
                        [ 'i' => 'show_price', 'n' => 'Price Size', 'o' => [ 'xs' => 'Extra Small', 'sm' => 'Small', 'm' => 'Medium', 'l' => 'Large', 'xl' => 'Extra Large' ], 'k' => 1, 't' => 'select' ],
                        [ 'i' => 'price_size', 'n' => 'Price Size', 'o' => [ 'xs' => 'Extra Small', 'sm' => 'Small', 'm' => 'Medium', 'l' => 'Large', 'xl' => 'Extra Large' ], 'k' => 1, 't' => 'select' ],
                    ];
                    $f->form( $filters_form, 'settings', 'store' );
                        // Price Style
                        // Variation Price Style
                        // Sale / New Tag Style
                        // Sale Tag Position
                        // New Tag Position
                        // Add to Cart Style
                        // Add to Cart Position
                        // Edit Product Icon Position
                    $c->post();

                    // Grid Product Options
                    $c->pre( 'grid_data', 'off' );
                    // Price Position
                    $c->post();

                    // List Product Options
                    $c->pre( 'list_data', 'off' );
                    // Image Position
                    $c->post();

                    // Product Page
                    $c->pre( 'product_page_data', 'off' );
                    // Page Style
                    // Gallery
                        // Gallery Style
                        // Show Gallery Arrows
                        // Show Gallery Dots
                        // Sub Gallery Style
                        // Sub Gallery Position
                    // Title Style
                    // Price Style
                    // Attributes Style
                    // Add to Cart Style
                    // Share Style
                    // Product Page Widgets
                    $c->post();
                $c->post();
            $c->post();

            $c->pre( 'reviews_data', 'off' );
            $review_form = [
                [ 't' => 'slide', 'i' => 'enable_reviews', 'n' => 'Product Reviews', 'off' => 'Disable', 'on' => 'Enable', 'c' => 4 ],
                [ 't' => 'slide', 'i' => 'strict_purchased_reviews', 'n' => 'Who can add review ?', 'off' => 'Anyone', 'on' => 'Only Purchased', 'c' => 4 ],
                [ 't' => 'slide', 'i' => 'moderate_reviews', 'n' => 'Moderate reviews ?', 'off' => 'No', 'on' => 'Yes', 'c' => 4 ],
            ];
            $f->form( $review_form, 'settings', 'store' );
            $c->post();

            $c->pre( 'stock_data', 'off' );
            $tax_form = [
                [ 't' => 'slide', 'i' => 'managed_stock', 'n' => 'Stock management', 'off' => 'Not needed', 'on' => 'Managed', 'c' => 4 ],
                [ 't' => 'number', 'i' => 'low_stock_threshold', 'n' => 'Low Stock Threshold', 'c' => 4 ],
                [ 't' => 'select', 'i' => 'stock_managers', 'n' => 'Stock Managers', 'o' => [], 'c' => 12 ],
            ];
            $f->form( $tax_form, 'settings', 'store' );
            $c->post();
            /* $c->pre( 'tax_data', 'off' );
            $tax_form = [
                [ 't' => 'slide', 'i' => 'tax_inclusive', 'n' => 'Enable guest checkout ?', 'c' => 4 ],
                [ 't' => 'slide', 'i' => 'checkout_guest_login', 'n' => 'Show login at checkout ?', 'c' => 4 ],
                [ 't' => 'slide', 'i' => 'checkout_guest_register', 'n' => 'Show registration at checkout ?', 'c' => 4 ],
            ];
            $f->form( $tax_form, 'row', 'store' );
            $c->post(); */
            // Tax
                // Tax Inclusive
                // Tax based on [ 'Buyer Delivery Address', 'Buyer Billing Address', 'Store Address' ]
            $c->pre( 'orders_data', 'off' );
            $orders_form = [
                [ 't' => 'slide', 'i' => 'guest_checkout', 'n' => 'Guest checkout', 'off' => 'Disable', 'on' => 'Enable', 'c' => 4 ],
                [ 't' => 'slide', 'i' => 'checkout_guest_login', 'n' => 'Login at checkout', 'off' => 'Hide', 'on' => 'Show', 'c' => 4 ],
                [ 't' => 'slide', 'i' => 'checkout_guest_register', 'n' => 'Sign up at checkout', 'off' => 'Hide', 'on' => 'Show', 'b' => 'Upload', 'c' => 4 ],
            ];
            $f->form( $orders_form, 'settings', 'store' );
            $c->post();
        $c->post();
        file_upload();
        // TODO: E Commerce Store Options
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

    function categories(): void {
        // TODO: Categories Table
        // TODO: Categories Cards
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

    function mini_cart(): void {

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

}

/**
 * AJAX Function to add product / variation to cart
 * @return array
 */
function update_item_to_cart_ajax(): array {
    return [];
}

function update_item_to_wishlist_ajax(): array {
    return [];
}

function remove_item_from_wishlist_ajax(): array {
    return [];
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