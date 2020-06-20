<?php

  function extractNumbers(&$string) {
    $string = preg_replace("/[^0-9]/", "", $string);
  }


?>