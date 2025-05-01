<?php

class CLIENTS {

    function client_card( string $url = '', string $name = '', string $reg_id = '', string $logo = '', bool $status = false, string $website = '' ): void {
        echo $this->__client_card( $url, $name, $reg_id, $logo, $status, $website );
    }

    function __client_card( string $url = '', string $name = '', string $reg_id = '', string $logo = '', bool $status = false, string $website = '' ): string {
        return __card( 'client_card', $name, $url, $reg_id, $logo, 'logo', T( $status == 1 ? 'Active' : 'Inactive' ), 'status', [ 'Website' => $website ] );
    }

    function client_row( int $id = 0, string $name = '', string $reg_name = '', string $email = '', string $phone = '' ): void {
        echo $this->__client_row( $id, $name, $reg_name, $email, $phone );
    }

    function __client_row( int $id = 0, string $name = '', string $reg_name = '', string $email = '', string $phone = '' ): string {
        return '';
    }

    function __client_filter_fields(): array {
        return [
            [ 'i' => 'client_name', 'n' => 'Company Name', 'p' => 'Ex: Apple...', 'c' => 3, 'q' => 'LIKE' ],
            [ 'i' => 'client_rid', 'n' => 'Reg. No.', 'p' => 'Ex: ABC123...', 'c' => 3, 'q' => 'LIKE' ],
            [ 'i' => 'client_country', 'n' => 'Country', 'p' => 'Choose...', 'c' => 3, 't' => 's2', 'q' => 'LIKE' ],
        ];
    }

    function client_filters( string $clear_url = '', string $method = 'GET' ): void {
        $f = new FORM();
        $f->filters( $this->__client_filter_fields(), $clear_url, $method );
    }

    function clients(): void {
        echo $this->__clients();
    }

    function __clients( string $query = '', int $count = 20, string $wrap_col = '4', string $view_client_url = '' ): string {
        $d = new DB();
        $clients = $d->select( 'clients', '', $query, $count );
        $r = '';
        if( !empty( $clients ) ) {
            foreach ( $clients as $c ) {
                $r .= __c( $wrap_col ) . $this->__client_card( $view_client_url.'/'.$c['client_id'], $c['client_name'], $c['client_rid'], $c['client_logo'], $c['client_status'], $c['client_website'] ) . c__();
            }
        } else {
            return __no_content('No clients found!');
        }
        return $r;
    }

    function __client_fields(): array {
        $cs = get_countries('iso2');
        return [
            [ 't' => 'step', 'n' => 'Details', 'fields' => [
                [ 'i' => 'name', 'n' => 'Company Name', 'p' => 'Ex: Mercedes or Apple etc.', 'c' => 9 ],
                [ 'i' => 'status', 'n' => 'Status', 't' => 'slide', 'v' => 1, 'c' => 3 ],
                [ 'i' => 'logo', 'n' => 'Logo', 'p' => 'Upload', 't' => 'file', 's' => .2, 'e' => 'svg,png,jpg,jpeg,gif', 'c' => 3 ],
                [ 'i' => 'rid', 'n' => 'Registration No.', 'p' => 'Ex: 2541-2435-4455', 'c' => 3 ],
                [ 'i' => 'tno', 'n' => 'Tax Reg. No.', 'p' => 'Ex: 2541-2435-4455', 'c' => 3 ],
                [ 'i' => 'website', 'n' => 'Website', 'p' => 'Ex: help@client.com', 'c' => 3 ],
            ] ],
            [ 't' => 'step', 'n' => 'Address', 'fields' => [
                [ 'i' => 'address', 'n' => 'Address', 'p' => 'Ex: #1501, Rolls Royce Tower', 'c' => 8 ],
                [ 'i' => 'city', 'n' => 'City / Region', 'p' => 'Ex: Downtown', 'c' => 4 ],
                [ 'i' => 'state', 'n' => 'State', 'p' => 'Ex: Dubai', 'c' => 4 ],
                [ 'i' => 'country', 'n' => 'Country', 'p' => 'Choose...', 't' => 'select2', 'o' => $cs, 'k' => 1, 'c' => 4 ],
                [ 'i' => 'zip', 'n' => 'Zip Code', 'p' => 'Ex: 12021', 'c' => 4 ],
                //[ 'i' => 'zone', 'n' => '', 'p' => '', 'c' => 1 ],
            ] ],
            /* [ 't' => 'step', 'n' => 'Communication', 'fields' => [
                [ 'i' => 'phone_code', 'i2' => 'phone', 'n' => 'Code', 'n2' => 'Phone', 'p2' => 'Ex: 1235 456 8574', 't' => 'phone', 'c' => 6 ],
                [ 'i' => 'fax', 'n' => 'Fax', 'p' => 'Ex: 1220 145 6585', 'c' => 6 ],
                [ 'i' => 'email', 'n' => 'Email', 'p' => 'Ex: www.website.com', 'c' => 6 ],
            ] ], */
        ];
    }

    function __client_form( string $class = '', string $data_attr = '', string $wrap_class = '', string $callback = '', string $redirect_url = '' ): string {
        $f = new FORM();
        $form = $this->__client_fields();
        return $f->__pre_process( '', 'clients', $data_attr, 'client_', [], 'Successfully saved client!', '', $callback, $redirect_url ) .
            $f->__form( $form, $class, $data_attr, '', $wrap_class ) .
            $f->__process_trigger( 'Save Client', '', '', '', '.tac' ) .
        $f->__post_process();
    }

    function client_form( string $class = '', string $data_attr = '', string $wrap_class = '', string $callback = '', string $redirect_url = '' ): void {
        echo $this->__client_form( $class, $data_attr, $wrap_class, $callback, $redirect_url );
    }

    function client_modal( string $title = 'Client', string $size = 'm', bool $editable = true ): void {
        echo $this->__client_modal( $title, $size, $editable );
    }

    function __client_modal( string $title = 'Client', string $size = 'm', bool $editable = true ): string {
        return __pre_modal( $title, $size, $editable ) .
            $this->__client_form() .
            __post_modal();
    }

    function client(): void {
        echo $this->__client();
    }

    function __client(): string {
        return '';
    }

}