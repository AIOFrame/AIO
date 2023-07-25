document.addEventListener('DOMContentLoaded', function () {

    // Manipulator
    let elements = document.querySelectorAll('[data-toggle-on],[data-on],[data-off],[data-show],[data-hide],[data-toggle],[data-slide],[data-remove],[data-action],[data-click],[data-href],[data-click-on-enter]');
    for (let i = 0; i < elements.length; i++) {
        elements[i].addEventListener("click", function(e) {
            let el = e.target;
            //console.log( document.querySelector('.forgot_wrap') );

            // Toggle class on
            el.getAttribute('data-toggle-on') !== null ? document.querySelector( el.getAttribute('data-toggle-on') ).classList.toggle('on') : '';

            // Toggle Elements
            if( el.getAttribute('data-toggle') !== null ) {
                let targets = document.querySelectorAll( el.getAttribute('data-toggle') );
                for( let x = 0; x < targets.length; x++ ) {
                    targets[x].style.display = ( targets[x].style.display === "none" ? "block" : "none" );
                }
            }

            // Hide Elements
            if( el.getAttribute('data-hide') !== null ) {
                let targets = document.querySelectorAll( el.getAttribute('data-hide') );
                for( let x = 0; x < targets.length; x++ ) {
                    targets[x].style.display = "none";
                }
            }

            // Show Elements
            if( el.getAttribute('data-show') !== null ) {
                let targets = document.querySelectorAll( el.getAttribute('data-show') );
                for( let x = 0; x < targets.length; x++ ) {
                    targets[x].style.display = "block";
                }
            }

            // Simulate Click
            if( el.getAttribute('data-click') !== null ) {
                let targets = document.querySelectorAll( el.getAttribute('data-click') );
                for( let x = 0; x < targets.length; x++ ) {
                    targets[x].click();
                }
            }

            // Remove Elements
            if( el.getAttribute('data-remove') !== null ) {
                let targets = document.querySelectorAll( el.getAttribute('data-remove') );
                for( let x = 0; x < targets.length; x++ ) {
                    targets[x].remove();
                }
            }

            /* $(this).data('toggle-on') === '' ? $(this).toggleClass('on') : $($(this).data('toggle-on')).toggleClass('on');
            // Adds class on
            $(this).data('on') === '' ? $(this).addClass('on') : $($(this).data('on')).addClass('on');
            // Removes class on
            $(this).data('off') === '' ? $(this).removeClass('on') : $($(this).data('off')).removeClass('on');

            $(this).data('toggle') === '' ? $(this).toggle() : $($(this).data('toggle')).toggle();
            // Slide Toggle Element
            $(this).data('slide') === '' ? $(this).slideToggle() : $($(this).data('slide')).slideToggle();
            // Fade Modal on show
            if($($(this).data('show')).hasClass('modal') && $($(this).data('on')).data('fade') === undefined ){
                $('article').addClass('fade');
            }
            // Fade Modal on toggle class on
            if($($(this).data('on')).hasClass('modal') && $($(this).data('on')).data('fade') === undefined ){
                $('article').addClass('fade');
            }
            // Prevent default
            if($(this).data('href')){
                if( $(e.target).data('prevent-default') === undefined ){
                    if( $(this).attr('target') !== undefined && $(this).attr('target') === '_blank' ) {
                        window.open( $(this).data('href'), '_blank').focus();
                    } else {
                        location.href = $(this).data('href');
                    }
                }
            } */


        });
    }

    let elements2 = document.querySelectorAll('[data-click-on-enter]');
    for (let i = 0; i < elements2.length; i++) {
        elements2[i].addEventListener("keyup", function(e) {
            let el = e.target;
            // Simulate Click on Enter
            if( el.getAttribute('data-click-on-enter') !== null && e.code === 'Enter' ) {
                let targets = document.querySelectorAll( el.getAttribute('data-click-on-enter') );
                for( let x = 0; x < targets.length; x++ ) {
                    targets[x].click();
                }
            }
        });
    }

}, false);