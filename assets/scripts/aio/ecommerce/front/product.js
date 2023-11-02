document.addEventListener('DOMContentLoaded', function () {
    let b = $('body');
    let combinations = $('[data-combinations]').data('combinations');
    if( combinations !== undefined ) {
        $(b).on('change',$('[data-combinations] input'),function () {
            //console.log('changed!');
            //console.log( combinations );
            let selected = [];
            //let variable = 0;
            $('.aio_variation_group').each(function (i,vg) {
                let v = $(vg).find('input:checked');
                if( v.length === 0 ) {
                    return;
                }
                let id = $(v).data('type');
                let m = parseInt( $(v).val() );
                selected.push({i:id,m:m});
                //console.log(id);
                //console.log(m);
            });
            //console.log( selected );
            //let var = [];
            //let common = [];
            /* $(selected).each(function (z,sv) { // 2
                //console.log(sv);
                $.each(combinations,function (i,c) { // 4
                    let vars_length = Object.keys( c['v'] ).length;
                    if( selected.length === vars_length ) {
                        let matched = 0;
                        $.each(c['v'], function (x, vs) { // 2
                            //console.log( 'selected id '+sv['i']+', var id '+vs['i']+', selected meta '+sv['m']+', var meta '+vs['m'] );
                            if( sv['i'] === vs['i'] && sv['m'] === vs['m'] ) {
                                matched++;
                            }
                        })
                        //console.log( matched );
                    }
                })
            }) */
            $.each(combinations,function (i,c) {
                let vars_length = Object.keys( c['v'] ).length;
                if( selected.length === vars_length ) {
                    //console.log(c['v']);
                    let matched = 0;
                    $.each( c['v'], function (x,vs) {
                        //console.log(vs);
                        matched++;
                        $(selected).each(function (y,sv) {
                            if( sv['i'] === vs['i'] && sv['m'] === vs['m'] ) {
                                matched++;
                            }
                        })
                    } );
                    //console.log( matched );
                    if( matched === ( selected.length + vars_length ) ) {
                        console.log( c );
                    }
                } else {
                    console.log('Full combinations not selected yet!')
                }
            });
        })
    }

})