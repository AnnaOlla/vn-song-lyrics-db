# vn-song-lyrics-db
## Description
A small project dedicated for lyrics of songs that sound in games of genre "visual novel".

The website is available at [vn-song-lyrics-db.ru](https://vn-song-lyrics-db.ru).

## Requirements
General modules that were tested:
* PHP 8.4.11-8.5.8
* Apache 2.4 or nginx 1.30.1
* MySQL 8.0.44 or MariaDB 10.11.18

Required PHP extensions in php.ini:
* gd
* mbstring
* exif
* pdo\_mysql

Required PHP settings in php.ini:
* upload\_max\_filesize = 512K

The project does not use external libraries in case of possible malfunctions caused by blockages of websites or impossiblity to maintain them for the same reason. Also, I wanted to do all on my own to get more experience.

## Setup
Automated setup is not available. Follow the steps:
* Install the selected server, database and PHP according to instructions provided with them.
* Apply changes in php.ini as mentioned above.
* Download files from this page.
* Unpack the archive into a folder named *htdocs*.
* Run the script `db-creation-code.sql` to initialize the database.
* Run the server on this folder. Example for Apache 2.4:
    * Find and edit the following lines in httpd.conf:
        * `Define SRVROOT "{absolute_path_to_apache_folder}"`
        * `LoadModule php_module "{absolute_path_to_php_folder}/php8apache2_4.dll"`
        * `PHPIniDir "{absolute_path_to_php_folder}"`
        * `DocumentRoot "{absolute_path_to_htdocs_folder}"`
        * `ErrorLog "{absolute_path_to_any_folder_you_want_to_use}/error.log"`
        * `CustomLog "{absolute_path_to_any_folder_you_want_to_use}/access.log" common`
    * Run the server with the following command:
        * `"{absolute_path_to_apache_folder}\bin\httpd.exe" -k runservice`

## Project Structure
By initial design, the website was supposed to use MVC. However, the Controller is used rather as the validator of incoming requests and included data. The Model uses only clean data.

All MVC parts are divided into roles. Each higher role inherits all the functional of the lower.

Main parts:
* **assets**
    * all images uploaded to the website or used by it statically
* **controllers**
    * methods that accept requests and bind models and views together
* **core**
    * router for the requested path, boot to include all required files, config for connecting to the database
* **css**
    * **core**
        * general settings used across all pages
    * **custom-inputs**
        * design of input fields across all pages
    * **moderation**
        * design of admin elements and pages
    * **shared**
        * elements shared by several pages
    * other files are used by separate pages
* **include**
    * all functions that does not belong to the MVC pattern
* **js**
    * **core**
        * general settings used across all pages
    * **custom-inputs**
        * design of input fields across all pages
    * **moderation**
        * design of admin elements and pages
    * **shared**
        * elements shared by several pages
    * other files are used by separate pages
* **localization**
    * strings used by views to show localized pages
* **models**
    * methods that process requests and return data
* **views**
    * methods that return the result of the request to the user
* **index.php**
    * the entry point of the website

General pipeline of the code execution:

`index.php` -> `core/router.php` -> `controllers/*-controller.php` -> `models/*-model.php` -> `controllers/*-controller.php`-> `views/*-view.php` (where \* is the user role)

## User Roles
At the moment, following roles are available:
* visitor: any unregistered user, granted automatically
* user: registered user, granted after signing-up or logging-in
* violator: read-only registered user, granted manually
* administrator: only the owner of the website, granted manually

## Missing Files
At the moment, following files are not included due to security reasons:
* .htaccess (all requests that do not point to files, should be sent to index.php)
* .administering/* (see router.php for details)
* SQL-file for creating roles in database
* core/boot.php should be filled with database info from the list item above
* include/cryptography.php does not implement methods
