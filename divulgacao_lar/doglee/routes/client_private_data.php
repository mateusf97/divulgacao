<?php

use \Output\Output;
use \Client\Client;
use \Authentication\Authentication;


/**
* Route update the private data for an Client
*
* @return 20# | 40# and response
*
**/

$app->patch('/client_data', function ($request, $response, array $args) {
  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));
  $Output = new Output();

  if (!$Authentication->isValid("CLIENT")) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $params = $request->getParsedBody();
  $Client = new Client($this->db, $Authentication->getToken());

  $res = $Client->savePrivateData($params);

  return $Output->response($response, $res['status'], $res['response']);
});






/**
* Route get the private data for a client
*
* @return 200 | 40# and response
*
**/

$app->get('/client_data', function ($request, $response, array $args) {
  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));
  $Output = new Output();

  if (!$Authentication->isValid("CLIENT")) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $Client = new Client($this->db, $Authentication->getToken());
  $res = $Client->getClientData();

  return $Output->response($response, 200, $res);
});
