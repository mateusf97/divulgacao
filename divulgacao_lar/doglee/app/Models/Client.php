<?php

namespace Client;

use \Output\Output;
use \Validator\Validator;
use \Utils\Utils;
use \Error\Error;

class Client {
  private $db;
  private $id;
  private $username;
  private $email;
  private $cpf;
  private $token;

  public function __construct($db, $token = false) {
    $this->db = $db;

    if ($token) {
      $token = (explode(':', $token[0]))[1];

      $client = $this->db->prepare("SELECT * FROM client WHERE access_token = '" . $token . "'");
      $client->execute();
      $client = $client->fetch();

      $this->id = $client['id'];
      $this->username = $client['username'];
      $this->email = $client['email'];
      $this->cpf = $client['cpf'];
      $this->token = $token;
    }
  }






  /**
  * Set new id for instance of client
  *
  * @param $id: int
  *
  **/

  public function setId($id) {
    $this->id = $id;
  }





  /**
  * Get id for a instance of client
  *
  * @return $id: int
  *
  **/

  public function getId() {
    return $this->id;
  }






  /**
  * Get CPF for a instance of client
  *
  * @return $id: int
  *
  **/

  public function getCpf() {
    return $this->cpf;
  }




  /**
  * change password for client
  *
  * @param $params: String => password
  * @return array(response, status code)
  *
  **/

  public function changePassword($params) {
    $Validator = new Validator($this->db);
    $Error = new Error();

    $validations = array(
      "new_password" => array(
        "empty" => false,
        "min_length" => PASSWORD_MIN_LENGTH,
        "required" => true
      )
    );

    $validationError = $Validator->validateFields($params, $validations);

    if ($validationError) {
      return array('status' => 400, 'response' => $validationError);
    }

    $params['new_password'] =  password_hash($params['new_password'], PASSWORD_DEFAULT);

    $sql = $this->db->prepare('UPDATE client SET password = ? WHERE id = ?');
    $sql->execute([$params['new_password'], $this->id]);

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

    $validations = array(
      "login" => array(
        "empty" => false,
        "required" => true
      ),

      "password" => array(
        "min_length" => PASSWORD_MIN_LENGTH,
        "empty" => false,
        "required" => true,
      )
    );

    $validationError = $Validator->validateFields($params, $validations);

    if ($validationError) {
      return array('status' => 400, 'response' => $validationError);
    }

    if (preg_match('/^\d+$/', $params['login'])) {
      if (strlen($params['login']) == CPF_LENGTH) {
        $login = $this->db->prepare("SELECT email FROM client WHERE cpf = '" . $params['login'] . "'");
        $login->execute();
        $login = $login->fetch()['email'];

        $params['login'] = $login;
      } else {
        return array('status' => 400, 'response' => $Error->getMessage('INVALID_CPF'));
      }
    }

    $validations = array(
      "login" => array(
        "required" => false,
        "empty" => true,
        "custom_validation" => "email"
      )
    );

    $validationError = $Validator->validateFields($params, $validations);

    if ($validationError) {
      return array('status' => 400, 'response' => $validationError);
    }

    $password = password_hash($params['password'], PASSWORD_DEFAULT);

    $user = $this->db->prepare("SELECT access_token, password FROM client WHERE email = '" . $params['login'] . "'");
    $user->execute();
    $user = $user->fetch();

    if ($user && password_verify($params['password'], $user['password'])) {
      $logged = $this->db->prepare("SELECT access_token, username FROM client WHERE email = '" . $params['login'] . "'");
      $logged->execute();
      $logged = $logged->fetch();
      $logged['type'] = "CLIENT";

      return array('status' => 200, 'response' => $logged);
    } else {
      return array('status' => 400, 'response' => $Error->getMessage('NOT_AUTHENTICATED'));
    }
  }





  /**
  * Create a client in database
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

    $validations = array(
      "name" => array(
        "required" => true,
        "empty" => false,
        "min_words" => 2
      ),

      "password" => array(
        "min_length" => PASSWORD_MIN_LENGTH,
        "empty" => false,
        "required" => true,
      ),

      "cpf" => array(
        "required" => false,
        "empty" => false,
        "custom_validation" => "cpf",
      ),

      "email" => array(
        "required" => true,
        "empty" => false,
        "custom_validation" => "email"
      )
    );

    $validationError = $Validator->validateFields($params, $validations);

    if ($validationError) {
      return array('status' => 400, 'response' => $validationError);
    }

    $Utils->extractNumbers($params['cpf']);
    $cpf = $params['cpf'];

    // Check if user exist
    $user_cpf = $this->db->prepare("SELECT cpf FROM client WHERE cpf = ?");
    $user_cpf->execute([$cpf]);
    $user_cpf = $user_cpf->fetchAll();

    if ($user_cpf) {
      return array('status' => 409, 'response' => $Error->getMessage('CPF_ALREADY_EXISTS'));
    }

    $user_email = $this->db->prepare("SELECT email FROM client WHERE email = ?");
    $user_email->execute([$params['email']]);
    $user_email = $user_email->fetchAll();

    if ($user_email) {
      return array('status' => 409, 'response' => $Error->getMessage('EMAIL_ALREADY_EXISTS'));
    }

    $params['username'] = $Utils->generateClientUsername($params['name']);

    $access_token = md5(uniqid($cpf, true));

    $params['password'] =  password_hash($params['password'], PASSWORD_DEFAULT);

    $sql = $this->db->prepare('INSERT INTO client (username, email, cpf, access_token, password) VALUES (?, ?, ?, ?, ?)');

    $sql->execute([
      $params['username'],
      $params['email'],
      $cpf,
      $access_token,
      $params['password']
    ]);

    $sql_update = $this->db->prepare('UPDATE client_private_data SET name = ? WHERE username = ?');

    $sql_update->execute([
      $params['name'],
      $params['username']
    ]);

    $registred = $this->db->prepare("SELECT access_token, username FROM client WHERE cpf = '" . $cpf . "'");
    $registred->execute();
    $registred = $registred->fetch();
    $registred['type'] = "CLIENT";

    return array('status' => 201, 'response' => $registred);
  }







  /**
  * Save the private data for a client.
  *
  * @param $params: array(name, phone_1, phone_2, profile_picture)
  *
  * @return array(response, status code)
  *
  **/

  public function savePrivateData($params) {
    $Validator = new Validator($this->db);
    $Utils = new Utils($this->db);
    $Error = new Error();

    $validations = array(
      "name" => array(
        "required" => true,
        "empty" => false,
        "min_words" => 2
      ),

      "phone_1" => array(
        "type" => "phone",
        "empty" => false,
        "required" => true
      ),

      "phone_2" => array(
        "type" => "phone",
        "empty" => false,
        "required" => true
      ),

      "profile_picture" => array(
        "empty" => false,
        "required" => true
      )
    );

    $validationError = $Validator->validateFields($params, $validations);

    if ($validationError) {
      return array('status' => 400, 'response' => $validationError);
    }

    $Utils->extractNumbers($params['phone_1']);
    $Utils->extractNumbers($params['phone_2']);

    $sql = $this->db->prepare('UPDATE client_private_data SET
      phone_1 = ?,
      phone_2 = ?,
      name = ?,
      profile_picture = ?
      WHERE id = ' . $this->id);

    $sql->execute([
      $params['phone_1'],
      $params['phone_2'],
      $params['name'],
      $params['profile_picture']
    ]);

    return array('status' => 204, 'response' => null);
  }









  /**
  * Return data for a client.
  *
  * @return array(client data)
  *
  **/

  public function getClientData() {
    $profile = $this->db->prepare("SELECT * FROM client_private_data WHERE id = ?");
    $profile->execute([$this->id]);
    $profile = $profile->fetch();

    unset($profile['id']);

    return $profile;
  }








  /**
  * Return data for a client.
  *
  * @param $userId: int
  * @return array(response, status code)
  *
  **/

  public function getClientPublicData($userId) {
    $profile = $this->db->prepare("SELECT name AS profile_title, username, profile_picture FROM client_private_data WHERE id = '" . $userId . "'");
    $profile->execute();
    $profile = $profile->fetch();

    return $profile;
  }









  /**
  * Return user id in database based on username
  *
  * @param $username: String
  *
  * @return id: int
  *
  **/

  public  function getIdByUsername($username) {
    $userId = $this->db->prepare("SELECT id FROM client WHERE username = ?");
    $userId->execute([$username]);
    $userId = $userId->fetch()['id'];

    return $userId;
  }







  /**
  * Update data from specific dog
  *
  * @param $params: (zip, number, complement,
  * district, city, state, picture_proof_of_address)
  * @param $dogId: int
  *
  * @return array(response, status code)
  *
  **/

  public function updateDog($dogId, $params) {
    $Validator = new Validator($this->db);
    $Utils = new Utils($this->db);
    $Error = new Error();

    /**
    * Validates that the pet being updated
    * belongs to the authenticated user
    */

    if (!$Validator->validateClientDogOwner($dogId, $this->id)) {
      return array('status' => 401, 'response' => $Error->getMessage('UNAUTHORIZED'));
    }

    $validations = array(
      "name" => array(
        "required" => true,
        "empty" => false,
        "custom_validation" => "pet_info_verbose",
      ),

      "breed" => array(
        "required" => true,
        "empty" => false,
        "custom_validation" => "pet_info_verbose",
      ),

      "size" => array(
        "required" => true,
        "empty" => false,
        "custom_validation" => "size",
      ),

      "age" => array(
        "required" => true,
        "empty" => false,
        "type" => "numeric",
        "max_value" => 100,
      ),

      "castrated" => array(
        "required" => true,
        "empty" => false,
        "type" => "bool",
      ),

      "gender" => array(
        "required" => true,
        "empty" => false,
        "custom_validation" => "gender",
      ),

      "docile" => array(
        "required" => true,
        "empty" => false,
        "type" => "bool",
      ),

      "vaccine_v8_v10" => array(
        "required" => true,
        "empty" => false,
        "type" => "bool",
      ),

      "vaccine_rabies" => array(
        "empty" => false,
        "required" => true,
        "type" => "bool",
      ),

      "vaccine_leishmaniasis" => array(
        "required" => true,
        "empty" => false,
        "type" => "bool",
      ),

      "about_dog" => array(
        "required" => true,
        "empty" => false,
        "max_length" => 500,
      ),

      "vaccine_card_photo" => array(
        "required" => true,
        "empty" => true,
      ),

      "photo" => array(
        "required" => true,
        "empty" => false,
      ),
    );

    $validationError = $Validator->validateDogFields($params, $validations);

    if ($validationError) {
      return array('status' => 400, 'response' => $validationError);
    }

    $Utils->convertToMysqlBoolean($params['castrated']);
    $Utils->convertToMysqlBoolean($params['docile']);
    $Utils->convertToMysqlBoolean($params['vaccine_v8_v10']);
    $Utils->convertToMysqlBoolean($params['vaccine_rabies']);
    $Utils->convertToMysqlBoolean($params['vaccine_leishmaniasis']);

    $sql = $this->db->prepare('UPDATE client_dogs SET
     name = ?,
     breed = ?,
     size = ?,
     age = ?,
     castrated = ?,
     gender = ?,
     docile = ?,
     vaccine_v8_v10 = ?,
     vaccine_rabies = ?,
     vaccine_leishmaniasis = ?,
     about_dog = ?,
     vaccine_card_photo = ?,
     photo = ?
     WHERE id = ' . $dogId);

    $sql->execute([
      $params['name'],
      $params['breed'],
      $params['size'],
      $params['age'],
      $params['castrated'],
      $params['gender'],
      $params['docile'],
      $params['vaccine_v8_v10'],
      $params['vaccine_rabies'],
      $params['vaccine_leishmaniasis'],
      $params['about_dog'],
      $params['vaccine_card_photo'],
      $params['photo'],
    ]);

    return array('status' => 204, 'response' => null);
  }







  /**
  * Return dog data from specific id
  *
  * @param $params: array(name, breed, size, age, castrated, gender, docile,
  *  vaccine_v8_v10, vaccine_rabies, vaccine_leishmaniasis,
  *  about_dog, vaccine_card_photo, photo)
  *
  * @return array(response, status code)
  *
  **/

  public function createDog($params) {
    $Validator = new Validator($this->db);
    $Utils = new Utils($this->db);
    $Error = new Error();

    $validations = array(
      "name" => array(
        "required" => true,
        "empty" => false,
        "custom_validation" => "pet_info_verbose",
      ),

      "breed" => array(
        "required" => true,
        "empty" => false,
        "custom_validation" => "pet_info_verbose",
      ),

      "size" => array(
        "required" => true,
        "empty" => false,
        "custom_validation" => "size",
      ),

      "age" => array(
        "required" => true,
        "empty" => false,
        "type" => "numeric",
        "max_value" => 100,
      ),

      "castrated" => array(
        "required" => true,
        "empty" => false,
        "type" => "bool",
      ),

      "gender" => array(
        "required" => true,
        "empty" => false,
        "custom_validation" => "gender",
      ),

      "docile" => array(
        "required" => true,
        "empty" => false,
        "type" => "bool",
      ),

      "vaccine_v8_v10" => array(
        "required" => true,
        "empty" => false,
        "type" => "bool",
      ),

      "vaccine_rabies" => array(
        "empty" => false,
        "required" => true,
        "type" => "bool",
      ),

      "vaccine_leishmaniasis" => array(
        "required" => true,
        "empty" => false,
        "type" => "bool",
      ),

      "about_dog" => array(
        "required" => true,
        "empty" => false,
        "max_length" => 500,
      ),

      "vaccine_card_photo" => array(
        "required" => true,
        "empty" => true,
      ),

      "photo" => array(
        "required" => true,
        "empty" => false,
      ),
    );

    $validationError = $Validator->validateDogFields($params, $validations);

    if ($validationError) {
      return array('status' => 400, 'response' => $validationError);
    }

    $Utils->convertToMysqlBoolean($params['castrated']);
    $Utils->convertToMysqlBoolean($params['docile']);
    $Utils->convertToMysqlBoolean($params['vaccine_v8_v10']);
    $Utils->convertToMysqlBoolean($params['vaccine_rabies']);
    $Utils->convertToMysqlBoolean($params['vaccine_leishmaniasis']);

    $sql = $this->db->prepare('INSERT INTO client_dogs SET
     user_id = ?,
     username = ?,
     name = ?,
     breed = ?,
     size = ?,
     age = ?,
     castrated = ?,
     gender = ?,
     docile = ?,
     vaccine_v8_v10 = ?,
     vaccine_rabies = ?,
     vaccine_leishmaniasis = ?,
     about_dog = ?,
     vaccine_card_photo = ?,
     photo = ?');

    $sql->execute([
      $this->id,
      $this->username,
      $params['name'],
      $params['breed'],
      $params['size'],
      $params['age'],
      $params['castrated'],
      $params['gender'],
      $params['docile'],
      $params['vaccine_v8_v10'],
      $params['vaccine_rabies'],
      $params['vaccine_leishmaniasis'],
      $params['about_dog'],
      $params['vaccine_card_photo'],
      $params['photo'],
    ]);

    return array('status' => 201, 'response' => null);
  }







  /**
  *
  * @param $dogId: int
  *
  * @return array(response, status code)
  *
  **/

  public  function deleteDog($dogId) {
    $Validator = new Validator($this->db);
    $Error = new Error();

    /**
    * Validates that the pet being updated
    * belongs to the authenticated user
    */

    $isOwner = $Validator->validateClientDogOwner($dogId, $this->id);

    if (!$isOwner) {
      return array('status' => 401, 'response' => $Error->getMessage('UNAUTHORIZED'));
    }

    $dog = $this->db->prepare("DELETE FROM client_dogs WHERE id = ? AND user_id = ?");
    $dog->execute([$dogId, $this->id]);

    return array('status' => 204, 'response' => 'DELETED');
  }







  /**
  * Return dog data from specific id
  *
  * @param $dogId: int
  *
  * @return array(dog data)
  *
  **/

  public  function getDog($dogId) {
    $Error = new Error();

    $dog = $this->db->prepare("SELECT count(*) as total FROM client_dogs WHERE id = ?");
    $dog->execute([$dogId]);
    $dog = $dog->fetch()['total'];

    if (!$dog) {
      return array('status' => 410, 'response' => $Error->getMessage('INVALID_DOG'));
    }

    $dog = $this->db->prepare("SELECT * FROM client_dogs WHERE id = ?");
    $dog->execute([$dogId]);
    $dog = $dog->fetch();

    $Utils = new Utils($this->db);

    $Utils->revertMysqlBooleanToPhp($dog['castrated']);
    $Utils->revertMysqlBooleanToPhp($dog['docile']);
    $Utils->revertMysqlBooleanToPhp($dog['vaccine_v8_v10']);
    $Utils->revertMysqlBooleanToPhp($dog['vaccine_rabies']);
    $Utils->revertMysqlBooleanToPhp($dog['vaccine_leishmaniasis']);

    return array('status' => 200, 'response' => $dog);
  }






  /**
  * Return list of dogs from specific client user
  *
  * @param $userId: int
  *
  * @return array(dog list)
  *
  **/

  public  function getDogs($userId) {
    $dogs = $this->db->prepare("SELECT id, name, photo FROM client_dogs WHERE user_id = ?");
    $dogs->execute([$userId]);
    $dogs = $dogs->fetchAll();

    return $dogs;
  }






  /**
  * Set new favorite
  *
  * @param $username: String
  *
  * @return array(response, status code)
  *
  **/

  public  function addFavorite($username) {
    $Error = new Error();

    $providerId = $this->db->prepare("SELECT id FROM provider WHERE username = ?");
    $providerId->execute([$username]);
    $providerId = $providerId->fetch()['id'];

    if (empty($providerId) || is_null($providerId) || !$providerId) {
      return array('status' => 400, 'response' => $Error->getMessage('INVALID_USERNAME'));
    }

    $constraintCheck = $this->db->prepare("SELECT id FROM client_favorite WHERE client_id = ? AND provider_id = ?");
    $constraintCheck->execute([$this->id, $providerId]);
    $constraintCheck = $constraintCheck->fetch()['id'];

    if ($constraintCheck) {
      return array('status' => 409, 'response' => 'ALREADY_EXISTS');
    }

    $favorite = $this->db->prepare("INSERT INTO client_favorite SET client_id = ?, provider_id = ?");
    $favorite->execute([$this->id, $providerId]);

    return array('status' => 201, 'response' => null);
  }








  /**
  *
  * @param $username: String
  *
  * @return array(response, status code)
  *
  **/

  public  function deleteFavorite($username) {
    $Error = new Error();

    $providerId = $this->db->prepare("SELECT id FROM provider WHERE username = ?");
    $providerId->execute([$username]);
    $providerId = $providerId->fetch()['id'];

    if (empty($providerId) || is_null($providerId) || !$providerId) {
      return array('status' => 400, 'response' => $Error->getMessage('INVALID_USERNAME'));
    }

    $constraintCheck = $this->db->prepare("SELECT id FROM client_favorite WHERE client_id = ? AND provider_id = ?");
    $constraintCheck->execute([$this->id, $providerId]);
    $constraintCheck = $constraintCheck->fetch()['id'];

    if (empty($constraintCheck ) || is_null($constraintCheck) || !$constraintCheck) {
      return array('status' => 401, 'response' =>  $Error->getMessage('UNAUTHORIZED'));
    }

    $favorite = $this->db->prepare("DELETE FROM client_favorite WHERE id = ?");
    $favorite->execute([$constraintCheck]);

    return array('status' => 200, 'response' => 'DELETED');
  }








  /**
  *
  * @return array(response, status code)
  *
  **/

  public  function getFavorites() {
    $favorites = $this->db->prepare("SELECT * FROM client_favorite WHERE client_id = ?");
    $favorites->execute([$this->id]);
    $favorites = $favorites->fetchAll();

    return array('status' => 200, 'response' => $favorites);
  }

}
