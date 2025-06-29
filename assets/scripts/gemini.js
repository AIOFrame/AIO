document.addEventListener('DOMContentLoaded', function () {
    // Auto Fill Forms
    $('body').on('click','.modal .ai_fill',function (e) {
        $(e.target).addClass('load');
        let fields = []; let titles = [];
        $('.modal .form > div').each(function(i,f){
            if( $(f).children('.slide_set').length === 0 ) { // && $(f).children('.aio_upload').length === 0

                // Ignore Logic
                if( $(f).find('[data-ai]').length > 0 && $(f).find('[data-ai]').data('ai') === 'ignore' ) {
                    return true;
                }

                let id = $(f).find('.lbl').attr('for');
                //console.log( $(f).find('[data-ai]') );
                let label = $(f).find('[data-ai]').length > 0 ? $(f).find('[data-ai]').data('ai') : $(f).find('.lbl').text().replaceAll('*','').replaceAll('Code','');
                let key = $(f).find('.lbl').next().data('key');
                if( $(f).children('.aio_upload').length !== 0 ) {
                    label = 'Find royalty-free ' + label + ' from pexels.com, should be functional direct image url' ;
                }
                if( $(f).children('.phone_set').length !== 0 ) {
                    titles.push('Calling Code');
                    fields.push({'label':'Calling Code','id':$(f).find('.phone_set > div:nth-child(1) .lbl').attr('for'),'key':'calling_code'});
                    id = $(f).find('.phone_set > div:nth-child(2) .lbl').attr('for');
                    key = 'phone';
                }
                if( $(f).find('.dater').length !== 0 ) {
                    id = $(f).find('> input').attr('id');
                    key = 'date';
                }
                titles.push(label);
                fields.push({'label':label,'id':id,'key':key});
            }
        });
        //console.log(titles);
        console.log(fields);
        let form_title = $(e.target).parent().parent('.modal').find('h2.title:nth-child(1)').text().replaceAll('Add ','');
        form_title = form_title.endsWith('s') ? form_title.slice(0, -1) : form_title;
        let titles_list = '';
        $(titles).each(function (i,t) {
            if( t !== undefined ) {
                titles_list += '"' + t + '",';
            }
        })
        let prompt = 'Filling form to add ' + form_title + ', suggest me random, not previously suggested and realistic text to fill each of ' + titles_list.replace(/,\s*$/, "") + '. Very strictly return suggestions as single array without any commentary, explanation, formatting, or extra output.';
        console.log(prompt);
        //return;
        $.ajax({
            type: "POST",
            url: location.origin + '/google_gemini',
            data: { prompt: prompt },
            success: function (r) {
                console.log(r);
                r = r.replaceAll('```','').replaceAll('json','');
                r = JSON.parse(r);
                if( Array.isArray(r) ){
                    $.each( fields, function (i, f) {
                        if (typeof r[i] === 'string' || r[i] instanceof String) {
                            if( fields[i]['key'] === 'phone' ) {
                                $('#' + f.id).val(parseInt(r[i].replaceAll('-','')));
                            } else {
                                $('#'+f.id).val( r[i] ).change();
                            }
                            //if( fields[i]['key'] === 'date' ) {
                                //$('#' + f.id).next().next().val(r[i].split("-").reverse().join("-")).change();
                            //}
                        }
                    })
                } else {
                    alert('Unable to parse response from AI, please try again!')
                }
                $(e.target).removeClass('load');
                file_ui();
                files_ui();
            },
            error: function (r) {
                console.log(r);
                $(e.target).removeClass('load');
            }
        });
        setTimeout(function () {
            $(e.target).removeClass('load');
        },5000);
    })
})

// async function askGenAI(text) {
//     const response = await fetch('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' + key, {
//         method: 'POST',
//         headers: { 'Content-Type': 'application/json' },
//         body: JSON.stringify({"contents":[{"parts":[{"text":text}]}]})
//     });
//     const data = await response.json();
//     //console.log( data.candidates[0].output );
//     //console.log( data.candidates[0].content );
//     console.log( data.candidates[0].content.parts[0].text.replaceAll('*','') );
// }