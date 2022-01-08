<?php

class Cookie
{
  // set a cookie.
  public static function set($name, $value, $expiry)
  {
    if (setcookie($name, $value, time() + $expiry, '/')) {
      return true;
    }
    return false;
  }

  // delete a cookie
  public static function delete($name)
  {
    self::set($name, '', time() - 1);
  }

  // get a cookie
  public static function get($name)
  {
    return $_COOKIE[$name];
  }

  // check if a cookie is exists
  public static function exists($name)
  {
    return isset($_COOKIE[$name]);
  }
}
