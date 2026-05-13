<?php
define('BASE_PATH', __DIR__);
define('APP_PATH',  BASE_PATH . '/app');

require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/config/session.php';
require_once BASE_PATH . '/routes/web.php';
