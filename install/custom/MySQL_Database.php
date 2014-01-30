<?php
/*
 * base on Damijan Cavar code
 * developed by open-ecommerce.org
 */


require_once('install/lib/ProgressBar.class.php');
require_once('install/lib/MysqlSearchReplace.class.php');

/**
 * Just check that the database configuration is okay
 */
class MySQL_Database implements IWebApplicationInstaller_Script {

    private $error_msg = '';

    /**
     * @see IWebApplicationInstaller_Script::run()
     */
    public function run() {


        $wai = WAI::getInstance();

        if ($wai->progresion < 2) {
            return true;
        }


        if (!function_exists('mysql_connect')) {
            $this->error_msg = 'MySQL support not included in PHP.';
            return false;
        }

        $sql_file_to_export = $this->backup_tables($wai->master_db_server, $wai->master_db_name, $wai->master_db_pass, $wai->master_db_name);

        $p = new ProgressBar();
        
        //was too slow using mysql replacement insted
        //$this->replace_file($sql_file_to_export, $wai->master_url, $wai->getRequest('url_destination'));

        echo '<div class="alert alert-info">Starting remote database transfer from script this will take about 5 minutes depending on the db.</div>';            
        $p->setProgressBarProgress(0);
        
        
        $conn = @mysql_connect(
                        $wai->getRequest('database_server'), $wai->getRequest('database_username'), $wai->getRequest('database_password')
        );

        if (!$conn) {
            $this->error_msg = 'That username/password doesn\'t work';
            return false;
        }

        if (@mysql_select_db($wai->getRequest('database_database'))) {
            // Temporary variable, used to store current query
            $templine = '';
            // Read in entire file
            $lines = file($sql_file_to_export);

            //aca quede limpiar base de datos
            $database_tables = mysql_query('show tables');

            while ($row = mysql_fetch_array($database_tables)) {
                $table = trim($row[0]);
                mysql_query('DROP TABLE IF EXISTS `' . $wai->getRequest('database_database') . '`.`' . $table . '`');
            }

            
            $counter = 0;
            foreach ($lines as $line) {
                $p->setProgressBarProgress($counter * 100 / count($lines));
                // Skip it if it's a comment
                if (substr($line, 0, 2) == '--' || $line == '')
                    continue;
                // Add this line to the current segment
                $templine .= $line;
                // If it has a semicolon at the end, it's the end of the query
                if (substr(trim($line), -1, 1) == ';') {
                    // Perform the query
                    mysql_query($templine) or print('Error performing query: ' . $templine . '\': ' . mysql_error() . '<br /><br />');
                    // Reset temp variable to empty
                    $templine = '';
                }
                ++$counter;
            }
            echo '<div class="alert alert-success">Database transfer done.</div>';            
            $p->setProgressBarProgress(100);
            WAI::warningMsg('The database ' . $wai->getRequest('database_database') . ' was created.');
        } else {
            $this->error_msg = array('User \'{p1}\' doesn\'t have permissions.', array($wai->getRequest('database_username')));
            return false;
        }


        echo '<div class="alert alert-info">The sql file: ' . $sql_file_to_export . ' will be replace with the correct url: ' . $wai->getRequest('url_destination') . '</div>';               
        $p->setProgressBarProgress(0);
        
        
        // Create an instace of the class
        $mysqlSearchAndReplace = new MysqlSearchReplace($wai->getRequest('database_username'), $wai->getRequest('database_server'), $wai->getRequest('database_username'), $wai->getRequest('database_password'));
        // Simple search and replace
        $mysqlSearchAndReplace->searchAndReplace($wai->master_url, $wai->getRequest('url_destination'));        
        
        
        echo '<div class="alert alert-success">The sql file was edited and ready to upload</div>';               
        $p->setProgressBarProgress(100);
        
        return true;

        //WAI::warningMsg('terminando validacion base');
        //return true; // no error
    }

    /**
     * @see IWebApplicationInstaller_Script::getErrorMsg()
     */
    public function getErrorMsg() {
        return $this->error_msg;
    }

    /* backup the db OR just a table */

    public function backup_tables($host, $user, $pass, $name, $tables = '*') {

        $link = mysql_connect($host, $user, $pass);
        mysql_select_db($name, $link);

        //get all of the tables
        if ($tables == '*') {
            $tables = array();
            $result = mysql_query('SHOW TABLES');
            while ($row = mysql_fetch_row($result)) {
                $tables[] = $row[0];
            }
        } else {
            $tables = is_array($tables) ? $tables : explode(',', $tables);
        }
        
        $p = new ProgressBar();
        $p->setProgressBarProgress(0);
        echo '<div class="alert alert-info">Starting sql file creation from master database</div>';            

        //cycle through
        $counter=0;
        foreach ($tables as $table) {
            $p->setProgressBarProgress($counter * 100 / count($tables));
            $result = mysql_query('SELECT * FROM ' . $table);
            $num_fields = mysql_num_fields($result);
            $return.= 'DROP TABLE IF EXISTS ' . $table . ';';
            $row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE ' . $table));
            $return.= "\n\n" . $row2[1] . ";\n\n";

            for ($i = 0; $i < $num_fields; $i++) {
                while ($row = mysql_fetch_row($result)) {
                    $return.= 'INSERT INTO ' . $table . ' VALUES(';
                    for ($j = 0; $j < $num_fields; $j++) {
                        $row[$j] = addslashes($row[$j]);
                        $row[$j] = ereg_replace("\n", "\\n", $row[$j]);
                        if (isset($row[$j])) {
                            $return.= '"' . $row[$j] . '"';
                        } else {
                            $return.= '""';
                        }
                        if ($j < ($num_fields - 1)) {
                            $return.= ',';
                        }
                    }
                    $return.= ");\n";
                }
            }
            
            $return.="\n\n\n";
            ++$counter;
        }
        //save file
        $filename = 'db-backup-' . time() . '-' . (md5(implode(',', $tables))) . '.sql';
        $handle = fopen($filename, 'w+');
        fwrite($handle, $return);
        fclose($handle);

        $p->setProgressBarProgress(100);
        echo '<div class="alert alert-success">The sql file:' . $filename . ' was created.</div>';      
        return $filename;
    }

    
function replace_file($path, $string, $replace)
{
    set_time_limit(0);

    if (is_file($path) === true)
    {
        $file = fopen($path, 'r');
        $temp = tempnam('./', 'tmp');

        if (is_resource($file) === true)
        {
            while (feof($file) === false)
            {
                file_put_contents($temp, str_replace($string, $replace, fgets($file)), FILE_APPEND);
            }

            fclose($file);
        }

        unlink($path);
    }

    return rename($temp, $path);
}    
    
    
}
?>