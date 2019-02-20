$(document).ready(function(){
    $(document).mouseup(function(e) {
        var c = $('#file_uploader');
        if (!c.is(e.target) && c.has(e.target).length === 0) {
            c.hide();
        }
    });
    $('body').on('click','.fup_file',function(){
        $('.fup_file').not(this).removeClass('on');
        $(this).toggleClass('on')
    }).on('click','.files_delete',function(){

    }).on('click','#file_uploader .close',function(){
        $(this).parents('#file_uploader').slideUp();
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
        if( f.data('url') !== undefined && f.data('url') !== "" ) {
            if( s ){ $( f.data('url') ).val( s.data('url') ) }
        }
        if( f.data('s_img') !== undefined && f.data('s_img') !== "" ) {
            if( s ){ $( f.data('s_img') ).html('').append( '<img width="150" height="150" src="'+f.data('dir')+ s.data('url') +'">' ) }
        }
        //console.log( f.data('bg') );
        if( f.data('bg') !== undefined && f.data('bg') !== "" ) {
            if( s ){ $( f.data('bg') ).css({'background-image':'url("'+f.data('dir')+ s.data('url') +'")'}) }
        }
        if( f.data('id') !== undefined && f.data('id') !== '' ){
            if( s ){ $( f.data('id') ).val( s.data('id') ) }
        }
        $('#file_uploader').slideUp();
        //console.log(s);
        setTimeout(function(){ var m = s.length > 0 ? 'File Selected Successfully!' : 'NO FILE SELECTED! File Uploader Closed!!' ; $('.file_notify').html(m).addClass('on') }, 500);
        setTimeout(function(){ $('.file_notify').removeClass('on') },1600);
    })
});

function file_upload(e){
    $('#file_uploader').slideDown().data('exts',$(e).data('exts')).data('url',$(e).data('url')).data('bg',$(e).data('bg')).data('id',$(e).data('id')).data('s_img',$(e).data('s_img')).data('path',$(e).data('path')).data('scope',$(e).data('scope'));
    if($(e).data('delete') !== undefined){
        if( $('#file_uploader .files_delete').length === 0 ){
            $('#file_uploader').find('.files_insert').parent().append('<div class="files_delete"></div>');
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
                var d = $.parseJSON(data);
                if (d[0] === 'success') {
                    $('#file_uploader .no_uploaded_files').remove();
                    $('#file_uploader .uploaded_files').prepend( '<div class="fup_file new" data-id="'+d[4]+'" style="background-image:url('+d[7]+')" data-url="'+d[3]+'">'+d[2]+'</div>' );
                    $('#file_uploader .fup_file').removeClass('on');
                    $('.file_notify').html('File Uploaded Successfully!').addClass('on');
                    setTimeout(function(){ $('.file_notify').removeClass('on'); },1000);
                    setTimeout(function(){ $('.fup_file').removeClass('new') },3000);
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

        var Percentage = (current * 100)/max;
        console.log(Percentage);


        if(Percentage >= 100)
        {
            // process completed
        }
    }
}

function remove_file() {

}