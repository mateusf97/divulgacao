<?php

namespace Validator;
use \Utils\Utils;

class Validator {
  public function __construct($db) {
    $this->db = $db;
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

    if (strlen($cep) == 8) {
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
