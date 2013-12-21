master-duplication
==================
PHP interface to duplicate web applications with MySQL DBs from server with limited privileges to another server.

Status:
I am cleaning the application to make it easer to reed.


Appliction Features
One single page interface
Multiple application selection
Compress folder into file
Replace configuration files and add to single tar file
Upload file with FTP to remote server
Transfer remote mySql db into the production server
Replace url on destination db


Why you might need it
If you are an open-source application integrator as we are in open-ecommerce.org you will lot of your time instaling and configuring applications like Joomla, Magento, WP etc. over and over again for your customers.

Our aproach to reduce deploiment times is to have a repository that we call it "master" with the latest tested versions of the web applications we use configured with the most used plugins, modules and themed with our wireframe template.

We was using bash scripts to duplicate this envirouments but it was a bit a mess to change credentials and other things every time.  Also we not always have access to the ssh console and our aim was build the site in our first meeting with our customer.

This application do all that but from a web interface.

License
We normaly use some GPL licence but difernt classes included in this application uses different licences and we keep all the comments with copiright in each file used.

Credits
We used as inspiration and main structure the application the André Fiedler
https://github.com/SunboX/Php-Web-Application-Installer

For the FTP functionality the simple-ftp class writen by Damijan Cavar
https://github.com/damijanc/simple-ftp

For the progress bar the ProgressBar class by Slawa Pidgorny
http://spidgorny.blogspot.com/2012/02/progress-bar-for-lengthy-php-process.html

For the replacement in the remote MySql db the class writen by Jon Segador
https://github.com/jonseg/mysql-search-replace

and boostrap for add some style











