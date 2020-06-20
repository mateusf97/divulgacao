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
}
