$(document).ready(function(){

    // Example AIO Dynamics Structure
    /*
    <div class="options_wrap" data-dynamics>
        <!-- Looped elements will be placed in div below -->
        <div class="options_loop" data-loop></div>
        <!-- Looped elements inputs data JSON will be placed in input below -->
        <input type="text" class="options_input dn" data-dynamics-data />
        <!-- Add button will duplicate a data-template -->
        <button class="btn" data-add>Add</button>
        <!-- Place your template inside data-template -->
        <div class="option_template" data-template>
            <label for="name">Name</label>
            <input type="text" name="name" id="name" />
            <label for="options">Options</label>
            <select id="options" name="options">
                <option value="1">Yes</option>
                <option value="0">No</option>
            </select>
        </div
        </div>
    </div>
     */

    let template_pre = '<div data-template-looped><div class="close" data-remove></div><div class="template">';
    let template_post = '</div></div>';

    // AIO Dynamics Repeats a template set upon clicking element with attribute data-add
    $('body').on('click','[data-dynamics] [data-add]',function(){
        let template = $(this).parents('[data-dynamics]').find('[data-template]').html();
        template = template.length > 0 ? template_pre+template+template_post : '';
        $(this).parents('[data-dynamics]').find('[data-loop]').append(template);
        $(this).data('callback') !== undefined ? eval( $(this).data('callback') + '()' ) : '';
    })

    .on('click','[data-dynamics] [data-remove]',function(){
        $(this).parents('[data-template-looped]').remove();
        update_dynamics_data($(this));
        $(this).data('callback') !== undefined ? eval( $(this).data('callback') + '()' ) : '';
    })

    .on('change keyup','[data-dynamics] input, [data-dynamics] textarea, [data-dynamics] select',function(){
        update_dynamics_data($(this));
    });

    $('[data-dynamics]').each(function(a,b){
        let dynamic_data = $(b).find('[data-dynamics-data]').val();
        let template = $(b).find('[data-template]').html();
        template = template.length > 0 ? template_pre+template+template_post : '';
        dynamic_data = dynamic_data !== '' && dynamic_data !== undefined ? JSON.parse(dynamic_data) : [];

        $(dynamic_data).each(function (c,d) {
            $(b).find('[data-loop]').append(template);
            let dynamic_el = $(b).find('[data-template-looped]');
            console.log(dynamic_el);

            let i = 0;
            $.each(d, function (x, y) {
                console.log(x);
                console.log(y);
                //console.log(template);
                i++;
                $(dy)
                /* i++;
                dyn += '<div class="set">';

                if (d[0] === 'text') {
                    dyn += '<label for="' + d[1] + '_' + i + '">' + d[2] + '</label><input type="' + d[0] + '" placeholder="' + d[2] + '" id="' + d[1] + '_' + i + '">'
                } else if (d[0] === 'div') {
                    dyn += '<div class="' + d[1] + '">' + location.href + 'page</div>';
                } else if (d[0] === 'checkbox' || d[0] === 'radio') {
                    dyn += '<input type="' + d[0] + '" id="' + d[1] + '_' + i + '"><label for="' + d[1] + '_' + i + '">' + d[2] + '</label>'
                }
                dyn += '</div>'; */
            });
            /* dyn += '</div>';
            $('<div class="aio_dynamics"><div class="fields">' + dyn + '</div><div class="btn add">+</div></div>').insertAfter($(b));
            $(this).data('dyn', dyn).hide(); */
        });
    })

})

function update_dynamics_data(e) {
    let data = [];
    $.each( $(e).parents('[data-dynamics]').find('[data-template-looped]'), function(a,b){
        data.push( get_values( b ) );
    });
    $(e).parents('[data-dynamics]').find('[data-dynamics-data]').val( JSON.stringify( data ) );
}