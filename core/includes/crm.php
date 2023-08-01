<?php

class CRM {

    function __construct() {
    }

    function lead_filters(): void {
        // TODO: Leads Filters
    }

    function lead_modal( string $title = 'New Lead', string $attr = '' ): void {
        $f = new FORM();
        $db = new DB();
        $os = $db->get_option('lead_statuses');
        $os = !empty( $os ) ? array_map( 'trim', explode( ',', $os ) ) : [];
        //skel( APPDEBUG==1&&isset($os[1])?$os[1]:'' );
        ?>
        <div class="actions">
            <button data-on="#new_lead"><?php E( $title ); ?></button>
        </div>
        <div id="new_lead" class="modal <?php echo $attr; ?>">
            <h2 class="title"><?php E( $title ); ?></h2>
            <div class="close"></div>
            <?php $f->process_params('clients','lead','client_',4,4,['type'=>'lead','by'=>get_user_id()]); ?>
                <div class="row">
                    <?php
                    $f->text('name','Name','Ex: John Doe or John Traders LLC...',APPDEBUG==1?'fake_name':'','data-lead',12);
                    $f->select2('progress','Lead Status','Choose status...',$os,'New Lead' ,'data-lead',6);
                    $f->slide('status','Activity Status','Inactive','Active',1,'m','data-lead',6);
                    $f->phone('m_code','mobile','Code','Mobile','','Ex: 501112222','+971',APPDEBUG==1?'fake_phone':'','data-lead',6);
                    $f->input('email','email','Email','Ex: john@website.com',APPDEBUG==1?'fake_email':'','data-lead',6);
                    $f->textarea('note','Notes','Ex: Lead has ABC requirements...',APPDEBUG==1?'fake_content':'','data-lead',12);
                    ?>
                </div>
                <div class="tac">
                    <?php $f->process_html('Save Lead','mb0'); ?>
                </div>
            </div>
        </div>
        <?php
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