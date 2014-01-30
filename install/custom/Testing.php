<?php

/**
 * Just check that the database configuration is okay
 */
class Testing implements IWebApplicationInstaller_Script {

    private $error_msg = '';

    /**
     * @see IWebApplicationInstaller_Script::run()
     */
    public function run() {
        $wai = WAI::getInstance();
        $files = "to-extract-" . date('Y-m-d') . ".tar.gz";
        //passthru('tar -zcvf  ' . $files . ' tocopy');


        $connection = ssh2_connect('example.com', 22);
        ssh2_auth_password($connection, 'username', 'password');

        $stream = ssh2_exec($connection, 'unzip /path/to/file.zip');        
        
        
        
    }

    /**
     * @see IWebApplicationInstaller_Script::getErrorMsg()
     */
    public function getErrorMsg() {
        return $this->error_msg;
    }

}

?>