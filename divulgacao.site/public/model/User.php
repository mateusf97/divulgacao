<?php

namespace User;

use \Output\Output;
use \Validator\Validator;
use \Utils\Utils;
use \Error\Error;

class User {
  private $id;
  private $db;
  private $cpf;
  private $email;
  private $token;

  public function __construct($db, $token = false) {
    $this->db = $db;

    if ($token) {
      $token = (explode(':', $token[0]))[1];

      $user = $this->db->prepare("SELECT * FROM user WHERE access_token = '" . $token . "'");
      $user->execute();
      $user = $user->fetch();

      $this->id = $user['id'];
      $this->cpf = $user['cpf'];
      $this->email = $user['email'];
      $this->token = $token;
    }
  }





  /**
  * Create a user in database
  *
  * @param $params: (name, email, password, cpf)
  * @param name must to have a space between two words: @example XXXX XXXXX
  * @param password must be at least 8 characters: @example XXXXXXXX
  *
  * @return array(response, status code)
  *
  **/

  public function create($params) {
    $Validator = new Validator($this->db);
    $Utils = new Utils($this->db);
    $Error = new Error();

    if (!isset($params['email'])) {
      return array('status' => 400, 'response' => $Error->getMessage('MISSING_EMAIL'));
    }

    if (!isset($params['name'])) {
      return array('status' => 400, 'response' => $Error->getMessage('MISSING_NAME'));
    }

    if (!isset($params['password'])) {
      return array('status' => 400, 'response' => $Error->getMessage('MISSING_PASSWORD'));
    }

    if (!isset($params['cpf'])) {
      return array('status' => 400, 'response' => $Error->getMessage('MISSING_CPF'));
    }

    if (!strrpos(trim($params['name']), ' ')) {
      return array('status' => 400, 'response' => $Error->getMessage('INVALID_NAME'));
    }

    if (!filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
      return array('status' => 400, 'response' => $Error->getMessage('INVALID_EMAIL'));
    }

    $Utils->extractNumbers($params['cpf']);
    $cpf = $params['cpf'];

    if (!$Validator->validateCpf($cpf)) {
      return array('status' => 400, 'response' => $Error->getMessage('INVALID_CPF'));
    }

    if (strlen($params['password']) < PASSWORD_MIN_LENGTH) {
      return array('status' => 400, 'response' => $Error->getMessage('INVALID_PASSWORD'));
    }

    // Check if user exist
    $user_cpf = $this->db->prepare("SELECT cpf FROM user WHERE cpf = ?");
    $user_cpf->execute([$cpf]);
    $user_cpf = $user_cpf->fetchAll();

    if ($user_cpf) {
      return array('status' => 403, 'response' => $Error->getMessage('CPF_ALREADY_EXISTS'));
    }

    $user_email = $this->db->prepare("SELECT email FROM user WHERE email = ?");
    $user_email->execute([$params['email']]);
    $user_email = $user_email->fetchAll();

    if ($user_email) {
      return array('status' => 403, 'response' => $Error->getMessage('EMAIL_ALREADY_EXISTS'));
    }

    $access_token = md5(uniqid($cpf, true));

    $params['password'] =  password_hash($params['password'], PASSWORD_DEFAULT);

    $sql = $this->db->prepare('INSERT INTO user SET cpf = ?, email = ?, access_token = ?, password = ?');

    $sql->execute([
      $params['cpf'],
      $params['email'],
      $access_token,
      $params['password']
    ]);

    $registred = $this->db->prepare("SELECT access_token FROM user WHERE cpf = '" . $cpf . "'");
    $registred->execute();
    $registred = $registred->fetch();

    return array('status' => 201, 'response' => $registred);
  }






  /**
  * change password for user
  *
  * @param $new_password: String
  * @return array(response, status code)
  *
  **/

  public function changePassword($new_password) {
    $Error = new Error();

    if (is_null($new_password) || empty($new_password) || strlen($new_password) < 8) {
      return array('status' => 400, 'response' => $Error->getMessage('INVALID_PASSWORD'));
    }

    $new_password =  password_hash($new_password, PASSWORD_DEFAULT);

    $sql = $this->db->prepare('UPDATE user SET password = ? WHERE id = ?');
    $sql->execute([$new_password, $this->id]);

    return array('status' => 200, 'response' => $Error->getMessage('PASSWORD_CHANGED_SUCCESSFULLY'));
  }




  /**
  * Return login data
  *
  * @param $params: (login, password) or
  * @var $params['login'] = cpf | email
  * @var $params['password'] = user authentication
  *
  * @return array(response, status code)
  *
  **/

  public function login($params) {

    $Validator = new Validator($this->db);
    $Error = new Error();

    if (!isset($params['login'])) {
      return array('status' => 400, 'response' => $Error->getMessage('MISSING_LOGIN'));
    }

    if (!isset($params['password'])) {
      return array('status' => 400, 'response' => $Error->getMessage('MISSING_PASSWORD'));
    }

    if (!filter_var($params['login'], FILTER_VALIDATE_EMAIL)) {
      return array('status' => 400, 'response' => $Error->getMessage('INVALID_LOGIN'));
    }

    $password = password_hash($params['password'], PASSWORD_DEFAULT);

    $user = $this->db->prepare("SELECT access_token, password FROM user WHERE email = '" . $params['login'] . "'");
    $user->execute();
    $user = $user->fetch();

    if ($user && password_verify($params['password'], $user['password'])) {
      $logged = $this->db->prepare("SELECT access_token FROM user WHERE email = '" . $params['login'] . "'");
      $logged->execute();
      $logged = $logged->fetch();

      return array('status' => 200, 'response' => $logged);
    } else {
      return array('status' => 400, 'response' => $Error->getMessage('NOT_AUTHENTICATED'));
    }
  }





  /**
  * Get CPF for a instance of user
  *
  * @return $id: int
  *
  **/

  public function getEmail() {
    return $this->email;
  }
}