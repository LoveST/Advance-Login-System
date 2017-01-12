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
define( "DBURL" , "localhost"); // Set the database url (ex : localhost)
define( "DBUSER" , "root"); // Set the database user
define( "DBPASS" , "199601997masis@"); // Set the database password to access it
define( "DBNAME" , "als"); // Set the database name used to store all the data
/**
 *  ** End database variables declaration**
 */


/**
 * Declare all the site needed information
 */
define("SITENAME" , ""); // Set the site main name
define("SITEURL" , "localhost/als/"); // Set the script main url ending with '/' (ex : htt://localhost/login/)
define("SITE_KEY" , "k4jhkwhdsf92h234rtjfdslfowruy23yrodjkhl");
define("COOKIE_AUTH_CODE" , "2k3j43hrwefjsdjoczucv9shshl$#@$@jhdfksh2#$@");
/**
 *  ** End Declare site needed information**
 */


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
    define("TBL_USERS_SINCE" , "since");
    define("TBL_USERS_EXPIRE" , "expire");
    define("TBL_USERS_TOKEN" , "token");
/**
 * ** End Declare all sql tables **
 */
