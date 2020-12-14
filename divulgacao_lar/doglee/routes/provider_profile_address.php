<?php

use \Output\Output;
use \Provider\Provider;
use \Authentication\Authentication;


/**
* Route update the profile address for a provider
*
* @return 20# | 40# and response
*
**/

$app->patch('/provider_profile_address', function ($request, $response, array $args) {
  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));
  $Output = new Output();

  if (!$Authentication->isValid("PROVIDER")) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $Provider = new Provider($this->db, $Authentication->getToken());
  $params = $request->getParsedBody();

  $res = $Provider->saveAddressData($params);

  return $Output->response($response, $res['status'], $res['response']);
});







/**
* Route get the profile address for a provider
*
* @return 20# | 40# and response
*
**/

$app->get('/provider_profile_address', function ($request, $response, array $args) {
  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));
  $Output = new Output();

  if (!$Authentication->isValid("PROVIDER")) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $Provider = new Provider($this->db, $Authentication->getToken());
  $res = $Provider->getProfileAddress();

  return $Output->response($response, 200, $res);
});






/**
* Route get the profile address data status for a provider
*
* @return 20# | 40# and response
*
**/

$app->get('/provider_profile_address_status', function ($request, $response, array $args) {
  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));
  $Output = new Output();

  if (!$Authentication->isValid("PROVIDER")) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $Provider = new Provider($this->db, $Authentication->getToken());
  $res = $Provider->getProfileAddressStatus();

  return $Output->response($response, 200, $res);
});
