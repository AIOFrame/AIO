$(document).ready(function () {

    $('body').on('keyup','.aio_dynamics .set:nth-child(2) input',function () {

        $(this).parents('.field_set').find('.set:nth-child(3) .url').html( location.href + $(this).val() );

    })

});