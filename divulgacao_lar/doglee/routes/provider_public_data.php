<?php

use \Output\Output;
use \Provider\Provider;
use \Authentication\Authentication;


/**
* Route update the public data for a provider
*
* @return 20# | 40# and response
*
**/

$app->patch('/provider_public_data', function ($request, $response, array $args) {
  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));
  $Output = new Output();

  if (!$Authentication->isValid("PROVIDER")) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $Provider = new Provider($this->db, $Authentication->getToken());
  $params = $request->getParsedBody();

  $res = $Provider->savePublicData($params);

  return $Output->response($response, $res['status'], $res['response']);
});






/**
* Route get the public data for a provider
*
* @return 20# | 40# and response
*
**/

$app->get('/provider_public_data', function ($request, $response, array $args) {
  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));
  $Output = new Output();

  if (!$Authentication->isValid("PROVIDER")) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $Provider = new Provider($this->db, $Authentication->getToken());
  $externalFields = false;

  $res = $Provider->getPublicData($externalFields);

  return $Output->response($response, $res['status'], $res['response']);
});





/**
* Route get the public data for a provider
*
* @return 20# | 40# and response
*
**/

$app->get('/provider_public_data/{username}', function ($request, $response, array $args) {
  $Output = new Output();
  $Provider = new Provider($this->db);

  $providerId = $Provider->getIdByUsername($args['username']);
  $Provider->setId($providerId);

  $externalFields = true;
  $res = $Provider->getPublicData($externalFields);

  return $Output->response($response, $res['status'], $res['response']);
});
