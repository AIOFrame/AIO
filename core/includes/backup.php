<?php

class BACKUP {

    // TODO: Update the backup script
    /**
     * Backups up database
     * @param string $database Database name
     * @param string|array $tables Tables to back up 'contacts,users' or ['contacts','users']
     * @param string $location Location to store the backup (Default is storage/backups)
     * @param bool $gzip Zip the back
     */
    function backup( string $database = '', string|array $tables = '*', string $location = APPPATH . '/storage/backups/', bool $gzip = true ) {

        global $db;

        $backupfile = 'aio-'.$database.'-'.date("h_i_a_d_M_Y", time()).'.sql';

        if( $tables == '*' ) {
            $tables = [];
            $results = $db->query( 'SHOW TABLES' );
            //skel( $results );
            if( !empty( $results ) ) {
                foreach( $results as $r ){
                    $tables[] = $r[0];
                }
            }
        } else {
            $tables = is_array($tables) ? $tables : explode(',', str_replace(' ', '', $tables));
        }

        $sql = '';

        if( $database !== '' ) {
            $sql .= 'CREATE DATABASE IF NOT EXISTS `' . $database . "`;\n\n";
            $sql .= 'USE `' . $database . "`;\n\n";
            $sql .= "SET foreign_key_checks = 0;\n\n";
        }

        //if ($this->disableForeignKeyChecks === true) {
        //}

        foreach($tables as $table) {

            //$this->obfPrint("Backing up `".$table."` table...".str_repeat('.', 50-strlen($table)), 0, 0);

            // CREATE TABLE
            $sql .= 'DROP TABLE IF EXISTS `'.$table.'`;';
            $row = $db->query( $db->prepare( 'SHOW CREATE TABLE `'.$table.'`') );
            skel( $row );
            $sql .= "\n\n".$row[1].";\n\n";

            // INSERT INTO
            /* $select_query = $db->query( 'SELECT COUNT(*) FROM `'.$table.'`' );
            $row =
            $row = mysqli_fetch_row(mysqli_query($db, ));
            $numRows = $row[0];

            // Split table in batches in order to not exhaust system memory
            $numBatches = intval($numRows / 1000) + 1; // Number of while-loop calls to perform
            for ($b = 1; $b <= $numBatches; $b++) {

                $query = 'SELECT * FROM `' . $table . '` LIMIT ' . ($b * 1000 - 1000) . ',' . 1000;
                $result = mysqli_query($db, $query);
                $realBatchSize = mysqli_num_rows ($result); // Last batch size can be different from 1000
                $numFields = mysqli_num_fields($result);
                if ($realBatchSize !== 0) {
                    $sql .= 'INSERT INTO `'.$table.'` VALUES ';
                    for ($i = 0; $i < $numFields; $i++) {
                        $rowCount = 1;
                        while($row = mysqli_fetch_row($result)) {
                            $sql.='(';
                            for($j=0; $j<$numFields; $j++) {
                                if (isset($row[$j])) {
                                    $row[$j] = addslashes($row[$j]);
                                    $row[$j] = str_replace("\n","\\n",$row[$j]);
                                    $row[$j] = str_replace("\r","\\r",$row[$j]);
                                    $row[$j] = str_replace("\f","\\f",$row[$j]);
                                    $row[$j] = str_replace("\t","\\t",$row[$j]);
                                    $row[$j] = str_replace("\v","\\v",$row[$j]);
                                    $row[$j] = str_replace("\a","\\a",$row[$j]);
                                    $row[$j] = str_replace("\b","\\b",$row[$j]);
                                    if ($row[$j] == 'true' or $row[$j] == 'false' or preg_match('/^-?[0-9]+$/', $row[$j]) or $row[$j] == 'NULL' or $row[$j] == 'null') {
                                        $sql .= $row[$j];
                                    } else {
                                        $sql .= '"'.$row[$j].'"' ;
                                    }
                                } else {
                                    $sql.= 'NULL';
                                }

                                if ($j < ($numFields-1)) {
                                    $sql .= ',';
                                }
                            }

                            if ($rowCount == $realBatchSize) {
                                $rowCount = 0;
                                $sql.= ");\n"; //close the insert statement
                            } else {
                                $sql.= "),\n"; //close the row
                            }

                            $rowCount++;
                        }
                    }

                    $this->saveFile( $sql, $backupfile, $location );
                    $sql = '';
                }
            } */

            $sql.="\n\n";
            //$this->obfPrint('OK');
        }
        $sql .= "SET foreign_key_checks = 1;\n";
        $this->saveFile( $sql, $backupfile, $location );

        if ( $gzip ) {
            $gfile = $this->gzipBackupFile( $backupfile, $location );
            if( $gfile ) {
                
                echo json_encode([1,T('Backup file successfully saved to ') . $gfile ]);
            
            } else {
                
                echo json_encode([1,T('Backup file not generated, please try again or contact developers!')]);
            
            }
        } else {
            echo json_encode([1,T('Backup file successfully saved to ') . $backupfile . '/' . $location]);
            //$this->obfPrint( . .'/'.$location, 1, 1);
        }

    }

    // TODO: Update the export script
    /**
     * Restores backed up file to database
     * @param string $file_path Path to the backed up file
     * @return bool
     */
    function restore( string $file_path ): bool {
        global $db;
        try {
            $sql = '';
            $multiLineComment = false;
            /**
             * Gunzip file if gzipped
             */
            $backupFileIsGzipped = substr( $file_path, -3, 3 ) == '.gz' ? true : false;
            if ( $backupFileIsGzipped ) {
                if (!$backupFile = $this->gunzipBackupFile( $file_path )) {
                    throw new Exception("ERROR: couldn't gunzip backup file " . $file_path );
                }
            }
            /**
             * Read backup file line by line
             */
            $handle = fopen( $file_path, 'r' );
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    $line = ltrim(rtrim($line));
                    if (strlen($line) > 1) { // avoid blank lines
                        $lineIsComment = false;
                        if (preg_match('/^\/\*/', $line)) {
                            $multiLineComment = true;
                            $lineIsComment = true;
                        }
                        if ($multiLineComment or preg_match('/^\/\//', $line)) {
                            $lineIsComment = true;
                        }
                        if (!$lineIsComment) {
                            $sql .= $line;
                            if (preg_match('/;$/', $line)) {
                                // execute query
                                if(mysqli_query( $db, $sql)) {
                                    if (preg_match('/^CREATE TABLE `([^`]+)`/i', $sql, $tableName)) {
                                        //$this->obfPrint("Table succesfully created: `" . $tableName[1] . "`");
                                    }
                                    $sql = '';
                                } else {
                                    throw new Exception("ERROR: SQL execution error: " . mysqli_error( $db ));
                                }
                            }
                        } else if (preg_match('/\*\/$/', $line)) {
                            $multiLineComment = false;
                        }
                    }
                }
                fclose($handle);
                echo json_encode([1, T('Database restored successfully!')]);
            } else {
                echo json_encode([0, T('Couldn\'t open backup file '). $file_path ]);
            }
        } catch (Exception $e) {
            echo json_encode([0, $e->getMessage()]);
            elog( $e->getMessage() );
            return false;
        }
        if ( $backupFileIsGzipped ) {
            unlink( $file_path );
        }
        return true;
    }

    /**
     * Renders a visual html to manage backups
     * @param string $backup_path Path where current backups exist
     * @return void
     */
    function manage( string $backup_path = APPPATH . 'storage/backups/' ): void {
        $f = new FORM();
        global $ui_params;
        $backup_path = !empty( $ui_params ) && isset( $ui_params['location'] ) ? $ui_params['location'] : ( !empty( $backup_path ) ? $backup_path . '/*' : APPPATH . 'storage/backups/*' );
        get_style('aio/art/cards,aio/art/buttons');
        get_script('aio/backup');
        echo '<div class="aio_backups">';
        $backups = glob( $backup_path );
        if( isset( $_POST['initiate'] ) ) {
            $this->backup();
        }
        if( !empty( $backups ) ) {
            foreach( $backups as $bk ) {
                $res = T('RESTORE');
                $del = T('DELETE');

                $fa = explode( '/', $bk );
                $fn = $fa[count($fa)-1];

                $na = explode( '-', $fn );
                $t = $na[count($na)-1];

                $t = str_replace( '.sql', '', str_replace( '.sql.gz', '', $t ) );
                $ed = DateTime::createFromFormat( 'h_i_a_d_M_Y', $t )->format('h:i a d M, Y'); ?>

                <div class="card">
                    <div class="b">
                        <button class="res"><?php echo $res; ?></button>
                        <button class="del"><?php echo $del; ?></button>
                    </div>
                    <div class="l"><?php echo $fn; ?></div>
                    <div class="ft">
                        <div><?php echo 'Location - ' . $bk; ?></div>
                        <div class="dt"><?php echo 'Date - ' . $ed; ?></div>
                    </div>
                </div>
            <?php }
        } else {
            no_content('You do not have any data backed up yet!','Try to add initiate new backup by clicking the button in the bottom','no_backup');
        }
        echo '<form method="post" class="actions float"><button name="initiate" class="m0">'.T('Initiate New Backup').'</button></form></div>';
    }

    protected function saveFile( $sql, $file, $location = APPPATH . '/storage/backups/' ) {
        if (!$sql) return false;
        try {
            if (!file_exists( $location . '' )) {
                mkdir( APPPATH . '/storage/backups/', 0777, true);
            }
            file_put_contents($location.'/'.$file, $sql, FILE_APPEND | LOCK_EX);
        } catch (Exception $e) {
            print_r($e->getMessage());
            return false;
        }
        return true;
    }

    protected function gzipBackupFile( string $source, $level = 9) {
        /* if (!$this->gzipBackupFile) {
            return true;
        } */
        $dest =  str_contains( $source, '.gz' ) ? $source : $source . '.gz';
        //$this->obfPrint('Gzipping backup file to ' . $dest . '... ', 1, 0);
        $mode = 'wb' . $level;
        if ($fpOut = gzopen($dest, $mode)) {
            if ($fpIn = fopen($source,'rb')) {
                while (!feof($fpIn)) {
                    gzwrite($fpOut, fread($fpIn, 1024 * 256));
                }
                fclose($fpIn);
            } else {
                return false;
            }
            gzclose($fpOut);
            if(!unlink($source)) {
                return false;
            }
        } else {
            return false;
        }

        //$this->obfPrint('OK');
        return $dest;
    }

    protected function gunzipBackupFile( $file, $location = APPPATH . '/storage/backups/' ) {
        // Raising this value may increase performance
        $bufferSize = 4096; // read 4kb at a time
        $error = false;
        $source = $location . '/' . $file;
        $dest = $location . '/' . date("Ymd_His", time()) . '_' . substr( $file, 0, -3);
        //$this->obfPrint('Gunzipping backup file ' . $source . '... ', 1, 1);
        // Remove $dest file if exists
        if (file_exists($dest)) {
            if (!unlink($dest)) {
                return false;
            }
        }

        // Open gzipped and destination files in binary mode
        if (!$srcFile = gzopen( $location . '/' . $file, 'rb')) {
            return false;
        }
        if (!$dstFile = fopen($dest, 'wb')) {
            return false;
        }
        while (!gzeof($srcFile)) {
            // Read buffer-size bytes
            // Both fwrite and gzread are binary-safe
            if(!fwrite($dstFile, gzread($srcFile, $bufferSize))) {
                return false;
            }
        }
        fclose($dstFile);
        gzclose($srcFile);
        // Return backup filename excluding backup directory
        return str_replace( $location . '/', '', $dest);
    }

}

