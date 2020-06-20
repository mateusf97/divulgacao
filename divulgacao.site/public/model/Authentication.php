<?php

namespace Authentication;

class Authentication {
  private $db;
  private $header_token;
  private $isAuthenticated;

  public function __construct($db, $header_token) {

    $this->db = $db;
    $this->header_token = $header_token;
    $this->isAuthenticated = $this->authenticate();
  }






  /**
  * Get if authenticaion is valid
  *
  * @return Boolean
  *
  **/

  public function isValid() {
    if ($this->isAuthenticated) {
      return true;
    }

    return false;
  }







  /**
  * Validate user authenticated header_token from REQUEST
  *
  * @return bool - true = autheticated | false = not autheticated
  *
  **/

  private function authenticate() {
    if (!$this->header_token) {
      return false;
    } else {
      $authorization = explode(':', $this->header_token[0]);

      if ($authorization[0] === 'SERGIOS') {

        $sql = $this->db->prepare('SELECT access_token FROM user WHERE access_token = "' . $authorization[1] . '"');
        $sql->execute();
        $result = $sql->fetch();

        if ($result['access_token'] != $authorization[1]) {
          return false;
        }

        return true;
      } else {
        return false;
      }
    }
  }
}