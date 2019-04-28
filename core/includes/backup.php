<?php

class DB {

    function backup( $database = '', $tables = '*', $location = APPPATH . '/storage/backups/', $gzip = true ) {

        global $db;

        $backupfile = 'aio-'.$database.'-'.date("h_i_a_d_M_Y", time()).'.sql';

        if( $tables == '*' ) {
            $tables = [];
            $result = query( 'SHOW TABLES' );
            while($row = mysqli_fetch_row($result)) {
                $tables[] = $row[0];
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
            $row = mysqli_fetch_row(mysqli_query($db, 'SHOW CREATE TABLE `'.$table.'`'));
            $sql .= "\n\n".$row[1].";\n\n";

            // INSERT INTO
            $row = mysqli_fetch_row(mysqli_query($db, 'SELECT COUNT(*) FROM `'.$table.'`'));
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
            }

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

    function restore( $file, $location = APPPATH . '/storage/backups/' ) {
        global $db;
        try {
            $sql = '';
            $multiLineComment = false;
            $backupDir = $location;
            $backupFile = $file;
            /**
             * Gunzip file if gzipped
             */
            $backupFileIsGzipped = substr($backupFile, -3, 3) == '.gz' ? true : false;
            if ($backupFileIsGzipped) {
                if (!$backupFile = $this->gunzipBackupFile( $file, $location )) {
                    throw new Exception("ERROR: couldn't gunzip backup file " . $backupDir . '/' . $backupFile);
                }
            }
            /**
             * Read backup file line by line
             */
            $handle = fopen($backupDir . '/' . $backupFile, "r");
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
                echo json_encode([0, T('Couldn\'t open backup file ').$backupDir . '/' . $backupFile]);
            }
        } catch (Exception $e) {
            echo json_encode([0, $e->getMessage()]);
            elog( $e->getMessage() );
            return false;
        }
        if ( $backupFileIsGzipped ) {
            unlink( $backupDir . '/' . $backupFile );
        }
        return true;
    }

    protected function saveFile( &$sql, $file, $location = APPPATH . '/storage/backups/' ) {
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

    protected function gzipBackupFile($file, $location = APPPATH . '/storage/backups/',$level = 9) {
        /* if (!$this->gzipBackupFile) {
            return true;
        } */
        $source = $location . '/' . $file;
        $dest =  $source . '.gz';
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

