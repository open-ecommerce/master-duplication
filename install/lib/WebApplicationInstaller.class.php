<?php

class WAI {

    const DB_MySQL = 'Database type mysql';
    const PHP5 = 'Php5';
    const GD2 = 'gd2';
    const XML = 'xml';
    const MEMORY_ALLOCATED = 'Memory allocated';
    const NOT_DEFINED_CLASSES = 'Not defined Classes';
    const FOLDER_EXISTS = 'Folder exists';
    const SERVER_ROOT = 'Server Root';
    const FILE_WRITE = 'File write';
    const FOLDER_WRITE = 'Folder write';
    const TEMPORARY_FOLDER = 'Temporary Folder';
    const SERVER_SOFTWARE = 'Server Software';
    const MOD_REWRITE = 'mod_rewrite';

    static private $instance = null;
    static private $wiki_parser = null;
    private $html_string = '';
    private $style = '';
    private $logo = '';
    private $title = '';
    private $request_data;
    public $do_validate = false;
    public $progresion = 0;
    private $lang;
    public $application;
    public $master_url;
    public $master_db_name;
    public $master_db_pass;
    public $master_db_server = 'server.com';

    public static function setAppliction() {
        $wai = self::getInstance();
        $application = $wai->getRequest('application');
        switch ($application) {
            case ('joomla') :
                $wai->master_url = 'joomla.domine.com';
                $wai->master_db_name = 'user_joomla';
                $wai->master_db_pass = 'password';
                break;
            case ('joomla25') :
                $wai->master_url = 'joomla25.domine.com';
                $wai->master_db_name = 'user_joomla25';
                $wai->master_db_pass = 'password';
                break;
            case ('wp') :
                $wai->master_url = 'wp.domine.com';
                $wai->master_db_name = 'user_wp';
                $wai->master_db_pass = 'pass';
                break;
            case ('magento') :
                $wai->master_url = 'magento.domine.com';
                $wai->master_db_name = 'user_magento';
                $wai->master_db_pass = 'pass';
                break;
            case ('drupal') :
                $wai->master_url = 'drupal.domine.com';
                $wai->master_db_name = 'user_drupal';
                $wai->master_db_pass = 'pass';
                break;
            case ('fundation') :
                $wai->master_url = 'fundation.domine.com';
                $wai->master_db_name = '';
                $wai->master_db_pass = '';
                break;
            case ('html') :
                $wai->master_url = 'html.domine.com';
                $wai->master_db_name = '';
                $wai->master_db_pass = '';
                break;
            case ('onepage') :
                $wai->master_url = 'onepage.domine.com';
                $wai->master_db_name = 'user_onepage';
                $wai->master_db_pass = 'pass';
                break;
        }
        $wai->application = $application;
        $wai->progresion = 1;
       
        
    }

    
    public static function setLanguage($lang = 'en_EN') {
        if (is_readable('install/lang/' . $lang . '.php')) {
            include('install/lang/' . $lang . '.php');
            $wai = self::getInstance();
            $wai->lang = $lang;
        }
    }

    public static function setStyle($css_file) {
        $wai = self::getInstance();
        $wai->style = $css_file;
    }

    public static function setTitle($title = 'Installer') {
        $wai = self::getInstance();
        $wai->title = $title;
    }

    public static function setLogo($logo_file) {
        $wai = self::getInstance();
        $wai->logo_file = $logo_file;
    }

    public static function setProgresion($value) {
        $wai = self::getInstance();
        $wai->progresion = $value;
    }

    public static function text($string = '') {
        $wai = self::getInstance();

        $wai->html_string .= $wai->translate($string);
    }

    public static function dropdownField($field_name, $label = '', $values = array(), $default_value = '', $field_description = '') {
        if (!is_array($values))
            return;

        $wai = self::getInstance();
        $id = $field_name . '_' . microtime();

        if ($label != '')
            $wai->html_string .= '<label for="' . $id . '">' . $wai->translate($label) . '</label>';
        $wai->html_string .= '<select name="' . $field_name . '" id="' . $id . '">';
        foreach ($values as $value) {
            $wai->html_string .= '<option value="' . $value . '"' . ($value == $wai->getRequest($field_name, $default_value) ? ' selected="selected"' : '') . '>' . $wai->translate($value) . '</option>';
        }
        $wai->html_string .= '</select>';
        if ($field_description != '')
            $wai->html_string .= '<span class="description">' . $wai->translate($field_description) . '</label>';
    }

    public static function textField($field_name, $label = '', $default_value = '', $field_description = '') {
        $wai = self::getInstance();
        $id = $field_name . '_' . microtime();

        if ($label != '')
            $wai->html_string .= '<label for="' . $id . '">' . $wai->translate($label) . '</label>';
        $wai->html_string .= '<input type="text" name="' . $field_name . '" id="' . $id . '" value="' . $wai->getRequest($field_name, $default_value) . '" />';
        if ($field_description != '')
            $wai->html_string .= '<span class="description">' . $wai->translate($field_description) . '</label>';
    }

    public static function textareaField($field_name, $label = '', $default_value = '', $field_description = '') {
        $wai = self::getInstance();
        $id = $field_name . '_' . microtime();

        if ($label != '')
            $wai->html_string .= '<label for="' . $id . '">' . $wai->translate($label) . '</label>';
        $wai->html_string .= '<textarea name="' . $field_name . '" id="' . $id . '">' . $wai->getRequest($field_name, $default_value) . '</textarea>';
        if ($field_description != '')
            $wai->html_string .= '<span class="description">' . $wai->translate($field_description) . '</label>';
    }

    public static function checkboxField($field_name, $label = '', $value = '', $checked = false, $field_description = '') {
        $wai = self::getInstance();
        $id = $field_name . '_' . microtime();

        if ($label != '')
            $wai->html_string .= '<label for="' . $id . '">' . $wai->translate($label) . '</label>';
        $wai->html_string .= '<input type="checkbox" name="' . $field_name . '" id="' . $id . '" value="' . $value . '"' . ($wai->getRequest($field_name, $checked) ? ' checked="checked"' : '') . ' />';
        if ($field_description != '')
            $wai->html_string .= '<span class="description">' . $wai->translate($field_description) . '</label>';
    }
    
    public static function validateApplication() {
        $wai = self::getInstance();
        $application = $wai->getRequest('application');
        if ((empty($application)) or ($application == 'Please select an application' )) {
            WAI::warningMsg('Please select an application');
            return true;
        }
    }
    

    public static function validateCustom($custom_class_name) {
        return self::validate($custom_class_name, 'custom');
    }

    public static function validate($class_name, $type = 'validate') {
        if (!class_exists($class_name, true)) {
            if (!is_readable('install/' . $type . '/' . $class_name . '.php')) {
                throw new Exception('ClassFile "install/' . $type . '/' . $class_name . '.php" Not Found');
            }

            include('install/' . $type . '/' . $class_name . '.php');
        }

        if (!class_exists($class_name, true)) {
            throw new Exception('Class "' . $class_name . '" Not Found');
        }
        $cls = new $class_name();

        if ($cls instanceof IWebApplicationInstaller_Script) {
            if (!$cls->run()) {
                self::errorMsg($cls->getErrorMsg());
            }
        } else {
            throw new Exception('Class "' . $custom_class_name . '" must be implementing IWebApplicationInstaller_Script');
        }
    }

    public static function requestDatabaseSettings($type, $parameter = null) {
        $wai = self::getInstance();

        switch ($type) {
            case self::DB_MySQL:

                //self::text('=== MySQL Database ===');

                if ($wai->do_validate) {
                    $wai->validateCustom('MySQL_Database');
                }
                WAI::textField('database_server', 'MySQL server:', 'lab.dns-systems.net');
                WAI::textField('database_username', 'MySQL username:', 'lab_joomla');
                WAI::textField('database_password', 'MySQL password:', 'fnzrZThXDIb6eMo2');
                WAI::textField('database_database', 'MySQL database:', 'lab_joomla');
                break;
        }
    }

    public static function requirePhpConfiguration($type, $parameter = null) {
        
    }

    public static function requirePermission($type, $parameter = null) {
        
    }

    public static function requireWebserverConfiguration($type, $parameter = null) {
        
    }

    public static function errorMsg($string) {
        $wai = self::getInstance();
        $wai->has_error = true;

        $wai->html_string .= '<p class="error alert alert-danger">' . $wai->translate($string) . '</p>';
    }

    public static function warningMsg($string) {
        $wai = self::getInstance();

        $wai->html_string .= '<p class="warning alert alert-warning">' . $wai->translate($string) . '</p>';
    }

    private function translate($string, $parse = true) {
        $wai = self::getInstance();

        $params = array();
        if (is_array($string)) {
            if (sizeof($string) > 1) {
                $params = $string[1];
            }
            $string = $string[0];
        }

        if (is_array($wai->lang) && isset($wai->lang[$string])) {
            $string = $wai->lang[$string];
        }

        if ($parse) {
            $parser = self::getWikiParser();
            $string = $parser->parse($string);
        }

        foreach ($params as $i => $param) {
            $string = str_replace('{p' . ($i + 1) . '}', $param, $string);
        }

        return $string;
    }

    public function getRequest($name, $default = null) {
        if ($this->request_data === null) {
            $this->request_data = array_merge($_POST, $_GET, $_COOKIE);
        }
        return isset($this->request_data[$name]) ? $this->request_data[$name] : $default;
    }

    private function getHtmlHeader() {
        $wai = self::getInstance();

        $header = '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
		<head>
		<title>' . $wai->translate($wai->title, false) . '</title>' .
                '<meta name="viewport" content="width=device-width, initial-scale=1.0">
                <!-- Bootstrap -->
                <link href="css/bootstrap.min.css" rel="stylesheet">
                <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
                <!--[if lt IE 9]>
                  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
                  <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
                <![endif]-->'
        ;

        if ($wai->style != '') {
            $header .= '<link rel="stylesheet" type="text/css" href="' . $wai->style . '" />';
        }

        $header .= '</head><body>';
        $header .= '<div class="container">';

        if ($wai->logo_file != '') {
            $header .= '<img src="' . $wai->logo_file . '" />';
        }


        $header .= '<form name="install_fom" id="install_fom" method="post" action="' . $_SERVER['PHP_SELF'] . '" enctype="application/x-www-form-urlencoded" accept-charset="UTF-8">';

        return $header;
    }

    private function getHtmlFooter() {
        return '<br><br><input type="submit" name="submit" value="' . self::translate('Install') . '" class="action btn btn-lg btn-success" />
                </div>
                <script src="https://code.jquery.com/jquery.js"></script>
                <!-- Include all compiled plugins (below), or include individual files as needed -->
                <script src="js/bootstrap.min.js"></script></body></html>';
    }

    public static function dispatch() {
        $wai = self::getInstance();

        echo $wai->getHtmlHeader() . $wai->html_string . $wai->getHtmlFooter();
    }

    public static function checkRequest() {
        if (isset($_POST) && sizeof($_POST) > 0) {
            $wai = self::getInstance();
            $wai->do_validate = true;
        }
    }

    public static function getWikiParser() {
        if (self::$wiki_parser === null) {
            require_once('install/lib/WikiParser.class.php');

            self::$wiki_parser = new WikiParser();
        }
        return self::$wiki_parser;
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct() {
        
    }

    private function __clone() {
        
    }

}

interface IWebApplicationInstaller_Script {

    public function run();

    public function getErrorMsg();
}

WAI::checkRequest();