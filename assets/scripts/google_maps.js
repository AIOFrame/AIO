let map;
let loc = {lat: 25.212212, lng: 55.275135};
async function render_google_maps() {
    const { Map } = await google.maps.importLibrary("maps");
    const { AdvancedMarkerElement, PinElement } = await google.maps.importLibrary("marker");
    setTimeout(function(){
        let map_elements = document.querySelectorAll('[data-google-map-render]');
        /* $.each( $('div[data-google-map-render]'), function( i,e ){
            GoogleMap(e, window.google_maps_key );
        }); */
        for (let m = 0; m < map_elements.length; m++) {
            let el = map_elements[m];
            //console.log( map_elements[m] );
            //console.log( el );
            //console.log( el.getAttribute('lat') );
            loc.lat = el.getAttribute('lat') !== null ? parseFloat( el.getAttribute('lat') ) : loc.lat;
            loc.lng = el.getAttribute('lng') !== null ? parseFloat( el.getAttribute('lng') ) : loc.lng;

            // Create the maps config array object
            let config = { zoomControl: true, center: loc, mapId: el.getAttribute('id') };
            // Sets the zoom level (default 13)
            config.zoom = el.getAttribute('level') !== null ? parseInt( el.getAttribute('level') ) : 13;
            // Sets the min and max zoom levels (default 5 and 15)
            config.minZoom = el.getAttribute('min_zoom') !== null ? parseInt( el.getAttribute('min_zoom') ) : 7;
            config.maxZoom = el.getAttribute('max_zoom') !== null ? parseInt( el.getAttribute('max_zoom') ) : 17;
            // If the zoom + and + buttons to be visible or not (default true)
            config.zoomControl = el.getAttribute('zoom_controls') !== null ? parseInt( el.getAttribute('zoom_controls') ) : false;
            // Map display style () (default roadmap)
            config.mapTypeId = el.getAttribute('type') !== null ? el.getAttribute('type') : 'roadmap';
            // If the map type buttons to be visible or not (default false)
            config.mapTypeControl = el.getAttribute('type_controls') !== null ? parseInt( el.getAttribute('type_controls') ) : false;
            // If the map street view button to be visible or not (default false)
            config.streetViewControl = el.getAttribute('street_controls') !== null ? parseInt( el.getAttribute('street_controls') ) : false;
            // If the map rotate button to be visible or not (default false)
            config.rotateControl = el.getAttribute('rotate_controls') !== null ? parseInt( el.getAttribute('rotate_controls') ) : false;
            // If the map fullscreen button to be visible or not (default false)
            config.fullscreenControl = el.getAttribute('full_screen_controls') !== null ? parseInt( el.getAttribute('full_screen_controls') ) : false;
            // Sets the design of the map (default null)
            config.styles = el.getAttribute('design') !== null ? el.getAttribute('design') : '';
            config.scrollwheel = el.getAttribute('scroll') !== null ? el.getAttribute('scroll') : false;

            map = new Map( el, config );

            // Click Event
            let marker;
            if( el.getAttribute('data-lat') !== null || el.getAttribute('data-long') !== null ) {
                // Add a sample marker
                let marker_config = {
                    position: loc,
                    map,
                    gmpDraggable: true
                };
                if( el.getAttribute('data-marker') !== null ) {
                    const pin = new PinElement({
                        background: el.getAttribute('data-marker'),
                        borderColor: 'white',
                        glyphColor: 'white',
                        scale: 1.2,
                    });
                    marker_config.content = pin.element;
                }
                //console.log( marker_config );
                marker = new AdvancedMarkerElement(marker_config);
                // On click move marker and get location details
                map.addListener('click',function (e) {
                    marker.position = e.latLng;
                    // Pan map to clicked position
                    setTimeout(function () { map.panTo( e.latLng ); },3000);
                    // Fetch Location
                    parse_location( el, e.latLng );
                })
                // On move marker get location
                marker.addListener('dragend', function (e) {
                    map.panTo( e.latLng );
                    parse_location( el, e.latLng );
                });
            }

            // Search
            const search = el.getAttribute('search');
            if( search !== null ) {
                const searchBox = search !== null ? new google.maps.places.SearchBox( document.getElementById(search) ) : '';
                searchBox.addListener("places_changed", function (e) {
                    const places = searchBox.getPlaces();
                    if( places.length > 0 ) {
                        const place = places[0];
                        if (!place.geometry || !place.geometry.location) {
                            console.log("Returned place contains no geometry");
                            return;
                        }
                        // Move marker to searched place
                        marker.position = place.geometry.location;
                        // Pan map to searched place
                        map.panTo( place.geometry.location );
                        parse_location( el, place.geometry.location );
                    }
                });
            }

        }
    },2000);
}

function parse_location( element, position ){
    let e = element;
    let x = position.lat();
    let y = position.lng();
    if( x !== undefined && x !== null && y !== undefined && y !== null ) {
        e.getAttribute('data-gps') !== null ? document.querySelectorAll( e.getAttribute('data-gps') )[0].value = x + ',' + y : '';
        e.getAttribute('data-coordinates') !== null ? document.querySelectorAll( e.getAttribute('data-coordinates') )[0].value = x + ',' + y : '';
        e.getAttribute('data-lat') !== null ? document.querySelectorAll( e.getAttribute('data-lat') )[0].value = x : '';
        e.getAttribute('data-lat') !== null ? document.querySelectorAll( e.getAttribute('data-long') )[0].value = y : '';
        if( $(e).data('area') || $(e).data('city') || $(e).data('state') || $(e).data('country') || $(e).data('country_code') || $(e).data('location')){
            let gc = new google.maps.Geocoder();
            gc.geocode({ 'location': position },function(r,s){
                console.log( r );
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
}

//render_google_maps();

/* let loc = {lat: 25.212212, lng: 55.275135};

$(document).ready(function(){
    setTimeout(function(){
        $.each( $('div[data-google-map-render]'), function( i,e ){
            GoogleMap(e, window.google_maps_key );
        })
    },1000)
});

window.google_maps_key = undefined;
window.google_map_icon = undefined;

function renderGoogleMap() {
    alert('test');
}

function GoogleMap(e, key ) {
    if(key === ''){ elog('Google Maps Key Error, Option \'google_maps_key\' is missing in options database or pass key as second parameter in GoogleMaps(e, key)'); return }
    if($(e).data('value')){
        let d = $(e).data('value');
        d = d.split(',');
        loc['lat'] = parseFloat(d[0]);
        loc['lng'] = parseFloat(d[1]);
    }
    let con = { center: loc };
    if( $(e).data('types') ){
        let mapTypeControlOptions = {
            mapTypeIds: $(e).data('types').split(',')
        };
        con['mapTypeControlOptions'] = mapTypeControlOptions;
    }
    // console.log(con);
    const map = new google.maps.Map($(e)[0], con);
    //console.log( e );
    console.log( $( '#' + $(e).attr('search') )[0] );
    const search = $( '#' + $(e).attr('search') )[0];
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
            icon: window.google_map_icon,
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

}
function GMapValues( e, m, p ){
    $($(e).data('gps')).val(x + ',' + y);
    $($(e).data('coordinates')).val(x + ',' + y);
    $($(e).data('lat'),$(e).data('latitude')).val(x);
    $($(e).data('long'),$(e).data('longitude')).val(y);
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
}*/