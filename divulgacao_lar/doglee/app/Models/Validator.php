<?php

namespace Validator;
use \Utils\Utils;
use \Error\Error;
use DateTime;

class Validator {
  private $validations;
  private $db;
  private $ignore_keys;
  private $dog_info_max_length;

  public function __construct($db) {
    $this->db = $db;
    $this->dog_info_max_length = 128;
  }





  /**
  * Validate generic fields.
  *
  * @param $fields = data array
  * @return bool - true = valid | false = not valid
  *
  **/

  public function validateFields($fields, $validations) {
    $Error = new Error();
    $Utils = new Utils($this->db);

    foreach ($validations as $field => $validation) {
      if (array_key_exists("required", $validation) && $validation['required']) {
        if (!isset($fields[$field])) {
          return $Error->getMessage("MISSING_" . $field);
        }
      }

      if ((array_key_exists('empty', $validation) && !$validation['empty'])) {
        if ((!strlen($fields[$field]) && !is_bool($fields[$field])) || (empty($fields[$field]) && !is_bool($fields[$field]))) {
          return $Error->getMessage("INVALID_" . $field);
        }
      }

      if (array_key_exists("max_value", $validation)) {
        if ($fields[$field] > $validation['max_value']) {
          return $Error->getMessage("INVALID_" . $field);
        }
      }

      if (array_key_exists("min_value", $validation)) {
        if ($fields[$field] < $validation['min_value']) {
          return $Error->getMessage("INVALID_" . $field);
        }
      }

      if (array_key_exists("max_length", $validation)) {
        if (strlen($fields[$field]) > $validation['max_length']) {
          return $Error->getMessage("INVALID_" . $field);
        }
      }

      if (array_key_exists("min_length", $validation)) {
        if (strlen($fields[$field]) < $validation['min_length']) {
          return $Error->getMessage("INVALID_" . $field);
        }
      }

      if (array_key_exists("length", $validation)) {
        if (strlen($fields[$field]) != $validation['length']) {
          return $Error->getMessage("INVALID_" . $field);
        }
      }

      if (array_key_exists("empty_list", $validation)) {
        if (count($fields[$field]) == 0) {
          return $Error->getMessage("INVALID_" . $field);
        }
      }

      if (array_key_exists("list_max_length", $validation)) {
        if (count($fields[$field]) > $validation["list_max_length"]) {
          return $Error->getMessage("INVALID_" . $field);
        }
      }

      if (array_key_exists("min_words", $validation)) {
        $words = explode(" ", trim($fields[$field]));

        if (count($words) < $validation["min_words"]) {
          return sprintf($Error->getMessage('INVALID_WORDS'), $field);
        }
      }

      if (array_key_exists("type", $validation)) {
        if ($validation['type'] == "positive_integer") {
          if (!$this->validatePostiveInteger($fields[$field])) {
            return $Error->getMessage("INVALID_" . $field);
          }
        }

        if ($validation['type'] == "bool") {
          if (!is_bool($fields[$field])) {
            return $Error->getMessage("INVALID_" . $field);
          }
        }

        if ($validation['type'] == "list") {
          if (!is_array($fields[$field])) {
            return $Error->getMessage("INVALID_" . $field);
          }
        }

        if ($validation['type'] == "numeric") {
          if (!is_numeric($fields[$field])) {
            return $Error->getMessage("INVALID_" . $field);
          }
        }
      }

      if (array_key_exists("custom_validation", $validation)) {
        if ($validation['custom_validation'] == "email") {
          if (!filter_var($fields[$field], FILTER_VALIDATE_EMAIL)) {
            return $Error->getMessage("INVALID_" . $field);
          }
        }

        if ($validation['custom_validation'] == "gender") {
          if (!$this->validateGender($fields[$field])) {
            return $Error->getMessage("INVALID_" . $field);
          }
        }

        if ($validation['custom_validation'] == "cpf") {
          if (!$this->validateCpf($fields[$field])) {
            return $Error->getMessage('INVALID_CPF');
          }
        }

        if ($validation['custom_validation'] == "phone") {
          $phone = $fields[$field];
          $Utils->extractNumbers($phone);

          if (strlen($phone) < 10 || strlen($phone) > 11) {
            return $Error->getMessage("INVALID_" . $field);
          }
        }

        if ($validation['custom_validation'] == "date") {
          $date = $fields[$field];
          $format =  'd/m/Y';

          $d = DateTime::createFromFormat($format, $date);
          if (!($d && $d->format($format) == $date)) {
            return $Error->getMessage("INVALID_" . $field);
          }
        }
      }
    }

    return null;
  }









  /**
  * Validate provider fields.
  *
  * @param $fields = data array
  * @return bool - true = valid | false = not valid
  *
  **/

  public function validateProviderFields($fields, $validations) {
    $Error = new Error();
    $Utils = new Utils($this->db);

    $genericValidatorError = $this->validateFields($fields, $validations);

    if ($genericValidatorError) {
      return $genericValidatorError;
    }

    foreach ($validations as $field => $validation) {
      if (array_key_exists("custom_validation", $validation)) {
        if ($validation['custom_validation'] == "zip") {
          $zip = $fields[$field];
          $Utils->extractNumbers($zip);

          if (strlen($zip) != CEP_LENGTH) {
            return $Error->getMessage("INVALID_" . $field);
          }
        }

        if ($validation['custom_validation'] == "experience") {
          if (!$this->validateprofilepetexperience($fields[$field])) {
            return $Error->getMessage("INVALID_" . $field);
          }
        }

        if ($validation['custom_validation'] == "residence") {
          if (!$this->validateResidenceType($fields[$field])) {
            return $Error->getMessage("INVALID_" . $field);
          }
        }

        if ($validation['custom_validation'] == "dog_size") {
          if (!$this->validateDogSize($fields[$field])) {
            return $Error->getMessage("INVALID_" . $field);
          }
        }

        if ($validation['custom_validation'] == "area_size") {
          if (!$this->validateExternalAreaSize($fields[$field])) {
            return $Error->getMessage("INVALID_" . $field);
          }
        }

        if ($validation['custom_validation'] == "children") {
          if (!$this->validateHasChildrens($fields[$field])) {
            return $Error->getMessage("INVALID_" . $field);
          }
        }

        if ($validation['custom_validation'] == "dog_gender") {
          if (!$this->validateWhichDogGender($fields[$field])) {
            return $Error->getMessage("INVALID_" . $field);
          }
        }

        if ($validation['custom_validation'] == "dog_quantity") {
          if (!$this->validateDogQuantity($fields[$field])) {
            return $Error->getMessage("INVALID_" . $field);
          }
        }

        if ($validation['custom_validation'] == "cancellation_policy") {
          if (!$this->validateCancellationPolicy($fields[$field])) {
            return $Error->getMessage("INVALID_" . $field);
          }
        }

        if ($validation['custom_validation'] == "frequency_outdoor_activity") {
          if (!$this->validateFrequencyOutdoorActivity($fields[$field])) {
            return $Error->getMessage("INVALID_" . $field);
          }
        }
      }
    }
  }










  /**
  * Validate dog fields
  *
  * @param $fields = data array
  * @return bool - true = valid | false = not valid
  *
  **/

  public function validateDogFields($fields, $validations) {
    $Error = new Error();
    $genericValidatorError = $this->validateFields($fields, $validations);

    if ($genericValidatorError) {
      return $genericValidatorError;
    }

    foreach ($validations as $field => $validation) {

      if (array_key_exists("custom_validation", $validation)) {
        if ($validation['custom_validation'] == "size") {
          if (!$this->validateDogSize($fields[$field])) {
            return $Error->getMessage("INVALID_" . $field);
          }
        }

        if ($validation['custom_validation'] == "pet_info_verbose") {
          if (!$this->validateVerbosePetInfo($fields[$field])) {
            return $Error->getMessage("INVALID_" . $field);
          }
        }
      }
    }
  }












  /**
  * Check if is a valid provider type
  *
  * @param string $provider
  * @example "HOST" || "VET" || "DOGWALKER"
  * @return bool - true = valid | false = not valid
  *
  **/

  public function validateProvider($provider) {

    if ($provider === "HOST" || $provider === "DOGWALKER") {
      return true;
    }

    return false;
  }





  /**
  * Check if is a valid positive non zero integer
  *
  * @param int $page
  * @return bool - true = valid | false = not valid
  *
  **/

  public function validatePostiveInteger(&$number) {
    if (!is_numeric($number)) {
      return false;
    }

    $number = (int) $number;

    /**
    * This casting was necessary, because PHP interpred the
    *query @param number like String
    *
    */

    if (!is_integer($number)) {
      return false;
    }

    if ($number <= 0) {
      return false;
    }

    return true;
  }







  /**
  * Check if is a valid cep format
  *
  * @param int $userId, int $dogId
  * @return bool - true = valid | false = not valid
  *
  **/

  public function validateCep(&$cep) {
    $cep = preg_replace("/[^0-9]/", "", $cep);

    if (strlen($cep) == CEP_LENGTH) {
      return true;
    }

    return false;
  }





  /**
  * Check if is a valid dog owner for Provider
  *
  * @param int $userId, int $dogId
  * @return bool - true = valid | false = not valid
  *
  **/

  public function validateProviderDogOwner($dogId, $userId) {
    $validOwner = $this->db->prepare('SELECT count(*) AS total FROM provider_dogs WHERE id = ? AND user_id = ?');
    $validOwner->execute([$dogId, $userId]);
    $validOwner = (boolean) $validOwner->fetch()['total'];

    if ($validOwner) {
      return true;
    }

    return false;
  }







  /**
  * Check if is a valid dog owner for Client
  *
  * @param int $userId, int $dogId
  * @return bool - true = valid | false = not valid
  *
  **/

  public function validateClientDogOwner($dogId, $userId) {
    $validOwner = $this->db->prepare('SELECT count(*) AS total FROM client_dogs WHERE id = ? AND user_id = ?');
    $validOwner->execute([$dogId, $userId]);
    $validOwner = (boolean) $validOwner->fetch()['total'];

    if ($validOwner) {
      return true;
    }

    return false;
  }





  /**
  * Check if is a valid email format
  *
  * @param int $userId, int $dogId
  * @return bool - true = valid | false = not valid
  *
  **/

  public function validateEmail($email) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return true;
    }

    return false;
  }





  /**
  * Check if is unique email in database
  *
  * @param int $userId, int $dogId
  * @return bool - true = valid | false = not valid
  *
  **/

  public function validateEmailPrimaryKey($email, $userId) {
    $alreadyExists = $this->db->prepare('SELECT count(*) AS total FROM provider WHERE email = ? AND id <> ?');
    $alreadyExists->execute([$email, $userId]);
    $alreadyExists = (boolean) $alreadyExists->fetch()['total'];

    if ($alreadyExists) {
      return false;
    }

    return true;
  }







  /**
  * Check if is a valid Cancelation Policy
  *
  * @param $policy = string
  * @example FLEXIVEL | MODERADA | RIGOROSA
  * @return bool - true = valid | false = not valid
  *
  **/

  public function validateCancellationPolicy($policy) {

    if ($policy == "FLEXIVEL" || $policy == "MODERADA" || $policy == "RIGOROSA") {
      return true;
    }

    return false;
  }







  /**
  * Check if is a valid frequency outdoor activity
  *
  * @param $frequency = int
  * @example "1" || "2" || "2-4" || "4-6" || "6-8" || "8-12"
  * @return bool - true = valid | false = not valid
  *
  **/

  public function validateFrequencyOutdoorActivity(&$frequency) {

    $frequency = (string) $frequency;

    /**
    * Casting in @var $frequency is necessary because PHP
    * understands that $frequency == "1" or $frequency == "integer"
    * is a valid boolean.
    *
    * The comparison is made with === (strict equals operator) for the same reason
    */

    if ($frequency === "1" || $frequency === "2" || $frequency === "2-4" || $frequency === "4-6" || $frequency === "6-8" || $frequency === "8-12") {
      return true;
    }

    return false;
  }






  /**
  * Check if is a valid dog gender
  *
  * @param $gender = string
  * @example "FEMEA" | "MACHO" | "TODOS"
  * @return bool - true = valid | false = not valid
  *
  **/

  public function validateWhichDogGender($gender) {


    if ($gender == "FEMEA" || $gender == "MACHO" || $gender == "TODOS") {
      return true;
    }

    return false;
  }





  /**
  * Check if is a valid verbose pet info string
  *
  * @param $string = string
  * @return bool - true = valid | false = not valid
  *
  **/

  public function validateVerbosePetInfo($string) {

    if ((strlen($string) > 0) && (strlen($string) <= $this->dog_info_max_length)) {
      return true;
    }

    return false;
  }






  /**
  * Check if is a valid residenceType
  *
  * @param $residenceType = string
  * @example "CASA" || "APARTAMENTO" || "CHACARA"
  * @return bool - true = valid | false = not valid
  *
  **/

  public function validateResidenceType($residenceType) {


    if ($residenceType == "CASA" || $residenceType == "APARTAMENTO" || $residenceType == "CHACARA") {
      return true;
    }

    return false;
  }






  /**
  * Check if is a valid dog quantity host same time
  *
  * @param $residenceType = int
  * @example 1 - 10
  * @return bool - true = valid | false = not valid
  *
  **/

  public function validateDogQuantity($quantity) {

    if (is_numeric($quantity) && is_integer($quantity) && ($quantity <= 10) && ($quantity >= 1)) {
      return true;
    }

    return false;
  }





  /**
  * Check if is a valid Has Childrens fields
  *
  * @param $residenceType = string
  * @example "MP" || "P" || "M" || "G" || "MG"
  * @return bool - true = valid | false = not valid
  *
  **/

  public function validateDogSize($dogSize) {

    if ($dogSize == "MP" || $dogSize == "P" || $dogSize == "M" || $dogSize == "G" || $dogSize == "MG") {
      return true;
    }

    return false;
  }





  /**
  * Check if is a valid Has Childrens fields
  *
  * @param $residenceType = string
  * @example "NAO" || "AMBAS" || "12-" || "12+"
  * @return bool - true = valid | false = not valid
  *
  **/

  public function validateHasChildrens($hasChildrens) {

    if ($hasChildrens == "NAO" || $hasChildrens == "AMBAS" || $hasChildrens == "12-" || $hasChildrens == "12+") {
      return true;
    }

    return false;
  }






  /**
  * Check if is a valid External Area Size
  *
  * @param $externalAreaSize = string
  * @example "P" | "M" | "G" | "SEM"
  * @return bool - true = valid | false = not valid
  *
  **/

  public function validateExternalAreaSize($externalAreaSize) {

    if ($externalAreaSize == "P" || $externalAreaSize == "M" || $externalAreaSize == "G" || $externalAreaSize == "SEM") {
      return true;
    }

    return false;
  }






  /**
  * Check if is a valid interget value and Cast this value
  *
  * @param $experience = int
  * @return bool - true = valid | false = not valid
  *
  **/

  public function validateProfilePetExperience(&$experience) {

    if ($experience == 1 || $experience == 2 || $experience == 3 || $experience == 6) {
      $experience = (int) $experience;
      return true;
    }

    return false;
  }










  /**
  * Check if is a valid gender
  *
  * @param $gender = caracter
  * @example F | M | O
  * @return bool - true = valid | false = not valid
  *
  **/

  public function validateGender($gender) {

    if ($gender == "M" || $gender == "F" || $gender == "O") {
      return true;
    }

    return false;
  }










  /**
  * Validate CPF
  *
  * @param $cpf = CPF number
  * @return bool - true = valid | false = not valid
  *
  **/

  public function validateCpf($cpf = null) {
    $Utils = new Utils($this->db);

    if(empty($cpf)) {
      return false;
    }

    // Remove mask
    $Utils->extractNumbers($cpf);

    // Varify if length is to equal 11
    if (strlen($cpf) != 11) {
      return false;
    }

    // Verify if invalid sequences below was typed. If yes, return false
    else if ($cpf == '00000000000' ||
      $cpf == '11111111111' ||
      $cpf == '22222222222' ||
      $cpf == '33333333333' ||
      $cpf == '44444444444' ||
      $cpf == '55555555555' ||
      $cpf == '66666666666' ||
      $cpf == '77777777777' ||
      $cpf == '88888888888' ||
      $cpf == '99999999999') {
      return false;

      //Verify if is a valid cpf
    } else {
      for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
          $d += $cpf{$c} * (($t + 1) - $c);
        }

        $d = ((10 * $d) % 11) % 10;

        if ($cpf{$c} != $d) {
          return false;
        }
      }

      return true;
    }
  }

}
