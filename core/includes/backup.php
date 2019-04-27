<?php

class DB {

    public function backup( $database = '', $tables = '*', $location = APPPATH . '/storage/backups/' ) {

        if( $tables == '*' ) {
            $tables = [];
            $result = query( 'SHOW TABLES' );
            while($row = mysqli_fetch_row($result)) {
                $tables[] = $row[0];
            }
        } else {
            $tables = is_array($tables) ? $tables : explode(',', str_replace(' ', '', $tables));
        }



    }

    public function restore() {



    }

}

