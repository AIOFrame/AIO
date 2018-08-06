<div id="file_permissions" class="modal_bg full dn">
    <div class="modal md p15">
        <span class="hider" data-action="hide" data-target="#file_permissions"></span>
        <div class="modal_header bb1">
            <h2 class="mt0">Set Access Permissions</h2>
        </div>
        <div class="modal_body tal">
            <?php
            $lvls = select('levels',true);
            if(!empty($lvls)){
                foreach($lvls as $lvl){
                    echo '<input type="checkbox" value="'.$lvl['level_id'].'" name="user_levels" id="level_'.$lvl['level_id'].'" class="levels"><label for="level_'.$lvl['level_id'].'">'.$lvl['level_name'].'</label>';
                }
            }
            ?>
        </div>
        <div class="modal_footer tar">
            <button type="button" data-bg="teal" id="set_file_perm">Finalize</button>
        </div>
    </div>
</div>
<script>
    function file_perm(e) {
        $('#file_permissions').removeClass('dn').show().data('target', e);
        if ($($('#file_pick_sort').data('target')).data('options')) {
            $.each($($('#file_pick_sort').data('target')).data('options').scripts, function (i, v) {
                $('#drop_scripts').append('<li class="file_icon p10 mb15"><i class="text_icon">CSS</i><div data-type="style" data-slug="' + v + '">' + v.substring(0, 1).toUpperCase() + v.slice(0, -7).substring(1) + '<div class="rem_menu abs"><i class="s7 close"></i></div></div>');
            });
        } else {
            $('#drop_scripts, #drop_styles').html('');
        }
    }
    $('#set_file_perm').on('click',function(){
        var perms = $(this).parent().prev().children('input:checkbox:checked').map(function() {
            return this.value;
        }).get();
        $($('#file_permissions').data('target')).parent().parent().parent().parent().data('access',perms);
        $('#file_permissions').addClass('dn').hide();
    });
</script>