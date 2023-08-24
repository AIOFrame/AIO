<div class="data">
    <?php
    foreach( $_POST as $k => $v ) {
        echo !is_array( $v ) ? '<input type="hidden" name="'.$k.'" value="'.$v.'">' : '<input type="hidden" name="'.$k.'[]" value=\''.serialize($v).'\'">';
    }
    ?>
    <div class="q">
        <label for="type">Select Database Type</label>
        <div class="row">
            <?php
            $bases = [
                ''=>'No Database Required',
                'mysql'=>'MySQL',
                'sql_lite'=>'SQL Lite',
                'mongodb'=>'MongoDB',
                'mssql'=>'Microsoft SQL Server',
                'firebase'=>'Firebase',
                'oracle'=>'Oracle',
                'pg_sql'=>'Post-gre SQL',
            ];
            $form->radios('type','',$bases,'','',0,4);
            ?>
        </div>
    </div>
    <div class="q">
        <div class="row">
            <div class="col-6">
                <label for="server"><span>Database Server Host / URL</span></label>
                <div><input type="text" id="server" name="server" placeholder="Ex: localhost"></div>
            </div>
            <div class="col-6">
                <label for="base"><span>Database Name</span></label>
                <div><input type="text" id="base" name="base" placeholder="Ex: <?php echo $appdir; ?>_db"></div>
            </div>
        </div>
    </div>
    <div class="q">
        <div class="row">
            <div class="col-6">
                <label for="user">Username</label>
                <input type="text" id="user" name="user" placeholder="Ex: <?php echo $appdir; ?>, admin etc.">
            </div>
            <div class="col-6">
                <?php $form->text('pass','Password'); ?>
            </div>
        </div>
    </div>
</div>