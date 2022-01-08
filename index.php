<?php

// to avoid conflict using different OS
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__));

// load configurations and helper functions
require_once(ROOT . DS . 'config' . DS . 'config.php');
require_once(ROOT . DS . 'app' . DS . 'lib' . DS . 'helpers' . DS . 'functions.php');

// Autoload classes
function autoloadClasses($className)
{
  if (file_exists(ROOT . DS . 'core' . DS . $className . '.php')) {
    require_once(ROOT . DS . 'core' . DS . $className . '.php');
  } elseif (file_exists(ROOT . DS . 'app' . DS . 'controllers' . DS . $className . '.php')) {
    require_once(ROOT . DS . 'app' . DS . 'controllers' . DS . $className . '.php');
  } elseif (file_exists(ROOT . DS . 'app' . DS . 'models' . DS . $className . '.php')) {
    require_once(ROOT . DS . 'app' . DS . 'models' . DS . $className . '.php');
  }
}

spl_autoload_register('autoloadClasses');

// spl_autoload_extensions(".php"); // comma-separated list
// spl_autoload_register();

session_start();
// unset($_SESSION[CURRENT_USER_SESSION_NAME]);

// convert path info string to an array.
$url = isset($_SERVER['PATH_INFO']) ? explode('/', ltrim($_SERVER['PATH_INFO'], '/')) : [];

if (!Session::exists(CURRENT_USER_SESSION_NAME) && Cookie::exists(REMEMBER_ME_COOKIE_NAME)) {
  // dnd($_SESSION);
  Users::loginUserFromCookie();
}

// Route the request
Router::route($url);
