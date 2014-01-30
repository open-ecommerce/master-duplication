<?php
/*
 * base on Damijan Cavar code
 * developed by open-ecommerce.org
 */

require_once('install/lib/ProgressBar.class.php');
require_once('install/lib/SimpleFtp.class.php');

/**
 * Just check that the database configuration is okay
 */
class CopyToRemoteServer implements IWebApplicationInstaller_Script {

    private $error_msg = '';

    /**
     * @see IWebApplicationInstaller_Script::run()
     */
    public function run() {

        $wai = WAI::getInstance();

        if (($wai->do_validate === false) or ($wai->progresion < 1)) {
            return true;
        }

        //get credentials from the user
        $ftp_server = $wai->getRequest('ftp_server_destination');
        $ftp_user_name = $wai->getRequest('ftp_user_destination');
        $ftp_user_pass = $wai->getRequest('ftp_pass_destination');
        if (empty($ftp_server)) {
            WAI::warningMsg('Please provide a valid FTP server destination.');
            return true;
        }
        if (empty($ftp_user_name)) {
            WAI::warningMsg('Please provide a valid FTP user.');
            return true;
        }
        if (empty($ftp_user_pass)) {
            WAI::warningMsg('Please provide a valid FTP password.');
            return true;
        }

        $p = new ProgressBar();
        echo '<div class="oe-progress-bar" style="width: 90%;">';
        $p->render();
        echo '</div>';
        echo '<div class="alert alert-info">The application files will be copy and compress, will take some time...</div>';
        $p->setProgressBarProgress(5);

        $master_files = "/var/www/testing/duplicator/" . $wai->application;
        $this->replaceConfigurationFiles($master_files, $wai->application);

        //compress folder into file
        $file1 = "1-application-" . date('Y-m-d-H-i-s') . ".tar.gz";
        //unlink($file1); i am using new files everytime with the seconds
        $phar = new PharData($file1);
        $phar->buildFromDirectory($master_files);

        //get the undated files to the tar
        $phar->buildFromDirectory(toupgrade);
        unset($phar);

        echo '<div class="alert alert-info">The file will be uploaded using an FTP conection.</div>';
        $p->setProgressBarProgress(0);

        //upload files with ftp
        $options = array();
        $options['server'] = $ftp_server;
        $options['port'] = 21; //22 is sftp
        $options['user'] = $ftp_user_name;
        $options['pass'] = $ftp_user_pass;

        //connect to server
        $ftp = new SimpleFtp($options);
        $ftp->connect();
        //upload file
        if ($ftp->put($file1)) {
            echo '<div class="alert alert-success">The files have being uploaded.</div>';
            $p->setProgressBarProgress(0);
            WAI::warningMsg('The file:' . $file1 . ' have being uploaded to:' . $ftp_server . ' Please uncompress the file with in the reseler');
        } else {
            echo '<div class="alert alert-danger">It was a problem with the ftp, but you can upload the file:' . $file1 . 'using an FTP program</div>';
            $p->setProgressBarProgress(0);
            WAI::warningMsg('The file:' . $file1 . ' have being uploaded to:' . $ftp_server . ' Please uncompress the file with in the reseler');
        }
        $wai->progresion = 2;
        //end session
        $ftp->disconnect();
        return true;
    }

    /**
     * @see IWebApplicationInstaller_Script::getErrorMsg()
     */
    public function getErrorMsg() {
        return $this->error_msg;
    }

    /**
     * @see IWebApplicationInstaller_Script::getErrorMsg()
     */
    function replaceConfigurationFiles($master_files, $application) {

        $wai = WAI::getInstance();

        //clean the content of the upgrade folder to start clean
        $this->rrmdir(toupgrade);

        switch ($application) {
            case ('joomla') :
                copy($master_files . '/configuration.php', 'toupgrade/configuration.php');
                $this->replace_file('toupgrade/configuration.php', $wai->master_db_server, $wai->getRequest(database_server));
                $this->replace_file('toupgrade/configuration.php', $wai->master_db_name, $wai->getRequest(database_username));
                $this->replace_file('toupgrade/configuration.php', $wai->master_db_pass, $wai->getRequest(database_password));
                break;
            case ('joomla25') :
                copy($master_files . '/configuration.php', 'toupgrade/configuration.php');
                $this->replace_file('toupgrade/configuration.php', $wai->master_db_server, $wai->getRequest(database_server));
                $this->replace_file('toupgrade/configuration.php', $wai->master_db_name, $wai->getRequest(database_username));
                $this->replace_file('toupgrade/configuration.php', $wai->master_db_pass, $wai->getRequest(database_password));
                break;
            case ('wp') :
                copy($master_files . '/configuration.php', 'toupgrade/configuration.php');
                $this->replace_file('toupgrade/configuration.php', $wai->master_db_server, $wai->getRequest(database_server));
                $this->replace_file('toupgrade/configuration.php', $wai->master_db_name, $wai->getRequest(database_username));
                $this->replace_file('toupgrade/configuration.php', $wai->master_db_pass, $wai->getRequest(database_password));
                break;
            case ('magento') :
                copy($master_files . '/configuration.php', 'toupgrade/configuration.php');
                $this->replace_file('toupgrade/configuration.php', $wai->master_db_server, $wai->getRequest(database_server));
                $this->replace_file('toupgrade/configuration.php', $wai->master_db_name, $wai->getRequest(database_username));
                $this->replace_file('toupgrade/configuration.php', $wai->master_db_pass, $wai->getRequest(database_password));
                break;
            case ('drupal') :
                copy($master_files . '/configuration.php', 'toupgrade/configuration.php');
                $this->replace_file('toupgrade/configuration.php', $wai->master_db_server, $wai->getRequest(database_server));
                $this->replace_file('toupgrade/configuration.php', $wai->master_db_name, $wai->getRequest(database_username));
                $this->replace_file('toupgrade/configuration.php', $wai->master_db_pass, $wai->getRequest(database_password));
                break;
            case ('fundation') :
                copy($master_files . '/configuration.php', 'toupgrade/configuration.php');
                $this->replace_file('toupgrade/configuration.php', $wai->master_db_server, $wai->getRequest(database_server));
                $this->replace_file('toupgrade/configuration.php', $wai->master_db_name, $wai->getRequest(database_username));
                $this->replace_file('toupgrade/configuration.php', $wai->master_db_pass, $wai->getRequest(database_password));
                break;
            case ('html') :
                copy($master_files . '/configuration.php', 'toupgrade/configuration.php');
                $this->replace_file('toupgrade/configuration.php', $wai->master_db_server, $wai->getRequest(database_server));
                $this->replace_file('toupgrade/configuration.php', $wai->master_db_name, $wai->getRequest(database_username));
                $this->replace_file('toupgrade/configuration.php', $wai->master_db_pass, $wai->getRequest(database_password));
                break;
            case ('onepage') :
                copy($master_files . '/configuration.php', 'toupgrade/configuration.php');
                $this->replace_file('toupgrade/configuration.php', $wai->master_db_server, $wai->getRequest(database_server));
                $this->replace_file('toupgrade/configuration.php', $wai->master_db_name, $wai->getRequest(database_username));
                $this->replace_file('toupgrade/configuration.php', $wai->master_db_pass, $wai->getRequest(database_password));
                break;
        }
    }

    function replace_file($path, $string, $replace) {
        set_time_limit(0);

        if (is_file($path) === true) {
            $file = fopen($path, 'r');
            $temp = tempnam('./', 'tmp');

            if (is_resource($file) === true) {
                while (feof($file) === false) {
                    file_put_contents($temp, str_replace($string, $replace, fgets($file)), FILE_APPEND);
                }

                fclose($file);
            }

            unlink($path);
        }

        return rename($temp, $path);
    }

    function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir")
                        rrmdir($dir . "/" . $object);
                    else
                        unlink($dir . "/" . $object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
        mkdir($dir, 0777, true);
    }

}