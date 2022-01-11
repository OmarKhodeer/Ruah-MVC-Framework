<?php

class Contacts extends Model
{
  public function __construct()
  {
    $table = 'contacts';
    parent::__construct($table);
    $this->_softDelete = true;
  }

  public function findAllByUserId($userId, $params = [])
  {
    $conditions = [
      'conditions' => 'user_id = ?',
      'bind' => [$userId]
    ];
    $conditions = array_merge($conditions, $params);
    return $this->find($conditions);
  }

  public function displayName()
  {
    return $this->fname . ' ' . $this->lname;
  }
}
