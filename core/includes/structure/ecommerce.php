<?php
$db = new DB();

$products_struct = [
    [ 'date', 'DATETIME', '', 1 ],
    [ 'update', 'DATETIME', '', 1 ],
    [ 'title', 'TEXT', '', 1 ],
    [ 'content', 'LONGTEXT', '', 0 ],
    [ 'url', 'VARCHAR', 128, 1 ],
    [ 'password', 'VARCHAR', 256, 0 ],
    [ 'type', 'TINYINT', 1, 0 ],
    [ 'parent', 'INT', 13, 0 ],
    [ 'image', 'VARCHAR', 512, 0 ],
    [ 'gallery', 'TEXT', '', 0 ],
    [ 'status', 'VARCHAR', 1, 1 ],
    [ 'birth', 'DATETIME', '', 0 ],
    [ 'expiry', 'DATETIME', '', 0 ],
    [ 'meta_desc', 'VARCHAR', 512, 0 ],
    [ 'meta_words', 'VARCHAR', 512, 0 ],
    [ 'meta_author', 'VARCHAR', 256, 0 ],
    [ 'by', 'INT', 13, 0 ],
    //[ 'client', 'INT', 13, 1 ],
    //[ 'note', 'TEXT', 2048, 1 ],
];
$meta_struct = [
    [ 'product', 'INT', 13, 1 ],
    [ 'name', 'VARCHAR', 256, 1 ],
    [ 'value', 'TEXT', '', 0 ],
    [ 'load', 'BOOL', '', 0 ],
];
$property_types_struct = [
    [ 'name', 'VARCHAR', 256, 1 ],
    [ 'type', 'VARCHAR', 8, 1 ],
    [ 'desc', 'VARCHAR', 512, 0 ],
    [ 'icon', 'VARCHAR', 32, 0 ],
    [ 'image', 'VARCHAR', 512, 0 ],
    [ 'multiple', 'BOOL', '', 0 ],
    [ 'status', 'BOOL', '', 1 ],
];
$properties_meta_struct = [
    [ 'property', 'INT', 13, 1 ],
    [ 'name', 'VARCHAR', 256, 1 ],
    //[ 'desc', 'VARCHAR', 512, 0 ],
    //[ 'icon', 'VARCHAR', 32, 0 ],
    //[ 'value', 'VARCHAR', 256, 0 ],
    //[ 'image', 'VARCHAR', 512, 0 ],
    [ 'color', 'VARCHAR', 12, 0 ],
    [ 'status', 'BOOL', '', 1 ],
];
$orders_struct = [];
$order_items_struct = [];
$cart_struct = [
    [ 'product', 'INT', 13, 1 ],
    [ 'user', 'INT', 13, 1 ],
    [ 'quantity', 'INT', 6, 1 ],
];
$loc_struct = [
    [ 'user', 'INT', 13, 1 ],
    [ 'name', 'VARCHAR', 64, 1 ],
    [ 'a_name', 'VARCHAR', 24, 1 ],
    [ 'address', 'VARCHAR', 512, 1 ],
    [ 'street', 'VARCHAR', 256, 0 ],
    [ 'lat', 'VARCHAR', 256, 0 ],
    [ 'long', 'VARCHAR', 256, 0 ],
    [ 'city', 'VARCHAR', 512, 1 ],
    [ 'state', 'VARCHAR', 512, 1 ],
    [ 'po', 'VARCHAR', 12, 0 ],
    [ 'country', 'VARCHAR', 128, 1 ],
    [ 'type', 'TINYINT', 1, 0 ],
    [ 'dt', 'DATETIME', 0, 1 ],
    [ 'code', 'VARCHAR', 5, 1 ],
    [ 'phone', 'VARCHAR', 24, 1 ],
    [ 'email', 'VARCHAR', 64, 1 ],
    [ 'status', 'TINYINT', 1, 1 ],
];

$struct[] = [ 'products', $products_struct, 'prod', 1 ];
$struct[] = [ 'product_meta', $meta_struct, 'prod_meta', 1 ];
$struct[] = [ 'product_prop_types', $property_types_struct, 'prod_prop', 1 ];
$struct[] = [ 'product_prop_meta', $properties_meta_struct, 'prod_prop_meta', 1 ];
$struct[] = [ 'orders', $orders_struct, 'p_cat', 1 ];
$struct[] = [ 'order_items', $order_items_struct, 'p_cat', 1 ];
$struct[] = [ 'cart', $cart_struct, 'cart', 1 ];
$struct[] = [ 'addresses', $loc_struct, 'ua', 1 ];

//skel( $struct );
$db->automate_tables( $struct );