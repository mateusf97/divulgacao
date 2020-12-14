<?php

namespace Authentication;

class Authentication {
  private $db;
  private $header_token;
  private $type;
  private $isAuthenticated;

  public function __construct($db, $header_token = false) {
    $this->db = $db;
    $this->header_token = $header_token;
    $this->isAuthenticated = $this->authenticate();

    if ($this->isAuthenticated) {
      $this->type = $this->generateType();
    }
  }







  /**
  * Get Authentication user type
  *
  * @return bool false if error or  string 'PROVIDER' | 'CLIENT'  if match
  *
  **/

  public function getAuthenticationType() {
    return $this->type;
  }






  /**
  * Get Authentication user token
  *
  * @return header_token
  *
  **/

  public function getToken() {
    return $this->header_token;
  }







  /**
  * Get if authenticaion is valid
  *
  * @return Boolean
  *
  **/

  public function isValid($assertType = false) {
    if ($this->isAuthenticated) {

      if ($assertType) {
        if ($this->type === $assertType) {
          return true;
        }

        return false;
      }

      return true;
    }

    return false;
  }








  /**
  * Generate user Type to help class constructor
  *
  * @return String: "PROVIDER" || "CLIENT"
  *
  **/

  private function generateType() {
    $type = explode(':', $this->header_token[0]);
    $type = $type[0];

    if ($type === 'PROVIDER_TOKEN') {
      return "PROVIDER";
    }

    if ($type === 'CLIENT_TOKEN') {
      return "CLIENT";
    }
  }






  /**
  * Validate user authenticated header_token from REQUEST
  *
  * @return bool - true = autheticated | false = not autheticated
  *
  **/

  private function authenticate() {
    if ($this->header_token) {

      $authorization = explode(':', $this->header_token[0]);

      if ($authorization[0] === 'PROVIDER_TOKEN') {

        $sql = $this->db->prepare('SELECT access_token FROM provider WHERE access_token = "' . $authorization[1] . '"');
        $sql->execute();
        $result = $sql->fetch();

      } elseif ($authorization[0] === 'CLIENT_TOKEN') {

        $sql = $this->db->prepare('SELECT access_token FROM client WHERE access_token = "' . $authorization[1] . '"');
        $sql->execute();
        $result = $sql->fetch();

      } else {
        return false;
      }


      if (!empty($result['access_token'])) {
        return true;
      }
    }

    return false;
  }

}