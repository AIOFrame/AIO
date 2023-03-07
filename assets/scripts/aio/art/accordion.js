$(document).ready(function(){

    if( $('.accordion').length ) {
        $('body').on('click','.accordion_head',function () {
            $(this).parent().toggleClass('on');
        })
    }

})