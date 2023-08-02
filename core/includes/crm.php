<?php

class CRM {

    function __construct() {
    }

    function lead_filters(): void {
        // TODO: Leads Filters
    }

    function lead_modal( string $title = 'Lead', string $size = 'm' ): void {
        $db = new DB();
        $c = new CODE();

        $os = $db->get_option('lead_statuses');
        $os = !empty( $os ) ? array_map( 'trim', explode( ',', $os ) ) : [];
        $fields = [
            [ 't' => 'text', 'i' => 'name', 'n' => 'Name', 'p' => 'Ex: John Doe or John Traders LLC...', 'v' => APPDEBUG==1?'fake_name':'', 'c' => 12 ],
            [ 't' => 'phone', 'i' => 'm_code', 'i2' => 'mobile', 'n' => 'Code', 'n2' => 'Mobile', 'v' => APPDEBUG==1?'+971':'', 'v2' => APPDEBUG==1?'fake_phone':'', 'c' => 6 ],
            [ 't' => 'email', 'i' => 'email', 'n' => 'Email', 'p' => 'Ex: john@website.com...', 'v' => APPDEBUG==1?'fake_email':'', 'c' => 6 ],
            [ 't' => 'slide', 'i' => 'status', 'n' => 'Activity Status', 'off' => 'Inactive', 'on' => 'Active', 'a' => 'class="s"', 'v' => APPDEBUG==1?'fake_name':'', 'c' => 6 ],
            [ 't' => 'select2', 'i' => 'progress', 'n' => 'Lead Status', 'p' => 'Choose status...', 'o' => $os, 'v' => 1, 'c' => 6 ],
            [ 't' => 'textarea', 'i' => 'note', 'n' => 'Notes', 'p' => 'Ex: Lead has ABC requirements......', 'v' => APPDEBUG==1?'fake_content':'', 'c' => 12 ],
        ];
        ?>
        <div class="actions">
            <button data-on="#lead_modal"><?php E( $title ); ?></button>
        </div>
        <?php
        $c->modal( 'Lead', $size, 'clients', $fields, ['type'=>'lead','by'=>get_user_id()], 'client_', 4, 4, 'Successfully saved lead!' );
    }

    function render_leads(): void {
        // TODO: Leads Table
        // TODO: Leads Cards
        // TODO: Leads Kanban
    }

    function render_lead_viewer(): void {
        // TODO: View Lead Popup
    }

    function customer_filters(): void {
        // TODO: Customer Filters
    }

    function customer_modal( string $attr = '' ): void {
        // TODO: Add / Edit Customer Modal
    }

    function render_customers(): void {
        // TODO: Customer Table
        // TODO: Customer Cards
    }

    function render_customer_viewer(): void {
        // TODO: View Customer Popup
    }

    function options(): void {
        $f = new FORM();
        $db = new DB();
        $ops = ['lead_statuses','client_statuses'];
        $os = $db->get_options( $ops );
        $f->option_params_wrap('cd',2,2);
        $f->text('lead_statuses','Lead Statuses','Ex: Potential Client, New Lead, No Response...',$os['lead_statuses'] ?? '','data-cd',12);
        //$f->text('client_statuses','Client Statuses','Ex: Potential Client, New Lead, No Response...',$os['lead_statuses'] ?? '','data-cd',12);
        $f->process_options('Save Options','store grad','','.col-12 tac');
        echo '</div>';
    }

}