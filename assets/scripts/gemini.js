document.addEventListener('DOMContentLoaded', function () {
    // Auto Fill Forms
    $('body').on('click','.modal .ai_fill',function () {
        let fields = []; let titles = [];
        $('.modal .form > div').each(function(i,f){
            if( $(f).children('.slide_set').length === 0 && $(f).children('.aio_upload').length === 0 ) {
                let id = $(f).find('.lbl').attr('for');
                let label = $(f).find('.lbl').text().replaceAll('*','').replaceAll('Code','');
                titles.push(label);
                fields.push({'label':label,'id':id});
            }
        });
        let form_title = $(this).parent().parent('.modal').find('h2.title:nth-child(1)').text().replaceAll('Add ','');
        let prompt = 'Filling form to add ' + form_title + ', suggest me realistic text to fill each of ' + titles.join(',') + '. Respond as single array without commentary.';
        console.log(prompt);
        $.ajax({
            type: "POST",
            url: location.origin + '/google_gemini',
            data: { prompt: prompt },
            success: function (r) {
                console.log(r);
                r = r.replaceAll('```','').replaceAll('json','');
                r = JSON.parse(r);
                console.log(r);
                if( Array.isArray(r) ){
                    $.each( fields, function (i, f) {
                        $('#'+f.id).val( r[i] );
                    })
                }
            }
        });
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