$(document).ready(function(){

    // Close Uploader on click outside
    $(document).mouseup(function(e) {
        var c = $('#aio_up');
        if (!c.is(e.target) && c.has(e.target).length === 0) {
            close_uploader();
        }
    });

    // Select or Deselect Uploaded File
    $('body').on('click','.f',function(){
        if( $('#aio_up').data('multiple') === undefined  ) {
            $('.f').not(this).removeClass('on');
        }
        $(this).toggleClass('on');
        //elog($(this).data('delete'));
        if( $(this).data('delete') !== '' && $(this).data('delete') !== undefined ){
            if( $(this).data('delete') === 0 ){
                $('#aio_up .fd').addClass('disabled');
            } else if( $(this).data('delete') === 1 ) {
                $('#aio_up .fd').removeClass('disabled');
            }
        }
    })

    // Delete a file from Uploaded Files
    .on('click','.fd',function(){
        var df = $('.f.on');
        if( df.length > 0 && df.data('id') !== undefined && df !== '' ) {
            $.post(location.origin,{'action':'file_delete','id':df.data('id')},function(r){
                if( r = JSON.parse(r) ) {
                    uploader_notify(r[1]);
                    if( r[0] === 1 ){
                        $('.f.on').remove();
                    }
                }
            })
        }
    })

    // Close Uploader
    .on('click','#aio_up .close',function(){
        close_uploader();
    })
    .on('keyup',function(e){
        if( $('#aio_up').hasClass('on') && e.key === 'Escape' ) {
            close_uploader();
        }
    })

    // Show Uploader
    .on('click','[data-upload]',function(e){
        file_upload(e.target);
    })


    // Standard File Chosen to Upload
    .on('change','#file_input',function(){
        var fs = this.files;
        process_upload(fs);
    })

    // File Insert
    .on('click','#aio_up .fi',function(){
        var f = $('#aio_up');
        var s = $('#aio_up .f.on');
        if( f.data('multiple') === undefined ) {

            var exts = $('#aio_up').data('exts');
            if( exts ){
                exts = exts.split(',');
                var ext = s.data('url').split('.')[s.data('url').split('.').length - 1];
                if( exts.indexOf(ext) < 0 ){
                    var msg = $('#aio_up .extension_limit').html();
                    uploader_notify(msg + ' <strong>' + exts + '</strong>');
                    return;
                }
            }
            elog(s.data);
            if( f.data('s_img') !== undefined && f.data('s_img') !== "" ) {
                if( s ){ $( f.data('s_img') ).html('').append( '<img width="150" height="150" src="'+f.data('dir')+ s.data('url') +'">' ) }
            }
            if( f.data('bg') !== undefined && f.data('bg') !== "" ) {
                if( s ){ $( f.data('bg') ).css({'background-image':'url("'+f.data('dir')+ s.data('url') +'")'}) }
            }
            if( f.data('id') !== undefined && f.data('id') !== '' ){
                if( s ){ $( f.data('id') ).val( s.data('id') ) }
            }
            if( f.data('url') !== undefined && f.data('url') !== "" ) {
                if( s ){ $( f.data('url') ).val( s.data('url') ) }
            }
            if( f.data('callback') !== undefined && f.data('callback') !== "" ) {
                //eval( callback + '(' + f.data('callback') + ')' );
            }

        } else {

            var urls = []; var ids = [];
            $.each(s,function(a,b){
                urls.push($(b).data('url'));
                ids.push($(b).data('ids'));
            });
            //elog(urls);
            urls = urls.join('|');
            ids = ids.join('|');
            //elog(urls);
            var ut = $(f.data('url'));
            var it = $(f.data('id'));
            //urls = ut.val() !== '' ? ut.val() + '|' + urls : urls;
            //ids = it.val() !== '' ? it.val() + '|' + ids : ids;
            $(f.data('url')).val(urls);
            $(f.data('id')).val(ids);
        }
        file_ui();
        files_ui();

        $('#aio_up').slideUp().addClass('on');
        //elog(s);
        setTimeout(function(){ var m = s.length > 0 ? $('#aio_up .file_select').html() : $('#aio_up .no_file_select').html() ; $('.file_notify').html(m).addClass('on') }, 500);
        setTimeout(function(){ $('.file_notify').removeClass('on') },1600);
    })

    // Delete File from Multiple Files UI
    .on('click','.aio_fsp .trash',function(){ //elog('test');
        var dfile = $(this).next('.f').html();
        if( dfile !== undefined && dfile !== '' && confirm( $('#aio_up .remove_confirm').html() ) ) {
            var files = $($(this).parents('[data-url]').data('url')).val().split('|');
            var val = '';
            if( $.isArray( files ) ) {
                $(files).each(function(c,d){
                    var efile = d.split('/')[ d.split('/').length - 1 ];
                    if( efile !== dfile ) {
                        val += d + '|';
                    }
                });
            }
            val = val.slice(0,-1);
            $($(this).parents('[data-url]').data('url')).val(val);
            files_ui();
        }
    })

    // Delete file from Files UI
    .on('click','.aio_fp .trash',function(){
        var dfile = $(this).next('.f').html();
        if( dfile !== undefined && dfile !== '' && confirm( $('#aio_up .remove_confirm').html() ) ) {
            $($(this).parents('[data-url]').data('url')).val('');
            file_ui();
        }
    });

    // Drag and Drop File Upload
    $('#aio_up .files_body').bind('dragover','.file_modal',function(e){
        e.stopPropagation();
        e.preventDefault();
        $(this).addClass('hover');
    }).bind('dragleave','.file_modal',function(e){
        e.stopPropagation();
        e.preventDefault();
        $(this).removeClass('hover');
    }).bind('drop','.file_modal',function(e){
        e.stopPropagation();
        e.preventDefault();
        process_upload(e.originalEvent.dataTransfer.files);
        $(this).removeClass('hover');
    });

    // Search Files
    $('#aio_up .search').on('search keyup',function(){
        var s = $(this).val().toLowerCase();
        $('#aio_up .uploaded_files .f').each(function(a,b){
            if( $(b).find('.name').html().toLowerCase().indexOf(s) >= 0 ) {
                $(b).show();
            } else {
                $(b).hide();
            }
        });
    });

    // File and Files UI
    file_ui();
    files_ui();
});

//elog('test');

function file_ui() {

    $('input[data-file]').each(function(i,f){

        $(f).hide();
        var d = $(f).val();
        var readonly = $(f).attr('readonly') !== undefined;
        var file = d.split('/')[ d.split('/').length - 1 ];
        var ext = file.split('.')[ file.split('.').length - 1 ];
        var trash = !readonly ? '<i class="i trash"></i>' : '';
        var file_ui = file !== '' ? '<div class="f"><i class="i file '+ext+'"></i>'+trash+'<div class="f">'+file+'</div></div>' : $(f).prev('button').show().clone();
        if( !$(f).next().hasClass('aio_fp') ){
            var id = $(f).prev('button').data('url');
            $(f).after('<div class="aio_fp aio_files" data-url="'+ id +'"></div>');
        }

        $(f).next('.aio_fp').html(file_ui);
        $(f).prev('button').hide();

    });
}

function files_ui() {

    $('input[data-files]').each(function(a,b){
        $(b).hide();
        var files = $(b).val() !== '' ? $(b).val().split('|') : '';
        var files_ui = '';
        var readonly = $(b).attr('readonly') !== undefined;
        var trash = !readonly ? '<i class="i trash"></i>' : '';
        $(files).each(function(c,d){
            var file = d.split('/')[ d.split('/').length - 1 ];
            var ext = file.split('.')[ file.split('.').length - 1 ];
            if( readonly ) {
                files_ui += file !== '' ? '<a href="'+location.origin+'/apps/'+location.host.split('.')[0]+d+'" class="f"><i class="i file '+ext+'"></i>'+trash+'<div class="f">'+file+'</div></a>' : '';
            } else {
                files_ui += file !== '' ? '<div class="f"><i class="i file '+ext+'"></i>'+trash+'<div class="f">'+file+'</div></div>' : '';
            }
        });
        //files_ui = files_ui === '' ? $(b).prev('button').show().clone() : files_ui;
        files_ui = files_ui !== '' ? '<div class="w">' + files_ui + '</div>' : '';

        if( !$(b).next().hasClass('aio_fsp') ){
            var id = $(b).prev('button').data('url');
            $(b).after('<div class="aio_fsp aio_files" data-url="'+ id +'"></div>');
        }

        //elog( files_ui );

        //$(b).next('.aio_fsp').find('button').show().html('+');
        var um = !$(b).prop('disabled') ? $(b).prev('button')[0].outerHTML : '';
        $(b).next('.aio_fsp').html( files_ui + um );
        if( !readonly ) {
            $(b).next('.aio_fsp').find('button').show().html('+');
        } else {
            $(b).next('.aio_fsp').find('button').hide();
        }
        if( $(b).data('color') !== undefined && $(b).data('color') !== '' ) {
            $('.aio_fsp button').css({'background-color':$(b).data('color')})
        }
        $(b).prev('button').hide();
        //elog(files_ui);

    });
}

function file_upload(e){
    var fu = $('#aio_up');
    if( ( $(e).data('url') !== '' && $(e).data('url') !== undefined ) || ( $(e).data('id') !== '' && $(e).data('id') !== undefined ) ) {
        // Show or Hide previous uploads
        if( $(e).data('history') === undefined ) {
            $('#aio_up .f').hide();
            $('.fb').click();
        } else {
            if( $(e).data('force-browse') !== undefined ) {
                $('.fb').click();
            }
            $('#aio_up .f').show();
        }
        // Sets a custom color
        var title = $('#aio_up .files_head h3');
        if( $(e).data('color') !== undefined && $(e).data('color') !== '' ) {
            title.css({'background-color':$(e).data('color')});
        } else {
            title.css({'background-color':'#333'});
        }
        // Sets a custom title
        if( $(e).data('title') !== undefined && $(e).data('title') !== '' ) {
            $('#aio_up').data('title_backup',title.html());
            title.html($(e).data('title'));
        } else {
            title.html($('#aio_up').data('title_backup'));
        }
        // Close if modal is already open
        if(!fu.is(':visible')) {
            fu.slideDown().addClass('on');
        }
        // Sets data parameters from initializer to modal
        $.each($(e).data(),function(a,b){
            fu.data(a,b)
        });
        //fu.data('exts', $(e).data('exts')).data('files',$(e).data('files')).data('url', $(e).data('url')).data('multiple', $(e).data('multiple')).data('bg', $(e).data('bg')).data('id', $(e).data('id')).data('s_img', $(e).data('s_img')).data('path', $(e).data('path')).data('scope', $(e).data('scope')).data('callback', $(e).data('callback'));
        if( fu.data('multiple') !== undefined ) {
            $('.fi').addClass('multiple');
        } else {
            $('.fi').removeClass('multiple');
        }
        // Shows or Hides delete button
        if ($(e).data('delete') === undefined) {
            $('.fd').hide();
            fu.data('delete', false)
        } else {
            $('.fd').show();
            fu.data('delete', true)
        }
        // Shows already selected files
        if( $( $(e).data('url') ).val() !== '' ) {
            var selected_files = $( $(e).data('url') ).val().split('|');
            $('#aio_up .f').removeClass('on');
            $.each( selected_files, function(a,sf){
                $('#aio_up .f[data-url="'+sf+'"]').addClass('on');
            })
        }
    }
}

function process_upload(fs) {
    for (var i = 0, f; f = fs[i]; i++) {
        //elog(i);
        //elog(f);
        var exts = $('#aio_up').data('exts');
        if( exts ){
            exts = exts.split(',');
            var ext = f.name.split('.')[1];
            if(exts.indexOf(ext) < 0){
                var msg = $('#aio_up .extension_limit').html();
                uploader_notify(msg + ' <strong>' + exts + '</strong>');
                return [ false, 'Extension Restricted', 'The file should be one of the extensions ' + exts ];
            }
        }
        var d = new FormData();
        d.append(i, f);
        d.append('action', 'file_process');
        if( $('#aio_up').data('path') !== '' && $('#aio_up').data('path') !== undefined ){
            d.append('path',$('#aio_up').data('path'));
        }
        if( $('#aio_up').data('scope') !== '' && $('#aio_up').data('scope') !== undefined ){
            d.append('scope',$('#aio_up').data('scope'));
        }
        if( $('#aio_up').data('delete') !== '' && $('#aio_up').data('delete') !== undefined && $('#aio_up').data('delete') ){
            d.append('delete','true');
        } else {
            d.append('delete','false');
        }

        $('#aio_up .uploaded_files').prepend( '<div class="uploading"><div class="name">'+f.name+'</div><div class="perc"><span>0</span>%</div><div class="progress"><div></div></div></div>' );

        //console.log(d);
        $.ajax({
            url: location.origin, type: 'POST', data: d, contentType: false, cache: false, processData: false,
            xhr: function() {
                var myXhr = $.ajaxSettings.xhr();
                if(myXhr.upload){
                    myXhr.upload.addEventListener('progress',upload_progress, false);
                }
                return myXhr;
            },
            success: function (data) {
                console.log(data);
                $('#aio_up .uploading').remove();
                var d = $.parseJSON(data);
                if (d[0] === 'success') {
                    $('#aio_up .no_uploaded_files').remove();
                    var size = parseInt(d[6]) > 1024 ? ( parseFloat(d[6]) / 1024 ).toFixed(2) + ' MB' : d[6] + ' KB';
                    var bg = $.inArray( d[5], Array('svg','jpg','png','jpeg') ) > -1 ? 'style="background-image:url(\''+d[7]+'\')"' : '';
                    var del = d[8] === 1 ? 'data-delete="1"' : 'data-delete="0"';
                    $('#aio_up .uploaded_files').prepend( '<div class="f new '+d[5]+'" data-id="'+d[4]+'" data-url="'+d[3]+'" '+bg+' '+del+'><div class="name">'+d[2]+'</div><div class="size">'+size+'</div></div>' );
                    $('#aio_up .f').removeClass('on');
                    uploader_notify( $('#aio_up .upload_success').html() );
                    setTimeout(function(){ $('.f').removeClass('new') },1000);
                    if($('#aio_up').data('files') === undefined){
                        $('.f.new').addClass('on');
                        $('.fi').click();
                    }
                    return [ 1, $('#aio_up .upload_success').html(), d ];
                } else {
                    uploader_notify( r[1] );
                    return [ 1, 'File Uploaded Failed', 'There was an issue while sending file to server, please try again' ];
                }
            }
        });
    }
}

function upload_progress(e){
    if( e.lengthComputable ){
        var max = e.total;
        var current = e.loaded;
        var perc = Math.round((current * 100)/max);
        $('#aio_up .progress>div').css({'width':perc+'%'});
        $('#aio_up .uploading .perc>span').html(perc);
    }
}

function uploader_notify( message ) {
    $('.file_notify').html(message).addClass('on');
    setTimeout(function(){ $('.file_notify').removeClass('on'); },2000);
}

function close_uploader() {
    $('.f').removeClass('on');
    $('#aio_up').slideUp().removeClass('on').removeData(['id','url','exts','s_img','scope','path','bg','multiple','files']);
}