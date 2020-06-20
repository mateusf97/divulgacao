<?php

namespace Output;

class Output {

  /**
  * Provide the response on request
  *
  * @param $response = response instance: Response @example ($response)
  * @param $code = status code to return: int
  * @param $msg = response content: String or Array
  *
  * @return json
  *
  **/

  public function response($response, $code, $msg) {
    return $response->withStatus($code)
      ->withHeader('Content-Type', 'text/html')
      ->write(json_encode($msg));
  }
}
