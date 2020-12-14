<?php

namespace Products;

use \Output\Output;
use \Validator\Validator;
use \Utils\Utils;
use \Error\Error;

class Products {
  private $db;


  public function __construct($db, $token = false) {
    $this->db = $db;
  }





  /**
  * Create a Products in database
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

    $params['category_books'] = ($params['category_books'] == "false") ? true : false;
    $params['category_courses'] = ($params['category_courses'] == "false") ? true : false;
    $params['category_subscriptions'] = ($params['category_subscriptions'] == "false") ? true : false;
    $params['category_free'] = ($params['category_free'] == "false") ? true : false;
    $params['is_important'] = ($params['is_important'] == "false") ? true : false;

    $sql = $this->db->prepare('INSERT INTO product SET
      image_url = ?,
      title = ?,
      description = ?,
      price = ?,
      parcel = ?,
      category_books = ?,
      category_courses = ?,
      category_subscriptions = ?,
      category_free = ?,
      is_important = ?
    ');

    $sql->execute([
      $params['image_url'],
      $params['title'],
      $params['description'],
      $params['price'],
      $params['parcel'],
      (int) $params['category_books'] == "true" ? 1 : 0,
      (int) $params['category_courses'] == "true" ? 1 : 0,
      (int) $params['category_subscriptions'] == "true" ? 1 : 0,
      (int) $params['category_free'] == "true" ? 1 : 0,
      (int) $params['is_important'] == "true" ? 1 : 0
    ]);

    $id = $this->db->prepare("SELECT id FROM product ORDER BY id DESC LIMIT 1");
    $id->execute();
    $id = $id->fetch()['id'];

    return array('status' => 201, 'response' => $id);
  }






  /**
  * change password for Products
  *
  * @param $new_password: String
  * @return array(response, status code)
  *
  **/

  public function list() {

    $sql = $this->db->prepare('SELECT * FROM `product` WHERE 1');
    $sql->execute();
    $products = $sql->fetchAll();

    return array('status' => 200, 'response' => $products);
  }




  /**
  * Return login data
  *
  * @param $params: (login, password) or
  * @var $params['login'] = cpf | email
  * @var $params['password'] = Products authentication
  *
  * @return array(response, status code)
  *
  **/

  public function login($params) {

    $Validator = new Validator($this->db);
    $Error = new Error();
    $Utils = new Utils($this->db);

    if (!isset($params['login'])) {
      return array('status' => 400, 'response' => $Error->getMessage('MISSING_LOGIN'));
    }

    $Utils->extractNumbers($params['login']);
    $cpf = $params['login'];

    if (!$Validator->validateCpf($cpf)) {
      return array('status' => 400, 'response' => $Error->getMessage('INVALID_CPF'));
    }

    if (!isset($params['password']) || empty($params['password'])) {
      return array('status' => 400, 'response' => $Error->getMessage('MISSING_PASSWORD'));
    }

    $password = password_hash($params['password'], PASSWORD_DEFAULT);

    $Products = $this->db->prepare("SELECT access_token, password FROM Products WHERE cpf = '" . $params['login'] . "'");
    $Products->execute();
    $Products = $Products->fetch();

    if ($Products && password_verify($params['password'], $Products['password'])) {
      $logged = $this->db->prepare("SELECT access_token FROM Products WHERE cpf = '" . $params['login'] . "'");
      $logged->execute();
      $logged = $logged->fetch();

      return array('status' => 200, 'response' => $logged);
    } else {
      return array('status' => 400, 'response' => $Error->getMessage('NOT_AUTHENTICATED'));
    }
  }





  /**
  * Get CPF for a instance of Products
  *
  * @return $id: int
  *
  **/

  public function getEmail() {
    return $this->email;
  }
}