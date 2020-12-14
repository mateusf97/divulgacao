<?php

namespace Error;

class Error {
  private $errors;

  public function __construct() {
    $this->errors = ERRORS;
  }


  /**
  * Return verbose error
  *
  * @param $error
  * @example SOME_ERROR_CONSTANT
  * @return string
  *
  **/

  public function getMessage($error) {

    if (array_key_exists($error, $this->errors)) {
      return ($this->errors[$error]);
    } else {
      return $error;
    }
  }
}
