<?php
class DB
{
  private static $_instance = null;
  private $_pdo, $_query, $_error = false, $_result, $_count = 0, $_lastInsertID = null;

  private function __construct()
  {
    try {
      $this->_pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
      $this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->_pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    } catch (PDOException $e) {
      die($e->getMessage());
    }
  }

  /**
   * create a new instance from DB class.
   * if there is an instance actually created return that instance.
   */
  public static function getInstance()
  {
    if (!isset(self::$_instance)) {
      self::$_instance = new DB();
    }
    return self::$_instance;
  }

  /**
   * prepare, bindValues and execute the sql statement
   * @param string $sql the sql statement you need to execute.
   * @param array $params the values that will bind to the statement, empty by default.
   */
  public function query($sql, $params = [])
  {
    $this->_error = false; // set _error to false.
    // check if the query prepared successfully and assign PDOStatement object to _query if success.
    if ($this->_query = $this->_pdo->prepare($sql)) {
      $i = 1; // to hold the index position of the parameter identifier.
      if (count($params)) { // check if there are passed params.
        foreach ($params as $param) { // loop through the params.
          $this->_query->bindValue($i, $param); // bind value to a param.
          $i++;
        } // end foreach
      } // end if
      if ($this->_query->execute()) {
        // fetch all records and return it as objects
        $this->_result = $this->_query->fetchAll(PDO::FETCH_OBJ);
        // get the count of rows that has been fetched.
        $this->_count = $this->_query->rowCount();
        // get the id of the last inserted record.
        $this->_lastInsertID = $this->_pdo->lastInsertId();
      } else {
        // _error will be true if there is an error in execution of the query.
        $this->_error = true;
      } // end else
    } // end if
    return $this;
  }

  protected function _read($table, $params = [])
  {
    $conditionString = '';
    $bind = [];
    $order = '';
    $limit = '';

    // conditions
    if (isset($params['conditions'])) {
      if (is_array($params['conditions'])) {
        foreach ($params['conditions'] as $column => $value) {
          $conditionString .= " $column = $value AND";
        }
        $conditionString = trim($conditionString);
        $conditionString = rtrim($conditionString, ' AND');
      } else {
        $conditionString = $params['conditions'];
      }
      if ($conditionString != '') {
        $conditionString = ' WHERE ' . $conditionString;
      }
    }
    // bind
    if (array_key_exists('bind', $params)) {
      $bind = $params['bind'];
    }
    // order
    if (array_key_exists('order', $params)) {
      $order = ' ORDER BY ' . $params['order'];
    }
    // limit
    if (array_key_exists('limit', $params)) {
      $limit = ' LIMIT ' . $params['limit'];
    }

    $sql = "SELECT * FROM {$table}{$conditionString}{$order}{$limit}";
    if ($this->query($sql, $bind)) {
      if (!count($this->_result)) return false;
      return true;
    }

    return false;
  }

  public function find($table, $params = [])
  {
    if ($this->_read($table, $params)) {
      return $this->result();
    }
    return false;
  }

  public function findFirst($table, $params = [])
  {
    if ($this->_read($table, $params)) {
      return $this->first();
    }
    return false;
  }

  public function insert($table, $fields = [])
  {
    $fieldString = '';
    $valueString = '';
    $values = [];

    foreach ($fields as $field => $value) {
      $fieldString .= '`' . $field . '`,';
      $valueString .= '?,';
      $values[] = $value;
    }
    $fieldString = rtrim($fieldString, ',');
    $valueString = rtrim($valueString, ',');
    $sql = "INSERT INTO $table ($fieldString) VALUES ($valueString)";
    if (!$this->query($sql, $values)->error()) {
      return true;
    }
    return false;
  }

  public function update($table, $id, $fields = [])
  {
    $fieldString = '';
    $values = [];
    foreach ($fields as $field => $value) {
      $fieldString .= ' ' . $field . ' = ?,';
      $values[] = $value;
    }
    $fieldString = trim($fieldString);
    $fieldString = rtrim($fieldString, ',');
    $sql = "UPDATE {$table} SET {$fieldString} WHERE id = {$id}";
    if (!$this->query($sql, $values)->error()) {
      return true;
    }
    return false;
  }

  public function delete($table, $id)
  {
    $sql = "DELETE FROM $table WHERE id = $id";
    if (!$this->query($sql)->error()) {
      return true;
    }
    return false;
  }

  // return the first record 
  public function first()
  {
    return (!empty($this->_result)) ? $this->_result[0] : [];
  }

  public function result()
  {
    return $this->_result;
  }

  public function count()
  {
    return $this->_count;
  }

  public function lastID()
  {
    return $this->_lastInsertID;
  }

  public function get_columns($table)
  {
    return $this->query("SHOW COLUMNS FROM $table")->result();
  }

  public function error()
  {
    return $this->_error;
  }
}
