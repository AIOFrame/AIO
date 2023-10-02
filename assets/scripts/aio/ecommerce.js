

function render_cart() {
    // Get Cart Item Template
    let template = $('[data-cart-item-template]').html();
        //'<li class="woocommerce-mini-cart-item mini_cart_item"><a href="#" class="remove remove_from_cart_button" aria-label="Remove this item" data-product_id="444806" data-cart_item_key="9ed6c8b624dcd377cc677f708ba66e44" data-product_sku="CT0979-003">×</a><a href="https://ossloop.com/product/jordan-1-zoom-cmft-metallic-silver-womens/"><img width="300" height="180" src="https://ossloop.com/wp-content/uploads/2023/09/jordan-w-jordan-1-zoom-air-cmft-300x180.jpg.webp" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail ls-is-cached lazyloaded" alt="" decoding="async" loading="lazy" data-src="https://ossloop.com/wp-content/uploads/2023/09/jordan-w-jordan-1-zoom-air-cmft-300x180.jpg.webp" data-eio-rwidth="300" data-eio-rheight="180" data-src-webp="https://ossloop.com/wp-content/uploads/2023/09/jordan-w-jordan-1-zoom-air-cmft-300x180.jpg.webp"><noscript><img width="300" height="180" src="https://ossloop.com/wp-content/uploads/2023/09/jordan-w-jordan-1-zoom-air-cmft-300x180.jpg" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" alt="" decoding="async" loading="lazy" data-eio="l"></noscript>Jordan 1 Zoom CMFT Metallic Silver (Women’s)</a><span class="quantity">1 × <span class="woocommerce-Price-amount amount"><bdi>1328&nbsp;<span class="woocommerce-Price-currencySymbol">AED</span></bdi></span></span></li>';

    // Get Cart items
    let items = [];

    // Empty Cart Wrapper
    if( items.count > 0 ) {
        $('.aio_mini_cart_wrap').removeClass('dn').html('');
        $('[data-empty-cart]').addClass('dn');
        // Loop and display cart items
        $.each( items, function (x, i) {
            let item = template
                .replaceAll('{{title}}','Product title')
                .replaceAll('{{params}}','Product params')
                .replaceAll('{{quantity}}',1)
                .replaceAll('{{price}}','250 AED')
                .replaceAll('{{url}}','')
                .replaceAll('{{image}}','https://placehold.it/100');
            $('.aio_mini_cart_wrap').append( item );
        });
    } else {
        $('.aio_mini_cart_wrap').addClass('dn');
        $('[data-empty-cart]').removeClass('dn');
    }


}