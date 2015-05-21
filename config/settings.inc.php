<?php
// Parse settings file
$settingsFile = __DIR__ . '../../shop.cnf';
$settings = parse_ini_file($settingsFile, true);
if (empty($settings)) {
	die('Settings file not found');
} else {
	define('_PS_SETTINGS_FILE_', $settingsFile);
}
define('_DB_SERVER_', $settings['shop']['server']);
define('_DB_NAME_', $settings['shop']['database']);
define('_DB_USER_', $settings['shop']['user']);
define('_DB_PASSWD_', $settings['shop']['password']);
define('_DB_PREFIX_', $settings['shop']['prefix']);
define('_MYSQL_ENGINE_', 'InnoDB');
define('_PS_CACHING_SYSTEM_', 'CacheMemcache');
define('_PS_CACHE_ENABLED_', '0');
define('_MEDIA_SERVER_1_', '');
define('_MEDIA_SERVER_2_', '');
define('_MEDIA_SERVER_3_', '');
define('_COOKIE_KEY_', $settings['shop']['cookie_key']);
define('_COOKIE_IV_', $settings['shop']['cookie_iv']);
define('_PS_CREATION_DATE_', '2015-04-27');
if (!defined('_PS_VERSION_'))
	define('_PS_VERSION_', '1.6.0.14');
define('_RIJNDAEL_KEY_', '0QhEq0mzKyCPLJ2e7ehh9O6hxnqaHij7');
define('_RIJNDAEL_IV_', 'FFaJTyak700kXHY5VeUlOg==');
define('_PS_DIRECTORY_', '/PS/NewThemes/WatchStore/trunk/site/');