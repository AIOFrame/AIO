<?php
$db = new DB();

$products_struct = [
    [ 'client', 'INT', 13, 1 ],
    [ 'note', 'TEXT', 2048, 1 ],
    [ 'status', 'TINYINT', 1, 1 ],
    [ 'by', 'INT', 13, 1 ],
    [ 'dt', 'DATETIME', '', 1 ],
];
$meta_struct = [];
$categories_struct = [];
$orders_struct = [];
$order_items_struct = [];
$cart_struct = [];
$loc_struct = [];

$struct[] = [ 'products', $products_struct, 'prod', 1 ];
$struct[] = [ 'product_meta', $meta_struct, 'p_meta', 1 ];
$struct[] = [ 'product_cats', $categories_struct, 'p_cat', 1 ];
$struct[] = [ 'orders', $orders_struct, 'p_cat', 1 ];
$struct[] = [ 'order_items', $order_items_struct, 'p_cat', 1 ];
$struct[] = [ 'cart', $cart_struct, 'cart', 1 ];
$struct[] = [ 'addresses', $loc_struct, 'ua', 1 ];

//skel( $struct );
$db->automate_tables( $struct );