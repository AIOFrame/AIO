// Fields and Validation
$(document).ready(function(){
    $(document).on('keyup','[data-help],[data-validate]',function(e){

        let p = $(this).parent();

        // Check if validation wrapper exist
        if( !$(p).find('.aio_valid').length ) {
            $(p).append('<div class="aio_valid on"></div>');
        }

        let av = $(p).find('.aio_valid');

        // Validate Minimum Length
        if( $(this).attr('minlength') !== undefined ) {
            // Check if message exist
            let minDiv = $(av).find('.min');
            if( !minDiv.length ) {
                let string = $(this).data('minlength') !== undefined ? $(this).data('minlength') : 'Min Characters';
                $(av).append('<div class="min"><span class="key">'+string+'</span><span class="value">'+$(this).attr('minlength')+'</span></div>');
            }
            // Color code based on validation
            if( $(this).val().length >= parseInt( $(this).attr('minlength') ) ) {
                minDiv.addClass('green').removeClass('red');
            } else {
                minDiv.addClass('red').removeClass('green');
            }
            $(minDiv).find('.live').html( $(this).val().length );
        }

        // Validate Maximum Length
        if( $(this).attr('maxlength') !== undefined ) {
            // Check if message exist
            let maxlengthDiv = $(av).find('.maxlength');
            console.log(maxlengthDiv);
            if( !maxlengthDiv.length ) {
                let string = $(this).data('maxlength') !== undefined ? $(this).data('maxlength') : 'Max Characters';
                $(av).append('<div class="maxlength"><span class="key">'+string+'</span><span class="value"><i class="live">'+$(this).val().length+'</i> of '+$(this).attr('maxlength')+'</span></div>');
            }
            // Color code based on validation
            if( $(this).val().length <= parseInt( $(this).attr('maxlength') ) ) {
                maxlengthDiv.addClass('green').removeClass('red');
            } else {
                $(this).val($(this).val().slice(0, $(this).attr('maxlength')));
                //maxlengthDiv.addClass('red').removeClass('green');
            }
            $(maxlengthDiv).find('.live').html( $(this).val().length );
        }

        // Validate Maximum Number
        if( $(this).attr('max') !== undefined ) {
            // Check if message exist
            let maxDiv = $(av).find('.max_num');
            console.log(maxDiv);
            if( !maxDiv.length ) {
                let string = $(this).data('max') !== undefined ? $(this).data('max') : 'Max Number';
                $(av).append('<div class="max_num"><span class="key">'+string+'</span><span class="value">'+$(this).attr('max')+'</span></div>');
            }
            // Color code based on validation
            if( parseInt( $(this).val() ) <= parseInt( $(this).attr('max') ) ) {
                maxDiv.addClass('green').removeClass('red');
            } else {
                maxDiv.addClass('red').removeClass('green');
            }
        }
        // Validate Email
        if( $(this).attr('type') === 'email' ) {
            // Check if message exist
            let mailDiv = $(av).find('.email');
            if( !mailDiv.length ) {
                let string = $(this).data('email') !== undefined ? $(this).data('email') : 'Format should be email address';
                $(av).append('<div class="email"><span class="value">'+string+'</span></div>');
            }
            // Color code based on validation
            const ve = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            if( $(this).val().length > 0 && ve.test($(this).val().toLowerCase()) ) {
                mailDiv.addClass('green').removeClass('red');
            } else {
                mailDiv.addClass('red').removeClass('green');
            }
            // Restrictions for defined emails
            if( $(this).data('restrict_domains') ) {
                if( $(this).val().indexOf($(this).data('restrict_domains')) >= 0 ) {
                    mailDiv.addClass('green').removeClass('red');
                }
            }
        }
        // Validate Password
        if( $(this).attr('type') === 'password' ) {
            // Check if message exist
            let passDiv = $(av).find('.pass');
            if( !passDiv.length ) {
                let string = $(this).data('password') !== undefined ? $(this).data('password') : 'Must have 1 number & 1 special character';
                $(av).append('<div class="pass"><span class="value">'+string+'</span></div>');
            }
            // Color code based on validation
            const vp = /^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{6,16}$/;
            if( $(this).val().length > 0 && vp.test($(this).val()) ) {
                passDiv.addClass('green').removeClass('red');
            } else {
                passDiv.addClass('red').removeClass('green');
            }
            // Password strength meter
            // TODO: Improve strength meter to work based on character variation + length
            if( $(this).data('strength') !== undefined ) {
                let meter = $(p).find('.aio_strength_meter');
                if( !$(meter).length ) {
                    $(av).append('<div class="aio_strength_meter"><i></i></div>')
                }
                let strength = parseInt( $(this).data('strength') );
                let length = $(this).val().length;
                let percent = ( length / strength ) * 100;
                $(meter).find('i').css({'width':percent+'%'});
            }
        }
        // TODO: Text suggestion
    })
    .on('keyup','input[data-format-number]',function(){
        let val = parseInt( $(this).val().replace(/,/g, '') ).toLocaleString();
        $(this).val( val.toLocaleString() );
    })
    .on('keydown','[data-no-space]',function(e){
        if (e.which === 32)
            return false;
    })
    .on('focus','[data-help]',function(){
        if( $(this).parent().find('.aio_valid').length ) {
            $(this).parent().find('.aio_valid:not(.green)').addClass('on');
        }
    })
    .on('focusout','[data-help]',function(){
        if( $(this).parent().find('.aio_valid').length ) {
            $(this).parent().find('.aio_valid').removeClass('on');
        }
    })
    .on('click','.password_visibility',function(){
        let attr = $(this).parent().hasClass('show_password') ? 'password' : 'text';
        $(this).parent().toggleClass('show_password').find('input').attr('type',attr);
    })
    $('[type=password][data-help]').each(function (i,f) {
        let dir = $('html').attr('dir');
        let input_css = dir === 'ltr' ? {'padding-right':'50px'} : {'padding-left':'50px'};
        //input_css.width = 'calc(100% - 50px)';
        let parent_css = {'position':'relative'};
        let label = $(f).prev('label').length > 0 ? $(f).prev().height() + parseFloat( $(f).prev().css('margin-top') )  + parseFloat( $(f).prev().css('margin-bottom') ) : 0;
        //let top = label + ( $(f).height() / 2 );
        //console.log( top );
        let visibility_css = {'position':'absolute'}; //'transform':'translateY(-20%)','top':top+'px'
        dir === 'ltr' ? visibility_css.right = '15px' : visibility_css.left = '15px';
        $(f).css(input_css).parent().css(parent_css).append('<div class="password_visibility"><i class="mat-ico p10 s on">visibility</i><i class="mat-ico p10 s off">visibility_off</i></div>').find('.password_visibility').css(visibility_css);
    })
})