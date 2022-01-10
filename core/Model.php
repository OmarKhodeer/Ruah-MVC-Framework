<?php

class Model
{
  protected $_db, $_table, $_modelName, $_softDelete = false, $_columnNames = [];
  public $id;

  public function __construct($table)
  {
    $this->_db = DB::getInstance();
    $this->_table = $table;
    $this->_setTableColumns();
    //example: $table = user_session ---> UserSession
    $this->_modelName = str_replace(' ', '', ucwords(str_replace('_', ' ', $this->_table)));
  }

  protected function _setTableColumns()
  {
    $columns = $this->get_columns();
    foreach ($columns as $column) {
      $columnName = $column->Field;
      $this->_columnNames[] = $columnName;
      // add a new property to the object
      $this->{$columnName} = null;
    }
  }

  public function get_columns()
  {
    return $this->_db->get_columns($this->_table);
  }

  protected function _softDeleteParams($params)
  {
    if ($this->_softDelete) {
      if (array_key_exists('conditions', $params)) {
        if (is_array($params['conditions'])) {
          $params['conditions']['deleted'] = 0;
        } else {
          $params['conditions'] .= 'AND deleted = 0';
        }
      } else {
        $params['conditions'] = 'deleted = 0';
      }
    }
    return $params;
  }

  public function find($params = [])
  {
    $params = $this->_softDeleteParams($params);
    $results = [];
    $resultsQuery = $this->_db->find($this->_table, $params);
    if ($resultsQuery) {
      foreach ($resultsQuery as $result) {
        // create a new object based on _modelName
        $obj = new $this->_modelName($this->_table);
        $obj->populateObjectData($result);
        $results[] = $obj;
      }
    }
    return $results;
  }

  public function findFirst($params = [])
  {
    $params = $this->_softDeleteParams($params);
    $resultQuery = $this->_db->findFirst($this->_table, $params);
    $result = new $this->_modelName($this->_table);
    if ($resultQuery) {
      $result->populateObjectData($resultQuery);
    }
    return $result;
  }

  public function findById($id)
  {
    $params = [
      'conditions' => ['id' => '?'],
      'bind' => [$id]
    ];
    $params = $this->_softDeleteParams($params);
    return $this->findFirst($params);
  }

  public function save()
  {
    $fields = [];
    foreach ($this->_columnNames as $column) {
      $fields[$column] = $this->$column;
    }
    // determine wether to update or insert
    if (property_exists($this, 'id') && $this->id != '') {
      return $this->update($this->id, $fields);
    } else {
      return $this->insert($fields);
    }
  }

  public function insert($fields)
  {
    if (empty($fields)) return false;
    return $this->_db->insert($this->_table, $fields);
  }

  public function update($id, $fields)
  {
    if (empty($fields) || $id = '') return false;
    return $this->_db->update($this->_table, $id, $fields);
  }

  public function delete($id = '')
  {
    if ($id = '' && $this->id == '') return false;
    $id = ($id == '') ? $this->id : $id;
    if ($this->_softDelete) {
      $this->update($id, ['deleted' => 1]);
    }
    return $this->_db->delete($this->_table, $id);
  }

  public function query($sql, $bind = [])
  {
    return $this->_db->query($sql, $bind);
  }

  // to return an object of the data without any methods inside object
  public function data()
  {
    $data = new stdClass();
    foreach ($this->_columnNames as $column) {
      $data->$column = $this->$column;
    }
    return $data;
  }

  public function assign($params)
  {
    if (!empty($params)) {
      foreach ($params as $key => $val) {
        if (in_array($key, $this->_columnNames)) {
          $this->$key = sanitize($val);
        }
      }
      return true;
    }
    return false;
  }

  protected function populateObjectData($result)
  {
    foreach ($result as $key => $value) {
      $this->$key = $value;
    }
  }
}
