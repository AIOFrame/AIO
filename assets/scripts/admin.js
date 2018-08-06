$(document).ready(function(){
    // Instant Search
    var s = $('#finder input');
    if($('body').hasClass('page_dashboard')){

    } else if($('body').hasClass('page_projects')){
        s.attr('placeholder','Search ID, Project, Client...').on('keyup',function(){
            if(s.val().length > 2){
                $('.project').hide().parent().parent().hide();
                $('.project').each(function(i,e){
                    var ss = s.val().toLowerCase();
                    if($(e).find('h3').html().toLowerCase().indexOf(ss) !== -1){
                        $(this).show().parent().parent().show();
                    }
                    if($(e).find('h5').html().toLowerCase().indexOf(ss) !== -1){
                        $(this).show().parent().parent().show();
                    }
                })
            } else {
                $('.project').show().parent().parent().show();
            }
        })
    } else if($('body').hasClass('page_project')){

    } else if($('body').hasClass('page_employees')){
        s.attr('placeholder','Search ID, Employee, Designation...').on('keyup',function(){
            if(s.val().length > 2){
                $('.employee').hide().parent().parent().hide();
                $('.employee').each(function(i,e){
                    var ss = s.val().toLowerCase();
                    if($(e).find('.name').html().toLowerCase().indexOf(ss) !== -1){
                        $(this).show().parent().parent().show();
                    }
                    if($(e).find('.id').html().toLowerCase().indexOf(ss) !== -1){
                        $(this).show().parent().parent().show();
                    }
                    if($(e).find('.design').html().toLowerCase().indexOf(ss) !== -1){
                        $(this).show().parent().parent().show();
                    }
                })
            } else {
                $('.employee').show().parent().parent().show();
            }
        })
    } else if($('body').hasClass('page_employee')){

    }

    $('.tab_heads>div').on('click',function(){
        $('.tab_heads>div').removeClass('on');
        $(this).addClass('on');
        $('.tab_content').children().hide();
        $($(this).data('t')).show();
    });

    $('body').on('click','.modal .hide, .modal .close',function () {
        $(this).parent('.modal').hide();
    });

    // Manipulator

    $(document).on('click','[data-action], [data-show], [data-hide], [data-remove],[data-toggle],[data-resetsrc],[data-resetinput]',function() {
        if ($(this).data('togglescroll') === true) {
            scroll_lock();
        }
        if ($(this).data('blur') === true) {
            blurred();
        }
        if ($(this).data('targettoggleclass')) {
            $($(this).data('hide')).toggleClass($(this).data('targettoggleclass'));
            $($(this).data('action')).toggleClass($(this).data('targettoggleclass'));
            $($(this).data('show')).toggleClass($(this).data('targettoggleclass'));
        }
        $($(this).data('remove')).remove();
        $($(this).data('hide')).hide();
        $($(this).data('show')).show();
        $($(this).data('toggle')).toggle();

        $($(this).data('resetsrc')).attr('src','');
        $($(this).data('resetinput')).val('');
    });

    // Views

    $('.views>div').on('click',function(){
        $('.views>div').removeClass('on');
        $(this).addClass('on');
        $($(this).parent().data('t')).toggleClass('table plates')
    });

    $('body').on('click','[data-t]',function(){
        var current = this;
        console.log($(current));
        $(current).removeClass('on');
        $(current).addClass('on');
        $($(current).data('t')).parent().children().hide();
        $($(current).data('t')).show();
    });
})