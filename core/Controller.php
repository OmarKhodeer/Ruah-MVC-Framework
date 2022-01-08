<?php

class Controller extends Application
{
  protected $_controller, $_action;
  public $view;

  public function __construct($controller, $action)
  {
    parent::__construct();
    $this->controller = $controller;
    $this->action = $action;
    /**
     * create a new View instance.
     * (controller class) will have a different View instances because it will extend this Controller Class.
     */
    $this->view = new View();
  }

  // create an instance from model class.
  protected function load_model($model)
  {
    if (class_exists($model)) {
      $this->{$model . 'Model'} = new $model(strtolower($model));
    }
  }
}
