<?php

use \Output\Output;
use \Provider\Provider;
use \Authentication\Authentication;






/**
* Route get provider host data for specific user
*
* @return 20# | 40# and response
*
**/

$app->get('/provider_host_data', function ($request, $response, array $args) {
  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));
  $Output = new Output();

  if (!$Authentication->isValid("PROVIDER")) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $Provider = new Provider($this->db, $Authentication->getToken());
  $res = $Provider->getHostData();

  return $Output->response($response, 200, $res);
});






/**
* Route update the private data for a provider
*
* @return 20# | 40# and response
*
**/

$app->patch('/provider_host_data', function ($request, $response, array $args) {
  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));
  $Output = new Output();

  if (!$Authentication->isValid("PROVIDER")) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $Provider = new Provider($this->db, $Authentication->getToken());
  $params = $request->getParsedBody();

  $res = $Provider->saveHostData($params);

  return $Output->response($response, $res['status'], $res['response']);
});
