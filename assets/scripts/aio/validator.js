// Fields and Validation
$(document).ready(function(){
    $(document).on('keyup change focus','[data-help],[data-validate]',function(e){

        let p = $(this).parent();
        let v = $(this).val();

        // Check if validation wrapper exist
        if( !$(p).find('.aio_valid').length ) {
            $(p).append('<div class="aio_valid on"></div>');
        }

        // TODO: Implement character format validation ex: ABC000
        // TODO: Implement lower than or higher than another field validation

        let av = $(p).find('.aio_valid');

        // Validate Minimum Length - Works on any input
        if( $(this).attr('minlength') !== undefined ) {
            // Check if message exist
            let minDiv = $(av).find('.min');
            if( !minDiv.length ) {
                let string = $(this).data('minlength') !== undefined ? $(this).data('minlength') : 'Min Characters';
                $(av).append('<div class="min"><span class="key">'+string+'</span><span class="value">'+$(this).attr('minlength')+'</span></div>');
            }
            // Color code based on validation
            if( v.length >= parseInt( $(this).attr('minlength') ) ) {
                minDiv.addClass('green').removeClass('red');
            } else {
                minDiv.addClass('red').removeClass('green');
            }
            $(minDiv).find('.live').html( v.length );
        }

        // Validate Maximum Length
        if( $(this).attr('maxlength') !== undefined ) {
            // Check if message exist
            let maxlengthDiv = $(av).find('.maxlength');
            console.log(maxlengthDiv);
            if( !maxlengthDiv.length ) {
                let string = $(this).data('maxlength') !== undefined ? $(this).data('maxlength') : 'Max Characters';
                $(av).append('<div class="maxlength"><span class="key">'+string+'</span><span class="value"><i class="live">'+v.length+'</i> of '+$(this).attr('maxlength')+'</span></div>');
            }
            // Color code based on validation
            if( v.length <= parseInt( $(this).attr('maxlength') ) ) {
                maxlengthDiv.addClass('green').removeClass('red');
            } else {
                $(this).val(v.slice(0, $(this).attr('maxlength')));
                //maxlengthDiv.addClass('red').removeClass('green');
            }
            $(maxlengthDiv).find('.live').html( v.length );
        }

        // Validate Minimum Number
        if( $(this).attr('min') !== undefined ) {
            // Check if message exist
            let minDiv = $(av).find('.min_num');
            let minNum = $(this).attr('min');
            console.log(minDiv);
            if( !minDiv.length ) {
                let string = $(this).data('min') !== undefined ? $(this).data('min') : 'Min Number';
                $(av).append('<div class="min_num"><span class="key">'+string+'</span><span class="value">'+minNum+'</span></div>');
            }
            // Color code based on validation
            if( parseInt( v ) >= minNum ) {
                minDiv.addClass('green').removeClass('red');
            } else {
                minDiv.addClass('red').removeClass('green');
            }
        }

        // Validate Minimum Number from another source
        if( $(this).attr('min-source') !== undefined ) {
            // Check if message exist
            let minSDiv = $(av).find('.min_source_num');
            let minSNum = parseInt( $( $(this).attr('min-source') ).val() );
            console.log(minSNum);
            if( !minSDiv.length ) {
                let string = $(this).data('min-source-message') !== undefined ? $(this).data('min-source-message') : 'Min Number';
                $(av).append('<div class="min_source_num"><span class="key">'+string+'</span><span class="value">'+minSNum+'</span></div>');
            } else {
                $(av).find('.min_source_num .value').text(minSNum);
            }
            // Color code based on validation
            if( parseInt( v ) >= minSNum ) {
                minSDiv.addClass('green').removeClass('red');
            } else {
                minSDiv.addClass('red').removeClass('green');
            }
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
            if( parseInt( v ) <= parseInt( $(this).attr('max') ) ) {
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
            if( v.length > 0 && ve.test( v.toLowerCase()) ) {
                mailDiv.addClass('green').removeClass('red');
            } else {
                mailDiv.addClass('red').removeClass('green');
            }
            // Restrictions for defined emails
            if( $(this).data('restrict_domains') ) {
                if( v.indexOf($(this).data('restrict_domains')) >= 0 ) {
                    mailDiv.addClass('green').removeClass('red');
                }
            }
        }

        // Validate Password
        if( $(this).attr('type') === 'password' ) {
            let passDiv = $(av).find('.pass');
            // Length Validation
            /* if( !passDiv. ) {
                let string = $(this).data('password') !== undefined ? $(this).data('password') : 'Must have 1 number & 1 special character';
                $(av).append('<div class="pass"><span class="value">'+string+'</span></div>');
            } */
            // Must have numeric
            if( $(this).data('numeric') !== undefined && /\d/.test( v ) ) {
                passDiv.addClass('red').removeClass('green');
                let string = $(this).data('numeric') !== undefined ? $(this).data('numeric') : 'Must have numeric characters';
                $(av).append('<div class="pass numeric"><span class="value">'+string+'</span></div>');
            } else {
                passDiv.addClass('green').removeClass('red');
                $(av).find('.numeric').remove();
            }
            // Must have special characters
            // Must have numeric
            if( $(this).data('special') !== undefined && /[ `!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/.test( v ) ) {
                passDiv.addClass('red').removeClass('green');
                let string = $(this).data('special') !== undefined ? $(this).data('special') : 'Must have special characters';
                $(av).append('<div class="pass special"><span class="value">'+string+'</span></div>');
            } else {
                passDiv.addClass('green').removeClass('red');
                $(av).find('.special').remove();
            }
            /* const specials = /^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{6,16}$/;
            // Valid password validation
            const vp = /^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{6,16}$/;
            if( $(this).val().length > 0 && vp.test($(this).val()) ) {
                passDiv.addClass('green').removeClass('red');
            } else {
                passDiv.addClass('red').removeClass('green');
            } */
            // Password strength meter
            // TODO: Improve strength meter to work based on character variation + length
            if( $(this).data('strength') !== undefined ) {
                let meter = $(p).find('.aio_strength_meter');
                if( !$(meter).length ) {
                    $(av).append('<div class="aio_strength_meter"><i></i></div>')
                }
                let strength = parseInt( $(this).data('strength') );
                let length = v.length;
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
    .on('keydown','[data-only-numbers],[data-only-numeric]',function(e){
        let key = e.keyCode;
        //console.log(key);
        if( (key > 64 && key < 91) ){
            e.preventDefault();
        }
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
    $('[type=password][data-help],[type=password][data-assist],[type=password][data-validate],[type=password][data-visiblity-toggle]').each(function (i,f) {
        let dir = $('html').attr('dir');
        let input_css = dir === 'ltr' ? {'padding-right':'50px'} : {'padding-left':'50px'};
        //input_css.width = 'calc(100% - 50px)';
        let parent_css = {'position':'relative'};
        let label = $(f).prev('label').length > 0 ? $(f).prev().height() + parseFloat( $(f).prev().css('margin-top') )  + parseFloat( $(f).prev().css('margin-bottom') ) : 0;
        //let top = label + ( $(f).height() / 2 );
        //console.log( top );
        //let visibility_css = {'position':'absolute','transform':'translateY(-20%)','top':top+'px'};
        //dir === 'ltr' ? visibility_css.right = '15px' : visibility_css.left = '15px';
        let password_toggle = $('[data-password_toggle_template]').html(); //'<div class="password_visibility"><i class="mico s on">visibility</i><i class="mico s off">visibility_off</i></div>';
        $(f).css(input_css).parent().css(parent_css).append(password_toggle); //.find('.password_visibility').css(visibility_css);
    })
})