// Fields and Validation
$(document).ready(function(){
    $(document).on('keyup','[data-help]',function(e){
        // Validate Minimum Length
        if( $(this).attr('minlength') !== undefined ) {
            // Check if validation wrapper exist
            if( !$(this).next('.valid').length ) {
                $(this).parent().append('<div class="valid on"></div>');
            }
            // Check if message exist
            let minDiv = $(this).next('.valid').find('.min');
            if( !minDiv.length ) {
                let string = $(this).data('minlength') !== undefined ? $(this).data('minlength') : 'Minimum Characters';
                $(this).next('.valid').append('<div class="min"><span class="title">'+string+'</span><span class="value">'+$(this).attr('minlength')+'</span></div>')
            }
            // Color code based on validation
            if( $(this).val().length >= parseInt( $(this).attr('minlength') ) ) {
                //$(this).next('.valid').removeClass('on');
                minDiv.addClass('green').removeClass('red');
            } else {
                //$(this).next('.valid').addClass('on');
                minDiv.addClass('red').removeClass('green');
            }
        }
        // Validate Maximum Length
        if( $(this).attr('maxlength') !== undefined ) {
            // Check if validation wrapper exist
            if( !$(this).next('.valid').length ) {
                $(this).parent().append('<div class="valid on"></div>');
            }
            // Check if message exist
            let maxDiv = $(this).next('.valid').find('.max');
            if( !maxDiv.length ) {
                let string = $(this).data('maxlength') !== undefined ? $(this).data('maxlength') : 'Maximum Characters';
                $(this).next('.valid').append('<div class="max"><span class="title">'+string+'</span><span class="value"><i class="live">'+$(this).val().length+'</i> of '+$(this).attr('maxlength')+'</span></div>')
            }
            // Color code based on validation
            if( $(this).val().length <= parseInt( $(this).attr('maxlength') ) ) {
                //$(this).next('.valid').removeClass('on');
                maxDiv.addClass('green').removeClass('red');
            } else {
                //$(this).next('.valid').addClass('on');
                maxDiv.addClass('red').removeClass('green');
            }
            $(this).next().find('.live').html( $(this).val().length );
        }
        // Validate Email
        if( $(this).attr('type') === 'email' ) {
            // Check if validation wrapper exist
            if( !$(this).next('.valid').length ) {
                $(this).parent().append('<div class="valid on"></div>');
            }
            // Check if message exist
            let mailDiv = $(this).next('.valid').find('.email');
            if( !mailDiv.length ) {
                let string = $(this).data('email') !== undefined ? $(this).data('email') : 'Format should be email address';
                $(this).next('.valid').append('<div class="email"><span class="value">'+string+'</span></div>')
            }
            // Color code based on validation
            const ve = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            if( $(this).val().length > 0 && ve.test($(this).val().toLowerCase()) ) {
                mailDiv.addClass('green').removeClass('red');
            } else {
                mailDiv.addClass('red').removeClass('green');
            }
        }
        // Validate Password
        if( $(this).attr('type') === 'password' ) {
            // Check if validation wrapper exist
            if( !$(this).next('.valid').length ) {
                $(this).parent().append('<div class="valid on"></div>');
            }
            // Check if message exist
            let passDiv = $(this).next('.valid').find('.pass');
            if( !passDiv.length ) {
                let string = $(this).data('password') !== undefined ? $(this).data('password') : 'Password must have 1 number & 1 special character';
                $(this).next('.valid').append('<div class="pass"><span class="value">'+string+'</span></div>')
            }
            // Color code based on validation
            const vp = /^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{6,16}$/;
            if( $(this).val().length > 0 && vp.test($(this).val()) ) {
                passDiv.addClass('green').removeClass('red');
            } else {
                passDiv.addClass('red').removeClass('green');
            }
        }
        // TODO: Text suggestion
        // TODO: Password strength meter
    })
    .on('keydown','[data-no-space]',function(e){
        if (e.which === 32)
            return false;
    })
    .on('focus','[data-help]',function(){
        if( $(this).next('.valid').length ) {
            $(this).next('.valid:not(.green)').addClass('on');
        }
    })
    .on('focusout','[data-help]',function(){
        if( $(this).next('.valid').length ) {
            $(this).next('.valid').removeClass('on');
        }
    })
})