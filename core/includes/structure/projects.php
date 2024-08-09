<?php
$db = new DB();
$client = [ 'client', 'INT', 13, 1 ];
$name = [ 'name', 'VARCHAR', 256, 1 ];
$desc = [ 'desc', 'VARCHAR', 512, 0 ];
$icon = [ 'icon', 'VARCHAR', 128, 0 ];
$color = [ 'color', 'VARCHAR', 7, 0 ];
$status = [ 'status', 'TINYINT', 1, 1 ];
$by = [ 'by', 'INT', 13, 1 ];
$cat = [ 'category', 'INT', 13, 1 ];
$dt = [ 'dt', 'DATETIME', '', 1 ];
$pro = [ 'project', 'INT', 13, 1 ];
$amount = [ 'amount', 'FLOAT', '', 0 ];
$user = [ 'user', 'INT', 13, 1 ];

// TODO: projects
// TODO: project_flows

$struct = [
    [ 'clients', [
        $name,
        $status,
        [ 'logo', 'VARCHAR', 512, 0 ],
        [ 'tno', 'VARCHAR', 128, 0 ],
        [ 'rid', 'VARCHAR', 128, 0 ],
        [ 'address', 'VARCHAR', 256, 0 ],
        [ 'city', 'VARCHAR', 256, 0 ],
        [ 'state', 'VARCHAR', 256, 0 ],
        [ 'country', 'VARCHAR', 128, 0 ],
        [ 'zone', 'VARCHAR', 256, 0 ],
        [ 'zip', 'VARCHAR', 64, 0 ],
        [ 'phone_code', 'VARCHAR', 5, 0 ],
        [ 'phone', 'VARCHAR', 16, 0 ],
        [ 'fax', 'VARCHAR', 24, 0 ],
        [ 'email', 'VARCHAR', 64, 0 ],
        [ 'website', 'VARCHAR', 64, 0 ],
    ], 'client', 1 ],
    [ 'features', [
            $name,
            $icon,
            $status,
            $desc,
            $color,
            [ 'type', 'VARCHAR', 128, 0 ]
    ], 'feat', 1 ],

    [ 'projects', [
        $client,
        $status,
        $cat,
        $by,
        [ 'start', 'DATE', '', 0 ],
        [ 'expiry', 'DATE', '', 0 ],
        [ 'lead', 'INT', 13, 0 ],
        [ 'sponsor', 'INT', 13, 0 ],
        [ 'structure', 'TEXT', '', 0 ],
        [ 'scope_version', 'VARCHAR', 8, 0 ],
        $dt,
    ], 'pro', 1 ],
    [ 'project_meta', [
        $pro,
        $name,
        [ 'value', 'TEXT', '', 0 ],
        [ 'type', 'VARCHAR', 128, 0 ],
        $status,
        $by
    ], 'pro_meta', 1 ],
    [ 'project_feats', [
        $pro,
        [ 'feature', 'INT', 13, 1 ],
        $status
    ], 'pro_feat', 1 ],
    [ 'project_users', [
        $pro,
        $user,
        [ 'role', 'INT', 13, 1 ],
        $status,
        $by,
        $dt
    ], 'pro_user', 1 ],
    [ 'project_scope', [
        $client,
        $name,
        $status,
        [ 'in', 'TINYINT', 1, 1 ],
        $by,
        $dt,
    ], 'pro_scope', 1 ],
    [ 'project_scope_meta', [
        $pro,
        [ 'scope', 'INT', 13, 1 ],
        [ 'name', 'VARCHAR', 256, 1 ],
        [ 'value', 'TEXT', '', 0 ],
        [ 'duration', 'FLOAT', '', 0 ],
        [ 'duration_unit', 'VARCHAR', 8, 0 ],
        [ 'type', 'VARCHAR', 128, 0 ],
        $status,
        $by
    ], 'pro_sm', 1 ],
    [ 'project_scope_users', [
        $pro,
        [ 'scope', 'INT', 13, 1 ],
        $user,
        $by,
        $dt
    ], 'pro_su', 1 ],
    [ 'project_finance', [
        $pro,
        $name,
        $amount,
        $status,
        $dt,
        $by,
    ] ],
    [ 'project_expenses', [
        $pro,
        $name,
        $desc,
        $amount,
        $status
    ] ],
];
// TODO: project_issues
// TODO: project_test_cases

//skel( $struct );
//$db->automate_tables( $struct );