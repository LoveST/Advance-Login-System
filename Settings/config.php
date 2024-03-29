<?php
/**
 * Created by PhpStorm.
 * User: LoveMST
 * Date: 5/12/2016
 * Time: 12:35 PM
 */

namespace ALS;
if (count(get_included_files()) == 1) exit("You don't have the permission to access this file.");

// Set Error Reporting
define("ERROR_REPORTING", E_ALL);
define("DB_ERROR_DIE", "0");

// Main Framework Path
define("FRAMEWORK_PATH", "D:\\xampp\htdocs\ALS/");
define("FRAMEWORK_PUBLIC_PATH", "Public/");

//Set the Database Connection Type
define("CONNECTION_TYPE", "PDO");

//Declare all the database variables
define("DBURL", "localhost"); // Set the database url (ex : localhost || 127.0.01 ).
define("DBUSER", "root"); // Set the database user.
define("DBPASS", ""); // Set the database password to access it.
define("DBNAME", "als"); // Set the database name used to store all the data.
define("DBPORT", "3306"); // Set the sql port that is used to connect to the database ( default : 3306 ).

//The Site Main Secret Key
//Make sure nobody have access to this variable at all times
define("SITE_SECRET", md5("This is Just An ExaMple123"));

/**
 * Declare all sql tables that's been used in the script
 * User Table
 */

define("TBL_USERS", "users");
define("TBL_USERS_ID", "id");
define("TBL_USERS_USERNAME", "username");
define("TBL_USERS_FNAME", "firstName");
define("TBL_USERS_LNAME", "lastName");
define("TBL_USERS_EMAIL", "email");
define("TBL_USERS_LEVEL", "level");
define("TBL_USERS_PASSWORD", "password");
define("TBL_USERS_DATE_JOINED", "date_joined");
define("TBL_USERS_LAST_LOGIN", "last_login");
define("TBL_USERS_EXPIRE", "expire");
define("TBL_USERS_TOKEN", "token");
define("TBL_USERS_RESET_CODE", "reset_code");
define("TBL_USERS_PIN", "pin_number");
define("TBL_USERS_BANNED", "banned");
define("TBL_USERS_ACTIVATED", "activated");
define("TBL_USERS_ACTIVATION_CODE", "activation_code");
define("TBL_USERS_XP", "xp");
define("TBL_USERS_LOST_XP", "xp_lost");
define("TBL_USERS_HAS_DOUBLEXP", "has_doubleXP");
define("TBL_USERS_DOUBLEXP_UNTIL", "doubleXP_until");
define("TBL_USERS_SIGNIN_AGAIN", "must_signin_again");
define("TBL_USERS_HEARTBEAT", "heartbeat");
define("TBL_USERS_DEVICES", "devices");
define("TBL_USERS_TWOFACTOR_ENABLED", "twoFactor_enabled");
define("TBL_USERS_TWOFACTOR_CODE", "twoFactor_code");
define("TBL_USERS_VERIFICATION_CODE", "verification_code");
define("TBL_USERS_LASTLOGIN_IP", "lastLogin_ip");
define("TBL_USERS_BIRTH_DATE", "birth_date");
define("TBL_USERS_PREFERRED_LANGUAGE", "preferred_language");
define("TBL_USERS_SECRET", "secret");
define("TBL_USERS_API_KEY", "api_key");
define("TBL_USERS_API_TOKEN", "api_token");
define("TBL_USERS_AUTHENTICATORS", "authenticators");
define("TBL_USERS_AVATAR_ID", "avatar_id");
define("TBL_USERS_LOGIN_ID", "login_id");
define("TBL_USERS_LOGIN_ID_INIT", "login_id_init");

/**
 * Settings Table
 */

define("TBL_SETTINGS", "settings");
define("TBL_SETTINGS_SITE_NAME", "site_name");
define("TBL_SETTINGS_SITE_URL", "site_url");
define("TBL_SETTINGS_SITE_PATH", "site_path");
define("TBL_SETTINGS_SITE_EMAIL", "site_email");
define("TBL_SETTINGS_SITE_ENABLED", "site_enabled");
define("TBL_SETTINGS_SITE_THEME", "site_theme");
define("TBL_SETTINGS_SITE_LANG", "site_lang");
define("TBL_SETTINGS_SITE_TIMEZONE", "site_timezone");
define("TBL_SETTINGS_SECRET_KEY", "secret_key");
define("TBL_SETTINGS_LOGIN_ENABLE", "login_enable");
define("TBL_SETTINGS_REGISTER_ENABLE", "register_enable");
define("TBL_SETTINGS_PIN_REQUIRED", "pin_required");
define("TBL_SETTINGS_ACTIVATION_REQUIRED", "activation_required");
define("TBL_SETTINGS_MINIMUM_AGE_REQUIRED", "minimum_age_required");
define("TBL_SETTINGS_MINIMUM_AGE", "minimum_age");
define("TBL_SETTINGS_USERNAME_CHANGE", "username_change");
define("TBL_SETTINGS_FORCE_HTTPS", "force_https");
define("TBL_SETTINGS_MAX_WARNINGS", "max_warnings");
define("TBL_SETTINGS_CAPTCHA_KEY", "captcha_key");
define("TBL_SETTINGS_CAPTCHA_SECRET", "captcha_secret");
define("TBL_SETTINGS_SAME_IP_LOGIN", "same_ip_login");
define("TBL_SETTINGS_MAX_VERIFIED_DEVICES", "max_verified_devices");
define("TBL_SETTINGS_TEMPLATES_FOLDER", "templates_folder");
define("TBL_SETTINGS_LOADING_TIMESTAMP", "loading_timestamp");
define("TBL_SETTINGS_MAX_REQUIRED_PIN_LENGTH", "max_required_pin_length");
define("TBL_SETTINGS_MAX_PASSWORD_LENGTH", "max_password_length");
define("TBL_SETTINGS_MIN_PASSWORD_LENGTH", "min_password_length");
define("TBL_SETTINGS_AVATARS_PATH", "avatars_path");
define("TBL_SETTINGS_AVATARS_URL", "avatars_url");
define("TBL_SETTINGS_AVATARS_DEFAULT_NAME", "avatars_default_name");
define("TBL_SETTINGS_LOGIN_ID_EXP_INTERVAL", "login_id_expiration_interval");

/**
 * Levels-Groups Table
 */

define("TBL_LEVELS", "levels");
define("TBL_LEVELS_LEVEL", "level");
define("TBL_LEVELS_NAME", "name");
define("TBL_LEVELS_PERMISSIONS", "permissions");
define("TBL_LEVELS_DEFAULT_GROUP", "isDefault");

/**
 * users_auths Table
 */

define("TBL_USERS_AUTHS", "users_auths");
define("TBL_USERS_AUTHS_ID", "id");
define("TBL_USERS_AUTHS_SITE", "site");
define("TBL_USERS_AUTHS_AUTH_TYPE", "auth_type");

/**
 * users_api_calls
 */

define("TBL_USERS_API_CALLS", "users_api_calls");
define("TBL_USERS_API_CALLS_USER_ID", "user_id");
define("TBL_USERS_API_CALLS_USER_TOKEN", "user_token");
define("TBL_USERS_API_CALLS_EXPIRATION_DATE", "expiration_date");
define("TBL_USERS_API_CALLS_APP_ID", "app_id");
define("TBL_USERS_API_CALLS_APP_KEY", "app_key");
define("TBL_USERS_API_CALLS_PIN_VERIFIED", "pin_verified");
define("TBL_USERS_API_CALLS_BROWSER_NAME", "browser_name");
define("TBL_USERS_API_CALLS_BROWSER_AOL", "browser_aol");
define("TBL_USERS_API_CALLS_USER_AGENT", "user_agent");
define("TBL_USERS_API_CALLS_USER_IP", "user_ip");
define("TBL_USERS_API_CALLS_PLATFORM", "platform");

/**
 * Links Table
 */

define("TBL_LINKS", "links");
define("TBL_LINKS_NAME", "name");
define("TBL_LINKS_VALUE", "value");

?>