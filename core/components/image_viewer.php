<?php

class IMAGE_VIEW {

    function view_image() {
        ?>
        <script>
            function view_image( url ) {
                var im = '<div class="modal l"><div class="close"></div><img src="'+url+'"></div>';
                $(body).append(im);
            }
        </script>
        <?php
    }
}