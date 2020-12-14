<?php

use \Output\Output;
use \Client\Client;


/**
* Autheticate client user
*
* @return 20# | 40# and response
*
**/

$app->post('/client_login', function ($request, $response, array $args) {
  $params = $request->getParsedBody();

  $Client = new Client($this->db);
  $Output = new Output();

  $res = $Client->login($params);

  return $Output->response($response, $res['status'], $res['response']);
});

