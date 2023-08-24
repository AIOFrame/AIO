<div class="languages scroll">
    <input type="search" class="filter_lang" placeholder="<?php E('Search'); ?>">
    <div class="list">
        <?php
        $ls = ['en'=>'English','ar'=>'العربية (Arabic)','zh'=>'中文 (Chinese Simplified)','fr'=>'Français (French)','hi'=>'हिंदी (Hindi)','in'=>'Bahasa Indonesian','ja'=>'日本語 (Japanese)','pr'=>'Português (Portuguese)','ru'=>'русский (Russian)','es'=>'Español (Spanish)'];
        if( !empty( $ls ) )
            foreach( $ls as $k => $v )
                echo '<div data-lang="' . $k . '">' . $v . '</div>';
        ?>
    </div>
</div>