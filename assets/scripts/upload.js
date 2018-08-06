$(document).ready(function(){
    $(document).mouseup(function(e) {
        var c = $('#file_uploader');
        if (!c.is(e.target) && c.has(e.target).length === 0) {
            c.hide();
        }
    });
    $('body').on('click','.fup_file',function(){
        $('.fup_file').removeClass('on');
        $(this).toggleClass('on')
    }).on('click','.files_insert',function(){

    }).on('click','.files_delete',function(){

    })

    // Standard File Chosen to Upload

    .on('change','#file_input',function(){
        console.log('file chosen');
        var fs = this.files;
        console.log(fs);
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
        console.log( f.data('bg') );
        if( f.data('bg') !== undefined && f.data('bg') !== "" ) {
            if( s ){ $( f.data('bg') ).css({'background-image':'url("'+f.data('dir')+ s.data('url') +'")'}) }
        }
        if( f.data('id') !== undefined && f.data('id') !== '' ){
            if( s ){ $( f.data('id') ).val( s.data('id') ) }
        }
        $('#file_uploader').hide();
    })
})

function file_upload(e){
    $('#file_uploader').show().data('exts',$(e).data('exts')).data('url',$(e).data('url')).data('bg',$(e).data('bg')).data('id',$(e).data('id')).data('s_img',$(e).data('s_img')).data('path',$(e).data('path')).data('scope',$(e).data('scope'));
}

function process_upload(fs) {
    for (var i = 0, f; f = fs[i]; i++) {
        console.log(i);
        console.log(f);
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
        console.log(d);
        $.ajax({
            url: location.origin, type: 'POST', data: d, contentType: false, cache: false, processData: false, success: function (data) {
                var d = $.parseJSON(data);
                if (d[0] === 'success') {
                    $('#file_uploader .uploaded_files').append( '<div class="fup_file" data-url="'+d[3]+'">'+d[2]+'</div>' )
                    return [ true, 'File Uploaded Successfully', d ];
                } else {
                    return [ true, 'File Uploaded Failed', 'There was an issue while sending file to server, please try again' ];
                }
            }
        });
    }

}

function remove_file() {

}