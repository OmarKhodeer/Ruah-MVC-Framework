<?php
class Users extends Model
{
  private $_isLoggedIn, $_sessionName, $_cookieName;
  public static $currentLoggedInUser = null;

  public function __construct($user = '')
  {
    $table = 'users';
    parent::__construct($table);
    $this->_sessionName = CURRENT_USER_SESSION_NAME;
    $this->_cookieName = REMEMBER_ME_COOKIE_NAME;
    $this->_softDelete = true;
    if ($user != '') {
      if (is_int($user)) { // check if $user is an int then find user by id.
        $u = $this->_db->findFirst('users', [
          'conditions' => ['id = ?'],
          'bind' => [$user]
        ]);
      } else { // if $user not int then find user by username.
        $u = $this->_db->findFirst('users', [
          'conditions' => ['username = ?'],
          'bind' => [$user]
        ]);
      }

      if ($u) { // check if we found a user.
        foreach ($u as $key => $val) {
          // assign columns values to object properties
          $this->$key = $val;
        }
      }
    }
  }

  public function findByUsername($username)
  {
    return $this->findFirst([
      'conditions' => ['username = ?'],
      'bind' => [$username]
    ]);
  }

  /**
   * get the current logged in user.
   */
  public static function currentLoggedInUser()
  {
    // check if current logged in user is set.
    if (!isset(self::$currentLoggedInUser)) {
      // check if we had session with CURRENT_USER_SESSION_NAME
      if (Session::exists(CURRENT_USER_SESSION_NAME)) {
        // get the current user by creating a new instance of Users and find by user id in Users __construct.
        self::$currentLoggedInUser = new Users((int) Session::get(CURRENT_USER_SESSION_NAME));
      }
    }
    return self::$currentLoggedInUser;
  }

  public function login($rememberMe = false)
  {
    Session::set($this->_sessionName, $this->id);
    if ($rememberMe) {
      $hash = md5(uniqid() + rand(0, 100));
      $user_agent = Session::uagent_no_version();
      // set the remember me cookie.
      Cookie::set($this->_cookieName, $hash, REMEMBER_ME_COOKIE_EXPIRY);
      $fields = [
        'session'     => $hash,
        'user_agent'  => $user_agent,
        'user_id'     => $this->id
      ];
      $this->_db->query(
        'DELETE FROM user_sessions WHERE user_id = ? AND user_agent = ?',
        [$this->id, $user_agent]
      );
      $this->_db->insert('user_sessions', $fields);
    }
  }

  public static function loginUserFromCookie()
  {
    $userSession = UserSessions::getFromCookie();
    if ($userSession && $userSession->user_id != '') {
      $user = new self((int) $userSession->user_id);
      if ($user) {
        $user->login();
      }

      return $user;
    }
    return;
  }

  public function logout()
  {
    $userSession = UserSessions::getFromCookie();
    // delete the session in database.
    if ($userSession) $userSession->delete();
    // delete the session from the agent.
    Session::delete(CURRENT_USER_SESSION_NAME);
    // delete the remember me cookie.
    if (Cookie::exists(REMEMBER_ME_COOKIE_NAME)) {
      Cookie::delete(REMEMBER_ME_COOKIE_NAME);
    }
    self::$currentLoggedInUser = null;
    return true;
  }

  public function registerNewUser($params)
  {
    $this->assign($params);
    $this->password = password_hash($this->password, PASSWORD_DEFAULT);
    $this->save();
  }

  /**
   * get acls json from database and convert it to an associative array.
   */
  public function acls()
  {
    if (empty($this->acl)) return [];
    return json_decode($this->acl, true);
  }
}
