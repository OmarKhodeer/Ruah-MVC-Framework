<?php
class RegisterController extends Controller
{
  public function __construct($controller, $action)
  {
    parent::__construct($controller, $action);
    $this->load_model('Users');
    $this->view->setLayout('default');
  }

  public function loginAction()
  {
    $validation = new Validate();
    if ($_POST) {
      // form validation
      $validation->check($_POST, [
        'username' => [
          'display' => 'Username',
          'required' => true
        ],
        'password' => [
          'display' => 'Password',
          'required' => true
        ]
      ]);
      if ($validation->passed()) {
        // UsersModel is an instance from Users class thats created by load_model method.
        $user = $this->UsersModel->findByUsername($_POST['username']);
        if ($user && password_verify(Input::get('password'), $user->password)) {
          // get the remember_me check box value from login view.
          $remember = (isset($_POST['remember_me']) && Input::get('remember_me')) ? true : false;
          $user->login($remember);
          Router::redirect('');
        } else {
          $validation->addError('Invalid Username or Password.');
        }
      }
    }
    $this->view->displayErrors = $validation->displayErrors();
    $this->view->render('register/login');
  }

  public function logoutAction()
  {

    if (currentUser()) {
      currentUser()->logout();
    }
    Router::redirect('register/login');
  }

  public function registerAction()
  {
    $validation = new Validate();
    $posted_values = ['fname' => '', 'lname' => '', 'username' => '', 'email' => '', 'password' => '', 'confirm' => ''];
    if ($_POST) {
      $posted_values = posted_values($_POST);
      $validation->check($_POST, [
        'fname' => [
          'display' => 'First Name',
          'required' => true
        ],
        'lname' => [
          'display' => 'Last Name',
          'required' => true
        ],
        'username' => [
          'display' => 'Username',
          'required' => true,
          'unique' => 'users',
          'min' => 6,
          'max' => 150
        ],
        'email' => [
          'display' => 'Email',
          'required' => true,
          'unique' => 'users',
          'max' => 150,
          'valid_email' => true
        ],
        'password' => [
          'display' => 'Password',
          'required' => true,
          'min' => 8,
        ],
        'confirm' => [
          'display' => 'Confirm Password',
          // 'required' => true,
          'matches' => 'password'
        ]
      ]);

      if ($validation->passed()) {
        $newUser = new Users();
        $newUser->registerNewUser($_POST);
        Router::redirect('register/login');
      }
    }
    $this->view->post = $posted_values;
    $this->view->displayErrors = $validation->displayErrors();
    $this->view->render('register/register');
  }
}
