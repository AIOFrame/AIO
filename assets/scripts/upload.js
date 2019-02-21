$(document).ready(function(){
    $(document).mouseup(function(e) {
        var c = $('#file_uploader');
        if (!c.is(e.target) && c.has(e.target).length === 0) {
            close_uploader();
        }
    });
    $('body').on('click','.fup_file',function(){
        if( $('#file_uploader').data('multiple') === undefined  ) {
            $('.fup_file').not(this).removeClass('on');
        }
        $(this).toggleClass('on');
        //elog($(this).data('delete'));
        if( $(this).data('delete') !== '' && $(this).data('delete') !== undefined ){
            if( $(this).data('delete') === 0 ){
                $('#file_uploader .files_delete').addClass('disabled');
            } else if( $(this).data('delete') === 1 ) {
                $('#file_uploader .files_delete').removeClass('disabled');
            }
        }
    }).on('click','.files_delete',function(){
        var df = $('.fup_file.on');
        if( df.length > 0 && df.data('id') !== undefined && df !== '' ) {
            $.post(location.origin,{'action':'file_delete','id':df.data('id')},function(r){
                if( r = JSON.parse(r) ) {
                    uploader_notify(r[1]);
                    if( r[0] === 1 ){
                        $('.fup_file.on').remove();
                    }
                }
            })
        }
    }).on('click','#file_uploader .close',function(){
        close_uploader();
    })

    // Standard File Chosen to Upload

    .on('change','#file_input',function(){
        var fs = this.files;
        process_upload(fs);
    })

    // Drag and Drop File Upload

    .bind('dragover','.file_modal',function(e){
        e.stopPropagation();
        e.preventDefault();
        $(this).addClass('hover');
    }).bind('dragleave','.file_modal',function(e){
        e.stopPropagation();
        e.preventDefault();
        $(this).removeClass('hover');
    }).bind('drop','.file_modal',function(){
        e.stopPropagation();
        e.preventDefault();
    })

    // File Insert
    .on('click','#file_uploader .files_insert',function(){
        var f = $('#file_uploader');
        var s = $('#file_uploader .fup_file.on');

        if( f.data('multiple') === undefined ) {
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
            $(f.data('url')).val(urls);
            $(f.data('id')).val(ids);
        }

        $('#file_uploader').slideUp();
        //console.log(s);
        setTimeout(function(){ var m = s.length > 0 ? 'File Selected Successfully!' : 'NO FILE SELECTED! File Uploader Closed!!' ; $('.file_notify').html(m).addClass('on') }, 500);
        setTimeout(function(){ $('.file_notify').removeClass('on') },1600);
    })

    .on('click','[data-file-upload]',function(){
        file_upload($(this));
    })
});

function file_upload(e){
    var fu = $('#file_uploader');
    if( ( $(e).data('url') !== '' && $(e).data('url') !== undefined ) || ( $(e).data('id') !== '' && $(e).data('id') !== undefined ) ) {
        if(!fu.is(':visible')) {
            fu.slideDown();
        }
        fu.data('exts', $(e).data('exts')).data('url', $(e).data('url')).data('multiple', $(e).data('multiple')).data('bg', $(e).data('bg')).data('id', $(e).data('id')).data('s_img', $(e).data('s_img')).data('path', $(e).data('path')).data('scope', $(e).data('scope'));
        elog(fu.data('multiple'));
        if( fu.data('multiple') !== undefined ) {
            $('.files_insert').addClass('multiple');
        } else {
            $('.files_insert').removeClass('multiple');
        }
        if ($(e).data('delete') === undefined) {
            $('.files_delete').hide();
            fu.data('delete', false)
        } else {
            $('.files_delete').show();
            fu.data('delete', true)
        }
    }
}

function process_upload(fs) {
    for (var i = 0, f; f = fs[i]; i++) {
        //console.log(i);
        //console.log(f);
        var exts = $('#file_uploader').data('exts');
        if( exts ){
            exts = exts.split(',');
            var ext = f.name.split('.')[1];
            if(exts.indexOf(ext) < 0){
                return [ false, 'Extension Restricted', 'The file should be one of the extensions ' + exts ];
            }
        }
        var d = new FormData();
        d.append(i, f);
        d.append('action', 'file_process');
        if( $('#file_uploader').data('path') !== '' && $('#file_uploader').data('path') !== undefined ){
            d.append('path',$('#file_uploader').data('path'));
        }
        if( $('#file_uploader').data('scope') !== '' && $('#file_uploader').data('scope') !== undefined ){
            d.append('scope',$('#file_uploader').data('scope'));
        }
        if( $('#file_uploader').data('delete') !== '' && $('#file_uploader').data('delete') !== undefined && $('#file_uploader').data('delete') ){
            d.append('delete','true');
        } else {
            d.append('delete','false');
        }

        $('#file_uploader .uploaded_files').prepend( '<div class="uploading"><div class="name">'+f.name+'</div><div class="perc"><span>0</span>%</div><div class="progress"><div></div></div></div>' );

        elog(d);
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
                $('#file_uploader .uploading').remove();
                var d = $.parseJSON(data);
                if (d[0] === 'success') {
                    $('#file_uploader .no_uploaded_files').remove();
                    var size = parseInt(d[6]) > 1024 ? ( parseFloat(d[6]) / 1024 ).toFixed(2) + ' MB' : d[6] + ' KB';
                    var bg = $.inArray( d[5], Array('svg','jpg','png','jpeg') ) > -1 ? 'style="background-image:url(\''+d[7]+'\')"' : '';
                    var del = d[8] === 1 ? 'data-delete="1"' : 'data-delete="0"';
                    $('#file_uploader .uploaded_files').prepend( '<div class="fup_file new '+d[5]+'" data-id="'+d[4]+'" data-url="'+d[3]+'" '+bg+' '+del+'><div class="name">'+d[2]+'</div><div class="size">'+size+'</div></div>' );
                    $('#file_uploader .fup_file').removeClass('on');
                    $('.file_notify').html('File Uploaded Successfully!').addClass('on');
                    setTimeout(function(){ $('.file_notify').removeClass('on'); },1000);
                    setTimeout(function(){ $('.fup_file').removeClass('new') },1000);
                    return [ true, 'File Uploaded Successfully', d ];
                } else {
                    return [ true, 'File Uploaded Failed', 'There was an issue while sending file to server, please try again' ];
                }
            }
        });
    }
}

function upload_progress(e){

    if(e.lengthComputable){
        var max = e.total;
        var current = e.loaded;

        var Percentage = Math.round((current * 100)/max);
        $('#file_uploader .progress>div').css({'width':Percentage+'%'});
        $('#file_uploader .uploading .perc>span').html(Percentage);

        if(Percentage >= 100)
        {
            // process completed
        }
    }
}

function uploader_notify( message ) {

    $('.file_notify').html(message).addClass('on');

    setTimeout(function(){ $('.file_notify').removeClass('on'); },2000);

}

function close_uploader() {
    $('.fup_file').removeClass('on');
    $('#file_uploader').slideUp().removeData(['id','url','exts','s_img','scope','path','bg','multiple']);
}