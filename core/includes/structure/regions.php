<?php

/**
 * Creates file upload database
 */

$regions_database[] = [ 'regions', [
    [ 'domain', 'VARCHAR', 253, 1 ],
    [ 'country', 'VARCHAR', 3, 1 ],
    [ 'language', 'VARCHAR', 3, 1 ],
    //[ 'name', 'VARCHAR', 512, 0 ],
    //[ 'company_code', 'VARCHAR', 64, 0 ],
    //[ 'tax_code', 'VARCHAR', 64, 0 ],
    //[ 'company_doc', 'VARCHAR', 512, 0 ],
    //[ 'tax_doc', 'VARCHAR', 512, 0 ],
    //[ 'tax', 'FLOAT', '', 1 ],

    [ 'timezone', 'VARCHAR', 64, 1 ],
    [ 'currency_code', 'VARCHAR', 8, 0 ],
    [ 'currency_symbol', 'VARCHAR', 6, 0 ],
    [ 'currency_rate', 'FLOAT', '', 1 ],
    [ 'date_format', 'VARCHAR', 16, 1 ],
    [ 'time_format', 'VARCHAR', 16, 1 ],
    [ 'status', 'BOOL', '', 1 ],
], 'reg', 1 ];

$db = new DB();
$db->automate_tables( $regions_database );