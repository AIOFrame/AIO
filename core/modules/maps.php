<?php

class MAPS {

    function google_maps() {
        $db = new DB();
        $k = $db->get_option( 'google_maps_key' );
        $k = empty( $k ) ? get_config('google_maps_key') : $k;
        if( !empty( $k ) ) {
            echo '<script async defer src="//maps.googleapis.com/maps/api/js?key=' . $k . '&libraries=places" type="text/javascript"></script>';
        }
        ?>
<script>
    let marker;
    $(document).ready(function(){
        setTimeout(function(){
            $.each( $('[data-map]'), function( i,e ){
                GoogleMap(e);
            })
        },500)
    });
    let loc = {lat: 25.212212, lng: 55.275135};
    let icon = '<?php echo APPURL; ?>assets/images/marker.png';
    function GoogleMap(e, key = '<?php echo $k; ?>'  ) {
        if(key === ''){ elog('Google Maps Key Error, Option \'google_maps_key\' is missing in options database or pass key as second parameter in GoogleMaps(e, key)'); return }

        /* if($(e).data('value')){
            let d = $(e).data('value');
            d = d.split(',');
            loc['lat'] = parseFloat(d[0]);
            loc['lng'] = parseFloat(d[1]);
        } */
        loc['lat'] = $(e).attr('lat') ? parseFloat( $(e).attr('lat') ) : loc['lat'];
        loc['long'] = $(e).attr('long') ? parseFloat( $(e).attr('long') ) : loc['long'];
        let con = { center: loc };
        con['zoom'] = $(e).attr('level') ? $(e).attr('level') : 13;
        con['mapTypeId'] = $(e).attr('type') ? $(e).attr('type') : 'roadmap';
        con['styles'] = $(e).attr('design') ? $(e).attr('design') : '';
        con['scrollwheel'] = false;
        con['streetViewControl'] = $(e).attr('streetview') ? $(e).attr('streetview') : false ;
        if( $(e).data('types') ){
            let mapTypeControlOptions = {
                mapTypeIds: $(e).data('types').split(',')
            }
            con['mapTypeControlOptions'] = mapTypeControlOptions;
        }
        con['zoomControl'] = true;
        const map = new google.maps.Map($(e)[0], con);
        const search = document.getElementById( $(e).attr('search') );
        const searchBox = new google.maps.places.SearchBox( search );

        if($(e).data('marks')){
            let marks = $(e).data('marks');
            if( marks.length > 0 ) {
                $.each( marks, function( a, b ){
                    let loc = {lat: b['lat'], lng: b['long']};
                    let ico = {
                        url: b['ico'],
                        scaledSize: new google.maps.Size(50, 50),
                        origin: new google.maps.Point(0,0),
                        anchor: new google.maps.Point(0, 0)
                    };
                    marker = new google.maps.Marker({
                        position: loc,
                        icon: icon,
                        map: map,
                        title: b['title'],
                        size: new google.maps.Size(25, 25)
                    });
                    if(b['center']){
                        let center = new google.maps.LatLng(b['lat'], b['long']);
                        map.panTo(center);
                    }
                })
            }
        } else {
            marker = new google.maps.Marker({
                position: loc,
                icon: icon,
                map: map,
                draggable: true
            });
            marker.addListener('dragend', function () {
                //console.log(map);
                z = map.getZoom();
                let pos = {lat: this.position.lat(), lng: this.position.lng()};
                GMapValues(e,marker,pos);
            });
        }

        // let markers = [];
        searchBox.addListener("places_changed", () => {
            const places = searchBox.getPlaces();

            if (places.length == 0) {
                return;
            }

            // Clear out the old markers.
            /* markers.forEach((marker) => {
                marker.setMap(null);
            });
            markers = []; */

            // For each place, get the icon, name and location.
            const bounds = new google.maps.LatLngBounds();

            /* places.forEach((place) => { */
            if( places.length > 0 ) {
                const place = places[0];
                if (!place.geometry || !place.geometry.location) {
                    console.log("Returned place contains no geometry");
                    return;
                }

                // Create a marker for each place.
                marker.setPosition( place.geometry.location );
                let pos = {lat: place.geometry.location.lat(), lng: place.geometry.location.lng()};
                GMapValues(e,marker,pos);
                /* marker = new google.maps.Marker({
                    map,
                    icon: icon,
                    title: place.name,
                    draggable: true,
                    position: place.geometry.location
                });
                /* new_marker.addListener('dragend', function () {
                    //console.log(map);
                    z = map.getZoom();
                    pos = {lat: this.position.lat(), lng: this.position.lng()};
                    GMapValues(e,marker,pos);
                }); */
                // markers.push(new_marker);
                if (place.geometry.viewport) {
                    // Only geocodes have viewport.
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }
            }
            map.fitBounds(bounds);
        });

    }
    function GMapValues( e, m, p ){
        $($(e).data('gps')).val(m.position.lat() + ', ' + m.position.lng());
        $($(e).data('lat'),$(e).data('latitude')).val(m.position.lat());
        $($(e).data('long'),$(e).data('longitude')).val(m.position.lng());
        if($(e).data('area') || $(e).data('city') || $(e).data('state') || $(e).data('country') || $(e).data('country_code') || $(e).data('location')){
            let gc = new google.maps.Geocoder();
            gc.geocode({'location':p},function(r,s){
                if (s === 'OK') {
                    if (r[0]['address_components']) {
                        let a = r[0]['address_components'];

                        let a1 = a[0] !== undefined && a[0].long_name !== 'undefined' ? a[0].long_name.replace('"','').replace("'","") : '';
                        let a2 = a[1] !== undefined && a[1].long_name !== 'undefined' ? a[1].long_name.replace('"','').replace("'","") : '';
                        let a3 = a[2] !== undefined && a[2].long_name !== 'undefined' ? a[2].long_name.replace('"','').replace("'","") : '';
                        let name = r[0]['formatted_address'];
                        $($(e).data('name')).val(name);

                        let address = a1 + ' ' + a2 + ' ' + a3;
                        $($(e).data('address')).val(address);

                        let area = a[2] !== undefined && a[2].long_name !== 'undefined' ? a[2].long_name.replace('"','').replace("'","") : '';
                        $($(e).data('area')).val(area);

                        let city = a[3] !== undefined && a[3].long_name !== 'undefined' ? a[3].long_name.replace('"','').replace("'","") : '';
                        $($(e).data('city')).val(city);

                        let state = a[4] !== undefined && a[4].long_name !== 'undefined' && a[4].long_name !== city ? a[4].long_name.replace('"','').replace("'","") : '';
                        $($(e).data('state')).val(state);

                        let country = a[5] !== undefined && a[5].long_name !== undefined ? a[5].long_name.replace('"','').replace("'","") : '';
                        $($(e).data('country')).val(country);

                        let code = a[5] !== undefined && a[5].short_name !== 'undefined' ? a[5].short_name.replace('"','').replace("'","") : '';
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