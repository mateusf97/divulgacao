<?php

namespace Utils;

class Utils {
  public function __construct($db) {
    $this->db = $db;
  }





  /**
  * Convert var in numeric value
  *
  * @param $string
  * @global @var changed by reference
  *
  **/

  public function convertToNumeric(&$number) {
    $number = (double) $number;
  }





  /**
  * Revert response in Boolean value
  *
  * @param $string
  * @global @var changed by reference
  *
  **/

  public function revertMysqlBooleanToPhp(&$var) {
    $var = (boolean) (int) $var;
  }




  /**
  * Convert dogsize to database column name
  *
  **/

  public function convertDogSizeField(&$dogSize) {

    if ($dogSize == "MP") {
      $dogSize = "xs";
    }

    if ($dogSize == "P") {
      $dogSize = "s";
    }

    if ($dogSize == "M") {
      $dogSize = "m";
    }

    if ($dogSize == "G") {
      $dogSize = "l";
    }

    if ($dogSize == "MG") {
      $dogSize = "xl";
    }
  }





  /**
  * Convert response in Boolean value
  *
  * @param $string
  * @global @var changed by reference
  *
  **/

  public function convertToMysqlBoolean(&$var) {
    $var = (int) (boolean) $var;
  }







  /**
  * Utils to remove Letters And Special Characters
  *
  * @param $string
  * Change by reference the @global @var
  *
  **/

  public function extractNumbers(&$string) {
    $string = preg_replace("/[^0-9]/", "", $string);
  }







  /**
  * Utils to transform generic data in php serialized data
  *
  * @param $data
  * Change by reference the @global @var
  *
  **/

  public  function serializeAndEncode(&$data) {
    $data = serialize(json_encode($data));
  }







  /**
  * Utils to transform generic data in php serialized data
  *
  * @param $data
  * Change by reference the @global @var
  *
  **/

  public  function unserializeAndDecode(&$data) {
    $data = json_decode(unserialize($data));
  }







  /**
  * Check if valid array Days of week
  *
  * @param $daysOfWeek - Array
  * @return boolean
  **/

  public  function checkDaysOfWeekListFormat($daysOfWeek) {

    if (!isset($daysOfWeek['monday'])) {
      return "MISSING_MONDAY";
    }

    if (!is_bool($daysOfWeek['monday'])) {
      return "INVALID_MONDAY";
    }

    if (!isset($daysOfWeek['tuesday'])) {
      return "MISSING_TUESDAY";
    }

    if (!is_bool($daysOfWeek['tuesday'])) {
      return "INVALID_TUESDAY";
    }

    if (!isset($daysOfWeek['wednesday'])) {
      return "MISSING_WEDNESDAY";
    }

    if (!is_bool($daysOfWeek['wednesday'])) {
      return "INVALID_WEDNESDAY";
    }

    if (!isset($daysOfWeek['thursday'])) {
      return "MISSING_THURSDAY";
    }

    if (!is_bool($daysOfWeek['thursday'])) {
      return "INVALID_THURSDAY";
    }

    if (!isset($daysOfWeek['friday'])) {
      return "MISSING_FRIDAY";
    }

    if (!is_bool($daysOfWeek['friday'])) {
      return "INVALID_FRIDAY";
    }

    if (!isset($daysOfWeek['saturday'])) {
      return "MISSING_SATURDAY";
    }

    if (!is_bool($daysOfWeek['saturday'])) {
      return "INVALID_SATURDAY";
    }

    if (!isset($daysOfWeek['sunday'])) {
      return "MISSING_SUNDAY";
    }

    if (!is_bool($daysOfWeek['sunday'])) {
      return "INVALID_SUNDAY";
    }

    foreach ($daysOfWeek as $key => $validateArrayDaysOfWeek) {
      if ($validateArrayDaysOfWeek) {
        return false;
      }
    }

    return "INVALID_CHECK_DAY_OF_WEEK";
  }






  /**
  * Check if valid array Dog Size
  *
  * @param $dogSize - Array(xs, s, m, l, xl)
  * Change by reference the @global @var
  *
  * @return boolean
  **/

  public  function checkDogSizeListFormat(&$dogSize) {

    if (!isset($dogSize['xs'])) {
      return "MISSING_SIZE_XS";
    }

    if (!is_bool($dogSize['xs'])) {
      return "INVALID_SIZE_XS";
    }

    $this->convertToMysqlBoolean($dogSize['xs']);

    if (!isset($dogSize['s'])) {
      return "MISSING_SIZE_S";
    }

    if (!is_bool($dogSize['s'])) {
      return "INVALID_SIZE_S";
    }

    $this->convertToMysqlBoolean($dogSize['s']);

    if (!isset($dogSize['m'])) {
      return "MISSING_SIZE_M";
    }

    if (!is_bool($dogSize['m'])) {
      return "INVALID_SIZE_MAY";
    }

    $this->convertToMysqlBoolean($dogSize['m']);

    if (!isset($dogSize['l'])) {
      return "MISSING_SIZE_L";
    }

    if (!is_bool($dogSize['l'])) {
      return "INVALID_SIZE_L";
    }

    $this->convertToMysqlBoolean($dogSize['l']);

    if (!isset($dogSize['xl'])) {
      return "MISSING_SIZE_XL";
    }

    if (!is_bool($dogSize['xl'])) {
      return "INVALID_SIZE_XL";
    }

    $this->convertToMysqlBoolean($dogSize['xl']);

    foreach ($dogSize as $key => $validateArrayDaysOfWeek) {
      if ($validateArrayDaysOfWeek) {
        return false;
      }
    }

    return "INVALID_CHECK_DOG_SIZE";
  }







  /**
  * Check duplicated provider username
  *
  * @param $backupUsername: String
  * @param $username: String
  *
  * @return username: String
  *
  **/

  private  function generateClientUsernameIfDuplicated($username, $backupUsername) {
    $exists = $this->db->prepare("SELECT username FROM client WHERE username = '" . $username . "'");
    $exists->execute();
    $exists = $exists->fetchAll();

    if ($exists) {
      $username = $backupUsername . rand();
      return $this->generateClientUsernameIfDuplicated($username, $backupUsername);
    }

    return $username;
  }







  /**
  * Check duplicated provider username
  *
  * @param $backupUsername: String
  * @param $username: String
  *
  * @return username: String
  *
  **/

  private  function generateProviderUsernameIfDuplicated($username, $backupUsername) {
    $exists = $this->db->prepare("SELECT username FROM provider WHERE username = '" . $username . "'");
    $exists->execute();
    $exists = $exists->fetchAll();

    if ($exists) {
      $username = $backupUsername . rand();
      return $this->generateProviderUsernameIfDuplicated($username, $backupUsername);
    }

    return $username;
  }







  /**
  * Generate an Provider Username Based on Name
  *
  * @param $name: String
  *
  * @return username: String
  *
  **/


  public  function generateProviderUsername($name) {
    $arrayName = explode(' ', trim($name));
    $firstName = $arrayName[0];
    $lastName = $arrayName[count($arrayName) - 1];
    $username = strtolower($firstName . $lastName);

    return $this->generateProviderUsernameIfDuplicated($username, $username);
  }





  /**
  * Generate an Client Username Based on Name
  *
  * @param $name: String
  *
  * @return username: String
  *
  **/

  public  function generateClientUsername($name) {
    $arrayName = explode(' ', trim($name));
    $firstName = $arrayName[0];
    $lastName = $arrayName[count($arrayName) - 1];
    $username = strtolower($firstName . $lastName);

    return $this->generateClientUsernameIfDuplicated($username, $username);
  }
}
