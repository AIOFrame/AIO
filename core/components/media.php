<div id="file_uploader" class="modal l">
    <div class="upload_header bb1">
        <h2 class="upload_title">File Manager</h2>
        <div class="row">
            <div class="col-4 tal">Drag and Drop Files or Choose file to upload</div>
            <div class="col-8">
                <input type="file" id="upload_file" class="mb0">
            </div>
        </div>
    </div>
    <div class="upload_body mt15">
        <div class="user_files drop_file row p10 fz0">
            <?php if (!empty($uimages = get_user_uploads())) {
                foreach ($uimages as $id => $img) {
                    if ($img[4] == 'jpg' || $img[4] == 'png' || $img[4] == 'jpeg') {
                        ?>
                        <div class="col-2">
                            <div class="uploaded_file  mb15" data-fileid="<?php echo $id; ?>"
                                 data-fileurl="<?php echo $img[1]; ?>">
                                <div class="upload_img"><img src="<?php echo $img[1]; ?>"></div>
                                <div class="file_name"><?php echo $img[0]; ?></div>
                            </div>
                        </div>

                    <?php } else { ?>
                        <div class="col-2">
                            <div class="uploaded_file gap15 mb15" data-fileid="<?php echo $id; ?>"
                                 data-fileurl="<?php echo $img[1]; ?>">
                                <div class="upload_file"><i class="s7 copy-file"></i></div>
                                <div class="file_name"><?php echo $img[0]; ?></div>
                            </div>
                        </div>

                    <?php }
                }
            } ?>
        </div>
        <div class="tac mb15">
            <button class="s blue" id="load_uploads" data-bg="white" onclick="load_uploads()" data-off="20">Load
                More
            </button>
        </div>
    </div>
    <div class="upload_footer modal_footer">
        <div class="mt15">
            <div class="image_details">
                <?php if (!empty($uimages = get_user_uploads())) { ?>
                <?php foreach ($uimages as $id => $img) { ?>
                <div class="row dn" data-imgid="<?php echo $id; ?>">
                    <?php if ($img[4] == 'jpg' || $img[4] == 'png' || $img[4] == 'jpeg') { ?>
                <img src="<?php echo $img[1]; ?>">
                    <span><?php echo $img[0]; ?></span>
                    <a href="<?php echo $img[1]; ?>" target="_blank">View in New Tab <i
                                class="s7 search"></i></a>
                    <span>Size: <?php echo $img[3]; ?> Kb</span>
                    <div class="joined">
                        <button class="s gray" data-bg="white"
                                onclick="ctc('<?php echo $img[1]; ?>')">Copy Link
                        </button>
                        <?php } else { ?>
                        <i class="s7 copy-file"></i> <?php echo $img[0]; ?>
                        <a class="s black" class="button" data-bg="white" href="<?php echo $img[1]; ?>"
                           target="_blank">Download</a>
                        Size: <?php echo $img[3]; ?> Kb
                        <div class="joined">
                            <?php } ?>
                            <button class="s black" data-bg="crimson"
                                    onclick="truncate_upload('<?php echo $id; ?>')"> Delete File
                            </button>
                        </div> <!-- // Col-joined-->
                    </div>
                    <?php } ?>
                    <div class="col-4 tar">
                        <button class="s red" id="insert_file" data-bg="lightgray">Insert File</button>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
</div>

<script>
    if(domain === ''){
        domain = window.location.href;
    }

    $(document).ready(function () {
        $('#insert_file').on('click', function () {
            if ($('.uptrigger').data('type') === 'id') {
                $('body').find($('.uptrigger').data('target')).val($('.uploaded_file.on').data('fileid'));
            } else {
                $('body').find($('.uptrigger').data('target')).val($('.uploaded_file.on').data('fileurl'));
            }
            $('body').find($('.uptrigger').data('preview')).html('<img src="' + $('.uploaded_file.on').data('fileurl') + '">');
            $('body').find($('.uptrigger').data('previewbg')).css('background-image', 'url("' + $('.uploaded_file.on').data('fileurl') + '")');
            $('.upload_bg').addClass('dn');
            $('body').find('.uptrigger').removeClass('uptrigger');
        });

        $('body').on('click', '.uploaded_file', function () {
            $('.uploaded_file').removeClass('on');
            $(this).addClass('on');
            var id = $(this).data('fileid');
            $('.image_details>div').addClass('dn');
            $('.image_details').find("[data-imgid='" + id + "']").removeClass('dn');
            $('#insert_file').attr('data-bg', 'teal');
        });
    })

    var dZ = $('.drop_file');
    dZ.on('drop', handleFileSelect);
    dZ.on('dragover', handleDragOver);
    dZ.on('dragleave', handleDragLeave);

    /* dropZone.addEventListener('drop', handleFileSelect, false);
    dropZone.addEventListener('dragover', handleDragOver, false);
    dropZone.addEventListener('dragleave', handleDragLeave, false); */

    function handleDragOver(evt) {
        evt.stopPropagation();
        evt.preventDefault();
        $('.drop_file').addClass('on');
    }

    function handleDragLeave(evt) {
        evt.stopPropagation();
        evt.preventDefault();
        $('.drop_file').removeClass('on');
    }

    function handleFileSelect(e) {
        e.stopPropagation();
        e.preventDefault();
        $('.drop_file').removeClass('on');

        var dt = e.dataTransfer || (e.originalEvent && e.originalEvent.dataTransfer);
        var files = e.target.files || (dt && dt.files);
        for (var i = 0, f; f = files[i]; i++) {
            var data = new FormData();
            var r = 'file_' + Math.round(Math.random() * 9999);
            $('#drop_zone').append('<div class="image_of_upload load ' + r + '"></div>');
            data.append(i, f);
            data.append('action', 'file_upload_process');
            $.ajax({
                url: '<?php echo APPURL; ?>',
                type: 'POST',
                data: data,
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    console.log(data);
                    var data = $.parseJSON(data);
                    if (data[0] === 'success') {
                        $('#drop_zone').find('.' + r).remove();
                        if (data[5] === 'jpg' || data[5] === 'png' || data[5] === 'jpeg') {
                            $('.user_files').prepend('<div class="uploaded_file col-1-6 gap15 mb15" data-fileid="' + data[4] + '" data-fileurl="' + data[3] + '"><div class="upload_img"><img src="' + data[3] + '"></div><div class="file_name">' + data[2] + '</div></div>');
                            $('.image_details').prepend('<div class="row dn" data-imgid="' + data[4] + '"><div class="col-4"><img src="' + data[3] + '"> ' + data[2] + '</div><div class="col col-5"><a href="' + data[3] + '" target="_blank">View in New Tab <i class="s7 search"></i></a></div><div class="col col-1-8">Size: ' + data[6] + ' Kb</div><div class="col col-1-8"><button class="s red" data-bg="white" onclick="ctc(\'' + data[3] + '\')">Copy Link</button></div><div class="col col-1-8"><button data-bg="crimson" onclick="truncate_upload(\'' + data[4] + '\')"> Delete File </button></div></div>');
                        } else {
                            $('.user_files').prepend('<div class="uploaded_file col-1-6 gap15 mb15" data-fileid="' + data[4] + '" data-fileurl="' + data[3] + '"><div class="upload_file"><i class="s7 copy-file"></i></div><div class="file_name">' + data[2] + '</div></div>');
                            $('.image_details').prepend('<div class="row dn" data-imgid="' + data[4] + '"><div class="col-4"><i class="s7 copy-file"></i> ' + data[2] + '</div><div class="col col-5"></div><div class="col col-1-8">Size: ' + data[6] + ' Kb</div><div class="col col-1-8"><a class="button" data-bg="white" href="' + data[3] + '" target="_blank">Download</a></div><div class="col col-1-8"><button data-bg="crimson" onclick="truncate_upload(\'' + data[4] + '\')"> Delete File </button></div></div>');
                        }
                        notify(data[0], data[1], data[2] + ' was successfully uploaded to cloud');
                    } else {
                        $('#drop_zone').find('.' + r).remove();
                        notify(data[0], data[1], data[2]);
                    }
                }
            });
        }
    }

    /* LOAD MORE UPLOADS */

    function load_uploads() {
        var off = $('#load_uploads').data('off'), fetch_files = {
            'action': 'ajax_user_uploads',
            'off': off
        };
        $.post(domain, fetch_files, function (r) {
            var files = $.parseJSON(r), q = 0;
            if (!$.isEmptyObject(files)) {
                for (var each in files) {
                    var file = files[each];
                    console.log(file[1]);
                    if (file[2] === 'jpg' || file[2] === 'png' || file[2] === 'jpeg') {
                        $('.user_files').append('<div class="uploaded_file col-1-6 gap15 mb15" data-fileid="' + each + '" data-fileurl="' + file[1] + '"><div class="upload_img"><img src="' + file[1] + '"></div><div class="file_name">' + file[0] + '</div></div>');
                        $('.image_details').append('<div class="row dn" data-imgid="' + each + '"><div class="col-4"><img src="' + file[1] + '"> ' + file[0] + '</div><div class="col col-5"><a href="' + file[1] + '" target="_blank">View in New Tab <i class="s7 search"></i></a></div><div class="col col-1-8">Size: ' + file[3] + ' Kb</div><div class="joied"><button  class="s gray" data-bg="white" onclick="ctc(\'' + each + '\')">Copy Link</button></div><div class="col col-1-8"><button class="s black" data-bg="crimson" onclick="truncate_upload(\'' + each + '\')"> Delete File </button></div></div>');
                    } else {
                        $('.user_files').append('<div class="uploaded_file col-1-6 gap15 mb15" data-fileid="' + each + '" data-fileurl="' + file[1] + '"><div class="upload_file"><i class="s7 copy-file"></i></div><div class="file_name">' + file[0] + '</div></div>');
                        $('.image_details').append('<div class="row dn" data-imgid="' + each + '"><div class="col-4"><i class="s7 copy-file"></i> ' + file[0] + '</div><div class="col col-5"></div><div class="col col-1-8">Size: ' + file[3] + ' Kb</div><div class="col col-1-8"><a class="button" data-bg="white" href="' + file[1] + '" target="_blank">Download</a></div><div class="col col-1-8"><button data-bg="crimson" onclick="truncate_upload(\'' + each + '\')"> Delete File </button></div></div>');
                    }
                    q++;
                }
                if (q < 20) {
                    $('#load_uploads').hide();
                }
                $('#load_uploads').data('off', off + q);
            } else {
                $('#load_uploads').hide();
            }
        });
    }

    /* UPLOAD MEDIA */
    //
    function media_upload(e) {
        console.log(e);
        $('#file_uploader').show();
        $('.upload_title').html($(e).data('title'));
        $('#insert_file').html($(e).data('button'));
        $(e).addClass('uptrigger');
    }

    /* REMOVE UPLOAD */

    function truncate_upload(id) {
        $.post(domain, {action: 'truncate_upload', id: id}, function (r) {
            var r = $.parseJSON(r);
            if (r[0] === 'success') {
                $('.user_files').find("[data-fileid='" + id + "']").remove();
                $('.image_details').find("[data-imgid='" + id + "']").remove();
            }
            notify(r[0], r[1], r[2]);
        })
    }

    function ctc(t) {
        prompt('Press Ctrl + C to copy', t);
        document.execCommand('copy');
    }
</script>
