<?php

class PERMS {

    function user_type_form( bool $modal = true ): void {
        $f = new FORM();
        $c = new CODE();
        $perms = CONFIG['permissions'] ?? [];
        $user_type_fields = [
            [ 'i' => 'role', 'n' => 'User Title / Role', 'p' => 'Ex: Developer, Marketing Manager, Accountant', 'a' => 'required' ],
            [ 'i' => 'perms', 't' => 'checkboxes', 'n' => 'Permissions', 'o' => $perms, 'c' => 12, 'iw' => 'row', 'i_p' => 4, 'a' => 'class="slide"' ]
        ];
        $r = $f->_random();
        $modal ? $c->pre_modal( 'User Restrictions', 'f on' ) : '';
        $f->pre_process( 'data-wrap id="perm_form"', 'update_user_type_ajax', $r, 'perm_', 2, 2 );
        $f->form( $user_type_fields, 'row', $r );
        $f->process_trigger('Save User Role','','','','.tac');
        $modal ? $c->post_modal() : '';
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