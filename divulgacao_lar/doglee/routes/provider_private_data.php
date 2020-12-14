<?php

use \Output\Output;
use \Provider\Provider;
use \Authentication\Authentication;


/**
* Route update the private data for a provider
*
* @return 20# | 40# and response
*
**/

$app->patch('/provider_private_data', function ($request, $response, array $args) {
  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));
  $Output = new Output();

  if (!$Authentication->isValid("PROVIDER")) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $Provider = new Provider($this->db, $Authentication->getToken());

  $params = $request->getParsedBody();
  $res = $Provider->savePrivateData($params);

  return $Output->response($response, $res['status'], $res['response']);
});






/**
* Route get the private data for a provider
*
* @return 20# | 40# and response
*
**/

$app->get('/provider_private_data', function ($request, $response, array $args) {
  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));
  $Output = new Output();

  if (!$Authentication->isValid("PROVIDER")) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $Provider = new Provider($this->db, $Authentication->getToken());
  $res = $Provider->getPrivateData();

  return $Output->response($response, 200, $res);
});






/**
* Route get the private data status for a provider
*
* @return 20# | 40# and response
*
**/

$app->get('/provider_private_data_status', function ($request, $response, array $args) {
  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));
  $Output = new Output();

  if (!$Authentication->isValid("PROVIDER")) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $Provider = new Provider($this->db, $Authentication->getToken());
  $res = $Provider->getPrivateDataStatus();

  return $Output->response($response, 200, $res);
});








/**
* Route get the private data status for a provider
*
* @return 20# | 40# and response
*
**/

$app->get('/provider_check_username_available/{username}', function ($request, $response, array $args) {
  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));
  $Output = new Output();

  if (!$Authentication->isValid("PROVIDER")) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $Provider = new Provider($this->db, $Authentication->getToken());

  if (!$Provider->checkProviderAvailable($args['username'])) {
    return $Output->response($response, 409, 'ALREADY_EXISTS_USERNAME');
  }

  return $Output->response($response, 200, 'AVAILABLE');
});








/**
* Route get the provider user data
*
* @return 200 | 40# and response
*
**/

$app->get('/provider', function ($request, $response, array $args) {
  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));
  $Output = new Output();

  if (!$Authentication->isValid("PROVIDER")) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $Provider = new Provider($this->db, $Authentication->getToken());
  $data = $Provider->getUserData();

  return $Output->response($response, 200, $data);
});
