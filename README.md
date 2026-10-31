# vn-song-lyrics-db
## Description
A small project dedicated for lyrics of songs that sound in games of genre "visual novel".

The website is available at [vn-song-lyrics-db.ru](https://vn-song-lyrics-db.ru).

## Requirements
General modules that were tested:
* PHP 8.4.11-8.5.12
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
* Create users in the database for "visitor", "user" and "administrator" roles.
  * Grant SELECT, UPDATE, INSERT and DELETE permissions for "user" and "administrator"
  * Grant SELECT, UPDATE, INSERT for "visitor".
  * Granting permissions only for certain columns also works when done according to the logic in the model files.

## Project Structure
By initial design, the website was supposed to use MVC. However, the Controller is used rather as the validator of incoming requests and included data. The Model uses only clean data.

All MVC parts are divided into roles. Each higher role inherits all the functional of the lower.

Following folders and files must exist:
* **assets**
  * **album-covers**
    * Empty by default.
  * **artist-photos**
    * Empty by default.
  * **character-images**
    * Empty by default.
  * **game-logos**
    * Empty by default.
  * **static-images**
    * Required files are included in this repository.
* **blockings**
  * **.blocked-ips.txt**
    * Empty by default. Filled by Authorizer in the process of running the service or by the user at their discretion.
  * **.blocked-requests.txt**
    * Empty by default. Filled by the user at their discretion.
  * **.blocked-user-agents.txt**
    * Empty by default. Filled by the user at their discretion.
* **controllers**
  * Methods that accept requests and bind models and views together.
* **core**
  * Main files that manage request recipience
* **css**
  * **core**
    * General settings used across all pages
  * **custom-inputs**
    * Design of input fields across all pages
  * **moderation**
    * Design of admin elements and pages
  * **shared**
    * Elements shared by several pages
  * Other files are used by separate pages
* **fonts**
  * Fonts used in the design
* **include**
    * All functions that does not belong to the classic MVC pattern
* **js**
  * **core**
    * General settings used across all pages
  * **custom-inputs**
    * Design of input fields across all pages
  * **moderation**
    * Design of admin elements and pages
  * **shared**
    * Elements shared by several pages
  * Other files are used by separate pages
* **localization**
  * Strings used by views to show localized pages
* **logs**
  * **access-logs**
    * Access logs are stored in this folder
  * **error-logs**
    * Error logs are stored in this folder
* **models**
  * Methods that process requests and return data
* **views**
  * Methods that return the result of the request to the user
* **index.php**
  * The entry point of the website
* **.htaccess**
  * Must route all non-file requests to `index.php`
* **.env**
  * Must have data corresponding to `configuration.php`
* Other files like robots.txt and logos as images.

General pipeline of the code execution:

`index.php` -> `core/router.php` -> `controllers/*-controller.php` -> `models/*-model.php` -> `controllers/*-controller.php`-> `views/*-view.php` (where \* is the user role)

## User Roles
At the moment, following roles are available:
* visitor: any unregistered user, granted automatically
* user: registered user, granted after signing-up or logging-in
* administrator: only the owner of the website, granted manually
