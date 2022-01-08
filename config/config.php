<?php

define('DB_HOST', '127.0.0.1'); // database host (using IP to avoid DNS lookup).
define('DB_NAME', 'ruah'); // database name
define('DB_USER', 'root'); // database username
define('DB_PASSWORD', ''); // database password

// true ---> showing on errors, false ---> log errors in logs/errors.log
define('DEBUG', true);

// default controller if there isn't defined in the url
define('DEFAULT_CONTROLLER', 'HomeController');

// if no layout is set in the controller use this layout.
define('DEFAULT_LAYOUT', 'default');

// set this to '/' for a live server. (Project Root)
define('PROOT', DS . 'ruah' . DS);

// This will be used if no site title is set.
define('SITE_TITLE', 'Ruah MVC Framework');

// This is the brand text menu.
define('MENU_BRAND', 'RUAH');

// session name for logged in user
define('CURRENT_USER_SESSION_NAME', 'dsfkWpqFsGgDsMj121asdA');

// cookie name for logged in user (remember me)
define('REMEMBER_ME_COOKIE_NAME', 'fisoabEWcSFHasdfCx5xchg');

// time in seconds for remember me cookie to live (30 days)
define('REMEMBER_ME_COOKIE_EXPIRY', 2592000);

// controller name for the restricted redirect.
define('ACCESS_RESTRICTED_CONTROLLER', 'RestrictedController');
