<?php

class PERMS {

    function user_type_form( string $title = 'User Role', string $modal_class = '' ): void {
        $f = new FORM();
        $c = new CODE();
        $perms = CONFIG['permissions'] ?? [];
        $user_type_fields = [
            [ 'i' => 'role', 'n' => 'User Title / Role', 'p' => 'Ex: Developer, Marketing Manager, Accountant', 'a' => 'required' ],
            [ 'i' => 'perms', 't' => 'checkboxes', 'n' => 'Permissions', 'o' => $perms, 'c' => 12, 'iw' => 'row', 'i_p' => 4 ]
        ];
        $r = $f->_random();
        !empty( $modal_class ) ? pre_modal( $title, $modal_class ) : '';
        $f->pre_process( 'data-wrap', 'update_user_type_ajax', $r, 'perm_' );
        $f->form( $user_type_fields, 'row', $r );
        $f->process_trigger('Save '.$title,'','','','.tac');
        !empty( $modal_class ) ? post_modal() : '';
    }

    function user_types(): void {
        echo $this->_user_types();
    }

    function _user_types(): string {
        return '';
    }

}

function update_user_type_ajax(): void {

}