let loc = {lat: 25.212212, lng: 55.275135};
function GoogleMap(e, key ) {
    if(key === ''){ elog('Google Maps Key Error, Option \'google_maps_key\' is missing in options database or pass key as second parameter in GoogleMaps(e, key)'); return }
    /* if($(e).data('value')){
        let d = $(e).data('value');
        d = d.split(',');
        loc['lat'] = parseFloat(d[0]);
        loc['lng'] = parseFloat(d[1]);
    } */
    loc['lat'] = $(e).attr('lat') ? parseFloat( $(e).attr('lat') ) : loc['lat'];
    loc['lng'] = $(e).attr('long') ? parseFloat( $(e).attr('long') ) : loc['lng'];
    let con = { center: loc };
    con['zoom'] = $(e).attr('level') ? parseInt( $(e).attr('level') ) : 13;
    con['mapTypeId'] = $(e).attr('type') ? $(e).attr('type') : 'roadmap';
    con['styles'] = $(e).attr('design') ? $(e).attr('design') : '';
    con['scrollwheel'] = false;
    con['streetViewControl'] = $(e).attr('streetview') ? $(e).attr('streetview') : false ;
    if( $(e).data('types') ){
        let mapTypeControlOptions = {
            mapTypeIds: $(e).data('types').split(',')
        };
        con['mapTypeControlOptions'] = mapTypeControlOptions;
    }
    con['zoomControl'] = true;
    console.log(con);
    const map = new google.maps.Map($(e)[0], con);
    const search = document.getElementById( $(e).attr('search') );
    const searchBox = new google.maps.places.SearchBox( search );
    let marker;
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
                    icon: ico,
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
    searchBox.addListener("places_changed", function () {
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
            //let marker;
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
    $($(e).data('gps')).val(m.position.lat() + ',' + m.position.lng());
    $($(e).data('coordinates')).val(m.position.lat() + ',' + m.position.lng());
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
                    $($(e).data('name')).val(name).trigger('change');

                    let address = a1 + ' ' + a2 + ' ' + a3;
                    $($(e).data('address')).val(address).trigger('change');

                    let area = a[2] !== undefined && a[2].long_name !== 'undefined' ? a[2].long_name.replace('"','').replace("'","") : '';
                    $($(e).data('area')).val(area).trigger('change');

                    let city = a[3] !== undefined && a[3].long_name !== 'undefined' ? a[3].long_name.replace('"','').replace("'","") : '';
                    $($(e).data('city')).val(city).trigger('change');

                    let state = a[4] !== undefined && a[4].long_name !== 'undefined' && a[4].long_name !== city ? a[4].long_name.replace('"','').replace("'","") : '';
                    $($(e).data('state')).val(state).trigger('change');

                    let country = a[5] !== undefined && a[5].long_name !== undefined ? a[5].long_name.replace('"','').replace("'","") : '';
                    $($(e).data('country')).val(country).trigger('change');

                    let code = a[5] !== undefined && a[5].short_name !== 'undefined' ? a[5].short_name.replace('"','').replace("'","") : '';
                    $($(e).data('country_code')).val(code).trigger('change');

                    $($(e).data('location')).val(area+', '+city+' '+state+', '+country).trigger('change');
                    //$('.select2').trigger("chosen:updated");
                }
            } else {
                elog('Geocoder failed due to: ' + status);
            }
        })
    }
}