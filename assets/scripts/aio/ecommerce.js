

function render_cart() {
    // Get Cart items
    post( $('[data-cart]').data('cart'), [], '', '', '', '', 'render_cart_items' );
}

function render_cart_items( items ) {
    // Get Cart Item Template
    let template = $('[data-cart-item-template]').html();
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
            let item = template
                .replaceAll('{{title}}',i['prod_title'])
                .replaceAll('{{params}}',i['prod_params']!==undefined?'':i['prod_params'])
                .replaceAll('{{quantity}}',1)
                .replaceAll('{{price}}',i['prod_price'])
                .replaceAll('{{url}}',i['prod_url'])
                .replaceAll('{{image}}',i['prod_image']);
            $('[data-cart-items]').append( item );
            quantity++;
            total += parseFloat( i['prod_price'] );
        });
    } else {
        $('[data-cart-items]').addClass('dn');
        $('[data-empty-cart]').removeClass('dn');
    }
    $('[data-cart-total]').html( total );
    $('[data-cart-quantity],[data-cart-count]').html( quantity );
}