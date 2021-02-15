<?php

class MAPS {

    function google_maps() {
        $k = get_option( 'google_maps_key' );
        if( empty( $k ) ){
            $k = get_config('google_maps_key');
        }
        if( !empty( $k ) ) {
            echo '<script async defer src="//maps.googleapis.com/maps/api/js?key=' . $k . '" type="text/javascript"></script>';
        }
        ?>
<script>
    $(document).ready(function(){
        setTimeout(function(){
            $.each( $('[data-map]'), function( i,e ){
                GoogleMap(e);
            })
        },500)
    });
    var loc = {lat: 25.212212, lng: 55.275135};
    function GoogleMap(e, key = '<?php echo $k; ?>'  ) {
        if(key === ''){ elog('Google Maps Key Error, Option \'google_maps_key\' is missing in options database or pass key as second parameter in GoogleMaps(e, key)'); return }
        var con = { center: loc };
        if($(e).data('value')){
            var d = $(e).data('value');
            d = d.split(',');
            loc['lat'] = parseFloat(d[0]);
            loc['lng'] = parseFloat(d[1]);
        }
        con['zoom'] = $(e).data('zoom') ? $(e).data('zoom') : 13;
        con['mapTypeId'] = $(e).data('type') ? $(e).data('type') : 'roadmap';
        con['styles'] = $(e).data('skin') ? $(e).data('skin') : '';
        con['scrollwheel'] = false;
        if( $(e).data('types') ){
            var mapTypeControlOptions = {
                mapTypeIds: $(e).data('types').split(',')
            }
            con['mapTypeControlOptions'] = mapTypeControlOptions;
        }
        con['zoomControl'] = $(e).data('nozoom') ? false : true;
        if (!$(e).data('streetview')) {
            con['streetViewControl'] = false
        }
        var map = new google.maps.Map($(e)[0], con);
        if($(e).data('marks')){
            var marks = $(e).data('marks');
            if( marks.length > 0 ) {
                $.each( marks, function( a, b ){
                    var loc = {lat: b['lat'], lng: b['long']};
                    var ico = {
                        url: b['ico'],
                        scaledSize: new google.maps.Size(50, 50),
                        origin: new google.maps.Point(0,0),
                        anchor: new google.maps.Point(0, 0)
                    };
                    var marker = new google.maps.Marker({
                        position: loc,
                        icon: ico,
                        map: map,
                        title: b['title'],
                        size: new google.maps.Size(25, 25)
                    });
                    if(b['center']){
                        var center = new google.maps.LatLng(b['lat'], b['long']);
                        map.panTo(center);
                    }
                })
            }
        } else {
            var marker = new google.maps.Marker({
                position: loc,
                icon: '<?php echo APPURL; ?>assets/images/marker.png',
                map: map,
                draggable: true
            });
            marker.addListener('dragend', function () {
                //console.log(map);
                z = map.getZoom();
                pos = {lat: this.position.lat(), lng: this.position.lng()};
                GMapValues(e,marker,pos);
            });
        }

    }
    function GMapValues( e, m, p ){
        $($(e).data('gps')).val(m.position.lat() + ', ' + m.position.lng());
        if($(e).data('area') || $(e).data('city') || $(e).data('state') || $(e).data('country') || $(e).data('country_code') || $(e).data('location')){
            var gc = new google.maps.Geocoder();
            gc.geocode({'location':p},function(r,s){
                if (s === 'OK') {
                    if (r[0]['address_components']) {
                        var a = r[0]['address_components'];

                        var a1 = a[0] !== undefined && a[0].long_name !== 'undefined' ? a[0].long_name.replace('"','').replace("'","") : '';
                        var a2 = a[1] !== undefined && a[1].long_name !== 'undefined' ? a[1].long_name.replace('"','').replace("'","") : '';
                        var a3 = a[2] !== undefined && a[2].long_name !== 'undefined' ? a[2].long_name.replace('"','').replace("'","") : '';
                        var area = a1 + ' ' + a2 + ' ' + a3;
                        $($(e).data('area')).val(area);

                        var city = a[3] !== undefined && a[3].long_name !== 'undefined' ? a[3].long_name.replace('"','').replace("'","") : '';
                        $($(e).data('city')).val(city);

                        var state = a[4] !== undefined && a[4].long_name !== 'undefined' && a[4].long_name !== city ? a[4].long_name.replace('"','').replace("'","") : '';
                        $($(e).data('state')).val(state);

                        var country = a[5] !== undefined && a[5].long_name !== undefined ? a[5].long_name.replace('"','').replace("'","") : '';
                        $($(e).data('country')).val(country);

                        var code = a[5] !== undefined && a[5].short_name !== 'undefined' ? a[5].short_name.replace('"','').replace("'","") : '';
                        $($(e).data('country_code')).val(code);

                        $($(e).data('location')).val(area+', '+city+' '+state+', '+country);
                        $('.chosen').trigger("chosen:updated");
                    }
                } else {
                    elog('Geocoder failed due to: ' + status);
                }
            })
        }
    }
    function PanTo(e,c){

    }
</script>
        <?php
    }
}

/*

HOW TO USE

WRITE PHP CODE TO INITIATE CLASS AND FUNCTION:
render_maps(); // Will include everything necessary

HTML STRUCTURE
<div class="mb15 gmap" data-zoom="17" data-value="25.183365022185065, 55.34723626990683" id="branch_map" data-gps="#branch_gps" data-city="#branch_city" data-country="#branch_country" data-area="#branch_area" data-location="#branch_location"></div>

Set Location:
data-zoom // You can set a default level of zoom on initiation of map
data-value // You can pass latitude and longitude separated by comma to have marker to that position

Get Location: (On Marker Move)
data-gps // Sets lat and long separated by comma to given class or id
data-area // Sets area of the position
data-city // Sets city of position
data-country // Sets country of position
data-country_code // Sets country code Ex: AE
*/