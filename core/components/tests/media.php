<h2>Single File Uploads</h2>
<div class="row" style="margin-bottom: 100px">
    <?php
    $f = new FORM();
    $f->upload('passport','Passport','Upload','',0,0,'','','jpg,png,pdf','.5',1,'',6);
    $f->upload('visa','Visa','Upload','',0,1,'','','','2',1,'',3);
    $f->upload('id','ID Front','Upload','',0,0,'','','jpg,png','.2',0,'',2);
    $f->upload('pic','Picture','','',0,1,'','','svg','.1',0,'',1);
    ?>
    <div class="col-6">
        <div>Previous Uploads - False</div>
        <div>Size - 512 Kb</div>
        <div>Types - jpg,png,pdf</div>
        <div>Deletable - True</div>
        <div>col-6</div>
    </div>
    <div class="col-3">
        <div>True</div>
        <div>2 Mb</div>
        <div>Any</div>
        <div>True</div>
        <div>col-3</div>
    </div>
    <div class="col-2">
        <div>False</div>
        <div>204 Kb</div>
        <div>jpg,png</div>
        <div>False</div>
        <div>col-2</div>
    </div>
    <div class="col-1">
        <div>True</div>
        <div>102 Kb</div>
        <div>svg</div>
        <div>False</div>
        <div>col-1</div>
    </div>
</div>
<h2>Multiple File Uploads</h2>
<div class="row" style="margin-bottom: 100px">
    <?php
    $f->upload('docs','Documents','Upload Documents','',8,0,'','','jpg,png,pdf','.5',1,'',12);
    $f->upload('passport','Passport','Upload','',8,0,'','','jpg,png,pdf','.5',1,'',6);
    $f->upload('visa','Visa','Upload','',5,1,'','','','2',1,'',3);
    $f->upload('id','ID Front','Upload','',4,0,'','','jpg,png','.2',0,'',2);
    $f->upload('pic','Picture','','',2,1,'','','svg','.1',0,'',1);
    ?>
</div>
<h2>TODO</h2>
<?php
$todo = file_get_contents(ROOTPATH . 'core/todo/media.md');
skel( $todo );
get_styles('bootstrap/css/bootstrap-grid');
file_upload();
?>