<?php
// TODO:

class ECOMMERCE {

    function __construct() {

    }

    // Backend

    /**
     * Add / Update product
     * @return void
     */
    function save_product(): void {}

    /**
     * Renders Product HTML
     * @return void
     */
    function render_product(): void {}

    /**
     * Render Products (Archive)
     * @return void
     */
    function render_products(): void {
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

    /**
     * Renders administration side product viewer
     * @return void
     */
    function render_product_viewer(): void {

    }

    /**
     * Shows ECommerce Options
     * @return void
     */
    function options(): void {

    }

}