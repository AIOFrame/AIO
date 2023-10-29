

function render_cart() {
    // Get Cart items
    post( $('[data-cart]').data('cart'), [], '', '', '', '', 'render_cart_items' );
}

function render_cart_items( items ) {
    // Get Cart Item Template
    let mini_cart_item = $('[data-mini-cart-item-template]').html();
    let cart_item = $('[data-cart-item-template]').html();
    // Empty Cart Wrapper
    //items = JSON.parse( items );
    let total = 0;
    let quantity = 0;
    if( items.length > 0 ) {
        $('[data-cart-items]').removeClass('dn').html('');
        $('[data-empty-cart]').addClass('dn');
        // Loop and display cart items
        $( items ).each( function (x, i) {
            console.log(i);
            if( mini_cart_item !== undefined ) {
                let mini_item = mini_cart_item
                    .replaceAll('{{title}}', i['prod_title'])
                    .replaceAll('{{params}}', i['prod_params'] !== undefined ? i['prod_params'] : '')
                    .replaceAll('{{quantity}}', i['cart_quantity'])
                    .replaceAll('{{price}}', i['prod_price_view'])
                    .replaceAll('{{url}}', i['prod_url'])
                    .replaceAll('{{id}}', i['prod_id'])
                    .replaceAll('{{image}}', i['prod_image']);
                $('[data-mini-cart-items]').append(mini_item);
            }
            if( cart_item !== undefined ) {
                let item = cart_item
                    .replaceAll('{{title}}', i['prod_title'])
                    .replaceAll('{{params}}', i['prod_params'] !== undefined ? i['prod_params'] : '')
                    .replaceAll('{{quantity}}', i['cart_quantity'])
                    .replaceAll('{{price}}', i['prod_price'])
                    .replaceAll('{{price_view}}', i['prod_price_view'])
                    .replaceAll('{{url}}', i['prod_url'])
                    .replaceAll('{{total}}', i['prod_total'])
                    .replaceAll('{{total_view}}', i['prod_total_view'])
                    .replaceAll('{{id}}', i['prod_id'])
                    .replaceAll('{{image}}', i['prod_image']);
                $('[data-cart-items]').append(item);
            }
            $('[data-cart-item-id]').val(  );
            quantity++;
            total += parseFloat( i['prod_total'] );
        });
    } else {
        $('[data-cart-items]').addClass('dn');
        $('[data-empty-cart]').removeClass('dn');
    }
    $('[data-cart-total]').html( total );
    $('[data-cart-quantity],[data-cart-count]').html( quantity );
}