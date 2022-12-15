<?php $f = new FORM(); ?>
<div class="config_form">
    <?php
    get_comp('config/general');
    get_comp('config/features');
    get_comp('config/modules');
    get_comp('config/database');
    get_comp('config/functions');
    get_comp('config/apis');
    // TODO: Routes in Config
    // TODO: Default Users in Config
    $f->process_html('Build Config','my40','','build_config_ajax','col-12 tac');
    ?>
</div>