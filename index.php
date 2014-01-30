<?php
require_once('install/lib/WebApplicationInstaller.class.php');
WAI::setLanguage('en_GB');
WAI::setStyle('install/custom/css/style.css');
WAI::setTitle('Open-ecommerce Website Creator');
WAI::setLogo('install/custom/images/open-ecommerce.gif');
WAI::text('== Welcome to the open-ecomerce Masters duplicator ==');
WAI::text('=== 1. Select the application you want to install ===');
WAI::dropdownField(
	'application',
	'Application to install:',
	array(
            'Please select an application',
            'joomla' ,
            'joomla25',
            'wp',
            'magento',
            'drupal',
            'fundation',
            'html',
            'onepage'
             ),
	'Please select an application',
	''
);
WAI::validateCustom('ValidateApplication'); //$progresion = 0
WAI::validateCustom('CopyToRemoteServer'); //$progresion = 1
WAI::text('=== 2. Provide the FTP credentials for the server destination ===');
WAI::textField('url_destination','URL destination','');
WAI::textField('ftp_server_destination','FTP server destination','');
WAI::textField('ftp_user_destination','FTP server destination user','');
WAI::textField('ftp_pass_destination','FTP server destination password','');
WAI::text('=== 3. Provide the credentials for the MySQL database destination ===');
WAI::requestDatabaseSettings(WAI::DB_MySQL, array('min_version' => 4)); //$progresion = 2
WAI::dispatch();