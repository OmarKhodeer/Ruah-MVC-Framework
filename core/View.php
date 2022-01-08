<?php

class View
{
  protected $_head, $_body, $_siteTitle = "Ruah MVC Framework", $_outputBuffer, $_layout = DEFAULT_LAYOUT;
  public function __construct()
  {
  }

  /**
   * including the view that provided by the controller actions.
   * including the layout. if not set then include the DEFAULT_LAYOUT.
   */
  public function render($viewPath)
  {
    $viewAry = explode('/', $viewPath);
    $viewString = implode(DS, $viewAry);
    if (file_exists(ROOT . DS . 'app' . DS . 'views' . DS . $viewString . '.php')) {
      include(ROOT . DS . 'app' . DS . 'views' . DS . $viewString . '.php'); // include the view.
      include(ROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . $this->_layout . '.php'); // include the layout.
    } else {
      die('The view "' . $viewPath . '" does not exist.');
    }
  }

  // return the output buffer after we call start($type) and end()
  public function content($type)
  {
    if ($type == 'head') {
      return $this->_head;
    } elseif ($type == 'body') {
      return $this->_body;
    }
    return false;
  }

  // save the output buffer type to _outputBuffer and turn on out buffering
  public function start($type)
  {
    $this->_outputBuffer = $type;
    ob_start(); // turn on output buffering
  }

  /**
   * delete current output buffering content after saving it in (_head or _body) 
   * to return it later using content($ype) method.
   */
  public function end()
  {
    if ($this->_outputBuffer == 'head') {
      $this->_head = ob_get_clean();
    } elseif ($this->_outputBuffer == 'body') {
      $this->_body = ob_get_clean();
    } else {
      die('You must first run the start method.');
    }
  }

  // get the site title.
  public function siteTitle()
  {
    return $this->_siteTitle;
  }

  // set the site title. default: "Ruah MVC Framework".
  public function setSiteTitle($title)
  {
    $this->_siteTitle = $title;
  }

  // set the layout name. default: DEFAULT_LAYOUT
  public function setLayout($layoutName)
  {
    $this->_layout = $layoutName;
  }
}
