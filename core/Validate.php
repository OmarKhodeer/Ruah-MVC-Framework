<?php

class Validate
{
  private $_passed = false, $_errors = [], $_db = null;

  public function __construct()
  {
    $this->_db = DB::getInstance();
  }

  /**
   * @param mixed $source eg: $_POST
   * @param array $items the input fields to validate. eg: 'fieldName' => array('rule' => 'rule_value')
   */
  public function check($source, $items = [])
  {
    $this->_errors = [];
    $this->_passed = true;
    foreach ($items as $item => $rules) {
      $item = Input::sanitize($item);
      $display = $rules['display'];

      foreach ($rules as $rule => $rule_value) {
        // trim spaces and sanitize the input value.
        $inputValue = Input::sanitize(trim($source[$item]));

        // check if input field matches the rule_value field.
        if ($rule === 'matches') {
          if ($inputValue !== $source[$rule_value]) {
            $matchDisplay = $items[$rule_value]['display']; // ??????????????
            // add a new error to _errors array.
            $this->addError(["{$matchDisplay} and {$display} must match.", $item]);
          }
        }

        if ($rule === 'required' && empty($inputValue)) {
          $this->addError(["{$display} is required.", $item]);
        } elseif (!empty($inputValue)) {
          switch ($rule) {
            case 'min': // check the min chars that can user input.
              if (strlen($inputValue) < $rule_value) {
                // add a new error to _errors array.
                $this->addError(["{$display} must be a minimum of {$rule_value} characters.", $item]);
              }
              break;

            case 'max': // check the max chars that can user input.
              if (strlen($inputValue) > $rule_value) {
                // add a new error to _errors array.
                $this->addError(["{$display} must be a maximum of {$rule_value} characters.", $item]);
              }
              break;

            case 'unique': // check the input uniqueness in a particular table.
              // if rule value contains (,) thats mean rule value in the form of 'tableName,id'
              if (str_contains($rule_value, ',')) {
                $rules = explode(',', $rule_value);
                $table = $rules[0];
                $id = $rules[1];
                // retrieve the cell we check it if it exists in the tableName.
                $check = $this->_db->query("SELECT $item FROM $table WHERE $item = ? AND id != ?", [$inputValue, $id]);
              } else { // if rule value in the form of 'tableName'
                $table = $rule_value;
                // retrieve the cell we check it if it exists in the tableName.
                $check = $this->_db->query("SELECT $item FROM $table WHERE $item = ?", [$inputValue]);
              }
              // check we have any objects returned from the query.
              if ($check->count()) {
                // add a new error to _errors array.
                $this->addError(["$display already exists. Try another $display", $item]);
              }
              break;

            case 'is_numeric':
              if (!is_numeric($inputValue)) {
                $this->addError(["$display has to be a number.", $item]);
              }
              break;

            case 'valid_email':
              if (!filter_var($inputValue, FILTER_VALIDATE_EMAIL)) {
                $this->addError(["$display must be a valid email address.", $item]);
              }
              break;
          } // end switch
        } // end elseif
      } // end foreach
    } // end foreach
  }

  /**
   * add an error to _errors array
   * @param array $error
   */
  public function addError($error)
  {
    $this->_errors[] = $error;
    if (!empty($this->_errors)) {
      $this->_passed = false;
    } else {
      $this->_passed = true;
    }
  }

  /** 
   * get validation errors.
   * @return array validation errors
   */
  public function errors()
  {
    return $this->_errors;
  }

  /**
   * @return bool
   */
  public function passed()
  {
    return $this->_passed;
  }

  public function displayErrors()
  {
    $html = '<ul class="bg-danger">';
    foreach ($this->_errors as $error) {
      if (is_array($error)) {
        $html .= '<li class="text-danger">' . $error[0] . '</li>';
        $html .= '<script>jQuery("document").ready(function(){jQuery("#' . $error[1] . '").parent().closest("div").addClass("has-error");});</script>'; // need to maintain
      } else {
        $html .= '<li class="text-danger">' . $error . '</li>';
      }
    }
    $html .= '</ul>';
    return $html;
  }
}
