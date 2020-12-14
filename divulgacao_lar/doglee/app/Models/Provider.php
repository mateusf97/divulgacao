<?php

namespace Provider;

use \Output\Output;
use \Validator\Validator;
use \Utils\Utils;
use \Error\Error;

class Provider {
  private $db;
  private $id;
  private $username;
  private $email;
  private $cpf;
  private $is_host;
  private $is_dogwalker;
  private $token;

  public function __construct($db, $token = false) {
    $this->db = $db;

    if ($token) {
      $token = (explode(':', $token[0]))[1];

      $provider = $this->db->prepare("SELECT * FROM provider WHERE access_token = '" . $token . "'");
      $provider->execute();
      $provider = $provider->fetch();

      $this->id = $provider['id'];
      $this->username = $provider['username'];
      $this->email = $provider['email'];
      $this->cpf = $provider['cpf'];
      $this->is_host = $provider['is_host'];
      $this->is_dogwalker = $provider['is_dogwalker'];
      $this->token = $token;
    }
  }






  /**
  * Set new id for instance of provider
  *
  * @param $id: int
  *
  **/

  public function setId($id) {
    $this->id = $id;
  }





  /**
  * Get id for a instance of provider
  *
  * @return $id: int
  *
  **/

  public function getId() {
    return $this->id;
  }




  /**
  * Get CPF for a instance of provider
  *
  * @return $id: int
  *
  **/

  public function getCpf() {
    return $this->cpf;
  }







  /**
  * Get all Provider situations (status)
  *
  * @return array(response, status code)
  *
  **/

  public function getSituations() {
    $sql = $this->db->prepare('SELECT ppp.private_profile_status, ppa.address_status, ppu.public_profile_status FROM provider_private_profile ppp
                               INNER JOIN provider_public_profile ppu ON ppp.username = ppu.username
                               INNER JOIN provider_profile_address ppa ON ppp.username = ppa.username
                               WHERE ppp.username = ?');
    $sql->execute([$this->username]);
    $result = $sql->fetch();


    if ($this->is_host) {
      $sql = $this->db->prepare('SELECT host_status FROM provider_host_data WHERE username = ?');
      $sql->execute([$this->username]);
      $result['host_status'] = $sql->fetch()['host_status'];
    }

    if ($this->is_dogwalker) {
      $sql = $this->db->prepare('SELECT dogwalker_status FROM provider_dogwalker_data WHERE username = ?');
      $sql->execute([$this->username]);
      $result['dogwalker_status'] = $sql->fetch()['dogwalker_status'];
    }


    return array('status' => 200, 'response' => $result);
  }





  /**
  * change password for Provider
  *
  * @param $new_password: String
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

    $sql = $this->db->prepare('UPDATE provider SET password = ? WHERE id = ?');
    $sql->execute([$params['new_password'], $this->id]);

    return array('status' => 200, 'response' => null);
  }






  /**
  * Update data from specific dog
  *
  * @param $dogId: int
  * @param $params: (zip, number, complement,
  * district, city, state, picture_proof_of_address)
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

    if (!$Validator->validateProviderDogOwner($dogId, $this->id)) {
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

    $sql = $this->db->prepare('UPDATE provider_dogs SET
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
  * Create new dog for specific user
  *
  * @param $params: array(name, breed, size, age, castrated,
  * gender, docile, vaccine_v8_v10, vaccine_rabies,
  * vaccine_leishmaniasis, about_dog, vaccine_card_photo, photo)
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

    $sql = $this->db->prepare('INSERT INTO provider_dogs SET
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
  * Delete specific dog based on id
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

    $isOwner = $Validator->validateProviderDogOwner($dogId, $this->id);

    if (!$isOwner) {
      return array('status' => 401, 'response' => $Error->getMessage('UNAUTHORIZED'));
    }

    $dog = $this->db->prepare("DELETE FROM provider_dogs WHERE id = ? AND user_id = ?");
    $dog->execute([$dogId, $this->id]);

    return array('status' => 204, 'response' => 'DELETED');
  }







  /**
  * Return dog data from specific id
  *
  * @param $dogId: int
  *
  * @return array(dogData)
  *
  **/

  public  function getDog($dogId) {
    $Error = new Error();

    $dog = $this->db->prepare("SELECT count(*) as total FROM provider_dogs WHERE id = ?");
    $dog->execute([$dogId]);
    $dog = $dog->fetch()['total'];

    if (!$dog) {
      return array('status' => 410, 'response' => $Error->getMessage('INVALID_DOG'));
    }

    $dog = $this->db->prepare("SELECT * FROM provider_dogs WHERE id = ?");
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
  * Return list of dogs from specific user
  *
  * @param $userId: int
  *
  * @return Array(listOfDogs)
  *
  **/

  public  function getDogs($userId) {
    $dogs = $this->db->prepare("SELECT id, name, breed, age, photo FROM provider_dogs WHERE user_id = ?");
    $dogs->execute([$userId]);
    $dogs = $dogs->fetchAll();

    return $dogs;
  }






  /**
  * Return username if valid or if already in use
  *
  * @param $username: String
  *
  * @return boolean
  *
  **/

  public  function checkProviderAvailable($username) {
    $alreadyExistsProvider = $this->db->prepare("SELECT username FROM provider WHERE username = ? AND id <> ?");
    $alreadyExistsProvider->execute([$username, $this->id]);
    $alreadyExistsProvider = $alreadyExistsProvider->fetchAll();

    if (!count($alreadyExistsProvider)) {
      return true;
    }

    return false;
  }








  /**
  * Return user list based on filtered cep
  *
  * @param $searchCep: int
  *
  * @return array(UserIdList)
  *
  **/

  private function getProvidersByCep($searchCep) {
    $userId = $this->db->prepare("SELECT id FROM provider_profile_address WHERE zip LIKE '" . $searchCep . "%'");
    $userId->execute();
    return $userId->fetchAll();
  }







  /**
  * Return provider list based on filters
  *
  * @param $filter: array(area, cep, injectable, oral, page,
  * page_size, pets_in_home, price, provider, size, uncastred)
  *
  * @return array(Users)
  *
  **/

  public  function getProviderList($filter) {
    $Error = new Error();
    $Validator = new Validator($this->db);
    $Utils = new Utils($this->db);

    $validations = array(
      "page_size" => array(
        "required" => true,
        "empty" => false,
        "type" => "postitive_integer",
      ),

      "page" => array(
        "required" => true,
        "empty" => false,
        "type" => "postitive_integer",
      ),

      "provider" => array(
        "required" => true,
        "empty" => false,
      ),
    );

    $validationError = $Validator->validateFields($filter, $validations);

    if ($validationError) {
      return array('status' => 400, 'response' => $validationError);
    }

    if (!$Validator->validateProvider($filter['provider'])) {
      return array('status' => 400, 'response' => $Error->getMessage('INVALID_PROVIDER'));
    }

    if ($filter['provider'] === "DOGWALKER") {
      $AllProvidersIds = $this->db->prepare("SELECT id FROM provider WHERE is_dogwalker = 1");
    }

    if ($filter['provider'] === "HOST") {
      $AllProvidersIds = $this->db->prepare("SELECT id FROM provider WHERE is_host = 1");
    }

    $AllProvidersIds->execute();
    $AllProvidersIds = $AllProvidersIds->fetchAll();

    $ids = array();

    foreach ($AllProvidersIds as $key => $value) {
      array_push($ids, $value['id']);
    }

    if (isset($filter['cep'])) {
      if (!$Validator->validateCep($filter['cep'])) {
        return array('status' => 400, 'response' => $Error->getMessage('INVALID_CEP'));
      }

      $searchCep = substr($filter['cep'], 0, 3);

      /**
      * @var $searchCep at this point has precision 3: high precision
      */

      $userId = $this->getProvidersByCep($searchCep);

      if (count($userId) < 20) {
        $searchCep = substr($filter['cep'], 0, 2);
        $userId = $this->getProvidersByCep($searchCep);

        /**
        * @var $searchCep at this point has precision 2: medium precision
        */
      }

      if (count($userId) < 20) {
        $searchCep = substr($filter['cep'], 0, 1);
        $userId = $this->getProvidersByCep($searchCep);
      }

      $filterIds = array();

      foreach ($userId as $key => $value) {
        array_push($filterIds, $value['id']);
      }

      $ids = array_intersect($ids, $filterIds);
    }

    if (isset($filter['size'])) {
      if (!$Validator->validateDogSize($filter['size'])) {
        return array('status' => 400, 'response' => $Error->getMessage('INVALID_SIZE'));
      }

      $Utils->convertDogSizeField($filter['size']);

      if ($filter['provider'] === "DOGWALKER") {
        $userId = $this->db->prepare("SELECT id FROM provider_dogwalker_data WHERE accept_dog_size_" . $filter['size'] . " = 1");
      }

      if ($filter['provider'] === "HOST") {
        $userId = $this->db->prepare("SELECT id FROM provider_host_data WHERE accept_dog_size_" . $filter['size'] . " = 1");
      }

      $userId->execute();
      $userId = $userId->fetchAll();

      $filterIds = array();

      foreach ($userId as $key => $value) {
        array_push($filterIds, $value['id']);
      }

      $ids = array_intersect($ids, $filterIds);
    }

    if (isset($filter['area']) && $filter['provider'] === "HOST") {
      if (!$Validator->validateExternalAreaSize($filter['area'])) {
        return array('status' => 400, 'response' => $Error->getMessage('INVALID_AREA'));
      }

      $userId = $this->db->prepare("SELECT id FROM provider_host_data WHERE external_area_size = ?");

      $userId->execute([$filter['area']]);
      $userId = $userId->fetchAll();

      $filterIds = array();

      foreach ($userId as $key => $value) {
        array_push($filterIds, $value['id']);
      }

      $ids = array_intersect($ids, $filterIds);
    }

    $prices = array();

    if (isset($filter['price'])) {
      if (!is_numeric($filter['price'])) {
        return array('status' => 400, 'response' => $Error->getMessage('INVALID_PRICE'));
      }

      $Utils->convertToNumeric($filter['price']);

      if ($filter['provider'] === "HOST") {
        $userId = $this->db->prepare("SELECT id, price FROM provider_host_data WHERE price >= ?");
        $userId->execute([$filter['price']]);
      }

      if ($filter['provider'] === "DOGWALKER") {
        $userId = $this->db->prepare("SELECT id, price_30, price_60 FROM provider_dogwalker_data WHERE price_30 >= ? OR price_60 >= ?");
        $userId->execute([$filter['price'], $filter['price']]);
      }

      $userId = $userId->fetchAll();

      $filterIds = array();


      if ($filter['provider'] === "DOGWALKER") {
        foreach ($userId as $key => $value) {
          array_push($filterIds, $value['id']);

          $Utils->convertToNumeric($value['price_30']);
          $Utils->convertToNumeric($value['price_60']);

          $prices[$value['id']] = array(
            'price_30' => $value['price_30'],
            'price_60' => $value['price_60']
          );

        }
      }

      if ($filter['provider'] === "HOST") {
        foreach ($userId as $key => $value) {
          array_push($filterIds, $value['id']);

          $Utils->convertToNumeric($value['price']);

          $prices[$value['id']] = array(
            'price' => $value['price']
          );
        }
      }

      $ids = array_intersect($ids, $filterIds);
    }

    if (isset($filter['uncastred'])  && $filter['provider'] === "HOST") {
      if ($filter['uncastred'] === "false") {
        $filter['uncastred'] = false;
      }

      $uncastred = (boolean) $filter['uncastred'];

      $userId = $this->db->prepare("SELECT id FROM provider_host_data WHERE
        preferences_uncastrated_male_dog = ? OR
        preferences_uncastrated_female_dog = ?");

      $userId->execute([$uncastred, $uncastred]);
      $userId = $userId->fetchAll();

      $filterIds = array();

      foreach ($userId as $key => $value) {
        array_push($filterIds, $value['id']);
      }

      $ids = array_intersect($ids, $filterIds);
    }

    // TODO BUG pets_in_home
    // if (isset($filter['pets_in_home']) AND $filter['provider'] === "HOST") {
    //   if ($filter['pets_in_home'] === "false") {
    //     $filter['pets_in_home'] = false;
    //   }
    //
    //   $pets_in_home = (boolean) $filter['pets_in_home'];
    //
    //   $userId = $this->db->prepare("SELECT user_id FROM provider_dogs");
    //
    //   $userId->execute([$pets_in_home, $pets_in_home]);
    //   $userId = $userId->fetchAll();
    //
    //   $filterIds = array();
    //
    //   foreach ($userId as $key => $value) {
    //     array_push($filterIds, $value['id']);
    //   }
    //
    //   $ids = array_intersect($ids, $filterIds);
    // }

    if (isset($filter['oral']) && $filter['provider'] === "HOST") {
      if ($filter['oral'] === "false") {
        $filter['oral'] = false;
      }

      $oral_experience = (boolean) $filter['oral'];

      $userId = $this->db->prepare("SELECT id FROM provider_public_profile WHERE profile_oral_experience = ?");

      $userId->execute([$oral_experience]);
      $userId = $userId->fetchAll();

      $filterIds = array();

      foreach ($userId as $key => $value) {
        array_push($filterIds, $value['id']);
      }

      $ids = array_intersect($ids, $filterIds);
    }

    if (isset($filter['oral']) && $filter['provider'] === "HOST") {
      if ($filter['oral'] === "false") {
        $filter['oral'] = false;
      }

      $oral_experience = (boolean) $filter['oral'];

      $userId = $this->db->prepare("SELECT id FROM provider_public_profile WHERE profile_oral_experience = ?");

      $userId->execute([$oral_experience]);
      $userId = $userId->fetchAll();

      $filterIds = array();

      foreach ($userId as $key => $value) {
        array_push($filterIds, $value['id']);
      }

      $ids = array_intersect($ids, $filterIds);
    }

    if (isset($filter['injectable']) && $filter['provider'] === "HOST") {
      if ($filter['injectable'] === "false") {
        $filter['injectable'] = false;
      }

      $injectable_experience = (boolean) $filter['injectable'];

      $userId = $this->db->prepare("SELECT id FROM provider_public_profile WHERE profile_injectable_medication = ?");

      $userId->execute([$injectable_experience]);
      $userId = $userId->fetchAll();

      $filterIds = array();

      foreach ($userId as $key => $value) {
        array_push($filterIds, $value['id']);
      }

      $ids = array_intersect($ids, $filterIds);
    }

    /**
    *
    * If the user does not decide to filter by price, the filter at this time
    *does not yet have information of the value (price) of that service
    *
    * Therefore, this SELECT query below is required to search for this information.
    *
    * This SELECT query must be at the end of the filter code.
    */

    if (!isset($filter['price']) && count($ids)) {
      $priceIds = implode(",", $ids);

      if ($filter['provider'] === "HOST") {
        $userId = $this->db->prepare("SELECT id, price FROM provider_host_data WHERE id IN (" . $priceIds . ") ORDER BY price");
      }

      if ($filter['provider'] === "DOGWALKER") {
        $userId = $this->db->prepare("SELECT id, price_30, price_60 FROM provider_dogwalker_data WHERE id IN (" . $priceIds . ") ORDER BY price_30");
      }

      $userId->execute();
      $userId = $userId->fetchAll();
      $filterIds = array();

      if ($filter['provider'] === "DOGWALKER") {
        foreach ($userId as $key => $value) {
          array_push($filterIds, $value['id']);

          $Utils->convertToNumeric($value['price_30']);
          $Utils->convertToNumeric($value['price_60']);

          $prices[$value['id']] = array(
            'price_30' => $value['price_30'],
            'price_60' => $value['price_60']
          );

        }
      }

      if ($filter['provider'] === "HOST") {
        foreach ($userId as $key => $value) {
          array_push($filterIds, $value['id']);

          $Utils->convertToNumeric($value['price']);

          $prices[$value['id']] = array(
            'price' => $value['price']
          );
        }
      }
    }

    $orderedIds = array();

    foreach ($prices as $key => $value) {
      array_push($orderedIds, $key);
    }

    $ids = $orderedIds;

    /**
    * page size and page items filter
    */

    $start = ($filter['page_size'] * $filter['page']) - $filter['page_size'];
    $end = $filter['page_size'] * $filter['page'];

    $filterIds = array();

    for ($i = $start; $i < $end; $i++) {
      if (isset($ids[$i])) {
        array_push($filterIds, $ids[$i]);
      } else {
        break;
      }
    }

    $ids = $filterIds;

    /**
    * @var ids now have the final list of ids to be fetched
    *
    */

    $ids = implode(",", $ids);

    if (!$ids || $ids == "" || $ids == array()) {
      return array('status' => 200, 'response' => array());
    }

    $profiles = $this->db->prepare("SELECT * FROM provider_public_profile WHERE id IN (" . $ids . ") ORDER BY FIELD(id, " . $ids . ");");
    $profiles->execute();
    $profiles = $profiles->fetchAll();

    $address = $this->db->prepare("SELECT state, district, city FROM provider_profile_address WHERE id IN (" . $ids . ") ORDER BY FIELD(id, " . $ids . ");");
    $address->execute();
    $address = $address->fetchAll();

    if ($filter['provider'] === "HOST") {
      $table = "provider_host_data";
    } elseif ($filter['provider'] === "DOGWALKER") {
      $table = "provider_dogwalker_data";
    }

    $days_of_week = $this->db->prepare("SELECT `days_of_week` FROM " . $table . " WHERE id IN (" . $ids . ") ORDER BY FIELD(id, " . $ids . ");");
    $days_of_week->execute();
    $days_of_week = $days_of_week->fetchAll();

    foreach ($profiles as $key => $profile) {
      $Utils->unserializeAndDecode($days_of_week[$key]['days_of_week']);

      $profiles[$key]['price'] = $prices[$profile['id']];
      $profiles[$key]['address'] = $address[$key];
      $profiles[$key]['days_of_week'] = $days_of_week[$key]['days_of_week'];

      unset($profile['id']);

      $Utils = new Utils($this->db);

      $Utils->revertMysqlBooleanToPhp($profile['profile_animal_care_income']);
      $Utils->revertMysqlBooleanToPhp($profile['profile_oral_experience']);
      $Utils->revertMysqlBooleanToPhp($profile['profile_elderly_pets']);
      $Utils->revertMysqlBooleanToPhp($profile['profile_first_aid']);
      $Utils->revertMysqlBooleanToPhp($profile['profile_injectable_medication']);
      $Utils->revertMysqlBooleanToPhp($profile['profile_special_care']);
    }

    return array('status' => 200, 'response' => $profiles);
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
    $userId = $this->db->prepare("SELECT id FROM provider WHERE username = ?");
    $userId->execute([$username]);
    $userId = $userId->fetch()['id'];

    return $userId;
  }








  /**
  * Return user private data
  *
  * @return array(userInfo)
  **/

  public  function getUserData() {

    $userInfo = $this->db->prepare("SELECT username, email, is_host,
      is_dogwalker FROM provider WHERE id = '" . $this->id . "'");

    $userInfo->execute();
    $userInfo = $userInfo->fetch();

    $userPicture = $this->db->prepare("SELECT profile_picture
      FROM provider_public_profile WHERE id = '" . $this->id . "'");

    $userPicture->execute();
    $userPicture = $userPicture->fetch()['profile_picture'];

    $userInfo['profile_picture'] = $userPicture;

    $Utils = new Utils($this->db);
    $Utils->revertMysqlBooleanToPhp($userInfo['is_host']);
    $Utils->revertMysqlBooleanToPhp($userInfo['is_dogwalker']);

    return $userInfo;
  }









  /**
  * Return return profile address data for an user based on id
  *
  * @return array(profileUserAddress)
  *
  **/

  public  function getProfileAddress() {
    $address = $this->db->prepare("SELECT * FROM provider_profile_address WHERE id = ?");
    $address->execute([$this->id]);
    $address = $address->fetch();
    unset($address['id']);

    return $address;
  }








  /**
  * Return return the status of profile address for a provider based on id
  *
  * @return status(IN_ANALYSIS | PENDING | CONFIRMED): String
  *
  **/

  public  function getProfileAddressStatus() {
    $status = $this->db->prepare("SELECT address_status FROM provider_profile_address WHERE id = ?");
    $status->execute([$this->id]);
    $status = $status->fetch()['address_status'];

    return $status;
  }







  /**
  * Return return the Status of private Data for a provider based on id
  *
  *
  * @return status(IN_ANALYSIS | PENDING | CONFIRMED): String
  *
  **/

  public  function getPrivateDataStatus() {
    $status = $this->db->prepare("SELECT private_profile_status FROM provider_private_profile WHERE id = '" . $this->id . "'");
    $status->execute();
    $status = $status->fetch()['private_profile_status'];


    return $status;
  }








  /**
  * Return private data for a provider user.
  *
  * @param $externalFields: boolean
  *
  * @return array(response, status code)
  *
  **/

  public function getPublicData($externalFields) {
    $Error = new Error();

    $profile = $this->db->prepare("SELECT * FROM provider_public_profile WHERE id = ?");
    $profile->execute([$this->id]);
    $profile = $profile->fetch();

    if (is_array($profile)) {
      unset($profile['id']);

      if ($externalFields) {
        $address = $this->db->prepare("SELECT city, state, district FROM provider_profile_address WHERE id = ?");
        $address->execute([$this->id]);
        $address = $address->fetch();

        $status = $this->db->prepare("SELECT is_host, is_dogwalker FROM provider WHERE id = ?");
        $status->execute([$this->id]);
        $status = $status->fetch();

        $host = array();

        if ($status['is_host']) {
          $host = $this->getHostData();
        }

        $dogwalker = array();

        if ($status['is_dogwalker']) {
          $dogwalker = $this->getDogwalkerData();
        }

        $profile['address'] = $address;
        $profile['status'] = $status;
        $profile['dogwalker'] = $dogwalker;
        $profile['host'] = $host;
      }

      $Utils = new Utils($this->db);

      $Utils->revertMysqlBooleanToPhp($profile['profile_animal_care_income']);
      $Utils->revertMysqlBooleanToPhp($profile['profile_oral_experience']);
      $Utils->revertMysqlBooleanToPhp($profile['profile_elderly_pets']);
      $Utils->revertMysqlBooleanToPhp($profile['profile_first_aid']);
      $Utils->revertMysqlBooleanToPhp($profile['profile_injectable_medication']);
      $Utils->revertMysqlBooleanToPhp($profile['profile_special_care']);
      $Utils->revertMysqlBooleanToPhp($profile['status']['is_host']);
      $Utils->revertMysqlBooleanToPhp($profile['status']['is_dogwalker']);

      return array('status' => 200, 'response' => $profile);
    } else {
      return array('status' => 400, 'response' => $Error->getMessage('USERNAME_DOES_NOT_EXISTS'));
    }
  }







  /**
  * Return dogwalker data for an user based on id
  *
  * @return array(profile)
  *
  **/

  public function getDogwalkerData() {
    $Utils = new Utils($this->db);
    $profile = $this->db->prepare("SELECT * FROM provider_dogwalker_data WHERE id = '" . $this->id . "'");
    $profile->execute();
    $profile = $profile->fetch();

    unset($profile['id']);

    $Utils->convertToNumeric($profile['price_30']);
    $Utils->convertToNumeric($profile['price_60']);
    $Utils->revertMysqlBooleanToPhp($profile['walking_dog_in_heat']);
    $Utils->revertMysqlBooleanToPhp($profile['walking_old_dog']);
    $Utils->revertMysqlBooleanToPhp($profile['walking_big_dog']);
    $Utils->revertMysqlBooleanToPhp($profile['walking_more_than_one_dog']);

    $photos = $this->db->prepare("SELECT photo FROM provider_dogwalker_photos WHERE user_id = '" . $this->id . "'");
    $photos->execute();
    $photos = $photos->fetchAll();

    $urls = array();

    foreach ($photos as $key => $item) {
      array_push($urls, $item['photo']);
    }

    $profile['dogwalker_photos'] = $urls;
    $Utils->unserializeAndDecode($profile['days_of_week']);

    $Utils->revertMysqlBooleanToPhp($profile['accept_dog_size_xs']);
    $Utils->revertMysqlBooleanToPhp($profile['accept_dog_size_s']);
    $Utils->revertMysqlBooleanToPhp($profile['accept_dog_size_m']);
    $Utils->revertMysqlBooleanToPhp($profile['accept_dog_size_l']);
    $Utils->revertMysqlBooleanToPhp($profile['accept_dog_size_xl']);

    $profile['preferences_dog_size'] = array(
      'xs' => $profile['accept_dog_size_xs'],
      's' => $profile['accept_dog_size_s'],
      'm' => $profile['accept_dog_size_m'],
      'l' => $profile['accept_dog_size_l'],
      'xl' => $profile['accept_dog_size_xl'],
    );

    unset($profile['accept_dog_size_xs']);
    unset($profile['accept_dog_size_s']);
    unset($profile['accept_dog_size_m']);
    unset($profile['accept_dog_size_l']);
    unset($profile['accept_dog_size_xl']);


    return $profile;
  }








  /**
  * Return host data for an user based on id
  *
  * @return array(profile)
  *
  **/

  public function getHostData() {
    $Utils = new Utils($this->db);

    $profile = $this->db->prepare("SELECT * FROM provider_host_data WHERE id = ?");
    $profile->execute([$this->id]);
    $profile = $profile->fetch();

    unset($profile['id']);

    $Utils->convertToNumeric($profile['price']);
    $Utils->revertMysqlBooleanToPhp($profile['has_smokers']);
    $Utils->revertMysqlBooleanToPhp($profile['pet_on_couch']);
    $Utils->revertMysqlBooleanToPhp($profile['pet_on_bed']);
    $Utils->revertMysqlBooleanToPhp($profile['pet_inside_house']);
    $Utils->revertMysqlBooleanToPhp($profile['pet_sleep_on_placeholder']);
    $Utils->revertMysqlBooleanToPhp($profile['preferences_dog_differents']);
    $Utils->revertMysqlBooleanToPhp($profile['preferences_dog_older_accepting']);
    $Utils->revertMysqlBooleanToPhp($profile['has_emergency_transport']);
    $Utils->revertMysqlBooleanToPhp($profile['preferences_dog_24h']);
    $Utils->revertMysqlBooleanToPhp($profile['preferences_hosts_very_young_puppies']);
    $Utils->revertMysqlBooleanToPhp($profile['preferences_uncastrated_male_dog']);
    $Utils->revertMysqlBooleanToPhp($profile['preferences_uncastrated_female_dog']);
    $Utils->revertMysqlBooleanToPhp($profile['preferences_female_dog_in_heat']);

    $photos = $this->db->prepare("SELECT photo FROM provider_host_photos WHERE user_id = ?");
    $photos->execute([$this->id]);
    $photos = $photos->fetchAll();

    $urls = array();

    foreach ($photos as $key => $item) {
      array_push($urls, $item['photo']);
    }

    $Utils->revertMysqlBooleanToPhp($profile['accept_dog_size_xs']);
    $Utils->revertMysqlBooleanToPhp($profile['accept_dog_size_s']);
    $Utils->revertMysqlBooleanToPhp($profile['accept_dog_size_m']);
    $Utils->revertMysqlBooleanToPhp($profile['accept_dog_size_l']);
    $Utils->revertMysqlBooleanToPhp($profile['accept_dog_size_xl']);

    $profile['preferences_dog_size'] = array(
      'xs' => $profile['accept_dog_size_xs'],
      's' => $profile['accept_dog_size_s'],
      'm' => $profile['accept_dog_size_m'],
      'l' => $profile['accept_dog_size_l'],
      'xl' => $profile['accept_dog_size_xl'],
    );

    unset($profile['accept_dog_size_xs']);
    unset($profile['accept_dog_size_s']);
    unset($profile['accept_dog_size_m']);
    unset($profile['accept_dog_size_l']);
    unset($profile['accept_dog_size_xl']);

    $profile['host_photos'] = $urls;
    $Utils->unserializeAndDecode($profile['days_of_week']);

    return $profile;
  }







  /**
  * Return private data for a provider user.
  *
  * @return array(response, status code)
  *
  **/

  public function getPrivateData() {
    $profile = $this->db->prepare("SELECT * FROM provider_private_profile WHERE id = '" . $this->id . "'");
    $profile->execute();
    $profile = $profile->fetch();

    unset($profile['id']);

    $photos = $this->db->prepare("SELECT photo FROM provider_house_photos WHERE user_id = '" . $this->id . "'");
    $photos->execute();
    $photos = $photos->fetchAll();

    $urls = array();

    foreach ($photos as $key => $item) {
      array_push($urls, $item['photo']);
    }

    $profile['house_photos'] = $urls;

    return $profile;
  }







  /**
  * Save the public data for a provider user.
  *
  * @param $params: (profile_picture, username, profile_title,
  * profile_bio, profile_pet_experience, profile_animal_care_income,
  * profile_oral_experience, profile_elderly_pets, profile_first_aid,
  * profile_injectable_medication, profile_special_care)
  *
  * @return array(response, status code)
  *
  **/

  public function savePublicData($params) {
    $Validator = new Validator($this->db);
    $Utils = new Utils($this->db);
    $Error = new Error();

    $validations = array(
      "profile_picture" => array(
        "required" => true,
        "empty" => false,
      ),

      "username" => array(
        "required" => true,
        "empty" => false,
      ),

      "profile_title" => array(
        "required" => true,
        "max_length" => 50,
        "empty" => false,
      ),

      "profile_bio" => array(
        "required" => true,
        "max_length" => 500,
        "empty" => false,
      ),

      "profile_pet_experience" => array(
        "required" => true,
        "empty" => false,
        "custom_validation" => "experience",
      ),

      "profile_animal_care_income" => array(
        "required" => true,
        "type" => "bool",
        "empty" => false,
      ),

      "profile_oral_experience" => array(
        "required" => true,
        "type" => "bool",
      ),

      "profile_elderly_pets" => array(
        "required" => true,
        "empty" => false,
        "type" => "bool",
      ),

      "profile_first_aid" => array(
        "required" => true,
        "empty" => false,
        "type" => "bool",
      ),

      "profile_injectable_medication" => array(
        "required" => true,
        "empty" => false,
        "type" => "bool",
      ),

      "profile_special_care" => array(
        "required" => true,
        "empty" => false,
        "type" => "bool",
      ),

      "cover_photo" => array(
        "required" => true,
        "empty" => false,
      ),
    );

    $validationError = $Validator->validateFields($params, $validations);

    if ($validationError) {
      return array('status' => 400, 'response' => $validationError);
    }

    if (!$this->checkProviderAvailable($params['username'])) {
      return array('status' => 409, 'response' => $Error->getMessage('USERNAME_ALREADY_EXISTS'));
    };

    $Utils->convertToMysqlBoolean($params['profile_animal_care_income']);
    $Utils->convertToMysqlBoolean($params['profile_oral_experience']);
    $Utils->convertToMysqlBoolean($params['profile_elderly_pets']);
    $Utils->convertToMysqlBoolean($params['profile_first_aid']);
    $Utils->convertToMysqlBoolean($params['profile_injectable_medication']);
    $Utils->convertToMysqlBoolean($params['profile_special_care']);

    $sql = $this->db->prepare('UPDATE provider SET username = ? WHERE id = ' . $this->id);
    $sql->execute([$params['username']]);

    $params['public_profile_status'] = 'CONFIRMED';

    $sql = $this->db->prepare('UPDATE provider_public_profile SET
      profile_picture = ?,
      profile_title = ?,
      profile_bio = ?,
      profile_pet_experience = ?,
      profile_animal_care_income = ?,
      profile_oral_experience = ?,
      profile_elderly_pets = ?,
      profile_first_aid = ?,
      profile_injectable_medication = ?,
      profile_special_care = ?,
      cover_photo = ?,
      public_profile_status = ?
      WHERE id = ' . $this->id);

    $sql->execute([
      $params['profile_picture'],
      $params['profile_title'],
      $params['profile_bio'],
      $params['profile_pet_experience'],
      $params['profile_animal_care_income'],
      $params['profile_oral_experience'],
      $params['profile_elderly_pets'],
      $params['profile_first_aid'],
      $params['profile_injectable_medication'],
      $params['profile_special_care'],
      $params['cover_photo'],
      $params['public_profile_status']
    ]);

    return array('status' => 204, 'response' => null);
  }







  /**
  * Save the private data for a provider user.
  *
  * @param $userId: int
  * @param $params: (name, phone_1, phone_2, gender, birth, father_name,
  * mother_name, rg_expedition, rg, rg_picture_front, rg_picture_back,
  * occupation, emergency_name, emergency_phone_1, emergency_phone_2, email)
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
      ),

      "phone_1" => array(
        "required" => true,
        "empty" => false,
        "type" => "phone",
      ),

      "phone_2" => array(
        "empty" => false,
        "type" => "phone",
        "required" => true
      ),

      "gender" => array(
        "required" => true,
        "empty" => false,
        "custom_validation" => "gender",
      ),

      "birth" => array(
        "required" => true,
        "empty" => false,
        "custom_validation" => "date",
      ),

      "occupation" => array(
        "required" => true,
        "empty" => false,
      ),

      "emergency_name" => array(
        "required" => true,
        "empty" => false,
      ),

      "emergency_phone_1" => array(
        "required" => true,
        "empty" => false,
        "type" => "phone",
      ),

      "emergency_phone_2" => array(
        "required" => true,
        "empty" => false,
        "type" => "phone",
      ),
    );

    $validationError = $Validator->validateFields($params, $validations);

    if ($validationError) {
      return array('status' => 400, 'response' => $validationError);
    }

    $Utils->extractNumbers($params['phone_1']);
    $Utils->extractNumbers($params['phone_2']);
    $Utils->extractNumbers($params['emergency_phone_1']);
    $Utils->extractNumbers($params['emergency_phone_2']);

    $status = $this->getPrivateDataStatus();

    if ($status == "PENDING") {
      $validations = array(
        "email" => array(
          "empty" => false,
          "required" => true,
          "custom_validation" => "email",
        ),

        "rg_expedition" => array(
          "empty" => false,
          "required" => true,
          "custom_validation" => "date",
        ),

        "rg" => array(
          "empty" => false,
          "required" => true,
          "max_length" => RG_MAX_LENGTH,
        ),

        "rg_picture_front" => array(
          "empty" => false,
          "required" => true,
        ),

        "rg_picture_back" => array(
          "empty" => false,
          "required" => true,
        ),

        "father_name" => array(
          "empty" => false,
          "required" => true,
        ),

        "mother_name" => array(
          "empty" => false,
          "required" => true,
        ),
      );

      $validationError = $Validator->validateFields($params, $validations);

      if ($validationError) {
        return array('status' => 400, 'response' => $validationError);
      }

      if (!$Validator->validateEmailPrimaryKey($params['email'], $this->id)) {
        return array('status' => 400, 'response' => $Error->getMessage('EMAIL_ALREADY_EXISTS'));
      }

      $Utils->extractNumbers($params['rg']);
    }

    $userHostStatus = $this->getUserData()['is_host'];
    $Utils->revertMysqlBooleanToPhp($userHostStatus);

    if ($userHostStatus) {
      $validations = array(
        "house_photos" => array(
          "required" => true,
          "type" => "list",
          "list_max_length" => 10
        ),
      );

      $validationError = $Validator->validateFields($params, $validations);

      if ($validationError) {
        return array('status' => 400, 'response' => $validationError);
      }

      $sql =  $sql = $this->db->prepare("DELETE FROM provider_house_photos WHERE user_id = ?");
      $sql->execute([$this->id]);

      $sql =  $sql = $this->db->prepare("INSERT INTO provider_house_photos SET user_id = ?, username = ?, photo = ?");

      foreach ($params['house_photos'] as $photo) {
        $sql->execute([$this->id, $this->username, $photo]);
      }
    }

    /**
    *
    * @param private_profile_status = IN_ANALYSIS
    * refers to the status of registration when user save the data for the
    * first time
    *
    */

    $params['private_profile_status'] = "IN_ANALYSIS";

    if ($status == "PENDING") {
      $sql = $this->db->prepare('UPDATE provider SET email = ? WHERE id = ' . $this->id);
      $sql->execute([$params['email'],]);

      $sql = $this->db->prepare('UPDATE provider_private_profile SET name = ?,
        father_name = ?,
        mother_name = ?,
        rg_expedition = ?,
        rg = ?,
        rg_picture_front = ?,
        rg_picture_back = ?,
        private_profile_status = ?
        WHERE id = ' . $this->id);

      $sql->execute([
        $params['name'],
        $params['father_name'],
        $params['mother_name'],
        $params['rg_expedition'],
        $params['rg'],
        $params['rg_picture_front'],
        $params['rg_picture_back'],
        $params['private_profile_status']
      ]);
    }

    $sql = $this->db->prepare('UPDATE provider_private_profile SET phone_1 = ?,
      phone_2 = ?,
      gender = ?,
      birth = ?,
      occupation = ?,
      emergency_name = ?,
      emergency_phone_1 = ?,
      emergency_phone_2 = ?,
      private_profile_status = ?
      WHERE id = ' . $this->id);

    $sql->execute([
      $params['phone_1'],
      $params['phone_2'],
      $params['gender'],
      $params['birth'],
      $params['occupation'],
      $params['emergency_name'],
      $params['emergency_phone_1'],
      $params['emergency_phone_2'],
      $params['private_profile_status']
    ]);

    return array('status' => 204, 'response' => null);
  }







  /**
  * Save the private data for a provider user.
  *
  * @param $params: (price, residence_type, external_area_size, has_smokers,
  * pet_on_couch, pet_on_bed, pet_inside_house, pet_sleep_on_placeholder,
  * has_childrens, routine, preferences_dog_size, preferences_dog_quantity,
  * preferences_dog_differents, has_emergency_transport,
  * preferences_frequency_outdoor_activity, preferences_dog_24h,
  * preferences_dog_older_accepting, preferences_hosts_very_young_puppies,
  * preferences_uncastrated_male_dog, preferences_female_dog_in_heat,
  * days_of_week, cancellation_policy, restrictions, cover_photo)
  *
  * @param external_area_size @example "CASA" || "APARTAMENTO" || "CHACARA"
  * @param preferences_dog_size @example "P" | "M" | "G" | "SEM"
  * @param has_childrens @example "NAO" || "AMBAS" || "12-" || "12+"
  * @param preferences_dog_quantity @example int between 1 to 10
  * @param preferences_frequency_outdoor_activity @example "1" || "2" || "2-4" || "4-6" || "6-8" || "8-12"
  * @param cancellation_policy @example "FLEXIVEL" || "MODERADA" || "RIGOROSA"
  * @param host_photos @example list ["url", "url"]
  * @param days_of_week @example array: bool
  *  "days_of_week" : {
  *    "monday": boolean,
  *    "tuesday": boolean,
  *    "wednesday": boolean,
  *    "thursday": boolean,
  *    "friday": boolean,
  *    "saturday": boolean,
  *    "sunday": boolean
  *  },
  *
  * @return array(response, status code)
  *
  **/

  public function saveHostData($params) {
    $Validator = new Validator($this->db);
    $Utils = new Utils($this->db);
    $Error = new Error();

    $validations = array(
      "price" => array(
        "required" => true,
        "empty" => false,
        "type" => "numeric",
        "max_value" => 250,
        "min_value" => 45,
      ),

      "residence_type" => array(
        "empty" => false,
        "required" => true,
        "custom_validation" => "residence",
      ),

      "external_area_size" => array(
        "empty" => false,
        "required" => true,
        "custom_validation" => "area_size",
      ),

      "has_smokers" => array(
        "empty" => false,
        "required" => true,
      ),

      "pet_on_couch" => array(
        "empty" => false,
        "required" => true,
      ),

      "pet_on_bed" => array(
        "empty" => false,
        "required" => true,
      ),

      "pet_inside_house" => array(
        "empty" => false,
        "required" => true,
      ),

      "pet_sleep_on_placeholder" => array(
        "empty" => false,
        "required" => true,
      ),

      "has_childrens" => array(
        "empty" => false,
        "required" => true,
        "custom_validation" => "children",
      ),

      "routine" => array(
        "empty" => false,
        "required" => true,
        "min_length" => 200,
      ),

      "preferences_dog_size" => array(
        "required" => true,
        "type" => "list",
        "empty_list" => true,
      ),

      "preferences_dog_gender" => array(
        "empty" => false,
        "required" => true,
        "custom_validation" => "dog_gender"
      ),

      "preferences_dog_quantity" => array(
        "required" => true,
        "empty" => false,
        "custom_validation" => "dog_quantity",
      ),

      "preferences_dog_differents" => array(
        "required" => true,
        "empty" => false,
        "type" => "bool",
      ),

      "has_emergency_transport" => array(
        "required" => true,
        "empty" => false,
        "type" => "bool",
      ),

      "preferences_frequency_outdoor_activity" => array(
        "required" => true,
        "empty" => false,
        "custom_validation" => "frequency_outdoor_activity",
      ),

      "preferences_dog_24h" => array(
        "required" => true,
        "empty" => false,
        "type" => "bool",
      ),

      "preferences_dog_older_accepting" => array(
        "required" => true,
        "empty" => false,
        "type" => "bool",
      ),

      "preferences_hosts_very_young_puppies" => array(
        "empty" => false,
        "required" => true,
        "type" => "bool",
      ),

      "preferences_uncastrated_male_dog" => array(
        "required" => true,
        "empty" => false,
        "type" => "bool",
      ),

      "preferences_uncastrated_female_dog" => array(
        "required" => true,
        "empty" => false,
        "type" => "bool",
      ),

      "preferences_female_dog_in_heat" => array(
        "required" => true,
        "empty" => false,
        "type" => "bool",
      ),

      "days_of_week" => array(
        "required" => true,
        "type" => "list",
        "empty_list" => true
      ),

      "cancellation_policy" => array(
        "required" => true,
        "empty" => false,
        "custom_validation" => "cancellation_policy",
      ),

      "restrictions" => array(
        "required" => true,
        "empty" => true,
      ),

      "host_photos" => array(
        "required" => true,
        "type" => "list",
        "empty_list" => true,
        "list_max_length" => 20,
      ),
    );

    $validationError = $Validator->validateProviderFields($params, $validations);

    if ($validationError) {
      return array('status' => 400, 'response' => $validationError);
    }

    $Utils->convertToMysqlBoolean($params['has_smokers']);
    $Utils->convertToMysqlBoolean($params['pet_on_couch']);
    $Utils->convertToMysqlBoolean($params['pet_on_bed']);
    $Utils->convertToMysqlBoolean($params['pet_inside_house']);
    $Utils->convertToMysqlBoolean($params['pet_sleep_on_placeholder']);
    $Utils->convertToMysqlBoolean($params['preferences_dog_differents']);
    $Utils->convertToMysqlBoolean($params['has_emergency_transport']);
    $Utils->convertToMysqlBoolean($params['preferences_dog_24h']);
    $Utils->convertToMysqlBoolean($params['preferences_dog_older_accepting']);
    $Utils->convertToMysqlBoolean($params['preferences_hosts_very_young_puppies']);
    $Utils->convertToMysqlBoolean($params['preferences_uncastrated_male_dog']);
    $Utils->convertToMysqlBoolean($params['preferences_uncastrated_female_dog']);
    $Utils->convertToMysqlBoolean($params['preferences_female_dog_in_heat']);

    $errorDogSize = $Utils->checkDogSizeListFormat($params['preferences_dog_size']);

    if ($errorDogSize) {
      return array('status' => 400, 'response' => $Error->getMessage($errorDogSize));
    }

    $errorDaysOfWeek = $Utils->checkDaysOfWeekListFormat($params['days_of_week']);

    if ($errorDaysOfWeek) {
      return array('status' => 400, 'response' => $Error->getMessage($errorDaysOfWeek));
    }

    $Utils->serializeAndEncode($params['days_of_week']);

    $sql =  $sql = $this->db->prepare("DELETE FROM provider_host_photos WHERE user_id = ?");
    $sql->execute([$this->id]);

    $sql =  $sql = $this->db->prepare("INSERT INTO provider_host_photos SET user_id = ?, username = ?, photo = ?");

    foreach ($params['host_photos'] as $photo) {
      $sql->execute([$this->id, $this->username, $photo]);
    }

    $params['host_status'] = 'CONFIRMED';

    $sql = $this->db->prepare('UPDATE provider_host_data SET
      price = ?,
      residence_type = ?,
      external_area_size = ?,
      has_smokers = ?,
      pet_on_couch = ?,
      pet_on_bed = ?,
      pet_inside_house = ?,
      pet_sleep_on_placeholder = ?,
      has_childrens = ?,
      routine = ?,
      accept_dog_size_xs = ?,
      accept_dog_size_s = ?,
      accept_dog_size_m = ?,
      accept_dog_size_l = ?,
      accept_dog_size_xl = ?,
      preferences_dog_quantity = ?,
      preferences_dog_gender = ?,
      preferences_dog_differents = ?,
      has_emergency_transport = ?,
      preferences_frequency_outdoor_activity = ?,
      preferences_dog_24h = ?,
      preferences_dog_older_accepting = ?,
      preferences_hosts_very_young_puppies = ?,
      preferences_uncastrated_male_dog = ?,
      preferences_uncastrated_female_dog = ?,
      preferences_female_dog_in_heat = ?,
      days_of_week = ?,
      cancellation_policy = ?,
      restrictions = ?,
      host_status = ?
      WHERE id = ' . $this->id);

    $sql->execute([
      $params['price'],
      $params['residence_type'],
      $params['external_area_size'],
      $params['has_smokers'],
      $params['pet_on_couch'],
      $params['pet_on_bed'],
      $params['pet_inside_house'],
      $params['pet_sleep_on_placeholder'],
      $params['has_childrens'],
      $params['routine'],
      $params['preferences_dog_size']['xs'],
      $params['preferences_dog_size']['s'],
      $params['preferences_dog_size']['m'],
      $params['preferences_dog_size']['l'],
      $params['preferences_dog_size']['xl'],
      $params['preferences_dog_quantity'],
      $params['preferences_dog_gender'],
      $params['preferences_dog_differents'],
      $params['has_emergency_transport'],
      $params['preferences_frequency_outdoor_activity'],
      $params['preferences_dog_24h'],
      $params['preferences_dog_older_accepting'],
      $params['preferences_hosts_very_young_puppies'],
      $params['preferences_uncastrated_male_dog'],
      $params['preferences_uncastrated_female_dog'],
      $params['preferences_female_dog_in_heat'],
      $params['days_of_week'],
      $params['cancellation_policy'],
      $params['restrictions'],
      $params['host_status'],
    ]);

    return array('status' => 204, 'response' => null);
  }






  /**
  * Save the dogwalker data for a provider user.
  *
  * @param $params: (price_30, price_60, walking_dog_size,
  * walking_dog_which_gender, walking_dog_in_heat, walking_old_dog,
  * walking_big_dog, walking_more_than_one_dog, days_of_week,
  * cancellation_policy, restrictions, cover_photo)
  *
  * @param cancellation_policy @example "FLEXIVEL" || "MODERADA" || "RIGOROSA"
  * @param walking_dog_which_gender @example "FEMALE" | "MALE" | "BOTH"
  * @param dogwalker_photos @example list ["url", "url"]
  * @param days_of_week: array(day_of_week: bool)
  * @example array(day_of_week: bool)
  *        "days_of_week" : {
  *          "monday": boolean,
  *          "tuesday": boolean,
  *          "wednesday": boolean,
  *          "thursday": boolean,
  *          "friday": boolean,
  *          "saturday": boolean,
  *          "sunday": boolean
  *        },
  *
  * @return array(response, status code)
  *
  **/

  public function saveDogwalkerData($params) {
    $Validator = new Validator($this->db);
    $Utils = new Utils($this->db);
    $Error = new Error();

    $validations = array(
      "price_30" => array(
        "required" => true,
        "type" => "numeric",
        "max_value" => 250,
        "min_value" => 45,
      ),

      "price_60" => array(
        "required" => true,
        "type" => "numeric",
        "max_value" => 250,
        "min_value" => 45,
      ),

      "walking_dog_size" => array(
        "required" => true,
        "custom_validation" => "dog_size",
        "empty" => false,
      ),

      "preferences_dog_size" => array(
        "required" => true,
        "type" => "list",
        "empty_list" => true,
      ),

      "walking_dog_which_gender" => array(
        "required" => true,
        "custom_validation" => "dog_gender",
        "empty" => false,
      ),

      "walking_dog_in_heat" => array(
        "required" => true,
        "type" => "bool",
      ),

      "walking_old_dog" => array(
        "required" => true,
        "type" => "bool",
      ),

      "walking_big_dog" => array(
        "required" => true,
        "type" => "bool",
      ),

      "walking_more_than_one_dog" => array(
        "required" => true,
        "type" => "bool",
      ),

      "days_of_week" => array(
        "required" => true,
        "type" => "list",
        "empty_list" => true,
      ),

      "cancellation_policy" => array(
        "required" => true,
        "custom_validation" => "cancellation_policy",
      ),

      "restrictions" => array(
        "required" => true,
        "empty" => true,
      ),

      "dogwalker_photos" => array(
        "required" => true,
        "type" => "list",
        "empty_list" => true,
        "list_max_length" => 20,
      ),
    );

    $validationError = $Validator->validateProviderFields($params, $validations);

    if ($validationError) {
      return array('status' => 400, 'response' => $validationError);
    }

    $errorDogSize = $Utils->checkDogSizeListFormat($params['preferences_dog_size']);

    if ($errorDogSize) {
      return array('status' => 400, 'response' => $Error->getMessage($errorDogSize));
    }

    $Utils->convertToMysqlBoolean($params['walking_dog_in_heat']);
    $Utils->convertToMysqlBoolean($params['walking_old_dog']);
    $Utils->convertToMysqlBoolean($params['walking_big_dog']);
    $Utils->convertToMysqlBoolean($params['walking_more_than_one_dog']);

    $errorDaysOfWeek = $Utils->checkDaysOfWeekListFormat($params['days_of_week']);

    if ($errorDaysOfWeek) {
      return array('status' => 400, 'response' => $Error->getMessage($errorDaysOfWeek));
    }

    $Utils->serializeAndEncode($params['days_of_week']);

    $sql =  $sql = $this->db->prepare("DELETE FROM provider_dogwalker_photos WHERE user_id = ?");
    $sql->execute([$this->id]);

    $sql =  $sql = $this->db->prepare("INSERT INTO provider_dogwalker_photos SET user_id = ?, username = ?, photo = ?");

    foreach ($params['dogwalker_photos'] as $photo) {
      $sql->execute([$this->id, $this->username, $photo]);
    }

    $params['dogwalker_status'] = 'CONFIRMED';

    $sql = $this->db->prepare('UPDATE provider_dogwalker_data SET
      `price_30` = ?,
      `price_60` = ?,
      `accept_dog_size_xs` = ?,
      `accept_dog_size_s` = ?,
      `accept_dog_size_m` = ?,
      `accept_dog_size_l` = ?,
      `accept_dog_size_xl` = ?,
      `walking_dog_which_gender` = ?,
      `walking_dog_in_heat` = ?,
      `walking_old_dog` = ?,
      `walking_big_dog` = ?,
      `walking_more_than_one_dog` = ?,
      `days_of_week` = ?,
      `cancellation_policy` = ?,
      `restrictions` = ?,
      `dogwalker_status` = ?
      WHERE id = ' . $this->id);

    $sql->execute([
      $params['price_30'],
      $params['price_60'],
      $params['preferences_dog_size']['xs'],
      $params['preferences_dog_size']['s'],
      $params['preferences_dog_size']['m'],
      $params['preferences_dog_size']['l'],
      $params['preferences_dog_size']['xl'],
      $params['walking_dog_which_gender'],
      $params['walking_dog_in_heat'],
      $params['walking_old_dog'],
      $params['walking_big_dog'],
      $params['walking_more_than_one_dog'],
      $params['days_of_week'],
      $params['cancellation_policy'],
      $params['restrictions'],
      $params['dogwalker_status']
    ]);

    return array('status' => 204, 'response' => null);
  }







  /**
  * Save the Address data for a provider user.
  *
  * @param $params: array(zip, number, complement,
  * district, city, state, picture_proof_of_address)
  *
  * @return array(response, status code)
  *
  **/

  public function saveAddressData($params) {

    $Validator = new Validator($this->db);
    $Utils = new Utils($this->db);
    $Error = new Error();

    $validations = array(
      "zip" => array(
        "required" => true,
        "custom_validation" => "zip",
        "empty" => false,
      ),

      "street" => array(
        "required" => true,
        "empty" => false,
      ),

      "number" => array(
        "required" => true,
        "empty" => false,
      ),

      "complement" => array(
        "required" => true,
        "empty" => true,
      ),

      "district" => array(
        "required" => true,
        "empty" => false,
      ),

      "city" => array(
        "required" => true,
        "empty" => false,
      ),

      "state" => array(
        "required" => true,
        "empty" => false,
        "length" => 2,
      ),

      "picture_proof_of_address" => array(
        "required" => true,
        "empty" => false,
      ),
    );

    $validationError = $Validator->validateProviderFields($params, $validations);

    if ($validationError) {
      return array('status' => 400, 'response' => $validationError);
    }

    $Utils->extractNumbers($params['zip']);

    $params['address_status'] = "IN_ANALYSIS";

    $sql = $this->db->prepare('UPDATE provider_profile_address SET
      zip = ?,
      street = ?,
      number = ?,
      complement = ?,
      district = ?,
      city = ?,
      state = ?,
      picture_proof_of_address = ?,
      address_status = ?
      WHERE id = ' . $this->id);

    $sql->execute([
      $params['zip'],
      $params['street'],
      $params['number'],
      $params['complement'],
      $params['district'],
      $params['city'],
      $params['state'],
      $params['picture_proof_of_address'],
      $params['address_status']
    ]);

    return array('status' => 204, 'response' => null);
  }






  /**
  * Return login data
  *
  * @param $params: (login, password)
  * @var $params['login'] = cpf | email
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

    $validationError = $Validator->validateProviderFields($params, $validations);

    if ($validationError) {
      return array('status' => 400, 'response' => $validationError);
    }

    if (preg_match('/^\d+$/', $params['login'])) {
      if (strlen($params['login']) == CPF_LENGTH) {
        $login = $this->db->prepare("SELECT email FROM provider WHERE cpf = ?");
        $login->execute([$params['login']]);
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

    $user = $this->db->prepare("SELECT access_token, password FROM provider WHERE email = ?");
    $user->execute([$params['login']]);
    $user = $user->fetch();

    if ($user && password_verify($params['password'], $user['password'])) {
      $logged = $this->db->prepare("SELECT access_token, username FROM provider WHERE email = ?");
      $logged->execute([$params['login']]);
      $logged = $logged->fetch();
      $logged['type'] = "PROVIDER";

      return array('status' => 200, 'response' => $logged);
    } else {
      return array('status' => 400, 'response' => $Error->getMessage('NOT_AUTHENTICATED'));
    }
  }





  /**
  * Create a provider user in database
  *
  * @param $params: (name, email, password, cpf)
  * @param name must to have a space between two words: @example XXXX XXXXX
  * @param password must be at least 8 characters: @example XXXXXXXX
  *
  * @return array(response, status code)
  *
  **/

  public function create($params, $type) {

    $Validator = new Validator($this->db);
    $Utils = new Utils($this->db);
    $Error = new Error();

    $validations = array(
      "email" => array(
        "required" => true,
        "empty" => false,
        "custom_validation" => "email",
      ),

      "name" => array(
        "required" => true,
        "empty" => false,
        "min_words" => 2,
      ),

      "password" => array(
        "required" => true,
        "empty" => false,
        "min_length" => PASSWORD_MIN_LENGTH,
      ),

      "cpf" => array(
        "required" => true,
        "empty" => false,
        "custom_validation" => "cpf",
      ),
    );

    $validationError = $Validator->validateFields($params, $validations);

    if ($validationError) {
      return array('status' => 400, 'response' => $validationError);
    }

    $Utils->extractNumbers($params['cpf']);
    $cpf = $params['cpf'];

    // Check if user exist
    $user_cpf = $this->db->prepare("SELECT cpf FROM provider WHERE cpf = ?");
    $user_cpf->execute([$cpf]);
    $user_cpf = $user_cpf->fetchAll();

    if ($user_cpf) {
      return array('status' => 403, 'response' => $Error->getMessage('CPF_ALREADY_EXISTS'));
    }

    $user_email = $this->db->prepare("SELECT email FROM provider WHERE email = ?");
    $user_email->execute([$params['email']]);
    $user_email = $user_email->fetchAll();

    if ($user_email) {
      return array('status' => 403, 'response' => $Error->getMessage('EMAIL_ALREADY_EXISTS'));
    }

    $params['username'] = $Utils->generateProviderUsername($params['name']);

    $access_token = md5(uniqid($cpf, true));

    $params['password'] =  password_hash($params['password'], PASSWORD_DEFAULT);

    $params['is_dogwalker'] = 0;
    $params['is_host'] = 0;

    if ($type === 'DOGWALKER') {
      $params['is_dogwalker'] = 1;
    } elseif ($type === 'HOST') {
      $params['is_host'] = 1;
    }

    $sql = $this->db->prepare('INSERT INTO provider (username, email, cpf, access_token, password, is_host, is_dogwalker) VALUES (?, ?, ?, ?, ?, ?, ?)');

    $sql->execute([
      $params['username'],
      $params['email'],
      $cpf,
      $access_token,
      $params['password'],
      $params['is_host'],
      $params['is_dogwalker']
    ]);

    $sql_update = $this->db->prepare('UPDATE provider_private_profile SET name = ? WHERE username = ?');

    $sql_update->execute([
      $params['name'],
      $params['username']
    ]);

    $registred = $this->db->prepare("SELECT access_token, username FROM provider WHERE cpf = '" . $cpf . "'");
    $registred->execute();
    $registred = $registred->fetch();
    $registred['type'] = "PROVIDER";

    return array('status' => 201, 'response' => $registred);
  }
}
