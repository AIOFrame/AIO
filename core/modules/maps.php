<?php

class MAPS {

    function google_maps(): void {
        $db = new DB();
        $k = $db->get_option( 'google_maps_key' );
        $k = empty( $k ) ? get_config('google_maps_key') : $k;
        if( !empty( $k ) ) {
            get_scripts( 'https://maps.googleapis.com/maps/api/js?key=' . $k . '&libraries=places,google_maps' );
        }
        ?>
<script>
    // let marker;
    $(document).ready(function(){
        setTimeout(function(){
            $.each( $('[data-map]'), function( i,e ){
                GoogleMap(e, '<?php echo $k; ?>' );
            })
        },500)
    });
    var marker = '<?php echo APPURL; ?>assets/images/marker.png';
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