<?php
// var_dump then die function
function dnd($data)
{
  echo '<pre>';
  var_dump($data);
  echo '</pre>';
  die();
}

function sanitize($dirty)
{
  return htmlentities($dirty, ENT_QUOTES, 'UTF-8');
}

/**
 * return current logged in user.
 */
function currentUser()
{
  return Users::currentLoggedInUser();
}

/**
 * return a sanitized version of all values of a Post request.
 */
function posted_values($post)
{
  $clean_array = [];
  foreach ($post as $key => $value) {
    $clean_array[$key] = sanitize($value);
  }
  return $clean_array;
}

/**
 * return the current page url
 */
function currentPage()
{
  $currentPage = implode(DS, explode('/', $_SERVER['REQUEST_URI']));

  if ($currentPage == PROOT || $currentPage == PROOT . 'home' . DS . 'index') {
    $currentPage = PROOT . 'home';
  }
  return $currentPage;
}
