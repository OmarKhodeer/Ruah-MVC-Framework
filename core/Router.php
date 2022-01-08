<?php
class Router
{
  public static function route($url)
  {
    // get controller from the url array.
    $controller = (isset($url[0]) && $url[0] != '') ? ucwords($url[0]) . 'Controller' : DEFAULT_CONTROLLER;
    $controllerName = str_replace('Controller', '', $controller);
    array_shift($url);

    // get action from the url array.
    $action = (isset($url[0]) && $url[0] != '') ? $url[0] . 'Action' : 'indexAction';
    $action_name = (isset($url[0]) && $url[0] != '') ? $url[0] : 'index';
    array_shift($url);

    // ACL check
    $grantAccess = self::hasAccess($controllerName, $action_name);
    /**
     * if user don't have access to this controller,
     * change controller to restricted controller and action to indexAction.
     */
    if (!$grantAccess) {
      $controller = ACCESS_RESTRICTED_CONTROLLER;
      $controllerName = str_replace('Controller', '', $controller);
      $action = 'indexAction';
    }

    // params
    $queryParams = $url;

    /** 
     * to dynamically create a new instance of controller based on $controller value.
     * $controller ---> replaced by its string value.
     */
    $dispatch = new $controller($controllerName, $action);

    // check if the method $action exists in the $controller class
    if (method_exists($controller, $action)) {
      /**
       * call_user_func_array(callable $callback, array $args): mixed
       * $callback ---> array($objectInstance, $methodName)
       * Call the callback given by the first parameter.
       * be like this: $dispatch->updateAction($queryParams);
       */
      call_user_func_array([$dispatch, $action], $queryParams);
    } else {
      die('That method does not exist in the controller "' . $controller . '"');
    }
  }

  public static function redirect($location)
  {
    if (!headers_sent()) {
      header('Location: ' . PROOT . $location);
      exit;
    } else {
      echo '<script type="text/javascript">';
      echo 'window.location.href="' . PROOT . $location . '"';
      echo '</script>';
      echo '<noscript>';
      echo '<meta http-equiv="refresh" content="0;url=' . $location . '" />';
      echo '</noscript>';
      exit;
    }
  }

  /**
   * check if user have access to this controller action.
   * acl.json file must be in app directory.
   */
  public static function hasAccess($controller, $action_name = 'index')
  {
    $acl_file = file_get_contents(ROOT . DS . 'app' . DS . 'acl.json'); // get json file content
    $acl = json_decode($acl_file, true); // turn json string to associative array.
    $current_user_acls = ["Guest"]; // everyone has a "Guest" access control by default
    $grantAccess = false;

    // check if user is logged in.
    if (Session::exists(CURRENT_USER_SESSION_NAME)) {
      $current_user_acls[] = "LoggedIn"; // add "LoggedIn" access control.
      foreach (currentUser()->acls() as $a) { // add acls from database.
        $current_user_acls[] = $a;
      }
    }

    // loop through rules
    foreach ($current_user_acls as $level) {
      /**
       * check if we have that access level in the acl json file and
       * if we have controller associated to that particular level.
       */
      if (array_key_exists($level, $acl) && array_key_exists($controller, $acl[$level])) {
        /**
         * check if we have an action associative to the controller or
         * if we have * meaning the user have access to all actions in that controller.
         */
        if (in_array($action_name, $acl[$level][$controller]) || in_array("*", $acl[$level][$controller])) {
          $grantAccess = true;
          break; // if user grant access then no need to loop over other acls.
        }
      }
    }

    // check for denied
    foreach ($current_user_acls as $level) {
      $denied = $acl[$level]['denied'];

      // check if we have denied array && there is a controller in denied && action associative to that controller.
      if (!empty($denied) && array_key_exists($controller, $denied) && in_array($action_name, $denied[$controller])) {
        $grantAccess = false;
        break; // if user denied then no need to loop over other acls.
      }
    }
    return $grantAccess;
  }

  /**
   * create menu based on menu_acl json.
   * @param string $menuFileName menu json filename must be in app directory.
   * @return array
   */
  public static function getMenu($menuFileName)
  {
    $menuArray = [];
    $menuFile = file_get_contents(ROOT . DS . 'app' . DS . $menuFileName . '.json'); // get json file content
    $menuAcl = json_decode($menuFile, true); // turn json string to associative array.

    foreach ($menuAcl as $menu => $url) {
      if (is_array($url)) { // if $url is an array then it's a sub menu not an url.
        $submenu = $url;
        $sub = [];
        foreach ($submenu as $subKey => $subUrl) {
          if ($subKey == 'Separator' && !empty($sub)) {
            $sub[$subKey] = '';
            continue;
          } elseif ($finalUrl = self::getLink($subUrl)) {
            $sub[$subKey] = $finalUrl;
          }
        }
        if (!empty($sub)) {
          $menuArray[$menu] = $sub;
        }
      } else {
        if ($finalUrl = self::getLink($url)) {
          $menuArray[$menu] = $finalUrl;
        }
      }
    }
    return $menuArray;
  }


  /**
   * get url if the user has access to it.
   * @return String|false
   */
  private static function getLink($url)
  {
    // check if url is an external link
    if (preg_match('/https?:\/\//', $url) == 1) {
      return $url;
    } else { // if url is an internal link
      $urlArray = explode('/', $url);
      $controller_name = ucwords($urlArray[0]);
      $action_name = (isset($urlArray[1])) ? $urlArray[1] : '';
      // check if we have access to that internal url
      if (self::hasAccess($controller_name, $action_name)) {
        return PROOT . implode(DS, $urlArray);
      }
      return false;
    }
  }
}
