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
        // TODO: E Commerce Store Options
        // Regional Options
            // Region
                // Address
                // Primary Language
                // Currency Symbol
                // Currency Rate
                // Serving Countries
                // Tax Options
                // Payment Options
        // Store Options
            // Placeholder
            // Weight Units
            // Size Units
            // Product Design
                // Page Style
                // Title Style
                //
            // Category Options
                // Show Grid / List Toggle
                // Grid Columns
                // Default Style [ Grid, List ]
                // Filters
                    // Filters Visibility
                    // Filters Style
                    // Filters Position
                // Sort Position
                // Product Options
                    // Title Style
                    // Title Position
                    // Price Style
                    // Variation Price Style
                    // Sale / New Tag Style
                    // Sale Tag Position
                    // New Tag Position
                    // Add to Cart Style
                    // Add to Cart Position
                    // Edit Product Icon Position
                // Grid Product Options
                    // Price Position
                // List Product Options
                    // Image Position
                    //
            // Reviews
                // Enable Reviews
                // Moderate Reviews
            // Stock
                // Enable Stock Management
                // Stock Managers
                // Low Stock Threshold
                // Out of Stock Threshold
            // Tax
                // Tax Inclusive
                // Tax based on [ 'Buyer Delivery Address', 'Buyer Billing Address', 'Store Address' ]
            // Orders
                // Guest Checkout
                // Show Guests login
                // Show Guests registration
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