<?
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 5/12/2016
 * Time: 12:35 PM
 */
if(count(get_included_files()) ==1) exit("You don't have the permission to access this file.");

/**
 * Declare all the database variables
 */
define( "DBURL" , "localhost"); // Set the database url (ex : localhost || 127.0.01 ).
define( "DBUSER" , "root"); // Set the database user.
define( "DBPASS" , "199601997masis@"); // Set the database password to access it.
define( "DBNAME" , "als"); // Set the database name used to store all the data.
define( "DBPORT" , "3306"); // Set the sql port that is used to connect to the database ( default : 3306 ).

/**
 * Declare all sql tables that's been used in the script
 */
    define("TBL_USERS", "users");
    define("TBL_USERS_ID" , "id");
    define("TBL_USERS_USERNAME" , "username");
    define("TBL_USERS_FNAME" , "firstName");
    define("TBL_USERS_LNAME" , "lastName");
    define("TBL_USERS_EMAIL" , "email");
    define("TBL_USERS_LEVEL" , "level");
    define("TBL_USERS_PASSWORD" , "password");
    define("TBL_USERS_DATE_JOINED" , "date_joined");
    define("TBL_USERS_EXPIRE" , "expire");
    define("TBL_USERS_TOKEN" , "token");
    define("TBL_USERS_RESET_CODE" , "reset_code");
    define("TBL_USERS_PIN" , "pin");
    define("TBL_USERS_BANNED" , "banned");
    define("TBL_SETTINGS","settings");
    define("TBL_SETTINGS_SITE_NAME","site_name");
    define("TBL_SETTINGS_SITE_URL","site_url");
    define("TBL_SETTINGS_SITE_EMAIL","site_email");
    define("TBL_SETTINGS_SITE_ENABLED","site_enabled");
    define("TBL_SETTINGS_SITE_THEME","site_theme");
    define("TBL_SETTINGS_SITE_LANG","site_lang");
    define("TBL_SETTINGS_SECRET_KEY","secret_key");
    define("TBL_SETTINGS_LOGIN_ENABLE","login_enable");
    define("TBL_SETTINGS_REGISTER_ENABLE","register_enable");
