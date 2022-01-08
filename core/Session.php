<?php
class Session
{
  // check if session is set or not.
  public static function exists($name)
  {
    return (isset($_SESSION[$name])) ? true : false;
  }

  public static function get($name)
  {
    return $_SESSION[$name];
  }

  public static function set($name, $value)
  {
    return $_SESSION[$name] = $value;
  }

  // unset the session
  public static function delete($name)
  {
    if (self::exists($name)) {
      unset($_SESSION[$name]);
    }
  }

  // return user agent without versions number
  public static function uagent_no_version()
  {
    $uagent = $_SERVER['HTTP_USER_AGENT'];
    $regx = '/\/[a-zA-Z0-9.]+/';
    $newString = preg_replace($regx, '', $uagent);
    return $newString;
  }
}
