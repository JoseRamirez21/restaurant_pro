<?php
define('APP_NAME',     'RestaurantePro');
define('APP_VERSION',  '1.0.0');
define('APP_URL', 'http://localhost/restaurant_pro');
define('MONEDA',       'S/');
define('TIMEZONE',     'America/Lima');
define('DEBUG_MODE',   true);

date_default_timezone_set(TIMEZONE);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
