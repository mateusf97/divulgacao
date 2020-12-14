<?php

use \Output\Output;
use \Client\Client;


/**
* Route to register a new client user
*
* @return 20# | 40# and response
*
**/

$app->post('/client_register', function ($request, $response, array $args) {
  $params = $request->getParsedBody();

  $Client = new Client($this->db);
  $Output = new Output();

  $res = $Client->create($params);

  return $Output->response($response, $res['status'], $res['response']);
});
